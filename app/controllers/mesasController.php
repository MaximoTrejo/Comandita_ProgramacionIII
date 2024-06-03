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

}