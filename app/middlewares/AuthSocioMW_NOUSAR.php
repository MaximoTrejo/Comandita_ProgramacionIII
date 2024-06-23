
<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;



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
