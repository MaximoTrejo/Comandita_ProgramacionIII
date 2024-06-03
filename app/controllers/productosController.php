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
    

}