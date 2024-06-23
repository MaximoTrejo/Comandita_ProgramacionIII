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
        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function ExportarCsv($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        // Definir la ruta y el nombre del archivo CSV
        $filePath = BASE_PATH . '/Archivos/';// Asegúrate de que esta ruta sea válida y tenga permisos de escritura
        $file = fopen($filePath, 'w');
        // Escribir la cabecera del archivo CSV
        fputcsv($file, array('id', 'usuario', 'clave', 'rol'));

        // Escribir los datos de cada mesa en el archivo CSV
        foreach ($lista as $obj) {
            fputcsv($file, array($obj->id, $obj->usuario, $obj->clave, $obj->rol));
        }

        // Cerrar el archivo
        fclose($file);

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
                            'hora' => date('H:i:s')
                        )
                    );

                    if (!empty($jwt)) {
                        //setcookie("token", $jwt, time() + 1800, '/', "localhost", false, true);
                        $payload = json_encode(array("jwt" => $jwt));
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

        if (Usuario::SubirDatosCsv()){
            $payload = json_encode(array("msg" => "Los datos del archivo se subieron correctamente!"));
        }else{
            $payload = json_encode(array("msg" => "Hubo un problema al subir los datos del archivo."));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
