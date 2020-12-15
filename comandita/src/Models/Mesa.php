<?php
namespace App\Models;

use Helpers\AppConfig as Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;
class Mesa extends Model{

    public $timestamps = false;

    public static function BuscarMesaDisponible(){
        $mesa=Mesa::where('estado','libre')->first();
        
        if(is_null($mesa)){//si no hay mesas disponible
            echo 'No hay mesas';
            return false;
        }
        return $mesa;
    }

}
