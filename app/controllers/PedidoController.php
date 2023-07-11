<?php
date_default_timezone_set("America/Buenos_Aires");
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutentificadorJWT.php';

require_once './models/Area.php';
require_once './models/Pedido.php';
require_once './models/PedidoEstado.php';
require_once './models/PedidoDetalle.php';
require_once './models/Mesa.php';
require_once './models/MesaEstado.php';
require_once './models/Producto.php';
require_once './models/ProductoTipo.php';
require_once './models/Usuario.php';
require_once './models/UsuarioTipo.php';
require_once './models/UsuarioAccion.php';
require_once './models/UsuarioAccionTipo.php';
require_once './models/ManejadorArchivos.php';

use \App\Models\Area as Area;
use \App\Models\Pedido as Pedido;
use \App\Models\PedidoEstado as PedidoEstado;
use \App\Models\PedidoDetalle as PedidoDetalle;
use \App\Models\Mesa as Mesa;
use \App\Models\MesaEstado as MesaEstado;
use \App\Models\Producto as Producto;
use \App\Models\ProductoTipo as ProductoTipo;
use \App\Models\Usuario as Usuario;
use \App\Models\UsuarioTipo as UsuarioTipo;
use \App\Models\UsuarioAccion as UsuarioAccion;
use \App\Models\UsuarioAccionTipo as UsuarioAccionTipo;

use Slim\Psr7\Response;
use Illuminate\Database\Capsule\Manager as DB;

class PedidoController
{
  public function GetAllBandejaPedidosPendientes($request, $response, $args)
  {
    try
    {
      $usuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request);

      // Si es Administrador, Socio ve los pedidos Generales
      if ($usuarioLogeado->idUsuarioTipo == UsuarioTipo::Administrador 
      || $usuarioLogeado->idUsuarioTipo == UsuarioTipo::Socio) {

        $listPendientes = Pedido::where('idPedidoEstado', PedidoEstado::Pendiente)->get();
          
        $payload = json_encode(array(
        'mensaje' => 'Lista de Pedidos tomados pendientes',
        "listPendientes" => $listPendientes));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
      }

      if ($usuarioLogeado->idUsuarioTipo == UsuarioTipo::Mozo) {
          $listosParaServir = PedidoDetalle::where('idPedidoEstado', PedidoEstado::Listo_Para_Servir)->get();
           
          $payload = json_encode(array(
          'mensaje' => 'Lista de Platos listos para servir',
          "listosParaServir" => $listosParaServir));

          $response->getBody()->write($payload);
          return $response->withHeader('Content-Type', 'application/json');
      }
      
      $query =
      'SELECT 
        pD.id as idPedidoDetalle,
        p.id as idPedido,
        pro.id as idProducto,
        pE.id as idPedidoEstado,
        p.idUsuarioMozo as idUsuarioMozo,
        p.idMesa as idMesa,
        IFNULL(pD.idUsuarioEncargado, "Espearando empleado que tome pedido") as idUsuarioEncargado,
        p.codigo as codigoPedidoGeneral,
        m.codigo as codigoMesa,
        a.descripcion as areaProducto,
        u.usuario as usuarioMozoEncargado,
        pro.nombre as nombreProducto,
        pD.cantidadProducto as cantidadProducto,
        pE.estado as estadoPedido
      FROM PedidoDetalle pD
      INNER JOIN Pedido p ON p.id = pD.idPedido
      INNER JOIN Usuario u ON u.id = p.idUsuarioMozo
      INNER JOIN Producto pro ON pro.id = pD.idProducto
      INNER JOIN PedidoEstado pE ON pE.id = pD.idPedidoEstado 
      INNER JOIN Area a ON a.id = pro.idArea
      INNER JOIN Mesa m ON m.id = p.idMesa 
      WHERE a.id = ' . $usuarioLogeado->idArea .
      ' AND ( pE.id = '. PedidoEstado::Pendiente .' OR pE.id =' . PedidoEstado::En_Preparacion . ')';

      $list = DB::select($query);

      $payload = json_encode(array(
      'mensaje' => 'Lista de Platos Pendientes o en Preparación por Area del Usuario',
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

  public function GetAllPedidoDetalleCliente($request, $response, $args)
  {
    try
    {
      $codigoPedido = isset($args['codigoPedido']) ? $args['codigoPedido'] : null;
      $codigoMesa = isset($args['codigoMesa']) ? $args['codigoMesa'] : null;
      if($codigoPedido == null  || $codigoMesa == null){ throw new Exception('Al menos un dato de consulta no fue seteado.'); }

      $mesa = Mesa::where('codigo',  $codigoMesa)->first();
      if($mesa == null){ throw new Exception('Mesa no encontrada.'); }

      $query =
      'SELECT 
        pD.id as idPedidoDetalle,
        pro.id as idProducto,
        pE.id as idPedidoEstado,
        p.idUsuarioMozo as idUsuarioMozo,
        p.idMesa as idMesa,
        IFNULL(pD.idUsuarioEncargado, "Espearando empleado que tome pedido") as idUsuarioEncargado,
        m.codigo as codigoMesa,
        u.usuario as usuarioMozoEncargado,
        pro.nombre as nombreProducto,
        pD.cantidadProducto as cantidadProducto,
        pE.estado as estadoPedido,
        pro.precio as precioProducto,
        IFNULL(pD.tiempoEstimado, "No disponible hasta que un empleado tome pedido e indique") as tiempoEstimadoEmpleado,
        IFNULL(pro.tiempoEstimado, "No disponible") as tiempoEstimadoProducto
      FROM PedidoDetalle pD
        INNER JOIN Pedido p ON p.id = pD.idPedido
        INNER JOIN Usuario u ON u.id = p.idUsuarioMozo
        INNER JOIN Producto pro ON pro.id = pD.idProducto
        INNER JOIN PedidoEstado pE ON pE.id = pD.idPedidoEstado 
        INNER JOIN Mesa m ON m.id = p.idMesa 
      WHERE  p.idMesa = ' . $mesa->id . ' AND p.codigo = ' ."'". $codigoPedido ."'";

      $list = DB::select($query);

      // Busco tiempo mas grande
      $arrTiempos = array();
      foreach ($list as $obj) {
        $numProducto = isset($obj) && is_numeric($obj->tiempoEstimadoProducto) ? $obj->tiempoEstimadoProducto : 0;
        $numEmpleado = isset($obj) && is_numeric($obj->tiempoEstimadoEmpleado) ? $obj->tiempoEstimadoEmpleado : 0;
        array_push($arrTiempos, $numEmpleado);
        array_push($arrTiempos, $numProducto);
      }
      $tiempoEstimadoMasGrande = max($arrTiempos);

      $payload = json_encode(array(
        'mensaje' => 'Seguimiento del estado del Pedido, tiempo estimado: '.$tiempoEstimadoMasGrande.' minutos',
        'tiempoEstimadoMasGrande' => $tiempoEstimadoMasGrande,
        'PedidoGeneral' => (Pedido::where('codigo', $codigoPedido)->first()),
        "listDetallePedidoGeneral" => $list));

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
    $lista = Pedido::all();
    $payload = json_encode(array(
      'mensaje' => 'Listado de Pedidos Generales',
      "listaPedido" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function GetAllBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $lista = Pedido::where($field, $value)->get();

    $payload = json_encode(array(
      'mensaje' => 'Consulta de Pedido General por ' . $args['field'],
      "listaPedido" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function GetFirstBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $obj = Pedido::where($field, $value)->first();

    $payload = json_encode($obj);
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function SaveFoto($request, $response, $args)
  {
    try
    {
      $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->id;
      if(!isset($_FILES) || !isset($_POST['codigoPedido'])) { throw new Exception('La foto o el código del Pedido no fueron seteados.'); }

      $obj = Pedido::where('codigo','=',$_POST['codigoPedido'])->first();
      if($obj == null){ throw new Exception('El código ingresado no le pertenece a ningún Pedido.'); }

      $directory = './imagenes/clientes';
      $fileName = $obj->id;
      if(!ManejadorArchivos::SaveImage($directory, $fileName, $_FILES))
      { throw new Exception('No fue posible guardar imagen de la Mesa con los Clientes en el Pedido '.$_POST['codigoPedido'] ); }

      $obj->foto = './imagenes/clientes/' . $obj->id . '.png';
      $obj->save();

      $payload = json_encode(
      array(
      "mensaje" => "Foto de Mesa con Clientes guardada con éxito",
      "idUsuario" => $idUsuarioLogeado,
      "idUsuarioAccionTipo" => UsuarioAccionTipo::CargaFoto,
      "idPedido" => $obj->id, 
      "idPedidoDetalle" => null, 
      "idMesa" => null, 
      "idProducto" => null, 
      "idArea" => null,
      "hora" => date('h:i:s')));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Exception $e){
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  private function ValidateInputData($data){
    if($data == null){ throw new Exception("No se encontraron datos de entrada"); }

    if (!isset($data['codigoMesa'])) { throw new Exception("codigoMesa no seteado"); }
    else if(Mesa::where('codigo','=',$data['codigoMesa'])->first() == null){ throw new Exception("No existe Mesa con el código indicado"); }
    else{
      $mesa = Mesa::where('idMesaEstado', MesaEstado::Cerrada)->first(); 
      if($mesa == null){ throw new Exception('Capacidad llena, no se encuentran Mesas disponibles'); }
    }
    
    if (!isset($data['nombreCliente'])) { throw new Exception("nombreCliente no seteado"); }

    if (!isset($data['listPedidoDetalle'])) { throw new Exception("listPedidoDetalle no seteada"); }
    else if (!is_array($data['listPedidoDetalle'])) { throw new Exception("listPedidoDetalle debe ser un array"); }
    else{
      $listPedidoDetalle = $data['listPedidoDetalle'];
      foreach ($listPedidoDetalle as $pedidoDetalle) {
        if (!isset($pedidoDetalle['idProducto'])) { throw new Exception("idProducto de Array listPedidoDetalle no seteado"); }
        else if(!is_numeric($pedidoDetalle['idProducto']) || intval($pedidoDetalle['idProducto']) < 1) { throw new Exception("idProducto de Array listPedidoDetalle debe ser numérico y mayor a 0"); }

        if (!isset($pedidoDetalle['cantidadProducto'])) { throw new Exception("cantidadProducto de Array listPedidoDetalle no seteado"); }
        else if(!is_numeric($pedidoDetalle['cantidadProducto']) || intval($pedidoDetalle['cantidadProducto']) < 1) { throw new Exception("cantidadProducto de Array listPedidoDetalle debe ser numérico y mayor a 0"); }
      }
    }

  }

  public function Save($request, $response, $args)
  {
    try
    {
      $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->id;
      $data = $request->getParsedBody();

      self::ValidateInputData($data);
      
      // ---------------- Verifico si Mesa indicada está disponible ----------------
      $mesa = Mesa::where('codigo','=',$data['codigoMesa'])->first();
      if($mesa->idMesaEstado != MesaEstado::Cerrada){ throw new Exception('La Mesa se encuentra ocupada.'); }

      $listPedidoDetalle = $data['listPedidoDetalle'];
      $importeTotal = self::GetImporteTotal($listPedidoDetalle); // Obtengo importe total del pedido sumando precio c/producto

      // ---------------- Save Pedido General ----------------
      $pedido = new Pedido();
      $pedido->codigo = Pedido::GenerarCodigoAlfanumerico();
      $pedido->idMesa = intval($mesa->id);
      $pedido->idPedidoEstado = PedidoEstado::Pendiente; 
      $pedido->idUsuarioMozo = intval($idUsuarioLogeado); 
      $pedido->nombreCliente = $data['nombreCliente'];
      $pedido->foto = null;
      $pedido->importe = floatval($importeTotal);
      $pedido->save();
      
      // ---------------- Save Detalle del Pedido General ----------------
      foreach ($listPedidoDetalle as $detalle) 
      {
        $pedidoDetalle = new PedidoDetalle();
        $pedidoDetalle->idPedido = $pedido->id;
        $pedidoDetalle->idProducto =  $detalle['idProducto'];
        $pedidoDetalle->idPedidoEstado = PedidoEstado::Pendiente;
        $pedidoDetalle->cantidadProducto =  $detalle['cantidadProducto'];
        $pedidoDetalle->tiempoEstimado =  Producto::find($detalle['idProducto']) != null ? Producto::find($detalle['idProducto'])->tiempoEstimado : null;
        $pedidoDetalle->save();
      }

      // ---------------- Modifico estado de mesa una ves creado Pedido ----------------
      $mesa->idMesaEstado = MesaEstado::Cliente_Esperando_Pedido;
      $mesa->save();

      $payload = json_encode(
      array(
      "codigoMesa" => "El código de su mesa es: ".$mesa->codigo,        
      "codigoPedido" => "El código de su pedido es: ".$pedido->codigo,        
      "mensaje" => "Pedido generado con éxito, podrá ver el seguimiento del Pedido en la siguiente sección: .../slim-php-mysql-heroku/app/pedidos/seguimiento/(suCodigoMesa)/(suCodigoPedido)",
      "idUsuario" => $idUsuarioLogeado,
      "idUsuarioAccionTipo" => UsuarioAccionTipo::Alta,
      "idPedido" => $pedido->id, 
      "idPedidoDetalle" => $pedidoDetalle->id, 
      "idMesa" => $pedido->idMesa, 
      "idProducto" => null, 
      "idArea" => null,
      "hora" => date('h:i:s')));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Exception $e){
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  static private function GetImporteTotal($arr = array())
  {
    $importeTotal = 0;
    foreach ($arr as $pedidoDetalle) {
      $importeTotal += floatval(Producto::where('id', $pedidoDetalle['idProducto'])->first()->precio);
    }
    return $importeTotal;
  }

  public function Update($request, $response, $args)
  {
    try
    {
      $obj = Pedido::find($args['id']);
      if($obj == null) { throw new Exception('Pedido no encontrado.'); }
      $usuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request);

      if($usuarioLogeado->idUsuarioTipo != UsuarioTipo::Administrador 
      && $usuarioLogeado->idUsuarioTipo != UsuarioTipo::Socio 
      && $obj->idUsuarioMozo != $usuarioLogeado->id) { throw new Exception('Acceso a modificar pedido sólo Mozo encargado, Administrador o Socios.');  }

      $data = $request->getParsedBody();
      $modificar = false;

      if (isset($data['idMesa']) && Mesa::find($data['idMesa']) != null) { 
        if(Mesa::find($data['idMesa'])->idMesaEstado != MesaEstado::Cerrada){ throw new Exception('No es posible modificar Mesa, se encuentra ocupada.'); }
        $obj->idMesa = intval($data['idMesa']); 
        $modificar = true;
      }

      if (isset($data['idUsuarioMozo'])) { 
        if(Usuario::find($data['idUsuarioMozo']) == null) { throw new Exception('id Mozo encargado a modificar no existe'); }
        if(Usuario::find($data['idUsuarioMozo'])->UsuarioTipo->id != UsuarioTipo::Mozo) { throw new Exception('No es posible modificar pedido, sólo Mozos tienen permitido encargase del pedido general'); }
        $obj->idUsuarioMozo = intval($data['idUsuarioMozo']); 
        $modificar = true;
      }

      if (isset($data['idArea']) && Area::find($data['idArea']) != null) { $obj->idArea = intval($data['idArea']); $modificar = true; }
      if (isset($data['idPedidoEstado']) && PedidoEstado::find($data['idPedidoEstado']) != null) { $obj->idPedidoEstado = intval($data['idPedidoEstado']); $modificar = true; }
      if (isset($data['nombreCliente']) != null) { $obj->nombreCliente = $data['nombreCliente']; $modificar = true; }
      
      if(!$modificar){ throw new Exception('El Pedido no fue modificado, verifique de indicar campos y valores seteados válidos.'); }
      
      $obj->save();

      $payload = json_encode(
      array(
      "mensaje" => "Pedido modificado con éxito",
      "idUsuario" => $usuarioLogeado->id,
      "idUsuarioAccionTipo" => UsuarioAccionTipo::Modificacion,
      "idPedido" => $obj->id, 
      "idPedidoDetalle" => null, 
      "idMesa" => null, 
      "idProducto" => null, 
      "idArea" => null,
      "hora" => date('h:i:s')));
  
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Exception $e){
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  public function UpdatePedidoDetalle($request, $response, $args)
  {
    try
    {
      $obj = PedidoDetalle::find($args['idPedidoDetalle']);
      if($obj == null) { throw new Exception('Detalle del Pedido no encontrado.'); }

      $usuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request);

      if(!(Producto::find($obj->idProducto)->idArea == $usuarioLogeado->idArea 
      || $usuarioLogeado->idArea == Area::Salon // Mozo
      || $usuarioLogeado->idArea == Area::Administracion)) // Socio, Administrador/Dueño
      { 
        throw new Exception('No tienes acceso a modificar Detalle del Pedido, pertenece al Area de '. Producto::find($obj->idProducto)->Area->descripcion); 
      }

      $data = $request->getParsedBody();
      $modificar = false;

      $mensajeAccion = "Plato del Pedido General modificado con éxito";
      if (isset($data['idPedidoEstado']) && PedidoEstado::find($data['idPedidoEstado']) != null) { 
        $obj->idPedidoEstado = intval($data['idPedidoEstado']); 
        $obj->idUsuarioEncargado = intval($usuarioLogeado->id); // queda el ult empleado en modificar
        $modificar = true;
        if(intval($data['idPedidoEstado']) == PedidoEstado::En_Preparacion) { $obj->tiempoInicio = date('h:i:s'); }
        if(intval($data['idPedidoEstado']) == PedidoEstado::Listo_Para_Servir) { $obj->tiempoFin = date('h:i:s'); }
      
        $mensajeAccion = 'Estado del Plato modificado a: '.PedidoEstado::find($data['idPedidoEstado'])->estado;
      }

      if (isset($data['tiempoEstimado'])) { 
        $obj->tiempoEstimado = intval($data['tiempoEstimado']); 
        $modificar = true;
      }

      if(!$modificar){ throw new Exception('El Detalle del Pedido no fue modificado, verifique de indicar campos válidos'); }
      
      $obj->save();

      $payload = json_encode(
      array(
      "mensaje" => $mensajeAccion,
      "idUsuario" => $usuarioLogeado->id,
      "idUsuarioAccionTipo" => UsuarioAccionTipo::Modificacion,
      "idPedido" => $obj->idPedido,
      "idPedidoDetalle" => $obj->id, 
      "idMesa" => null, 
      "idProducto" => null, 
      "idArea" => null,
      "hora" => date('h:i:s')));
  
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Exception $e){
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  public function Delete($request, $response, $args)
  {
    $obj = Pedido::find($args['id']);
    if ($obj !== null) {
      $obj->pedido = $objModificado;

      $pedido->delete();
      $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));
    } 
    else {
      $payload = json_encode(array("mensaje" => "Pedido no encontrado"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

}
