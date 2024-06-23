<?php

//llamado a clase usaurio 
require_once './models/Mesas.php';

//llamado a index 
require_once './interfaces/IApiUsable.php';

class mesasController extends Mesas implements IApiUsable
{
    //
    public  function CargarUno($request, $response, $args)
    {
        //traer datos desde el compose
        $parametros = $request->getParsedBody();
        
        // Creamos el usuario
        $usr = new  Mesas();
        $usr->estado = $parametros['estado'];
        //llamado a funcion 
        $usr->crearMesa();

        //revisar
        $payload = json_encode(array("mensaje" => "Mesa creado con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesas::obtenerTodos();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function ExportarCsv($request, $response, $args)
    {
        $lista = Mesas::obtenerTodos();
        // Definir la ruta y el nombre del archivo CSV

        $filePath = BASE_PATH . '/Archivos/';
        $file = fopen($filePath, 'w');
        // Escribir la cabecera del archivo CSV
        fputcsv($file, array('id', 'estado'));

        // Escribir los datos de cada mesa en el archivo CSV
        foreach ($lista as $obj) {
            fputcsv($file, array($obj->id, $obj->estado));
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