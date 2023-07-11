<?php
date_default_timezone_set("America/Buenos_Aires");
require_once './models/Producto.php';
require_once './models/ProductoTipo.php';
require_once './models/Area.php';
require_once './models/UsuarioAccionTipo.php';
require_once './models/ManejadorArchivos.php';

require_once './interfaces/IApiUsable.php';

use \App\Models\Area as Area;
use \App\Models\Producto as Producto;
use \App\Models\ProductoTipo as ProductoTipo;
use \App\Models\UsuarioAccionTipo as UsuarioAccionTipo;
use \App\Models\ManejadorArchivos as ManejadorArchivos;
use Illuminate\Database\Capsule\Manager as DB;

class ProductoController implements IApiUsable
{
  public function GetAll($request, $response, $args)
  {
    $lista = Producto::all();
    $payload = json_encode(array("listaProducto" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  public function GetAllOrdered($request, $response, $args)
  {
    $lista = Producto::all();
    $payload = json_encode(array("listaProducto" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function GetAllBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $lista = Producto::where($field, $value)->get();

    $payload = json_encode(array("listaProducto" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function GetFirstBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $obj = Producto::where($field, $value)->first();

    $payload = json_encode($obj);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  private static function ValidateInputData($data){
    if($data == null) { throw new Exception("No se encontró datos de entrada"); }

    if (!isset($data['idArea'])) { throw new Exception("idArea no seteado"); }
    else if(Area::find($data['idArea']) == null) { throw new Exception("No existe idArea indicada"); }

    if (!isset($data['idProductoTipo'])) { throw new Exception("idProductoTipo no seteado"); }
    else if(ProductoTipo::find($data['idProductoTipo']) == null) { throw new Exception("No existe idProductoTipo indicado"); }

    if (!isset($data['nombre'])) { throw new Exception("Nombre no seteado"); }
    if (Producto::where('nombre', '=', $data['nombre'])->first() != null) { throw new Exception("El Nombre indicado ya existe en Productos"); }

    if (!isset($data['precio'])) { throw new Exception("Precio no seteado"); }
    if (floatval($data['precio']) < 0) { throw new Exception("Precio indicado debe ser '>' o '=' a 0"); }
    
    if (!isset($data['stock'])) { throw new Exception("Stock no seteado"); }
    if (intval($data['stock']) < 0) { throw new Exception("Stock indicado debe ser '>' o '=' a 0"); }
   
    if (!isset($data['tiempoEstimado'])) { throw new Exception("Tiempo Estimado no seteado"); }
    if (intval($data['tiempoEstimado']) < 1) { throw new Exception("Tiempo estimado indicado debe ser '>' o '=' a 1"); }
  }

  public function Save($request, $response, $args)
  {
    try
    {
      $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->id;
      $data = $request->getParsedBody();

      self::ValidateInputData($data);

      $obj = new Producto();
      $obj->idArea = $data['idArea'];
      $obj->idProductoTipo = $data['idProductoTipo'];
      $obj->nombre = $data['nombre'];
      $obj->precio = floatval($data['precio']);
      $obj->stock = intval($data['stock']);
      $obj->tiempoEstimado = intval($data['tiempoEstimado']);
      $obj->save();
      
      $payload = json_encode(
      array(
      "mensaje" => "Producto creado con éxito",
      "idUsuario" => $idUsuarioLogeado,
      "idUsuarioAccionTipo" => UsuarioAccionTipo::Alta,
      "idPedido" => null, 
      "idPedidoDetalle" => null, 
      "idMesa" => null, 
      "idProducto" => $obj->id, 
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
      $obj = Producto::find($args['id']);
      if($obj == null) { throw new Exception('Producto no encontrado.'); }
      $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->id;

      $data = $request->getParsedBody();

      if(isset($data['idArea'])) { 
        if(Area::find($data['idArea']) == null) { throw new Exception("No existe idArea indicada"); }
        $obj->idArea = $data['idArea']; 
      }

      if(isset($data['idProductoTipo'])) { 
        if(ProductoTipo::find($data['idProductoTipo']) == null) { throw new Exception("No existe idProductoTipo indicado"); }
        $obj->idProductoTipo = $data['idProductoTipo']; 
      }

      if(isset($data['nombre'])) { 
        if (Producto::where('nombre', '=', $data['nombre'])->first() != null) { throw new Exception("El Nombre a modificar ya existe"); }
        $obj->nombre = $data['nombre'];
      }

      if(isset($data['precio'])) { 
        if (floatval($data['precio']) < 0) { throw new Exception("Precio indicado debe ser '>' o '=' a 0"); }
        $obj->precio = floatval($data['precio']); 
      }

      if(isset($data['stock'])) { 
        if (intval($data['stock']) < 0) { throw new Exception("Stock indicado debe ser '>' o '=' a 0"); }
        $obj->stock = intval($data['stock']); 
      }

      if (isset($data['tiempoEstimado'])) { 
        if (intval($data['tiempoEstimado']) < 1) { throw new Exception("Tiempo estimado indicado debe ser '>' o '=' a 1"); }
        $obj->tiempoEstimado = intval($data['tiempoEstimado']); 
      }

      $obj->save();
      $payload = json_encode(
      array(
      "mensaje" => "Producto modificado con éxito",
      "idUsuario" => $idUsuarioLogeado,
      "idUsuarioAccionTipo" => UsuarioAccionTipo::Modificacion,
      "idPedido" => null, 
      "idPedidoDetalle" => null, 
      "idMesa" => null, 
      "idProducto" => $obj->id, 
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

  public function Delete($request, $response, $args)
  {
    try
    {
      $obj = Producto::find($args['id']);
      if($obj == null) { throw new Exception('Producto no encontrado.'); }
      $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->id;

      $obj->delete();
      $payload = json_encode(
      array(
      "mensaje" => "Producto borrado con éxito",
      "idUsuario" => $idUsuarioLogeado,
      "idUsuarioAccionTipo" => UsuarioAccionTipo::Baja,
      "idPedido" => null, 
      "idPedidoDetalle" => null, 
      "idMesa" => null, 
      "idProducto" => $obj->id, 
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

  static private function LeerCsvInterno($fileName)
  {
    $list = array();
    try 
    {
      $file = fopen($fileName, "r");
      $numFila = 0;
      while(!feof($file))
      {
        $numFila++;
        $line = fgets($file);
        if (!empty($line) && strlen($line) > 5)
        {
          $data = explode(',', $line);
          if(!empty($data)){
            $obj = new Producto();
            $obj->idArea = (Area::find($data[0]) != null) ? $data[0] : throw new Exception("No existe idArea indicada en la fila ".$numFila);
            $obj->idProductoTipo = (ProductoTipo::find($data[1]) != null) ? $data[1] : throw new Exception("No existe idProductoTipo indicado en la fila ".$numFila);
            $obj->nombre = $data[2];
            $obj->precio = (floatval($data[3]) > 0) ? floatval($data[3]) : throw new Exception("Precio en la fila ".$numFila." debe ser '>' o '=' a 0");
            $obj->stock = (intval($data[4]) > 0) ? intval($data[4]) : throw new Exception("Stock en la fila ".$numFila." debe ser '>' o '=' a 0");
            $obj->tiempoEstimado = (intval($data[5]) > 0) ? intval($data[5]) : throw new Exception("Tiempo Estimado en la fila ".$numFila." debe ser '>' o '=' a 1");
            array_push($list, $obj);
          }
        }
      }
      $seActualizo = false;
      if (count($list) > 0){
        foreach ($list as $objNew)
        {
          $objExistente = Producto::where('nombre', '=', $objNew->nombre)->first();
          if ($objExistente == null){ $objNew->save(); } 
          else{
            $objExistente->idArea = intval($objNew->idArea);
            $objExistente->idProductoTipo = intval($objNew->idProductoTipo);
            $objExistente->nombre = $objNew->nombre;
            $objExistente->precio = floatval($objNew->precio);
            $objExistente->stock = intval($objNew->stock) + intval($objExistente->stock);
            $objExistente->tiempoEstimado = intval($objNew->tiempoEstimado);
            $objExistente->update();
            $seActualizo = true;
          }
        }
      }
      if($seActualizo) { echo '- Hubo actualizaciones de Productos que ya se encontraban en el sistema.'.PHP_EOL; }
      return $list;
    } 
    catch(Exception $e){
      throw $e;
    }
    finally{
      fclose($file);
    }
  }

  public function CargarDataCsvExterno($request, $response, $args)
  {
    try 
    {
      $idUsuarioLogeado = AutentificadorJWT::GetUsuarioLogeado($request)->id;
      $fileName = explode(".", $_FILES["productosCSV"]["name"]);
      $directory = './cargasExternas/';
      if (!file_exists($directory)) { mkdir($directory, 0777, true); }

      $destino = $directory . $fileName[0] . '.csv';
      $r = move_uploaded_file($_FILES["productosCSV"]["tmp_name"], $destino);
      if($r){
        $list = self::LeerCsvInterno($destino);
        if(is_array($list) && count($list) > 0){
          $payload = json_encode(
          array(
            "mensaje" => "Carga de Productos vía archivo CSV con éxito",
            "idUsuario" => $idUsuarioLogeado,
            "idUsuarioAccionTipo" => UsuarioAccionTipo::CargaCSV,
            "idPedido" => null, 
            "idPedidoDetalle" => null, 
            "idMesa" => null, 
            "idProducto" => null, 
            "idArea" => null,
            "hora" => date('h:i:s'))
          );
        }else{
          $payload = json_encode(
            array(
              "mensaje" => "No fue posible obtener lista convertida",
              "hora" => date('h:i:s'))
            );
        }
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch (Exception $e) {
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  static public function TraerProductoMasPedido($data)
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
      pr.id as idProducto,
      pr.nombre as nombreProducto,
      COUNT( pr.id ) AS cantidadPedida
      FROM producto pr
      INNER JOIN pedidodetalle ped ON pr.id = ped.idProducto'. $condicionFechas . '
      GROUP BY pr.id
      ORDER BY cantidadPedida DESC';

      $lista = DB::select($query);
      
      return $lista == null ? [] : $lista;
    }
    catch(Exception $ex){
      throw $ex;
    }
  }

  public function GetMasPedido($request, $response, $args)
  {
    try
    {
      $data = $request->getParsedBody();
      $lista = self::TraerProductoMasPedido($data);
      $mesa = count($lista) > 0 && $lista[0] != null ? $lista[0] : [];
      $payload = json_encode(array(
        'mensaje' => 'Producto mas pedido',
        "producto" => $mesa));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch (Exception $e) {
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  static public function TraerProductoMenosPedido($data)
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
      pr.id as idProducto,
      pr.nombre as nombreProducto,
      COUNT( pr.id ) AS cantidadPedida
      FROM producto pr
      INNER JOIN pedidodetalle ped ON pr.id = ped.idProducto'. $condicionFechas . '
      GROUP BY pr.id
      ORDER BY cantidadPedida ASC';

      $lista = DB::select($query);
      
      return $lista == null ? [] : $lista;
    }
    catch(Exception $ex){
      throw $ex;
    }
  }

  public function GetMenosPedido($request, $response, $args)
  {
    try
    {
      $data = $request->getParsedBody();
      $lista = self::TraerProductoMenosPedido($data);
      $mesa = count($lista) > 0 && $lista[0] != null ? $lista[0] : [];
      $payload = json_encode(array(
        'mensaje' => 'Producto Menos pedido',
        "producto" => $mesa));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch (Exception $e) {
      $response = $response->withStatus(401);
      $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  public function TraerProductosMasPedidos($data)
{
    try {
        $condicionFechas = '';
        if (isset($data['fechaDesde']) && isset($data['fechaHasta'])) {
            $desde = $data['fechaDesde'];
            $hasta = $data['fechaHasta'];

            $condicionFechas = ' AND (p.fechaAlta >= "' . $desde . '")' . ' AND (p.fechaAlta <= "' . $hasta . '")';
        }

        $query =
            'SELECT 
            pr.id as idProducto,
            pr.nombre as nombreProducto,
            COUNT( pr.id ) AS cantidadPedida
            FROM producto pr
            INNER JOIN pedidodetalle ped ON pr.id = ped.idProducto' . $condicionFechas . '
            GROUP BY pr.id
            ORDER BY cantidadPedida DESC';

        $lista = DB::select($query);

        return $lista == null ? [] : $lista;
    } catch (Exception $ex) {
        throw $ex;
    }
}

public function GetMasPedidos($request, $response, $args)
{
    try {
        $data = $request->getParsedBody();
        $lista = self::TraerProductosMasPedidos($data);
        $payload = [
            'mensaje' => 'Productos más pedidos',
            'productos' => $lista
        ];

        // Ordenar la lista de mayor a menor cantidad pedida
        usort($payload['productos'], function ($a, $b) {
            return $b->cantidadPedida - $a->cantidadPedida;
        });

        $payload = json_encode($payload);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response = $response->withStatus(401);
        $response->getBody()->write(json_encode(array('error' => $e->getMessage())));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
}
