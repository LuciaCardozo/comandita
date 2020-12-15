<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Helpers\JWTAuth;
use App\Models\User;
use stdClass;

class  UsuarioController{

    public function addOne(Request $request, Response $response, $args)
    {
        $user = new User;

        // OBTENER LOS DATOS QUE ME DA EL POSTMAN
        $user->nombre = $_POST['nombre'];
        $user->email = $_POST['email'];
        $user->tipo = $_POST['tipo'];
        $user->clave = $_POST['clave'];

        if(is_null($user->nombre) || is_null($user->email) || is_null($user->tipo)
        || is_null($user->clave)){
            $rta = "no se puede recibir valores nulos";
        }else{
            if($user->tipo == 'admin' || $user->tipo == 'mozo' || $user->tipo == 'cocinero'
            || $user->tipo == 'cervecero' || $user->tipo == 'bartender'){
                if(self::is_valid_email($user->email)){
                    if(!UsuarioController::existUser($user)){
                        if(strlen($user->clave)>3){
                            $user->save();
                            $rta = "Alta existosa";
                        }else{
                            $rta = "clave es demasiado corta";
                        }
                    }else{
                        $rta = "ya existe usuario";
                    }
                }else{
                    $rta = "Mail incorrecto";
                }
            }else{
                $rta = "El tipo es incorrecto";
            }
        }
        $response->getBody()->write(json_encode($rta));
        return $response;
    }
    
    public static function is_valid_email($str){
        if(filter_var($str,FILTER_VALIDATE_EMAIL)){
            return true;
        }
        return false;
    }

    public function existUser($user){
        $aux = json_decode(User::where("email",'=',$user->email)
        ->get());
        if($aux != null){
            return true;
        }
        return false;
    }

    public function logIn($request, $response, $args){
        $user = new User;
        $user->email = $_POST['email'];
        $user->clave = $_POST['clave'];
        
        if(!is_null($user->email) || !is_null($user->clave)){
            $aux = json_decode(User::where("clave",'=',$user->clave)
            ->where("email",'=',$user->email)
            ->get());
            if($aux != null){
                $obj = [
                "nombre" => $aux[0]->nombre,
                "clave" => $aux[0]->clave,
                "email" => $aux[0]->email,
                "tipo" => $aux[0]->tipo,
                "id" => $aux[0]->id
                ];
                $rta = JWTAuth::CreateToken($obj);
                $aux= new stdClass;
                $aux->data=$rta;
                //$response->getBody()->write(json_encode($rta));
            } else{
                $rta = "No existe el usuario";
            }
        }else{
            $rta = "no se puede recibir valores nulos";
        }
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function getAllUser(Request $request, Response $response, $args){
        //date("Y-m-d H:i:s");
        $aux=User::get();
        $response->getBody()->write(json_encode($aux));
        return $response;
    }
}
?>