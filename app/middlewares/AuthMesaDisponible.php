<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;

class AuthMesaDisponible{

    public function  __invoke(Request $request,RequestHandler $handler){
        $parametros = $request->getParsedBody();

        if(isset($parametros["idMesa"])){

            $mesa = $parametros["idMesa"];
            $mesaEncontrada = Mesas::obtenerUno($mesa);

            if($mesaEncontrada != null){

                if($mesaEncontrada[0]->estado = "DISPONIBLE"){
                    $response = $handler->handle($request);
                }else{
                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "La mesa no esta disponible")));
                }
               
            }else{
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "La mesa no existe")));
            }


        }
        return  $response;


    }

}


class AuthMesaExiste{

    public function  __invoke(Request $request,RequestHandler $handler){
        
        $parametros = $request->getParsedBody();

        if(isset($parametros["idMesa"])){

            $mesa = $parametros["idMesa"];
            $mesaEncontrada = Mesas::obtenerUno($mesa);

            if($mesaEncontrada != null){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "La mesa no existe")));
            }


        }
        return  $response;


    }

}