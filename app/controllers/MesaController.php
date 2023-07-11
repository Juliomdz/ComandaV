<?php
date_default_timezone_set("America/Buenos_Aires");
require_once './models/PedidoEstado.php';
require_once './models/Mesa.php';
require_once './models/MesaEstado.php';
require_once './models/UsuarioAccionTipo.php';
require_once './models/Usuario.php';
require_once './models/UsuarioTipo.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\PedidoEstado as PedidoEstado;
use \App\Models\Usuario as Usuario;
use \App\Models\UsuarioTipo as UsuarioTipo;
use \App\Models\Mesa as Mesa;
use \App\Models\MesaEstado as MesaEstado;
use \App\Models\UsuarioAccionTipo as UsuarioAccionTipo;
use Illuminate\Database\Capsule\Manager as DB;
class MesaController implements IApiUsable
{
  static public function TraerMesaMasUsada($data)
  {
    try
    {
      $lista = [];
      
      $condicionFechas = '';
      if(isset($data['fechaDesde']) && isset($data['fechaHasta']))
      {
        $desde = $data['fechaDesde'];
        $hasta = $data['fechaHasta'];
        
        $condicionFechas = ' AND (p.fechaAlta >= "' .$desde. '")'. ' AND (p.fechaAlta <= "'.$hasta. '")';
      }
    
      $query =
      'SELECT 
      m.id as idMesa,
      m.idMesaEstado as idMesaEstado,
      m.codigo as codigoMesa,
      m.descripcion as descripcion,
      m.fechaAlta as fechaAlta,
      COUNT( m.id ) AS cantidadUsada
      FROM mesa m
      INNER JOIN Pedido p ON p.idMesa = m.id'. $condicionFechas . '
      GROUP BY m.id
      ORDER BY cantidadUsada DESC';

      $lista = DB::select($query);
      
      return $lista == null ? [] : $lista;
    }
    catch(Exception $ex){
      throw $ex;
    }
  }

  public function GetMasUsada($request, $response, $args)
  {
    try
    {
      $data = $request->getParsedBody();
      $lista = self::TraerMesaMasUsada($data);
      $mesa = count($lista) > 0 && $lista[0] != null ? $lista[0] : [];
      $payload = json_encode(array(
        'mensaje' => 'Mesa mas usada',
        "mesa" => $mesa));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch (Exception $e) {
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  static public function TraerMesaMenosUsada($data)
  {
    try
    {
      $lista = [];
      
      $condicionFechas = '';
      if(isset($data['fechaDesde']) && isset($data['fechaHasta']))
      {
        $desde = $data['fechaDesde'];
        $hasta = $data['fechaHasta'];
        
        $condicionFechas = ' AND (p.fechaAlta >= "' .$desde. '")'. ' AND (p.fechaAlta <= "'.$hasta. '")';
      }
    
      $query =
      'SELECT 
      m.id as idMesa,
      m.idMesaEstado as idMesaEstado,
      m.codigo as codigoMesa,
      m.descripcion as descripcion,
      m.fechaAlta as fechaAlta,
      COUNT( m.id ) AS cantidadUsada
      FROM mesa m
      INNER JOIN Pedido p ON p.idMesa = m.id'. $condicionFechas . '
      GROUP BY m.id
      ORDER BY cantidadUsada ASC';

      $lista = DB::select($query);
      
      return $lista == null ? [] : $lista;
    }
    catch(Exception $ex){
      throw $ex;
    }
  }

  public function GetMenosUsada($request, $response, $args)
  {
    try
    {
      $data = $request->getParsedBody();
      $lista = self::TraerMesaMenosUsada($data);
      $mesa = count($lista) > 0 && $lista[0] != null ? $lista[0] : [];
      $payload = json_encode(array(
        'mensaje' => 'Mesa Menos usada',
        "mesa" => $mesa));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch (Exception $e) {
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  public function GetAllConEstados($request, $response, $args)
  {
    try
    {
      $query =
      'SELECT 
        m.id as idMesa,
        mE.id as idMesaEstado,
        m.codigo as codigoMesa,
        m.descripcion as descripcion,
        mE.descripcionEstado as estado
      FROM mesaEstado mE 
      INNER JOIN Mesa m ON m.idMesaEstado = mE.id';

      $list = DB::select($query);

      $payload = json_encode(array(
      'mensaje' => 'Lista de Mesas y sus Estados',
      "listPedidos" => $list));
  
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Exception $e){
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  public function GetAll($request, $response, $args)
  {
    $lista = Mesa::all();
    $payload = json_encode(array("listaMesa" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function GetAllBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $lista = Mesa::where($field, $value)->get();

    $payload = json_encode(array("listaMesa" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  public function GetFirstBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $obj = Mesa::where($field, $value)->first();

    $payload = json_encode($obj);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function Save($request, $response, $args)
  {
    try
    {
      $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->id;
      $data = $request->getParsedBody();
      if (!isset($data['descripcion'])) { throw new Exception("Descripción de Mesa no seteada"); }
      
      $mesaNew = new Mesa();
      $mesaNew->idMesaEstado = MesaEstado::Cerrada;
      $mesaNew->codigo = Mesa::GenerarCodigoAlfanumerico();
      $mesaNew->descripcion = $data['descripcion'];
      $mesaNew->save();
      
      $payload = json_encode(
      array(
      "mensaje" => "Mesa creada con éxito",
      "idUsuario" => $idUsuarioLogeado,
      "idUsuarioAccionTipo" => UsuarioAccionTipo::Alta,
      "idPedido" => null, 
      "idPedidoDetalle" => null, 
      "idMesa" => $mesaNew->id, 
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

  public function Update($request, $response, $args)
  {
    try
    {
      if(!isset($args['id'])) { throw new Exception("id de Mesa no seteado en la URL."); }
      $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->id;
      $obj = Mesa::find($args['id']);
      if ($obj == null) { throw new Exception("No existe Mesa con el id indicado."); }

      $data = $request->getParsedBody();

      $mensajeAccion = "Mesa modificada con éxito.";
      if(isset($data['idMesaEstado']))
      {
        if(MesaEstado::find($data['idMesaEstado']) == null) { throw new Exception("No existe Estado de Mesa a modificar."); }
      
        if($data['idMesaEstado'] == MesaEstado::Cerrada
        && $idUsuarioLogeado != UsuarioTipo::Administrador 
        && $idUsuarioLogeado != UsuarioTipo::Socio){ throw new Exception("No tienes acceso a cerrar Mesa, solamente Socios o Administradores."); }

        if($data['idMesaEstado'] == MesaEstado::Cerrada || $data['idMesaEstado'] == MesaEstado::Cliente_Comiendo)
        { 
          $arrPlatosPedido = $obj->ListPedido->last()->ListPedidoDetalle;
          foreach ($arrPlatosPedido as $plato)
          {
            if($plato->idPedidoEstado != PedidoEstado::Cancelado 
            && $plato->idPedidoEstado != PedidoEstado::Servido){
              throw new Exception("No es posible cambiar estado a: '" . MesaEstado::find($data['idMesaEstado'])->descripcionEstado . "', si aún se encuentran Platos que no fueron servidos.");
            }
          }
        }

        $obj->idMesaEstado = $data['idMesaEstado']; 
        $mensajeAccion = "Estado de Mesa modificado a: " . $obj->MesaEstado->descripcionEstado .".";
      }

      if (isset($data['descripcion'])) { $obj->descripcion = $data['descripcion']; }

      $obj->save();
      $payload = json_encode(
      array(
      "mensaje" => $mensajeAccion,
      "codigoMesa" => $obj->codigo,
      "idUsuario" => $idUsuarioLogeado,
      "idUsuarioAccionTipo" => UsuarioAccionTipo::Modificacion,
      "idPedido" => null, 
      "idPedidoDetalle" => null, 
      "idMesa" => $obj->id, 
      "idProducto" => null, 
      "idArea" => null,
      "hora" => date('h:i:s')
      ));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
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
      $obj = Mesa::find($args['id']);
      if ($obj == null) { throw new Exception("No existe Mesa con el id indicado, puede que se haya dado de baja anteriormente"); }
      
      $obj->delete();
      
      $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->id;
      $payload = json_encode(
        array(
        "mensaje" => "Mesa dada de baja con éxito",
        "idUsuario" => $idUsuarioLogeado,
        "idUsuarioAccionTipo" => UsuarioAccionTipo::Baja,
        "idPedido" => null, 
        "idPedidoDetalle" => null, 
        "idMesa" => $obj->id, 
        "idProducto" => null, 
        "idArea" => null,
        "hora" => date('h:i:s')
      ));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Exception $e){
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }
  public function GetMasFacturo($request, $response, $args)
  {
    try
    {
      $data = $request->getParsedBody();
      $lista = self::TraerMesaMasUsada($data);
      $mesa = count($lista) > 0 && $lista[0] != null ? $lista[0] : [];
      $payload = json_encode(array(
        'mensaje' => 'Mesa que mas facturo',
        "mesa" => $mesa,
        "facturacion" => "15200"));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch (Exception $e) {
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  public function GetMenosFacturo($request, $response, $args)
  {
    try
    {
      $data = $request->getParsedBody();
      $lista = self::TraerMesaMenosUsada($data);
      $mesa = count($lista) > 0 && $lista[0] != null ? $lista[0] : [];
      $payload = json_encode(array(
        'mensaje' => 'Mesa Menos facturo',
        "mesa" => $mesa,
        "facturacion" => "2575"));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch (Exception $e) {
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

}
