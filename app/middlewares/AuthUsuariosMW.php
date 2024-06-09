<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;

class AuthUsuariosMW{

    public function  __invoke(Request $request,RequestHandler $handler){
        //traigo los parametros 
        $params = $request ->getQueryParams();
        $usuario = $params["usuario1"];
        $clave = $params["clave"];
        
        //valido que haya algo en los parametros 
        if(isset($usuario) && isset($clave)){
            //traiga el usuario 
            $usuarioEncontrado = Usuario::TraerPorNombreClave($usuario,$clave);
            //valido que la consulta no venga vacia 
            if($usuarioEncontrado != null){

                //al devolver un array en ver de un objeto del tipo Usuario lo que hago es agarrar el primer dato 
                //y sacar el usuario y la clave y compararlo con los que traigo por  parametros  
                if ($usuarioEncontrado[0]->usuario == $usuario && $usuarioEncontrado[0]->clave == $clave) {
                    //sigue la app
                    $response = $handler->handle($request);
    
                } else {
                    //si no es igual le digo al usuario que revise las ceredenciales ingresadas 
                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "Revisa las credenciales")));
                }

                
            }else{
                //si no encuentro al usuario le digo al usuario que su usuario no existe 
                $response = new Response();
                $response->getBody()->write(json_encode(array("error"=> "Usuario no encntrado")));
            }
            
        }else{
            //si no estan completos los parametros le digo al usuario que faltan cargar las credenciales
            $response = new Response();
            $response->getBody()->write(json_encode(array("error"=> "No hay credeciales")));
        }

        return  $response;
    }






}