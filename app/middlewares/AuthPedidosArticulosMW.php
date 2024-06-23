<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;


class AuthArticulosMW{

    public function  __invoke(Request $request,RequestHandler $handler){

        $params = $request ->getParsedBody();

        if(isset($params["ID_pedido"]) && isset($params["ID_articulo"])){

            $articulo = $params["ID_articulo"];
            $pedido = $params["ID_pedido"];

            $pedidoEncontrado = Ped_Productos::obtePedPenID($pedido,$articulo);

            if($pedidoEncontrado != null){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "No existe ese pedido con ese articulo")));
            }
        }
        return  $response;
    }




}