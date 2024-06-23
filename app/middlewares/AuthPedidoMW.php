<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;

class AuthPedidoMW{

    public function  __invoke(Request $request,RequestHandler $handler){
        $params = $request ->getQueryParams();

        if(isset($params["ID_F_pedido"])){

            $pedido = $params["ID_F_pedido"];

            $pedidoEncontrado = Pedidos::obtenerPedido_ID($pedido);

            if($pedidoEncontrado!= null){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "No existe ese pedido")));
            }

        }
        return  $response;
    }



}