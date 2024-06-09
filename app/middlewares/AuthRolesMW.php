<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;


class  AuthRolesMW{

    public function  __invoke(Request $request,RequestHandler $handler){

        $params = $request ->getQueryParams();
        $roles = ['bartender', 'cervecero', 'cocinero', 'mozo', 'socio'];

        if(isset($params["rol"])){

            if(in_array($params['rol'],$roles,true)){

                $response = $handler ->handle($request);
            }else{
                $response = new Response();
                $response->getBody()->write(json_encode(array("error"=> "Revise el rol ingresado!"  )));
            }

        }else{
            $response = new Response();
            $response->getBody()->write(json_encode(array("error"=> "No hay credeciales")));
        }
        return  $response;
        
    }



}