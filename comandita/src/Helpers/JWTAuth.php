<?php
namespace Helpers;
use \Firebase\JWT\JWT;

class JWTAuth{
    private static $key = "multitask";
    private static $encryption = ['HS256'];

  public static function CreateToken($datos)
  {
    $payload = array(
      'iat' => time(),
      'exp' => time() + (60000),
      'data' => $datos,
      'app' => "ApiRest Lucia Cardozo"

    );
    //return password_hash(JWT::encode($payload, self::key), PASSWORD_DEFAULT);
    return JWT::encode($payload, self::$key);
  }
  
  public static function GetPayload($token)
  {
    try
    {
      return JWT::decode($token, self::$key, self::$encryption);
    }
    catch(\Exception $e)
    {
      return null;
    }
  }

  public static function GetData($token)
  {
    $data = self::GetPayload($token);
    if(is_null($data))
    {
      echo '<br>Debe Logearse';
    }
    else
    {
      return is_null($data->data) ? null : $data->data;
    }
  }

}

?>