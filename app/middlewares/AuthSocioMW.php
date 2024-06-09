
<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;



class AuthSocioMW{

    public function __invoke (Request $request, RequestHandler $handler){
        $params = $request ->getQueryParams();
        $rol =$params["rol"];

        if(isset( $rol)){
            if($rol == "socio"){
                $response = $handler ->handle($request);
            }else{
                $response = new Response();
                $response->getBody()->write(json_encode(array("error"=> "No sos socio" )));
            }

        }else{
            $response = new Response();
            $response->getBody()->write(json_encode(array("error"=> "No hay credeciales")));
        }
        return  $response;
    }

}
