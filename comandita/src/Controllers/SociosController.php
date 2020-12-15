<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Helpers\JWTAuth;
use App\Models\Mesa;
use App\Models\Vino;
use App\Models\Comida;
use App\Models\Bebida;
use App\Models\User;
use App\Controllers\CocinerosController;
use App\Controllers\BartendersController;
use App\Controllers\CervecerosController;
use App\Models\PedidosMozo;

use stdClass;

class SociosController{

    public function addOneMesa(Request $request, Response $response, $args){
        $mesa = new Mesa;
        $mesa->estado = $_POST['estado'];
        $mesa->codigoMesa = $_POST['codigoMesa'];
        
        if(is_null($mesa->estado) || is_null($mesa->codigoMesa) ){
            $rta = "Nose pudo registrar la mesa";
        }else{
            $mesa->save();
            $rta = "Registrada";
        }
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function addOneComida(Request $request, Response $response, $args){
        $comida = new Comida;
        $comida->comida = $_POST['comida'];
        $comida->precio = $_POST['precio'];
        
        if(is_null($comida->comida) || is_null($comida->precio) ){
            $rta = "Nose pudo registrar la comida";
        }else{
            $comida->save();
            $rta = "Registrada";
        }
        $response->getBody()->write(json_encode($rta));
        return $response;
    }
    public function addOneBebida(Request $request, Response $response, $args){
        $bebida = new Bebida;
        $bebida->bebida = $_POST['bebida'];
        $bebida->precio = $_POST['precio'];
        
        if(is_null($bebida->bebida) || is_null($bebida->precio) ){
            $rta = "Nose pudo registrar la bebida";
        }else{
            $bebida->save();
            $rta = "Registrada";
        }
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function addOneVino(Request $request, Response $response, $args){
        $vino = new Vino;
        $vino->vino = $_POST['vino'];
        $vino->precio = $_POST['precio'];
        
        if(is_null($vino->vino) || is_null($vino->precio) ){
            $rta = "Nose pudo registrar la bebida";
        }else{
            $vino->save();
            $rta = "Registrada";
        }
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function getAllSocios(Request $request, Response $response, $args){
        //date("Y-m-d H:i:s");
        $aux=User::where('tipo','admin')->get();
        $response->getBody()->write(json_encode($aux));
        return $response;
    }

    public function cerrarPedido(Request $request, Response $response, $args){
        MozosController::cambiarEstadoAFinalizado();
        MozosController::cambiarEstadoDeMesa();
        $mesas = Mesa::where("estado","cliente pagando")->get();
        
        if(is_null($mesas)){
            foreach($mesas as $key=>$mesa){
                $mesa->estado = "libre";
                $mesa->codigoMesa=0;
                $mesa->save();
            }
            $rta = "cambio exitoso";
        }else{
            $rta = "no hay mesas para cerrar";
        }
        $response->getBody()->write(json_encode($rta));
        return $response; 
        
    }

    public function mostrarTodosLosPedidos(Request $request, Response $response, $args)
    {
        $pedidos = PedidosMozo::get();
        if(!is_null($pedidos)){
            foreach($pedidos as $key=>$pedido){
                $PedidoAMostrarCocina = CocinerosController::mostrarComida($pedido->codigoMesa);
                $PedidoAMostrarCerveza = CervecerosController::mostrarBebida($pedido->codigoMesa);
                $PedidoAMostrarVino = BartendersController::mostrarVino($pedido->codigoMesa);
                 $rta = "Cocina:";
                $rta .= '<br/>'.$PedidoAMostrarCocina;
                $rta .= "<br/>Bebida:";
                $rta .= "<br/>".$PedidoAMostrarCerveza;
                $rta .= "<br/>Bartender:";
                $rta .= "<br/>".$PedidoAMostrarVino;
                $aux = explode("<br/>",$rta);
                $response->getBody()->write(json_encode($aux));
            }
        }
        return $response; 
    }
}
?>