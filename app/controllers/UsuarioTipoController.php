<?php
date_default_timezone_set("America/Buenos_Aires");
require_once './models/UsuarioTipo.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\UsuarioTipo as UsuarioTipo;

class UsuarioTipoController implements IApiUsable
{
  public function GetAll($request, $response, $args)
  {
    $lista = UsuarioTipo::all();
    $payload = json_encode(array("listaUsuarioTipo" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function GetAllBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $lista = UsuarioTipo::where($field, $value)->get();

    $payload = json_encode(array("listaUsuarioTipo" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  public function GetFirstBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $obj = UsuarioTipo::where($field, $value)->first();

    $payload = json_encode($obj);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function Save($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $tipo = $parametros['tipo'];
    
    $usr = new UsuarioTipo();
    $usr->tipo = $tipo;
    $usr->save();
    
    $payload = json_encode(array("mensaje" => "UsuarioTipo creado con exito"));
    
    $response->getBody()->write($payload);
    return $response
    ->withHeader('Content-Type', 'application/json');
  }

  public function Update($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $tipo = $parametros['tipo'];
    $id = $args['id'];

    // Conseguimos el objeto
    $usr = UsuarioTipo::where('id', '=', $id)->first();

    // Si existe
    if ($usr !== null) {
      $usr->tipo = $tipo;

      // Guardamos en base de datos
      $usr->save();

      $payload = json_encode(array("mensaje" => "UsuarioTipo modificado con exito"));
    } 
    else {
      $payload = json_encode(array("mensaje" => "UsuarioTipo no encontrado"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function Delete($request, $response, $args)
  {
    $id = $args['id'];
    
    // Buscamos el usuariotipo
    $obj = UsuarioTipo::find($id);

    // Borramos
    $obj->delete();

    $payload = json_encode(array("mensaje" => "UsuarioTipo borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
