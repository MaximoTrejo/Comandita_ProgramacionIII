<?php
require_once './models/Productos.php';
require_once './interfaces/IApiUsable.php';

class productosController extends Productos implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $rol = $parametros['rol'];

        // Creamos el pd
        $usr = new Productos();
        $usr->nombre = $nombre;
        $usr->precio = $precio;
        $usr->rol = $rol;
        
        $usr->crearProductos();

        $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Productos::obtenerTodos();
        $payload = json_encode(array("listaProductos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ExportarCsv($request, $response, $args)
    {
        $lista = Productos::obtenerTodos();
        // Definir la ruta y el nombre del archivo CSV
        $filePath = BASE_PATH . '/Archivos/';
        $file = fopen($filePath, 'w');
        // Escribir la cabecera del archivo CSV
        fputcsv($file, array('id', 'nombre','precio','rol'));

        // Escribir los datos de cada mesa en el archivo CSV
        foreach ($lista as $obj) {
            fputcsv($file, array($obj->id, $obj->nombre, $obj->precio,$obj->rol));
        }

        // Cerrar el archivo
        fclose($file);

        $payload = json_encode(array("mensaje" => "Se exporto el csv"));
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