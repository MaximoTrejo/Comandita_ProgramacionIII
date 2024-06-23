<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
//DB
require_once './db/AccesoDatos.php';

//Middleware
require_once './middlewares/AuthRolesMW.php';
require_once './middlewares/AuthPedidosArticulosMW.php';
require_once './middlewares/AuthPedidoMW.php';

//Controllers 
require_once './controllers/usuarioController.php';
require_once './controllers/mesasController.php';
require_once './controllers/productosController.php';
require_once './controllers/pedidosController.php';

/*
// Load ENV
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
*/

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();



$app->post('/login', \UsuarioController::class .':Login');

define ('BASE_PATH',__DIR__);

//Usuarios
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->post('/CargaCsv', \UsuarioController::class . ':CargaCsv');
    $group->post('/ExportarCsv', \UsuarioController::class . ':ExportarCsv');
    $group->post('[/]', \UsuarioController::class . ':CargarUno') ->add (new chekRolesMW);
})->add(new AuthSocioMW())
->add(new AuthRolesMW());

//Productos
$app->group('/Productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \productosController::class . ':TraerTodos');
    $group->post('/CargaCsv', \productosController::class . ':CargaCsv');
    $group->post('/ExportarCsv', \productosController::class . ':ExportarCsv');
    $group->post('[/]', \productosController::class . ':CargarUno') ->add(new AuthSocioMW());
})->add(new AuthRolesMW());

//Mesas
$app->group('/Mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \mesasController::class . ':TraerTodos');
    $group->post('/CargaCsv', \mesasController::class . ':CargaCsv');
    $group->post('/ExportarCsv', \mesasController::class . ':ExportarCsv');
    $group->post('[/]', \mesasController::class . ':CargarUno')->add(new AuthSocioMW());
})->add(new AuthRolesMW());


//------------------------------------------------------------------------------------------------------------------
//Pedidos mozos
$app->group('/Pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \pedidosController::class . ':TraerTodos');
    $group->post('[/]', \pedidosController::class . ':CargarUno')->add(new AuthMozoMW());
    $group->get('/TraerPedidosPendientes', \pedidosController::class . ':TraerPedidosPendientesID')->add(new AuthMozoMW());
    $group->post('/Entregar', \pedidosController::class . ':FinalizarPedido');
})->add(new AuthRolesMW());

//bartender
$app->group('/PedidosPendientesBarterder', function (RouteCollectorProxy $group) {
    $group->get('[/]', \pedidosController::class . ':TraerPedidosPendientesBartender');
    $group->post('[/]', \pedidosController::class . ':FinalizarEstadoProducto')->add(new AuthArticulosMW());
})->add(new AuthRolesMW());

//cocineros
$app->group('/PedidosPendientesCocinero', function (RouteCollectorProxy $group) {
    $group->get('[/]', \pedidosController::class . ':TraerPedidosPendientesCocineros');
    $group->post('[/]', \pedidosController::class . ':FinalizarEstadoProducto')->add(new AuthArticulosMW());
})->add(new AuthRolesMW());

//cerveceros
$app->group('/PedidosPendientesCerveceros', function (RouteCollectorProxy $group) {
    $group->get('[/]', \pedidosController::class . ':TraerPedidosPendientesCerveceros');
    $group->post('[/]', \pedidosController::class . ':FinalizarEstadoProducto')->add(new AuthArticulosMW());
})->add(new AuthRolesMW());


//-----------------------------------------------------------------------------------------------------------------------------
//mensaje random
$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "MaximoTrejo - La comandita"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});


$app->run();