<?php

require_once './models/Usuario.php';
require_once './models/UsuarioTipo.php';
require_once './middlewares/AutentificadorJWT.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

use \App\Models\Usuario as Usuario;
use \App\Models\UsuarioTipo as UsuarioTipo;
class Acceso
{
    public function isAdmin($request, $handler)
    {
        $response = new Response();
        try
        {
            $header = $request->getHeaderLine('Authorization');
            
            if(empty($header)) { throw new Exception('Es necesario Token para acceso'); }
            $token = trim(explode("Bearer", $header)[1]);

            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);

            self::ValidarDataToken($data);
            
            $obj = Usuario::find($data->idUsuario);
            
            $obj->PrintUsuario();
            if ($obj->idUsuarioTipo != UsuarioTipo::Administrador) { throw new Exception('Acceso sólo Administradores'); }

            $response = $handler->handle($request); 
        }
        catch(Exception $e){
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            $response = $response->withStatus(401);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function isAdminOSocio($request, $handler)
    {
        $response = new Response();
        try
        {
            $header = $request->getHeaderLine('Authorization');
            
            if(empty($header)) { throw new Exception('Es necesario Token para acceso'); }
            $token = trim(explode("Bearer", $header)[1]);
            
            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);
            
            self::ValidarDataToken($data);

            $obj = Usuario::find($data->idUsuario);
            
            $obj->PrintUsuario();
            if(!($obj->idUsuarioTipo == UsuarioTipo::Administrador ||
            $obj->idUsuarioTipo == UsuarioTipo::Socio)) { throw new Exception('Acceso sólo Administradores o Socios'); }

            $response = $handler->handle($request); 
        }
        catch(Exception $e){
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            $response = $response->withStatus(401);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function isMozo($request, $handler)
    {
        $response = new Response();
        try
        {
            $header = $request->getHeaderLine('Authorization');
            
            if(empty($header)) { throw new Exception('Es necesario Token para acceso'); }
            $token = trim(explode("Bearer", $header)[1]);

            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);
            
            self::ValidarDataToken($data);

            $obj = Usuario::find($data->idUsuario);
        
            $obj->PrintUsuario();
            if(!($obj->idUsuarioTipo == UsuarioTipo::Administrador ||
            $obj->idUsuarioTipo == UsuarioTipo::Socio ||
            $obj->idUsuarioTipo == UsuarioTipo::Mozo)) { throw new Exception('Acceso sólo Administradores, Socios o Mozos'); }

            $response = $handler->handle($request); 
        }
        catch(Exception $e){
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            $response = $response->withStatus(401);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function isCocinero($request, $handler)
    {
        $response = new Response();
        try
        {
            $header = $request->getHeaderLine('Authorization');
            
            if(empty($header)) { throw new Exception('Es necesario Token para acceso'); }
            $token = trim(explode("Bearer", $header)[1]);

            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);
            
            self::ValidarDataToken($data);

            $obj = Usuario::find($data->idUsuario);
        
            $obj->PrintUsuario();
            if(!($obj->idUsuarioTipo == UsuarioTipo::Administrador ||
            $obj->idUsuarioTipo == UsuarioTipo::Socio ||
            $obj->idUsuarioTipo == UsuarioTipo::Cocinero)) { throw new Exception('Acceso sólo Administradores, Socios o Cocineros'); }

            $response = $handler->handle($request); 
        }
        catch(Exception $e){
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            $response = $response->withStatus(401);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function isUsuario($request, $handler)
    {
        $response = new Response();
        try
        {
            $header = $request->getHeaderLine('Authorization');
            if(empty($header)) { throw new Exception('Es necesario Token para acceso'); }
            
            $token = trim(explode("Bearer", $header)[1]);
            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);
            

            self::ValidarDataToken($data);
            
            $obj = Usuario::find($data->idUsuario);
            
            $obj->PrintUsuario();
            $response = $handler->handle($request);
        }catch(Exception $e){
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            $response = $response->withStatus(401);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    static private function ValidarDataToken($data)
    {
        if(isset($data->idUsuario) && isset($data->usuario) && isset($data->clave)){
            $obj = Usuario::find($data->idUsuario);
            if($obj == null) { throw new Exception('No existe Usuario'); }
            if($obj->estado = 0) { throw new Exception('El Usuario se encuentra registrado pero su estado es "inactivo", si cree que es un error verifique con un Administrador o Socio.'); }
        }
        else {
            throw new Exception('id, usuario o clave no fueron seteados en el Token de acceso.');
        }
    }

}