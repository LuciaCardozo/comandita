<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Helpers\JWTAuth;
use App\Models\PedidosVino;
use App\Models\PedidosMozo;
use App\Models\Mesa;
use App\Models\User;
use stdClass;
class BartendersController{

    public function tomarPedidoPendiente(Request $request, Response $response, $args){
        $token = $_SERVER['HTTP_TOKEN'];
        $auxId = JWTAuth::GetPayload($token);
        $aux=PedidosVino::where('estado','pendiente')->get();
        $idPedido = PedidosVino::find($args['id']);
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
        $aux=PedidosVino::where('estado','en preparacion')->get();
        $idPedido = PedidosVino::find($args['id']);
        if(!is_null($aux)){
            foreach($aux as $key=>$pedido){
                if($pedido->id==$idPedido->id){
                    //$pedido->hora = date("Y-m-d H:i:s");
                    //$pedido->idEmpleado = $auxId->data->id;
                    $pedido->estado = "listo para servir";
                   // var_dump($pedido);
                    $mozo=PedidosMozo::where('codigoMesa',$pedido->codigoMesa)->first();
                    if(!is_null($mozo)){
                        $mozo->estado = "listo para servir";
                        $mesa = Mesa::where('codigoMesa',$pedido->codigoMesa)->first();
                        if(!is_null($mesa)){
                            $mesa->estado = "cliente comiendo";
                            
                            $mesa->save();
                            $mozo->save();
                            $pedido->save();
                            $rta = "pedido listo";  
                        }
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

    public function getAllBartender(Request $request, Response $response, $args){
        //date("Y-m-d H:i:s");
        $aux=User::where('tipo','bartender')->get();
        $response->getBody()->write(json_encode($aux));
        return $response;
    }
    
    public function mostrarPedidosPendientesBtd(Request $request, Response $response, $args){
        //date("Y-m-d H:i:s");
        $aux=PedidosVino::where('estado','pendiente')->get();
        $response->getBody()->write(json_encode($aux));
        return $response;
    }
    public function mostrarPedidosEnPreparacionBtd(Request $request, Response $response, $args){
        //date("Y-m-d H:i:s");
        $aux=PedidosVino::where('estado','en preparacion')->get();
        $response->getBody()->write(json_encode($aux));
        return $response;
    }

    public function mostrarPedidosTerminadosBtd(Request $request, Response $response, $args){
        //date("Y-m-d H:i:s");
        $aux=PedidosVino::where('estado','listo para servir')->get();
        $response->getBody()->write(json_encode($aux));
        return $response;
    }

    public function mostrarVino($codigo){
        //date("Y-m-d H:i:s");
        $aux=PedidosVino::Select()->where("codigoMesa",$codigo)->first();
        if(is_null($aux)){
            $aux = "";
            return false;
        }
        return $aux;
    }

    public function getAllPedidoBartender(Request $request, Response $response, $args){
        //date("Y-m-d H:i:s");
        $aux=PedidosVino::get();
        $response->getBody()->write(json_encode($aux));
        return $response;
    }
}