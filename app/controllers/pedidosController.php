<?php

//llamado a clase usaurio 
require_once './models/Pedidos.php';

//llamado a index 
require_once './interfaces/IApiUsable.php';

class pedidosController extends Pedidos implements IApiUsable
{
    //
    public  function CargarUno($request, $response, $args)
    {
        //traer datos desde el compose
        $parametros = $request->getParsedBody();
        
        // Creamos el usuario
        $usr = new Pedidos();
        $usr->idUsuario = $parametros['idUsuario'];
        $usr->idArticulo = $parametros['idArticulo'];
        $usr->idArticulo = $parametros['idMesa'];
        $usr->total = $parametros['total'];
        $usr->estado = $parametros['estado'];
        $usr->foto = $parametros['foto'];
        $usr->puntuacion = $parametros['puntuacion'];
        $usr->descripcionPuntuacion = $parametros['descripcionPuntuacion'];
        //llamado a funcion 
        $usr->crearPedido();

        //revisar
        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedidos::obtenerTodos();
        $payload = json_encode(array("listaPedido" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

}