<?php
date_default_timezone_set("America/Buenos_Aires");
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutentificadorJWT.php';
require_once './models/PedidoEncuesta.php';
require_once './models/Pedido.php';
require_once './models/PedidoEstado.php';
require_once './models/PedidoDetalle.php';
require_once './models/Mesa.php';
require_once './models/MesaEstado.php';
require_once './models/Usuario.php';
require_once './models/UsuarioTipo.php';
require_once './models/UsuarioAccion.php';
require_once './models/UsuarioAccionTipo.php';
require_once './models/ManejadorArchivos.php';
use \App\Models\PedidoEncuesta as PedidoEncuesta;
use \App\Models\Pedido as Pedido;
use \App\Models\PedidoEstado as PedidoEstado;
use \App\Models\PedidoDetalle as PedidoDetalle;
use \App\Models\Mesa as Mesa;
use \App\Models\MesaEstado as MesaEstado;
use \App\Models\Usuario as Usuario;
use \App\Models\UsuarioTipo as UsuarioTipo;
use \App\Models\UsuarioAccion as UsuarioAccion;
use \App\Models\UsuarioAccionTipo as UsuarioAccionTipo;
use Slim\Psr7\Response;
use Illuminate\Database\Capsule\Manager as DB;

class PedidoEncuestaController 
{

  public function GetAllPorDebajoPuntaje($request, $response, $args)
  {
    try{
      if(!isset($args['puntaje'])) { throw new Exception('Argumento puntaje no seteado.'); }
      if(!is_numeric($args['puntaje'])) { throw new Exception('puntaje debe ser numerico.'); }
      $lista = PedidoEncuesta::all()
      ->where('puntajeMesa','<=',$args['puntaje'])
      ->where('puntajeRestaurante','<=',$args['puntaje'])
      ->where('puntajeMozo','<=',$args['puntaje'])
      ->where('puntajeCocinero','<=',$args['puntaje']);
      
      $payload = json_encode(array(
      "mensaje" => "Lista de Encuestas con puntaje menor a ".$args['puntaje'],  
      "listaPedidoEncuesta" => $lista));
      
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Exception $ex){
      $response = $response->withStatus(401);
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
  }
  
  public function GetAllPorEncimaPuntaje($request, $response, $args)
  {
    try{
      if(!isset($args['puntaje'])) { throw new Exception('Argumento puntaje no seteado.'); }
      if(!is_numeric($args['puntaje'])) { throw new Exception('puntaje debe ser numerico.'); }
      $lista = PedidoEncuesta::all()
      ->where('puntajeMesa','>=',$args['puntaje'])
      ->where('puntajeRestaurante','>=',$args['puntaje'])
      ->where('puntajeMozo','>=',$args['puntaje'])
      ->where('puntajeCocinero','>=',$args['puntaje']);
      
      $payload = json_encode(array(
      "mensaje" => "Lista de Encuestas con puntaje mayor a ".$args['puntaje'],  
      "listaPedidoEncuesta" => $lista));
      
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Exception $ex){
      $response = $response->withStatus(401);
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  public function GetAll($request, $response, $args)
  {
    $lista = PedidoEncuesta::all();
    $payload = json_encode(array("listaPedidoEncuesta" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function GetAllBy($request, $response, $args)
  {
    $field = $args['field'];
    $value = $args['value'];

    $lista = PedidoEncuesta::where($field, $value)->get();

    $payload = json_encode(array("listaPedidoEncuesta" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  private function ValidateInputData($data){
    if($data == null) { throw new Exception("No se encontraron datos de entrada"); }

    if (!isset($data['codigoMesa'])) { throw new Exception("codigoMesa no seteado"); }
    else if(Mesa::where('codigo','=',$data['codigoMesa'])->first() == null) { throw new Exception("No existe Mesa con el código indicado"); }

    if (!isset($data['codigoPedido'])) { throw new Exception("codigoPedido no seteado"); }
    else if(Pedido::where('codigo','=', $data['codigoPedido'])->first() == null) { throw new Exception("No existe Pedido con el código indicado"); }
    
    if (!isset($data['puntajeMesa'])) { throw new Exception("Puntaje de Mesa no indicado"); }
    if (intval($data['puntajeMesa']) < 1 || intval($data['puntajeMesa']) > 10) { throw new Exception("El puntaje de Mesa debe estar entre el rango [1-10]"); }

    if (!isset($data['puntajeRestaurante'])) { throw new Exception("Puntaje de Restaurante no indicado"); }
    if (intval($data['puntajeRestaurante']) < 1 || intval($data['puntajeRestaurante']) > 10) { throw new Exception("El puntaje del Restaurante debe estar entre el rango [1-10]"); }
   
    if (!isset($data['puntajeMozo'])) { throw new Exception("Puntaje del Mozo no indicado"); }
    if (intval($data['puntajeMozo']) < 1 || intval($data['puntajeMozo']) > 10) { throw new Exception("El puntaje del Mozo debe estar entre el rango [1-10]"); }

    if (!isset($data['puntajeCocinero'])) { throw new Exception("Puntaje del Cocinero no indicado"); }
    if (intval($data['puntajeCocinero']) < 1 || intval($data['puntajeCocinero']) > 10) { throw new Exception("El puntaje del Cocinero debe estar entre el rango [1-10]"); }
    
    if (!isset($data['comentario'])) { throw new Exception("Comentario no indicado"); }
  }

  public function Save($request, $response, $args)
  {
    try
    {
      $data = $request->getParsedBody();

      // 'idMesa', 
      // 'idPedido', 
      // 'puntajeMesa', 
      // 'puntajeRestaurante',
      // 'puntajeMozo', 
      // 'puntajeCocinero', 
      // 'comentario', 

      self::ValidateInputData($data);

      $idMesa = Mesa::where('codigo','=',$data['codigoMesa'])->first()->id;
      $idPedido = Pedido ::where('codigo','=', $data['codigoPedido'])->first()->id;

      $obj = new PedidoEncuesta();
      $obj->idMesa = intval($idMesa);
      $obj->idPedido = intval($idPedido);
      $obj->puntajeMesa = intval($data['puntajeMesa']);
      $obj->puntajeRestaurante = intval($data['puntajeRestaurante']);
      $obj->puntajeMozo = intval($data['puntajeMozo']);
      $obj->puntajeCocinero = intval($data['puntajeCocinero']);
      $obj->comentario = trim($data['comentario']);
      $obj->save();
      
      $payload = json_encode(
      array(
      "mensaje" => "Encuesta enviada con éxito. ¡Gracias, vuelva pronto!",
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
    $id = $args['id'];
    
    $obj = PedidoEncuesta::find($id);
    $obj->delete();

    $payload = json_encode(array("mensaje" => "Encuesta borrada con éxito"));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
