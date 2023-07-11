<?php
date_default_timezone_set("America/Buenos_Aires");
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutentificadorJWT.php';

require_once './models/Area.php';
require_once './models/Usuario.php';
require_once './models/UsuarioTipo.php';
require_once './models/UsuarioAccion.php';
require_once './models/UsuarioAccionTipo.php';

use \App\Models\Area as Area;
use \App\Models\Usuario as Usuario;
use \App\Models\UsuarioTipo as UsuarioTipo;
use \App\Models\UsuarioAccion as UsuarioAccion;
use \App\Models\UsuarioAccionTipo as UsuarioAccionTipo;

use Illuminate\Database\Capsule\Manager as DB;

class UsuarioController implements IApiUsable
{
  
  public function GetAllCantidadAccionesArea($request, $response, $args)
  {
    try
    {
      $query =
      'SELECT 
        a.descripcion AS area,
        COUNT( u.idArea ) AS cantidadAcciones
      FROM Usuario u
      INNER JOIN UsuarioAccion ua ON u.id = ua.idUsuario
      INNER JOIN Area a ON u.idArea = a.id
        GROUP BY u.idArea
        ORDER BY cantidadAcciones DESC';

      $lista = DB::select($query);

      $payload = json_encode(array(
        'mensaje' => 'Lista de Acciones por Area',
        "listAccionesArea" => $lista));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch (Exception $e) {
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  public function GetAllCantidadAccionesUsuario($request, $response, $args)
  {
    try
    {
      $query =
      'SELECT 
        u.id as idUsuario,
        u.usuario,
        ut.tipo AS tipoUsuario,
        a.descripcion AS area,
        COUNT( ua.idUsuario ) AS cantidadAcciones
      FROM Usuario u
      INNER JOIN UsuarioAccion ua ON u.id = ua.idUsuario
      INNER JOIN UsuarioTipo ut ON u.idUsuarioTipo = ut.id
      INNER JOIN Area a ON u.idArea = a.id
        GROUP BY ua.idUsuario
        ORDER BY cantidadAcciones DESC';

      $lista = DB::select($query);

      $payload = json_encode(array(
        'mensaje' => 'Lista de Usuarios con cantidad de acciones realizadas',
        "list" => $lista));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch (Exception $e) {
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  public function GetAllUsuarioAccionIngresos($request, $response, $args)
  {
    try
    {
    $query =
    'SELECT 
      u.id as idUsuario,
      ua.id as idUsuarioAccion,
      uat.id as idUsuarioAccionTipo,
      u.usuario,
      ut.tipo AS tipoUsuario,
      a.descripcion AS area,
      uat.tipo as tipoAccion,
      ua.fechaAlta as fecha,
      ua.hora as horaAccion
    FROM Usuario u 
      INNER JOIN UsuarioAccion ua ON u.id = ua.idUsuario
      INNER JOIN UsuarioAccionTipo uat ON ua.idUsuarioAccionTipo = uat.id
      INNER JOIN UsuarioTipo ut ON u.idUsuarioTipo = ut.id
      INNER JOIN Area a ON u.idArea = a.id
        WHERE uat.id = 1';

    $lista = DB::select($query);

    $payload = json_encode(array(
    'mensaje' => 'Lista de ingresos al sistema de todos los Usuarios / Empleados',
    "listUsuarioAccion" => $lista));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  catch (Exception $e) {
    $response = $response->withStatus(401);
    $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
    return $response->withHeader('Content-Type', 'application/json');
  }
  }

  public function GetAllUsuarioAccion($request, $response, $args)
  {

    $query =
    'SELECT 
      ua.id as idUsuarioAccion,
      uat.id as idUsuarioAccionTipo,
      u.id as idUsuario,
      ua.idPedido,
      ua.idPedidoDetalle,
      ua.idMesa,
      ua.idProducto,
      ua.idArea,
      u.usuario,
      ut.tipo AS tipoUsuario,
      a.descripcion AS area,
      uat.tipo as tipoAccion,
      ua.fechaAlta as fecha,
      ua.hora as horaAccion
    FROM Usuario u
    INNER JOIN UsuarioAccion ua ON u.id = ua.idUsuario
    INNER JOIN UsuarioAccionTipo uat ON ua.idUsuarioAccionTipo = uat.id
    INNER JOIN UsuarioTipo ut ON u.idUsuarioTipo = ut.id
    INNER JOIN Area a ON u.idArea = a.id';

    // if(isset($args['idUsuarioAccionTipo']) 
    // && UsuarioAccionTipo::find($args['idUsuarioAccionTipo']) != null)
    // {
    //   $query .= 'WHERE ua.id = ' . $args['idUsuarioAccionTipo'];
    // }
    // echo $query;

    $lista = DB::select($query);

    $payload = json_encode(array(
    'mensaje' => 'Lista de Acciones de todos los Usuarios / Empleados',
    "listUsuarioAccion" => $lista));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function GetAll($request, $response, $args)
  {
    $lista = Usuario::all();
    $payload = json_encode(array("listaUsuario" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function GetAllBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $lista = Usuario::where($field, $value)->get();

    $payload = json_encode(array("listaUsuario" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function GetFirstBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $obj = Usuario::where($field, $value)->first();

    $payload = json_encode($obj);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function Save($request, $response, $args)
  {
    try{
      $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->id;
      $data = $request->getParsedBody();

      if(Usuario::ExisteUsuario($data['usuario'])) { throw new Exception("El nombre de Usuario ya existe"); }
      if(UsuarioTipo::find($data['idUsuarioTipo']) == null) { throw new Exception("El tipo de Usuario no existe"); }
      if(Area::find($data['idArea']) == null) { throw new Exception("El Area indicada no existe"); }
        
      $usr = new Usuario();
      $usr->idUsuarioTipo = $data['idUsuarioTipo'];
      $usr->idArea = $data['idArea'];
      $usr->usuario = $data['usuario'];
      $usr->clave = $data['clave'];
      $usr->save();

      $payload = json_encode(
      array(
        "mensaje" => "Usuario creado con éxito",
        "idUsuario" => $idUsuarioLogeado,
        "idUsuarioAccionTipo" => UsuarioAccionTipo::Alta,
        "idPedido" => null, 
        "idPedidoDetalle" => null, 
        "idMesa" => null, 
        "idProducto" => null, 
        "idArea" => null,
        "hora" => date('h:i:s'))
      );
      
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch (Exception $e) {
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }
    
  public function Update($request, $response, $args)
  {
      try
      {
        $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->id;
        $obj = Usuario::find($args['id']);
        if ($obj == null) { throw new Exception("No existe Usuario con el id indicado"); }

        $data = $request->getParsedBody();

        if (isset($data['usuario'])) { 
          if(Usuario::ExisteUsuario($data['usuario'])) { throw new Exception("El nombre de Usuario ya existe"); }
          $obj->usuario = $data['usuario']; 
        }
        if (isset($data['clave'])) { $obj->clave = $data['clave']; }
        if (isset($data['estado']) && is_numeric($data['estado'])) { $obj->estado = intval($data['estado']); }
        if (isset($data['idArea']) && Area::find($data['idArea']) != null) { $obj->idArea = intval($data['idArea']); }
        if (isset($data['idUsuarioTipo']) && UsuarioTipo::find($data['idUsuarioTipo']) != null) { $obj->idUsuarioTipo = intval($data['idUsuarioTipo']); }
          
        $obj->save();
        $payload = json_encode(
        array(
        "mensaje" => "Usuario modificado con éxito",
        "idUsuario" => $idUsuarioLogeado,
        "idUsuarioAccionTipo" => UsuarioAccionTipo::Modificacion,
        "idPedido" => null, 
        "idPedidoDetalle" => null, 
        "idMesa" => null, 
        "idProducto" => null, 
        "idArea" => null,
        "hora" => date('h:i:s'))
        );
      
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
      }
      catch(Exception $e){
        $response = $response->withStatus(401);
        $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
        return $response->withHeader('Content-Type', 'application/json');
      }
  }

  public function Delete($request, $response, $args)
  {

    try
    {
      $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->id;
      $obj = Usuario::find($args['id']);
      if ($obj == null) { throw new Exception("Usuario no encontrado"); }

      $obj->delete();
      
      $payload = json_encode(
      array(
      "mensaje" => "Usuario dado de baja con éxito",
      "idUsuario" => $idUsuarioLogeado,
      "idUsuarioAccionTipo" => UsuarioAccionTipo::Baja,
      "idPedido" => null, 
      "idPedidoDetalle" => null, 
      "idMesa" => null, 
      "idProducto" => null, 
      "idArea" => null,
      "hora" => date('h:i:s'))
      );

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Exception $e){
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  static private function EscribirAccionesUsuariosCsv($fileName, $list = array())
  {
    try 
    {
      $file = fopen($fileName, 'a');
      foreach ($list as $obj)
      {
        if(!is_null($obj)) 
        {
          $dataCsv = $obj->idUsuarioAccion
          . ',' . $obj->idUsuarioAccionTipo
          . ',' . $obj->idUsuario
          . ',' . $obj->idPedido
          . ',' . $obj->idPedidoDetalle
          . ',' . $obj->idMesa
          . ',' . $obj->idProducto
          . ',' . $obj->idArea
          . ',' . $obj->usuario
          . ',' . $obj->tipoUsuario
          . ',' . $obj->area
          . ',' . $obj->tipoAccion
          . ',' . $obj->fecha
          . ',' . $obj->horaAccion;

          $r = fwrite($file, $dataCsv . PHP_EOL);
        }
      }
      fclose($file);
      return ($r == false) ? false : true;
    } 
    catch (Exception $e) {
      throw $e;
    }
  }

  public function DescargarAccionesUsuariosCsv($request, $response, $args)
  {
    try
    {
      $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->id;

      $directory = './descargas/usuarios';
      if (!file_exists($directory)) { mkdir($directory, 0777, true); }
      $filename = 'usuarioAcciones_'.date('Ymd_his').'.csv';
      $path = $directory .'/'. $filename;

      $query =
      'SELECT 
        ua.id as idUsuarioAccion,
        uat.id as idUsuarioAccionTipo,
        u.id as idUsuario,
        ua.idPedido,
        ua.idPedidoDetalle,
        ua.idMesa,
        ua.idProducto,
        ua.idArea,
        u.usuario,
        ut.tipo AS tipoUsuario,
        a.descripcion AS area,
        uat.tipo as tipoAccion,
        ua.fechaAlta as fecha,
        ua.hora as horaAccion
      FROM Usuario u
      INNER JOIN UsuarioAccion ua ON u.id = ua.idUsuario
      INNER JOIN UsuarioAccionTipo uat ON ua.idUsuarioAccionTipo = uat.id
      INNER JOIN UsuarioTipo ut ON u.idUsuarioTipo = ut.id
      INNER JOIN Area a ON u.idArea = a.id';
      $list = DB::select($query);

      if(!self::EscribirAccionesUsuariosCsv($path, $list)) { throw new Exception('No fue posible descargar acciones de Usuario.'); }
      
      $payload = json_encode(
      array(
      "mensaje" => "Descarga de Acciones de Usuarios vía archivo CSV con éxito, ruta de acceso: ",
      "rutaArchivoDescargado" => $path,
      "idUsuario" => $idUsuarioLogeado,
      "idUsuarioAccionTipo" => UsuarioAccionTipo::DescargaCSV,
      "idPedido" => null, 
      "idPedidoDetalle" => null, 
      "idMesa" => null, 
      "idProducto" => null, 
      "idArea" => null,
      "hora" => date('h:i:s'))
      );
  
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Exception $e){
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

}
