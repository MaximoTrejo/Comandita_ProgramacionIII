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

require_once './db/AccesoDatos.php';

require_once './middlewares/Logger.php';

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

// Routes

//Usuarios
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
});

//Productos
$app->group('/Productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \productosController::class . ':TraerTodos');
    $group->post('[/]', \productosController::class . ':CargarUno');
});

//Mesas
$app->group('/Mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \mesasController::class . ':TraerTodos');
    $group->post('[/]', \mesasController::class . ':CargarUno');
});

//Pedidos
$app->group('/Pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \pedidosController::class . ':TraerTodos');
    $group->post('[/]', \pedidosController::class . ':CargarUno');
});

$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});


$app->run();