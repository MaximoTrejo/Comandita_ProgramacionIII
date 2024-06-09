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
require_once './middlewares/AuthSocioMW.php';
require_once './middlewares/AuthRolesMW.php';
require_once './middlewares/AuthUsuariosMW.php';

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

// Routes

//Usuarios
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
})->add(new AuthSocioMW())
->add(new AuthRolesMW())
->add(new AuthUsuariosMW());

//Productos
$app->group('/Productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \productosController::class . ':TraerTodos');
    $group->post('[/]', \productosController::class . ':CargarUno') ->add(new AuthSocioMW());
})->add(new AuthRolesMW())
->add(new AuthUsuariosMW());

//Mesas
$app->group('/Mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \mesasController::class . ':TraerTodos');
    $group->post('[/]', \mesasController::class . ':CargarUno')->add(new AuthSocioMW());
})->add(new AuthRolesMW())
->add(new AuthUsuariosMW());

//Pedidos
$app->group('/Pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \pedidosController::class . ':TraerTodos');
    $group->post('[/]', \pedidosController::class . ':CargarUno')->add(new AuthMozoMW());
})->add(new AuthRolesMW())
->add(new AuthUsuariosMW());


$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "MaximoTrejo - La comandita"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});


$app->run();