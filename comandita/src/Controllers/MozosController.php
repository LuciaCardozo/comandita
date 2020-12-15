<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Helpers\JWTAuth;
use App\Models\Mesa;
use App\Models\PedidosMozo;
use App\Models\PedidosComida;
use App\Models\PedidosBebida;
use App\Models\PedidosVino;
use App\Models\User;
use App\Models\Comida;
use App\Models\Bebida;
use App\Models\Vino;
use stdClass;

class MozosController{

    public function addOnePedido(Request $request, Response $response, $args){
        $token = $_SERVER['HTTP_TOKEN'];
        $auxId = JWTAuth::GetPayload($token);
        $facturacion = 0;
        $pedidosMozo = new PedidosMozo;
        $pedidosMozo->codigoMesa = $_POST['codigoMesa'];
        $pedidosMozo->nombreCliente = $_POST['nombreCliente'];
        $pedidosMozo->estado = $_POST['estado'];
        $pedidosComida = new PedidosComida;
        $pedidosComida->orden = $_POST['comida'];
        $pedidosComida->cantidad = $_POST['cantidadComida'];
        $pedidosBebida = new PedidosBebida;
        $pedidosBebida->orden = $_POST['bebida'];
        $pedidosBebida->cantidad = $_POST['cantidadBebida'];
        $pedidosVino = new PedidosVino;
        $pedidosVino->orden = $_POST['bartender'];
        $pedidosVino->cantidad = $_POST['cantidadBartender'];

        $comida = explode(",",$pedidosComida->orden);
        $cantidad = explode(",",$pedidosComida->cantidad);
        $bebida = explode(",",$pedidosBebida->orden);
        $cantBebida = explode(",",$pedidosBebida->cantidad);
        $vino = explode(",",$pedidosVino->orden);
        $cantVino = explode(",",$pedidosVino->cantidad);

        $mesa = Mesa::BuscarMesaDisponible();
        if($mesa){
            if(strlen($pedidosMozo->codigoMesa)>0 && strlen($pedidosMozo->codigoMesa)<=5){
                if(MozosController::existeCodigoMesa($pedidosMozo->codigoMesa)){

                    ///Probando solo con comida dsp se aplicará en el resto
                    if($pedidosComida->orden != null){
                        for($i=0;$i<count($comida);$i++){
                            $precio = MozosController::facturarComida($comida[$i],$cantidad[$i]);
                            $facturacion+=$precio;
                        }
                        $pedidosComida->estado = "pendiente";
                        $pedidosComida->codigoMesa = $pedidosMozo->codigoMesa;
                        $pedidosComida->save();
                    }
                    if($pedidosBebida->orden != null){
                        for($i=0;$i<count($bebida);$i++){
                            $precio = MozosController::facturarBebida($bebida[$i],$cantBebida[$i]);
                            $facturacion+=$precio;
                        }
                        $pedidosBebida->estado = "pendiente";
                        $pedidosBebida->codigoMesa = $pedidosMozo->codigoMesa;
                        $pedidosBebida->save();
                    }
                    if($pedidosVino->orden != null){
                        for($i=0;$i<count($vino);$i++){
                            $precio = MozosController::facturarVino($vino[$i],$cantVino[$i]);
                            $facturacion+=$precio;
                        }
                        $pedidosVino->estado = "pendiente";
                        $pedidosVino->codigoMesa = $pedidosMozo->codigoMesa;
                        $pedidosVino->save();
                    }
        
                    $mesa->codigoMesa = $pedidosMozo->codigoMesa;
                    $mesa->estado = "cliente esperando pedido";
                    $mesa->uso = $mesa->uso+1;
                    $mesa->save();

                    $pedidosMozo->idMozo = $auxId->data->id;
                    $pedidosMozo->hora = date("Y-m-d H:i:s");
                    $pedidosMozo->facturacion = $facturacion;
                    $pedidosMozo->save();

                    $rta = "Pedido Registrado";
                }else{
                    $rta = "ya existe una orden con ese codigo";
                }
            }else{
                $rta = "maximo 5 caracteres";
            }
        }else{
            $rta = 'disponibles';//toma el retorno de no hay mesas y le agrego disponibles para que no me salga un warning :)
        }

        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function facturarComida($orden,$cantidad){
        $aux=Comida::where('comida',$orden)->first();
        if(is_null($aux)){
            return false;
        }else{
            return $aux->precio * $cantidad;
        }
    }
    public function facturarBebida($orden,$cantidad){
        $aux=Bebida::where('bebida',$orden)->first();
        if(is_null($aux)){
            return false;
        }else{
            
            return $aux->precio * $cantidad;
        }
    }
    public function facturarVino($orden,$cantidad){
        $aux=Vino::where('vino',$orden)->first();
        if(is_null($aux)){
            return false;
        }else{
            return $aux->precio * $cantidad;
        }
    }

    public function existeCodigoMesa($codigo){
        $aux=PedidosMozo::where('codigoMesa',$codigo)->first();
        if(is_null($aux)){
            return true;
        }else{
            return false;
        }
    }

    public function getAllMozos(Request $request, Response $response, $args){
        //date("Y-m-d H:i:s");
        $aux=User::where('tipo','mozo')->get();
        $response->getBody()->write(json_encode($aux));
        return $response;
    }

    public function getAllPedidoMozo(Request $request, Response $response, $args){
        //date("Y-m-d H:i:s");
        $aux=PedidosMozo::get();
        $response->getBody()->write(json_encode($aux));
        return $response;
    }

    public function cambiarEstadoDeMesa(){
        $rta="";
        $aux=Mesa::where('estado','cliente comiendo')->get();
        if(!is_null($aux)){
            foreach($aux as $key=>$pedido){
                $mesas = Mesa::where('codigoMesa',$pedido->codigoMesa)->get();
                if(!is_null($mesas)){
                    foreach($mesas as $key=>$mesa){
                        $mesa->estado = "cliente pagando";
                        $mesa->save();
                    }
                }
                else{
                    $rta="400";
                }
            }   
            $rta = "cambio exitoso";
        }else{
            $rta="401";
        }
        return $rta;
    }

    public function cambiarEstadoAFinalizado(){
        $pedidos = PedidosMozo::where('estado','listo para servir')->get();
        if(!is_null($pedidos)){
            foreach($pedidos as $key=>$pedido){
                $comida = PedidosComida::where('codigoMesa',$pedido->codigoMesa)->first();
                $bebida =  PedidosBebida::where('codigoMesa',$pedido->codigoMesa)->first();
                $vino = PedidosVino::where('codigoMesa',$pedido->codigoMesa)->first();
                $pedido->estado="finalizado";
                $pedido->save();
                if(!is_null($comida)){
                    $comida->estado = "finalizado";
                    $comida->save();
                }
                if(!is_null($bebida)){
                    $bebida->estado = "finalizado";
                    $bebida->save();
                }
                if(!is_null($vino)){
                    $vino->estado = "finalizado";
                    $vino->save();
                }
            }  
            $rta='200'; 
        }else{
            $rta="401";
        }
        return $rta;
    }

}
?>