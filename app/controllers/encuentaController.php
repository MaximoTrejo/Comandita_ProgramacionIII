<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';

class EncuestaController extends Encuesta implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {

        $params = $request->getParsedBody();

        $encuesta = new Encuesta();
        $encuesta->idMesa = intval($params['idMesa']);
        $encuesta->idPedido = $params['ID_F_pedido'];
        $encuesta->puntMesa = intval($params['puntMesa']);
        $encuesta->puntRestaurante = intval($params['puntRestaurante']);
        $encuesta->puntMozo = intval($params['puntMozo']);
        $encuesta->puntCocina = intval($params['puntCocina']);
        $encuesta->comentarios = $params['comentarios'];

        $encuesta->CrearEncuesta();

        $payload = json_encode(array("msg" => "Encuesta subida con exito!"));
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application\json');
    }

    public function TraerTodos($request, $response, $args)
    {


        $lista = Encuesta::ObtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));


        $response->getBody()->write($payload);
        return $response ->withHeader('Content-Type', 'application/json');
    }

}