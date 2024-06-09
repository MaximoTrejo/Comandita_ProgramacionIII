
<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;



class AuthSocioMW{

    public function  __invoke(Request $request, RequestHandler $handler)
    {
        $params = $request->getQueryParams();

        if (isset($params["usuario"]) && isset($params["clave"])) {
            $usuario = $params["usuario"];
            $clave = $params["clave"];

            //traiga el usuario 
            $usuarioEncontrado = Usuario::TraerPorNombreClave($usuario, $clave);
            //valido que la consulta no venga vacia 
            if ($usuarioEncontrado != null) {

                if ($usuarioEncontrado[0]->rol == "socio") {
                    $response = $handler->handle($request);
                } else {
                    //si no es igual le digo al usuario que revise las ceredenciales ingresadas 
                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "El usuario no es socio")));
                }
            } else {
                //si no encuentro al usuario le digo al usuario que su usuario no existe 
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Usuario no encntrado")));
            }
        } else {
            //si no estan completos los parametros le digo al usuario que faltan cargar las credenciales
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "No hay credeciales")));
        }
        return  $response;
    }

}

class AuthMozoMW{

    public function  __invoke(Request $request, RequestHandler $handler)
    {
        $params = $request->getQueryParams();

        if (isset($params["usuario"]) && isset($params["clave"])) {
            $usuario = $params["usuario"];
            $clave = $params["clave"];

            //traiga el usuario 
            $usuarioEncontrado = Usuario::TraerPorNombreClave($usuario, $clave);
            //valido que la consulta no venga vacia 
            if ($usuarioEncontrado != null) {

                if ($usuarioEncontrado[0]->rol == "mozo") {
                    $response = $handler->handle($request);
                } else {
                    //si no es igual le digo al usuario que revise las ceredenciales ingresadas 
                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "El usuario no es socio")));
                }
            } else {
                //si no encuentro al usuario le digo al usuario que su usuario no existe 
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Usuario no encntrado")));
            }
        } else {
            //si no estan completos los parametros le digo al usuario que faltan cargar las credenciales
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "No hay credeciales")));
        }
        return  $response;
    }

}
