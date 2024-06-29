<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;


class  AuthRolesMW{

    public function  __invoke(Request $request,RequestHandler $handler){

        $params = $request ->getParsedBody();
        $roles = ['bartender', 'cervecero', 'cocinero', 'mozo', 'socio'];

        $response = new Response();
        $header = $request->getHeaderLine('Authorization');

        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            try {
                AutentificadorJWT::VerificarToken($token);
                $dataJWT = AutentificadorJWT::ObtenerData($token);

                if(in_array($dataJWT->rol,$roles)){
                    $response = $handler->handle($request);
                }else{
                    $response->getBody()->write(json_encode(array("msg" => "El rol del usuario no es valido")));
                }

            }catch (Exception $ex) {
				$response->getBody()->write($ex->getMessage());
			}
        
        }
        return  $response;
        
    }

}


class  chekRolesMW{

    public function  __invoke(Request $request,RequestHandler $handler){

        $params = $request ->getParsedBody();
        $roles = ['bartender', 'cervecero', 'cocinero', 'mozo', 'socio', 'cliente'];

        if(isset($params["rol"])){

            if(in_array($params['rol'],$roles,true)){

                $response = $handler ->handle($request);
            }else{
                $response = new Response();
                $response->getBody()->write(json_encode(array("error"=> "Revise el rol ingresado!"  )));
            }

        }else{
            $response = new Response();
            $response->getBody()->write(json_encode(array("error"=> "Rol incorrecto")));
        }
        return  $response;

    }

}

class AuthSocioMW{

    public function  __invoke(Request $request, RequestHandler $handler)
    {
        $response = new Response();
        $header = $request->getHeaderLine('Authorization');
        if (!empty($header)) {

            $token = trim(explode("Bearer", $header)[1]);
            try {
                AutentificadorJWT::VerificarToken($token);


                $dataJWT = AutentificadorJWT::ObtenerData($token);

                if(!strcasecmp($dataJWT->rol, "socio")){
                    $response = $handler->handle($request);
                }else{
                    $response->getBody()->write(json_encode(array("msg" => "Solo los socios pueden realizar esta accion!")));
                }

            }catch (Exception $ex) {
				$response->getBody()->write($ex->getMessage());
			}
        
        } else {
            //si no estan completos los parametros le digo al usuario que faltan cargar las credenciales
            $response = new Response();
            $response->getBody()->write(json_encode(array("msg" => "No hay un token registrado. Inicie sesion.")));
        }
        return  $response;
    }

}

class AuthMozoMW{

    public function  __invoke(Request $request, RequestHandler $handler)
    {
        $response = new Response();
        $header = $request->getHeaderLine('Authorization');
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            try {
                AutentificadorJWT::VerificarToken($token);
                $dataJWT = AutentificadorJWT::ObtenerData($token);
                if(!strcasecmp($dataJWT->rol, "mozo")){
                    $response = $handler->handle($request);
                }else{
                    $response->getBody()->write(json_encode(array("msg" => "Solo los socios pueden realizar esta accion!")));
                }

            }catch (Exception $ex) {
				$response->getBody()->write($ex->getMessage());
			}
        
        } else {
            //si no estan completos los parametros le digo al usuario que faltan cargar las credenciales
            $response = new Response();
            $response->getBody()->write(json_encode(array("msg" => "No hay un token registrado. Inicie sesion.")));
        }
        return  $response;
    }

}


class  AuthEstadoMW{

    public function  __invoke(Request $request, RequestHandler $handler)
    {
        $response = new Response();
        $header = $request->getHeaderLine('Authorization');
        if (!empty($header)) {

            $token = trim(explode("Bearer", $header)[1]);
            try {
                AutentificadorJWT::VerificarToken($token);

                $dataJWT = AutentificadorJWT::ObtenerData($token);

                if(!strcasecmp($dataJWT->estado, "activo")){
                    $response = $handler->handle($request);
                }else{
                    $response->getBody()->write(json_encode(array("msg" => "Solo los usuarios activos pueden realizar esta accion!")));
                }

            }catch (Exception $ex) {
				$response->getBody()->write($ex->getMessage());
			}
        
        } else {
            //si no estan completos los parametros le digo al usuario que faltan cargar las credenciales
            $response = new Response();
            $response->getBody()->write(json_encode(array("msg" => "No hay un token registrado. Inicie sesion.")));
        }
        return  $response;
    }

}
