<?php

//llamado a clase usaurio 
require_once './models/Usuario.php';

//llamado a index 
require_once './interfaces/IApiUsable.php';
require_once './utils/AutentificadorJWT.php';

class UsuarioController extends usuario implements IApiUsable
{
    public  function CargarUno($request, $response, $args)
    {
        //traer datos desde el compose
        $parametros = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);  
        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $rol = $parametros['rol'];
        // Creamos el usuario
        $usr = new usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->rol = $rol;
        //llamado a funcion 
        $usr->crearUsuario();
        //revisar
        self::AlmacenarLog($token,"CargarUno");
        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);  
 
        $payload = json_encode(array("listaUsuario" => $lista));

        self::AlmacenarLog($token,"TraerTodos");

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function ExportarCsv($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        // Definir la ruta y el nombre del archivo CSV
        $filePath = BASE_PATH .'/Archivos/Usuarios.csv';// Asegúrate de que esta ruta sea válida y tenga permisos de escritura

        $file = fopen($filePath, 'w');
        // Escribir la cabecera del archivo CSV
        fputcsv($file, array('id', 'usuario', 'clave', 'rol'));

        // Escribir los datos de cada mesa en el archivo CSV
        foreach ($lista as $obj) {
            fputcsv($file, array($obj->id, $obj->usuario, $obj->clave, $obj->rol));
        }

        // Cerrar el archivo
        fclose($file);
        self::AlmacenarLog($token,"ExportarCSV");
        $payload = json_encode(array("mensaje" => "Se exporto el csv"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Login($request, $response, $args)
    {
        $params = $request->getParsedBody();
        if (isset($params['id']) && isset($params['usuario']) && isset($params['clave'])) {


            $usuario = Usuario::TraerPorId($params['id']);


            if (!empty($usuario)) {


                if (!strcasecmp($params['usuario'], $usuario[0]->usuario) && $params['clave'] == $usuario[0]->clave) {

                    //$payload = json_encode(array("mensaje" => "OK", 'rol' => $usuario[0]->rol));

                    $jwt = AutentificadorJWT::CrearToken(
                        array(
                            'id' => $usuario[0]->id,
                            'rol' => $usuario[0]->rol,
                            'fecha' => date('Y-m-d'),
                            'hora' => date('H:i:s'),
                            'estado'=>$usuario[0]->estado
                        )
                    );

                    if (!empty($jwt)) {
                        $payload = json_encode(array("jwt" => $jwt));
                        self::AlmacenarLog($jwt,"Login");
                    } else {
                        $payload = json_encode(array("mensaje" => "No se pudo crear el token "));
                    }
                } else {
                    //Borra cookie existente
                    //setcookie("token", " ", time() - 3600, "/", "localhost", false, true);
                    $payload = json_encode(array("mensaje" => "Los datos del usuario #{$params['id']} no coinciden."));
                }
            } else {
                $payload = json_encode(array("mensaje" => "No existe un usuario con ese id."));
            }
        } else {
            $response->getBody()->write(json_encode(array("mensaje" => "Ingrese los datos para el login!")));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargaCsv($request, $response, $args)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        if (Usuario::SubirDatosCsv()){
            self::AlmacenarLog($token,"CargarCSV");
            $payload = json_encode(array("msg" => "Los datos del archivo se subieron correctamente!"));
        }else{
            $payload = json_encode(array("msg" => "Hubo un problema al subir los datos del archivo."));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function AlmacenarLog($jwt,$accion)
	{
		try {
			AutentificadorJWT::VerificarToken($jwt);
			$data = AutentificadorJWT::ObtenerData($jwt);
			$objAccesoDatos = AccesoDatos::ObtenerInstancia();
			$req = $objAccesoDatos->PrepararConsulta("INSERT INTO logs (idUser, fecha, hora ,accion) VALUES (:idUser, :fecha, :hora,:accion)");
			$req->bindValue(':idUser', $data->id, PDO::PARAM_INT);
			$req->bindValue(':fecha', $data->fecha, PDO::PARAM_STR);
			$req->bindValue(':hora', $data->hora, PDO::PARAM_STR);
            $req->bindValue(':accion', $accion, PDO::PARAM_STR);
			$req->execute();
		} catch (Exception $ex) {
			throw new Exception("Error al almacenar el log.");
		}
	}

    public  function EstadoEmpleado($request, $response, $args)
    {
        //traer datos desde el compose
        $parametros = $request->getParsedBody();
        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $estado = $parametros['estado'];
        $usu = new Usuario;
        $usuarioEncontrado = $usu ->TraerPorNombreClave($usuario,$clave);

        if(!empty($usuarioEncontrado)){ 

            $usu ->modificarEstado($estado,$usuarioEncontrado[0]->id);
            $payload = json_encode(array("mensaje" => "El estado se modifico con exito"));

        }else{
            $payload = json_encode(array("mensaje" => "El usuario no existe"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

}
