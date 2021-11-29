<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './db/AccesoDatos.php';
require_once './middlewares/TokenAutentificador.php';
require_once './middlewares/Logger.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$app = AppFactory::create();
$app->setBasePath('/public');
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
// peticiones
$app->group('/alta', function (RouteCollectorProxy $group) {
  $group->post('/usuario', \UsuarioController::class . ':CargarUno');
  $group->post('/producto', \ProductoController::class . ':CargarUno');
  $group->post('/mesa', \MesaController::class . ':CargarUno');
  $group->post('/pedido', \PedidoController::class . ':CargarUno');//->add(\Logger::class . ':LogMozo');
  });  

$app->group('/listar', function (RouteCollectorProxy $group) {
  $group->post('/productos', \ProductoController::class . ':TraerTodos');
  $group->post('/mesas', \MesaController::class . ':TraerTodos');
  $group->post('/usuarios', \UsuarioController::class . ':TraerTodos');
  $group->post('/pedidos', \PedidoController::class . ':TraerTodos');
  $group->post('/pendientesBar', \PedidoController::class . ':ListarPendientes')->add(\Logger::class . ':LogBartender');
  $group->post('/pendientesCerveceria', \PedidoController::class . ':ListarPendientes')->add(\Logger::class . ':LogCervecero');
  $group->post('/pendientesCocina', \PedidoController::class . ':ListarPendientes')->add(\Logger::class . ':LogCocinero');
  $group->post('/preparacionBar', \PedidoController::class . ':ListarEnPreparacion')->add(\Logger::class . ':LogBartender');
  $group->post('/preparacionCerveceria', \PedidoController::class . ':ListarEnPreparacion')->add(\Logger::class . ':LogCervecero');
  $group->post('/preparacionCocina', \PedidoController::class . ':ListarEnPreparacion')->add(\Logger::class . ':LogCocinero');
  });  

  $app->group('/preparar', function (RouteCollectorProxy $group) {
    $group->post('/bar', \PedidoController::class . ':PrepararBar')->add(\Logger::class . ':LogBartender');
    $group->post('/cocina', \PedidoController::class . ':PrepararCocina')->add(\Logger::class . ':LogCocinero');
    $group->post('/cerveceria', \PedidoController::class . ':PrepararCerveceria')->add(\Logger::class . ':LogCervecero');
  });

  $app->group('/servir', function (RouteCollectorProxy $group) {
    $group->post('/bar', \PedidoController::class . ':ServirBar')->add(\Logger::class . ':LogBartender');
    $group->post('/cocina', \PedidoController::class . ':ServirCocina')->add(\Logger::class . ':LogCocinero');
    $group->post('/cerveceria', \PedidoController::class . ':ServirCerveceria')->add(\Logger::class . ':LogCervecero');
    $group->post('/pedido', \PedidoController::class . ':ServirPedido')->add(\Logger::class . ':LogMozo');
  });


$app->post('/RelacionarFoto', \PedidoController::class . ':AgregarFoto')->add(\Logger::class . ':LogMozo');
$app->post('/login', \UsuarioController::class . ':Login');

$app->group('/pedido', function (RouteCollectorProxy $group) {
  $group->post('/cobrar', \PedidoController::class . ':CobrarPedido')->add(\Logger::class . ':LogMozo');
  $group->post('/cerrarMesa', \PedidoController::class . ':CerrarMesa')->add(\Logger::class . ':LogSocio');
});

$app->post('/CalificarExperiencia', \PedidoController::class . ':CargarExperiencia');

$app->group('/Consultas', function(RouteCollectorProxy $group){
  $group->post('/ProductoMasVendido', \ProductoController::class . 'ObtenerProductoMasVendido')->add(\Logger::class . ':LogSocio');
});





// Run app
$app->run();

?>
