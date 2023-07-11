<?php
date_default_timezone_set("America/Buenos_Aires");
require_once './models/Usuario.php';
require_once './models/UsuarioAccionTipo.php';
use \App\Models\Usuario as Usuario;
use \App\Models\UsuarioAccionTipo as UsuarioAccionTipo;

class LoginController 
{

    private static function ValidarDatosLogin($usuario, $clave){
        if(!isset($usuario) || !isset($clave) 
        || $usuario == null || $clave == null
        || $usuario == "" || $clave == "") 
        { 
            throw new Exception("Usuario o Clave no definidos."); 
        }
    }

    public function AccesApp($request, $response, $args)
    {
        try
        {
            $params = $request->getParsedBody();
            $usuarioNombre = $params['usuario'];
            $clave = $params['clave'];

            self::ValidarDatosLogin($usuarioNombre , $clave);
            
            $obj = Usuario::where('usuario', $usuarioNombre)->first();

            if($obj == null) { throw new Exception("No existe Usuario con ese nombre"); }
            if($obj->clave !== $clave) { throw new Exception("Clave incorrecta"); }
            
            $datos = array('idUsuario' => $obj->id, 'usuario' => $obj->usuario, 'clave' => $obj->clave);
            
            $token = AutentificadorJWT::CrearToken($datos);

            $obj->PrintUsuario();
            $payload = json_encode(
            array(
                'jwt' => $token,
                "mensaje" => "Login Usuario con éxito",
                "idUsuario" => $obj->id,
                "idUsuarioAccionTipo" => UsuarioAccionTipo::Login,
                "idPedido" => null, 
                "idPedidoDetalle" => null, 
                "idMesa" => null, 
                "idProducto" => null, 
                "idArea" => null,
                "hora" => date('h:i:s'))
            );
            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }catch (Exception $e) {
            $response = $response->withStatus(401);
            $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json');
        }
        
    }
}

