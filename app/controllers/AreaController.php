<?php
date_default_timezone_set("America/Buenos_Aires");

require_once './models/Area.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Area as Area;
use Illuminate\Database\Capsule\Manager as DB;

class AreaController implements IApiUsable
{
  public function GetAll($request, $response, $args)
  {
    $lista = Area::all();
    $payload = json_encode(array("listaArea" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function GetAllBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $lista = Area::where($field, $value)->get();

    $payload = json_encode(array("listaArea" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  public function GetFirstBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $obj = Area::where($field, $value)->first();

    $payload = json_encode($obj);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function Save($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $descripcion = $parametros['descripcion'];
    
    $usr = new Area();
    $usr->descripcion = $descripcion;
    $usr->save();
    
    $payload = json_encode(array("mensaje" => "Area creada con exito"));
    
    $response->getBody()->write($payload);
    return $response
    ->withHeader('Content-Type', 'application/json');
  }

  public function Update($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $descripcion = $parametros['descripcion'];
    $id = $args['id'];

    // Conseguimos el objeto
    $usr = Area::where('id', '=', $id)->first();

    // Si existe
    if ($usr !== null) {
      $usr->descripcion = $descripcion;

      // Guardamos en base de datos
      $usr->save();

      $payload = json_encode(array("mensaje" => "Area modificado con exito"));
    } 
    else {
      $payload = json_encode(array("mensaje" => "Area no encontrado"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function Delete($request, $response, $args)
  {
    $id = $args['id'];
    
    // Buscamos el area
    $obj = Area::find($id);

    // Borramos
    $obj->delete();

    $payload = json_encode(array("mensaje" => "Area borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
