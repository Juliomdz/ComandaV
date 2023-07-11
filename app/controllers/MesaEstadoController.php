<?php
date_default_timezone_set("America/Buenos_Aires");
require_once './models/MesaEstado.php';
require_once './models/Usuario.php';
require_once './models/UsuarioTipo.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\MesaEstado as MesaEstado;
use \App\Models\Usuario as Usuario;
use \App\Models\UsuarioTipo as UsuarioTipo;

class MesaEstadoController implements IApiUsable
{
  public function GetAll($request, $response, $args)
  {
    $lista = MesaEstado::all();
    $payload = json_encode(array("listaMesaEstado" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function GetAllBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $lista = MesaEstado::where($field, $value)->get();

    $payload = json_encode(array("listaMesaEstado" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  public function GetFirstBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $obj = MesaEstado::where($field, $value)->first();

    $payload = json_encode($obj);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function Save($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $descripcionEstado = $parametros['descripcionEstado'];
    
    $usr = new MesaEstado();
    $usr->descripcionEstado = $descripcionEstado;
    $usr->save();
    
    $payload = json_encode(array("mensaje" => "MesaEstado creada con exito"));
    
    $response->getBody()->write($payload);
    return $response
    ->withHeader('Content-Type', 'application/json');
  }

  public function Update($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $descripcionEstado = $parametros['descripcionEstado'];
    $id = $args['id'];

    // Conseguimos el objeto
    $usr = MesaEstado::where('id', '=', $id)->first();

    // Si existe
    if ($usr !== null) {
      $usr->descripcionEstado = $descripcionEstado;

      // Guardamos en base de datos
      $usr->save();

      $payload = json_encode(array("mensaje" => "MesaEstado modificada con exito"));
    } 
    else {
      $payload = json_encode(array("mensaje" => "MesaEstado no encontrada"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function Delete($request, $response, $args)
  {
    $id = $args['id'];
    
    // Buscamos el mesaestado
    $obj = MesaEstado::find($id);

    // Borramos
    $obj->delete();

    $payload = json_encode(array("mensaje" => "MesaEstado borrada con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
