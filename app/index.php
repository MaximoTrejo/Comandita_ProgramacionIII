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
require_once './middlewares/AuthMesaDisponible.php';

//Controllers 
require_once './controllers/usuarioController.php';
require_once './controllers/mesasController.php';
require_once './controllers/productosController.php';
require_once './controllers/pedidosController.php';
require_once './controllers/encuentaController.php';
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
    $group->put('/ModificarEstado', \UsuarioController::class . ':EstadoEmpleado');
    $group->post('[/]', \UsuarioController::class . ':CargarUno') ->add (new chekRolesMW);
    $group->post('/MesaMasUsada', \pedidosController::class . ':TraerMesaMasUsada');
    $group->post('/MejoresComentarios', \pedidosController::class . ':TraerMejoresComentarios');
    $group->post('/Socios/PedidoNoEntregadoEnTiempoEstipulado', \pedidosController::class . ':PedidoNoEntregadosTiempo')->add(new AuthPedidoMW());
    $group->post('/Socios/TodosPedidosNoEntregadoEnTiempoEstipulado', \pedidosController::class . ':TodosPedidosNoEntregadosTiempo');
})->add(new AuthSocioMW())
->add(new AuthRolesMW())
->add(new AuthEstadoMW());

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
    $group->post('/CerrarMesa', \mesasController::class . ':CerrarMesa')->add(new AuthMesaExiste())->add(new AuthSocioMW());
})->add(new AuthRolesMW())
->add(new AuthEstadoMW());

//------------------------------------------------------------------------------------------------------------------
//Pedidos mozos
$app->group('/Pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \pedidosController::class . ':TraerTodos');
    $group->post('[/]', \pedidosController::class . ':CargarUno')->add(new AuthMozoMW())->add(new AuthMesaDisponible()); 
    $group->get('/TraerPedidosPendientes', \pedidosController::class . ':TraerPedidosPendientesMozo')->add(new AuthMozoMW());    
    $group->post('/Entregar', \pedidosController::class . ':FinalizarPedido')->add(new AuthPedidoMW());//(mesa a comiendo)
    //cuenta(cambiar el estado de la mesa por pagando)
    $group->post('/cuenta', \PedidosController::class . ':pedirCuenta')->add(new AuthPedidoMW())->add(new AuthMozoMW()); 
    //pago(cambiar el estado de la mesa por disponible)
    $group->post('/pagar', \PedidosController::class . ':pagarCuenta')->add(new AuthPedidoMW())->add(new AuthMozoMW()); 
    $group->post('/cargarFoto', \pedidosController::class . ':CargarFoto')->add(new AuthPedidoMW());
    $group->post('/Cliente/verPedido', \pedidosController::class . ':verPedidoCliente')->add(new AuthPedidoMW())->add(new AuthMesaDisponible()); 
    $group->post('/Socios/verPedidosFinalizados', \pedidosController::class . ':verPedidosFinalizados')->add(new AuthSocioMW());
    $group->post('/DescargarPDF', \pedidosController::class . ':PDF')->add(new AuthPedidoMW());
})->add(new AuthRolesMW())
->add(new AuthEstadoMW());

//bartender
$app->group('/PedidosPendientesBarterder', function (RouteCollectorProxy $group) {
    $group->get('[/]', \pedidosController::class . ':TraerPedidosPendientesBartender');
    $group->post('/cambiarEstadoPreparado', \pedidosController::class . ':CambiarEstadoProductoPreparacion')->add(new AuthArticulosMW())->add(new AuthPedidoMW());
    $group->post('/cambiarEstadoListo', \pedidosController::class . ':CambiarEstadoProductoListo')->add(new AuthArticulosMW())->add(new AuthPedidoMW());
})->add(new AuthRolesMW())
->add(new AuthEstadoMW());
//cocineros
$app->group('/PedidosPendientesCocinero', function (RouteCollectorProxy $group) {
    $group->get('[/]', \pedidosController::class . ':TraerPedidosPendientesCocineros');
    $group->post('/cambiarEstadoPreparado', \pedidosController::class . ':CambiarEstadoProductoPreparacion')->add(new AuthArticulosMW())->add(new AuthPedidoMW());
    $group->post('/cambiarEstadoListo', \pedidosController::class . ':CambiarEstadoProductoListo')->add(new AuthArticulosMW())->add(new AuthPedidoMW());
})->add(new AuthRolesMW())
->add(new AuthEstadoMW());
//cerveceros
$app->group('/PedidosPendientesCerveceros', function (RouteCollectorProxy $group) {
    $group->get('[/]', \pedidosController::class . ':TraerPedidosPendientesCerveceros');
    $group->post('/cambiarEstadoPreparado', \pedidosController::class . ':CambiarEstadoProductoPreparacion')->add(new AuthArticulosMW())->add(new AuthPedidoMW());
    $group->post('/cambiarEstadoListo', \pedidosController::class . ':CambiarEstadoProductoListo')->add(new AuthArticulosMW())->add(new AuthPedidoMW());
})->add(new AuthRolesMW())
->add(new AuthEstadoMW());


$app->group('/encuestas', function (RouteCollectorProxy $group) {
    $group->post('[/]', \EncuestaController::class . ':CargarUno')->add(new AuthPedidoMW())->add(new AuthMesaExiste());
    $group->get('[/]', \EncuestaController::class . ':TraerTodos');
})->add(new AuthRolesMW());


//-----------------------------------------------------------------------------------------------------------------------------
//mensaje random
$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "MaximoTrejo - La comandita"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});


$app->run();