<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Config\AppConfig;
use App\Controllers\UsuarioController;
use App\Controllers\SociosController;
use App\Controllers\MozosController;
use App\Controllers\CocinerosController;
use App\Controllers\CervecerosController;
use App\Controllers\BartendersController;
use App\Models\PedidosMozo;
use App\Models\PedidosComida;
use App\Models\Mesa;
use App\Models\Comida;
use App\Models\Bebida;
use App\Models\Vino;
use App\Middlewares\JsonMiddleware;
use App\Middlewares\AuthMiddleware;

require __DIR__ . '/../vendor/autoload.php';

new AppConfig;

$app = AppFactory::create();
$app->setBasePath('/comandita/public');

//------USUARIOS-----//
$app->group('/users', function (RouteCollectorProxy $group) {
    $group->post('[/]', UsuarioController::class . ":addOne");
    $group->get('[/]', UsuarioController::class .":getAllUser");
});

//-----Login-----//
$app->group('/login', function (RouteCollectorProxy $group) {
    $group->post('[/]', UsuarioController::class . ":logIn");
})->add(new JsonMiddleware);

//--------MOZO PEDIDOS--------//
$app->group('/pedido', function (RouteCollectorProxy $group) {
    $group->post('[/]', MozosController::class . ":addOnePedido")->add(new AuthMiddleware('mozo'));
    $group->get('[/]',MozosController::class . ":getAllPedidoMozo");
})->add(new JsonMiddleware);

//--------COMIDA COCINERO-----//
$app->group('/mostrarPedidoComida', function (RouteCollectorProxy $group) {  
    $group->get('[/]',CocinerosController::class . ":getAllPedidoComida");
})->add(new JsonMiddleware);

$app->group('/tomarPedidoPendienteComida', function (RouteCollectorProxy $group) {  
    $group->get('[/{id}]', CocinerosController::class . ":tomarPedidoPendiente")->add(new AuthMiddleware('cocinero'));
})->add(new JsonMiddleware);

$app->group('/tomarPedidoEnPreparacionComida', function (RouteCollectorProxy $group) {  
    $group->get('[/enPreparacion/{id}]', CocinerosController::class . ":tomarPedidoEnPreparacion")->add(new AuthMiddleware('cocinero'));
})->add(new JsonMiddleware);

//-------BEBIDA CERVECERO--------//
$app->group('/mostrarPedidoBebida',function(RouteCollectorProxy $group){
    $group->get('[/]', CervecerosController::class . ":getAllPedidoBebida");
})->add(new JsonMiddleware);

$app->group('/tomarPedidoPendienteBebida',function(RouteCollectorProxy $group){
    $group->get('[/{id}]', CervecerosController::class . ":tomarPedidoPendiente");
})->add(new JsonMiddleware);

$app->group('/tomarPedidoEnPreparacionBebida',function(RouteCollectorProxy $group){
    $group->get('[/{id}]', CervecerosController::class . ":tomarPedidoEnPreparacion");
})->add(new JsonMiddleware);


//--------BARTENDER---------//
$app->group('/mostrarBartender',function(RouteCollectorProxy $group){
    $group->get('[/]', BartendersController::class . ":getAllPedidoBartender");
})->add(new JsonMiddleware);

$app->group('/tomarPedidoPendienteBartender',function(RouteCollectorProxy $group){
    $group->get('[/{id}]', BartendersController::class . ":tomarPedidoPendiente");
    //$group->get('[/{id}]', BartendersController::class . ":tomarPedidoEnPreparacion");
})->add(new JsonMiddleware);

$app->group('/tomarPedidoEnPreparacionBartender',function(RouteCollectorProxy $group){
    $group->get('[/{id}]', BartendersController::class . ":tomarPedidoEnPreparacion");
})->add(new JsonMiddleware);

//-------SOCIO CERRAR PEDIDOS--------//
$app->group('/cerrarPedidos',function(RouteCollectorProxy $group){
    $group->get('[/]', SociosController::class . ":cerrarPedido");
})->add(new AuthMiddleware('admin'))->add(new JsonMiddleware);

$app->group('/mostrarPedidos',function(RouteCollectorProxy $group){
    $group->get('[/]', SociosController::class . ":mostrarTodosLosPedidos");
})->add(new AuthMiddleware('admin'))->add(new JsonMiddleware);

//------Alta bebida,comida,vino y mesa-----//
$app->group('/stock', function (RouteCollectorProxy $group) {
    $group->post('/comida', SociosController::class . ":addOneComida");
    $group->post('/bebida', SociosController::class . ":addOneBebida");
    $group->post('/vino', SociosController::class . ":addOneVino");
    $group->post('[/mesa]', SociosController::class . ":addOneMesa");
})->add(new AuthMiddleware('admin'))->add(new JsonMiddleware);

//----Mostrar cada empleado-----//
$app->group('/mostrarCocineros', function(RouteCollectorProxy $group){
    $group->get('[/]',CocinerosController::class . ":getAllCocineros");
})->add(new JsonMiddleware);

$app->group('/mostrarMozos', function(RouteCollectorProxy $group){
    $group->get('[/]',MozosController::class . ":getAllMozos");
})->add(new JsonMiddleware);

$app->group('/mostrarBartenders', function(RouteCollectorProxy $group){
    $group->get('[/]',BartendersController::class . ":getAllBartender");
})->add(new JsonMiddleware);

$app->group('/mostrarCerveceros', function(RouteCollectorProxy $group){
    $group->get('[/]',CervecerosController::class . ":getAllCerveceros");
})->add(new JsonMiddleware);

$app->run();
