<?php
date_default_timezone_set("America/Buenos_Aires");

require_once './models/UsuarioAccion.php';
require_once './models/UsuarioAccionTipo.php';
use \App\Models\UsuarioAccionTipo as UsuarioAccionTipo;
use \App\Models\UsuarioAccion as UsuarioAccion;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class Util
{
    public static function LogOperacion($request, $response, $next)
    {
        $retorno = $next($request, $response);
        return $retorno;
    }

    public static function RegistrarAccionUsuario(Request $request, RequestHandler $handler)
    {
        $response = $handler->handle($request);
        $body = json_decode($response->getBody());

        if(isset($body->idUsuario) && isset($body->idUsuarioAccionTipo) &&
        intval($body->idUsuario) > 0 && intval($body->idUsuarioAccionTipo) > 0)
        {
            $registro = new UsuarioAccion();
            $registro->idUsuario = $body->idUsuario;
            $registro->idUsuarioAccionTipo = $body->idUsuarioAccionTipo; 
            $registro->idPedido = $body->idPedido; 
            $registro->idMesa = $body->idMesa; 
            $registro->idProducto = $body->idProducto; 
            $registro->idArea = $body->idArea;
            $registro->hora = $body->hora; 
            $registro->idPedidoDetalle = $body->idPedidoDetalle;
            $registro->save();
        }

        return $response;
    }
}