<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Helpers\JWTAuth;
use App\Models\pedidosBebida;
use App\Models\PedidosMozo;
use App\Models\Mesa;
use App\Models\User;
use stdClass;
class CervecerosController{

    public function tomarPedidoPendiente(Request $request, Response $response, $args){
        $token = $_SERVER['HTTP_TOKEN'];
        $auxId = JWTAuth::GetPayload($token);
        $aux=PedidosBebida::where('estado','pendiente')->get();
        $idPedido = PedidosBebida::find($args['id']);
        if(!is_null($aux)){
            foreach($aux as $key=>$pedido){
                if($pedido->id==$idPedido->id){
                    $pedido->hora = date("Y-m-d H:i:s");
                    $pedido->idEmpleado = $auxId->data->id;
                    $pedido->estado = "en preparacion";
                   // var_dump($pedido);
                    $mozo=PedidosMozo::where('codigoMesa',$pedido->codigoMesa)->first();
                    if(!is_null($mozo)){
                        $mozo->estado = "en preparacion";
                        $mozo->save();
                        $pedido->save();
                        $rta = "pedido en preparacion";  
                    }
                }else{
                    $rta = "ya se tomo el pedido";
                }
                //$pedido->hora=date("Y-m-d H:i:s");
            } 
        }else{
            $rta= "aux null";
        }
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function tomarPedidoEnPreparacion(Request $request, Response $response, $args){
        $token = $_SERVER['HTTP_TOKEN'];
        $auxId = JWTAuth::GetPayload($token);
        $aux=PedidosBebida::where('estado','en preparacion')->get();
        $idPedido = PedidosBebida::find($args['id']);
        if(!is_null($aux)){
            foreach($aux as $key=>$pedido){
                if($pedido->id==$idPedido->id){
                    //$pedido->hora = date("Y-m-d H:i:s");
                    //$pedido->idEmpleado = $auxId->data->id;
                    $pedido->estado = "listo para servir";
                   // var_dump($pedido);
                    
                    $pedido->save();
                    $rta = "pedido listo";  
                    
                }else{
                    $rta = "ya se tomo el pedido";
                }
                //$pedido->hora=date("Y-m-d H:i:s");
            } 
        }else{
            $rta= "aux null";
        }
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function getAllCerveceros(Request $request, Response $response, $args){
        //date("Y-m-d H:i:s");
        $aux=User::where('tipo','cervecero')->get();
        $response->getBody()->write(json_encode($aux));
        return $response;
    }

    public function mostrarPedidosPendientesBebida(Request $request, Response $response, $args){
        //date("Y-m-d H:i:s");
        $aux=PedidosBebida::where('estado','pendiente')->get();
        $response->getBody()->write(json_encode($aux));
        return $response;
    }
    public function mostrarPedidosEnPreparacionBebida(Request $request, Response $response, $args){
        //date("Y-m-d H:i:s");
        $aux=PedidosBebida::where('estado','en preparacion')->get();
        $response->getBody()->write(json_encode($aux));
        return $response;
    }

    public function mostrarPedidosTerminadosBebida(Request $request, Response $response, $args){
        //date("Y-m-d H:i:s");
        $aux=PedidosBebida::where('estado','listo para servir')->get();
        $response->getBody()->write(json_encode($aux));
        return $response;
    }

    public function getAllPedidoBebida(Request $request, Response $response, $args){
        //date("Y-m-d H:i:s");
        $aux=PedidosBebida::get();
        $response->getBody()->write(json_encode($aux));
        return $response;
    }
    public function mostrarBebida($codigo){
        //date("Y-m-d H:i:s");
        $aux=PedidosBebida::Select()->where("codigoMesa",$codigo)->first();
        if(is_null($aux)){
            $aux = "";
            return false;
        }
        return $aux;
    }
}