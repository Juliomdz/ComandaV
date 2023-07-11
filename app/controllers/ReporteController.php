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

require ('./fpdf/fpdf.php');

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

class ReporteController
{
  static public function DiferenciaMinutosPedidos($data)
  {
    $lista = [];
      
    $condicionFechas = '';
    if(isset($data['fechaDesde']) && isset($data['fechaHasta']))
    {
      $desde = $data['fechaDesde'];
      $hasta = $data['fechaHasta'];
      
      $condicionFechas = ' AND (pD.fechaAlta >= "' .$desde. '")'. ' AND (pD.fechaAlta <= "'.$hasta. '")';
    }
  
    $query = 'SELECT 
    TIMEDIFF(pD.tiempoFin,pD.tiempoInicio) as diferenciaMinutos,
    pD.tiempoEstimado,
    p.codigo as codigoPedidoGeneral
    from pedidoDetalle pD
    INNER JOIN Pedido p ON p.id = pD.idPedido
    INNER JOIN Producto pro ON pro.id = pD.idProducto'. 
    $condicionFechas . ' AND TIMEDIFF(pD.tiempoFin,pD.tiempoInicio) > pD.tiempoEstimado';

    return DB::select($query);
  }

  static public function AgregarDiferenciaMinutosPedidos($pdf, $data)
  {
    try
    {
      $lista = self::DiferenciaMinutosPedidos($data);

      if(count($lista) > 0){
        $pdf->Cell(20,10,'---------------- Pedidos que se pasaron del tiempo estimado ----------------','C');
        $pdf->SetFont('Arial','B', 10);
        foreach ($lista as  $value) {
          $pdf->Ln(13);
          $pdf->Cell(0,0,'Codigo Pedido General:     '. $value->codigoPedidoGeneral,'C');
          $pdf->Ln(7);
          $pdf->Cell(0,0,'Diferencia Minutos:      '. $value->diferenciaMinutos,'C');
          $pdf->Ln(7);
          $pdf->Cell(0,0,'Tiempo Estimado:     '. $value->tiempoEstimado,'C');
          $pdf->Ln(7);
        }
      }
      
      return $lista == null ? [] : $lista;
    }
    catch(Exception $ex){
      throw $ex;
    }
  }

  static public function ProductoMenosVendido($data)
  {
    $lista = [];
      
    $condicionFechas = '';
    if(isset($data['fechaDesde']) && isset($data['fechaHasta']))
    {
      $desde = $data['fechaDesde'];
      $hasta = $data['fechaHasta'];
      
      $condicionFechas = ' AND (pD.fechaAlta >= "' .$desde. '")'. ' AND (pD.fechaAlta <= "'.$hasta. '")';
    }
  
    $query =
    'SELECT 
    pro.nombre AS producto,
    SUM( pD.cantidadProducto ) AS cantidadVendidas
    FROM pedidoDetalle pD
    INNER JOIN Producto pro ON pro.id = pD.idProducto'. $condicionFechas . '
    GROUP BY pro.nombre
    ORDER BY cantidadVendidas asc';

    return DB::select($query);
  }
  static public function ProductoMasVendido($data)
  {
    $lista = [];
      
    $condicionFechas = '';
    if(isset($data['fechaDesde']) && isset($data['fechaHasta']))
    {
      $desde = $data['fechaDesde'];
      $hasta = $data['fechaHasta'];
      
      $condicionFechas = ' AND (pD.fechaAlta >= "' .$desde. '")'. ' AND (pD.fechaAlta <= "'.$hasta. '")';
    }
  
    $query =
    'SELECT 
    pro.nombre AS producto,
    SUM( pD.cantidadProducto ) AS cantidadVendidas
    FROM pedidoDetalle pD
    INNER JOIN Producto pro ON pro.id = pD.idProducto'. $condicionFechas . '
    GROUP BY pro.nombre
    ORDER BY cantidadVendidas desc';

    return DB::select($query);
  }
  static public function AgregarProductoMenosVendido($pdf, $data)
  {
    try
    {
      $lista = self::ProductoMenosVendido($data);

      if(count($lista) > 0){
        $pdf->Cell(20,10,'---------------- PRODUCTO MENOS VENDIDO ----------------','C');
        $pdf->SetFont('Arial','B', 10);
        $pdf->Ln(13);
        $pdf->Cell(0,0,'Producto:      '. $lista[0]->producto,'C');
        $pdf->Ln(7);
        $pdf->Cell(0,0,'Cantidad ventas:     '. $lista[0]->cantidadVendidas,'C');
        $pdf->Ln(7);
      }
      
      return $lista == null ? [] : $lista;
    }
    catch(Exception $ex){
      throw $ex;
    }
  }
  static public function AgregarProductoMasVendido($pdf, $data)
  {
    try
    {
      $lista = self::ProductoMasVendido($data);

      if(count($lista) > 0){
        $pdf->Cell(20,10,'---------------- PRODUCTO MAS VENDIDO ----------------','C');
        $pdf->SetFont('Arial','B', 10);
        $pdf->Ln(13);
        $pdf->Cell(0,0,'Producto:      '. $lista[0]->producto,'C');
        $pdf->Ln(7);
        $pdf->Cell(0,0,'Cantidad ventas:     '. $lista[0]->cantidadVendidas,'C');
        $pdf->Ln(7);
      }
      
      return $lista == null ? [] : $lista;
    }
    catch(Exception $ex){
      throw $ex;
    }
  }
  static public function TotalVendido($pdf, $data)
  {
    $list = Pedido::all();
    if(isset($data['fechaDesde']) && isset($data['fechaHasta']))
    {
      $desde = $data['fechaDesde'];
      $hasta = $data['fechaHasta'];

      $list = Pedido::all()
      ->where('fechaAlta','>=', $desde)
      ->where('fechaHasta','<=', $desde);
    }

    $total = 0;
    foreach ($list as $pedido) {
      $total += $pedido->importe;
    }

    $pdf->Ln(13);
    $pdf->Cell(20,10,'---------------- TOTAL VENDIDO','C');
    $pdf->SetFont('Arial','B', 10);
    $pdf->Ln(13);
    $pdf->Cell(0,0,'Importe:      $'. $total,'C');
    $pdf->Ln(7);
  }
  static public function GetPedidoMasBarato($pdf, $data)
  {
    $min = Pedido::all()
    ->where('idPedidoEstado','=', PedidoEstado::Cobrado)
    ->min('importe');
    
    $pedido = Pedido::where('importe','=', $min)->first();
    if(isset($data['fechaDesde']) && isset($data['fechaHasta']))
    {
      $desde = $data['fechaDesde'];
      $hasta = $data['fechaHasta'];

      $pedido = Pedido::all()
      ->where('fechaAlta','>=', $desde)
      ->where('fechaHasta','<=', $desde)
      ->where('importe','=', $min)->first();
    }

    $pdf->Cell(20,10,'---------------- FACTURA MENOR IMPORTE ----------------','C');
    $pdf->SetFont('Arial','B', 10);
    $pdf->Ln(13);
    $pdf->Cell(0,0,'Costo:      $'. $pedido->importe,'C');
    $pdf->Ln(7);
    $pdf->Cell(0,0,'Codigo del Pedido:     '. $pedido->codigo,'C');
    $pdf->Ln(7);
    $pdf->Cell(0,0,'Codigo de Mesa:     '. $pedido->Mesa->codigo,'C');
    $pdf->Ln(7);
    $pdf->Cell(0,0,'Nombre del Cliente:    '. $pedido->nombreCliente,'C');
    $pdf->Ln(7);
    $pdf->Cell(0,0,'Fecha:     '. $pedido->fechaAlta,'C');
    $pdf->Ln(7);
  }
  static public function GetPedidoMasCaro($pdf, $data)
  {
    $max = Pedido::all()
    ->where('idPedidoEstado','=', PedidoEstado::Cobrado)
    ->max('importe');
    
    $pedido = Pedido::where('importe','=', $max)->first();
    if(isset($data['fechaDesde']) && isset($data['fechaHasta']))
    {
      $desde = $data['fechaDesde'];
      $hasta = $data['fechaHasta'];

      $pedido = Pedido::all()
      ->where('fechaAlta','>=', $desde)
      ->where('fechaHasta','<=', $desde)
      ->where('importe','=', $max)->first();
    }

    $pdf->Cell(20,10,'---------------- FACTURA MAYOR IMPORTE ----------------','C');
    $pdf->SetFont('Arial','B', 10);
    $pdf->Ln(13);
    $pdf->Cell(0,0,'Costo:      $'. $pedido->importe,'C');
    $pdf->Ln(7);
    $pdf->Cell(0,0,'Codigo del Pedido:     '. $pedido->codigo,'C');
    $pdf->Ln(7);
    $pdf->Cell(0,0,'Codigo de Mesa:     '. $pedido->Mesa->codigo,'C');
    $pdf->Ln(7);
    $pdf->Cell(0,0,'Nombre del Cliente:    '. $pedido->nombreCliente,'C');
    $pdf->Ln(7);
    $pdf->Cell(0,0,'Fecha:     '. $pedido->fechaAlta,'C');
    $pdf->Ln(7);
  }

  static public function PedidosCancelados($data)
  {
    {
      $condicionFechas = '';
      if(isset($data['fechaDesde']) && isset($data['fechaHasta']))
      {
        $desde = $data['fechaDesde'];
        $hasta = $data['fechaHasta'];
        
        $condicionFechas = ' WHERE (pD.fechaAlta >= "' .$desde. '")'. ' AND (pD.fechaAlta <= "'.$hasta. '")';
      }
    
      $query =
      'SELECT 
      pro.nombre AS productoCancelado,
      p.codigo AS codigoPedidoGeneral,
      pD.fechaModificacion AS fechaCancelacion
      FROM pedidoDetalle pD
      INNER JOIN Pedido p ON p.id = pD.idPedido
      INNER JOIN Producto pro ON pro.id = pD.idProducto
      INNER JOIN PedidoEstado pE ON pE.id = '. PedidoEstado::Cancelado . ' 
      '. $condicionFechas .' AND pD.idPedidoEstado = '.PedidoEstado::Cancelado ;
  
      return DB::select($query);
    }
  }

  static public function AgregarPedidosCancelados($pdf, $data)
  {
    try
    {
      $lista = self::PedidosCancelados($data);

      if(count($lista) > 0){
        $pdf->Cell(20,10,'---------------- PEDIDOS CANCELADOS ----------------','C');
        $pdf->SetFont('Arial','B', 10);
        $pdf->Ln(13);
        foreach ($lista as $value) {
          $pdf->Cell(0,0,'Producto Cancelado:      '. $value->productoCancelado,'C');
          $pdf->Ln(5);
          $pdf->Cell(0,0,'Ccdigo Pedido General:     '. $value->codigoPedidoGeneral,'C');
          $pdf->Ln(5);
          $pdf->Cell(0,0,'fecha de cancelacion:     '. $value->fechaCancelacion,'C');
          $pdf->Ln(5);
        }
      }
      
      return $lista == null ? [] : $lista;
    }
    catch(Exception $ex){
      throw $ex;
    }
  }
  public function DescargarReportePedido($request, $response, $args)
  {
    try
    {
      $directory = './reportes/';
      if (!file_exists($directory)) { mkdir($directory, 0777, true); }

      $pdf = new FPDF();
      $pdf->AddPage();
      $pdf->SetFont('Arial','B', 14);

      $mensaje = "Reporte del mes descargado con éxito";
      $data = $request->getParsedBody();
      if(isset($data['fechaDesde']) && isset($data['fechaHasta'])){
        $mensaje = 
        "Reporte entre fechaDesde " .$data['fechaDesde']." y fechaHasta ".$data['fechaHasta']. " descargado con éxito.";
        $pdf->Cell(20,10,"Reporte entre Fecha Desde " .$data['fechaDesde']." y Fecha Hasta ".$data['fechaHasta'],'C');
        $pdf->Ln(13);
      }
      
      self::TotalVendido($pdf, $data);
      self::AgregarProductoMasVendido($pdf, $data);
      self::AgregarProductoMenosVendido($pdf, $data);
      self::GetPedidoMasCaro($pdf, $data);
      self::GetPedidoMasBarato($pdf, $data);
      self::AgregarPedidosCancelados($pdf, $data);
      self::AgregarDiferenciaMinutosPedidos($pdf, $data);

      $fileName = 'reporte_' .date('Ymd_his_A'). '.pdf';
      $path = $directory . $fileName;

      $pdf->Output('F', $path, 'I');

      //TEST HEADERS

      //header("Content-type:application/pdf");

      // It will be called downloaded.pdf
      //header("Content-Disposition:attachment;filename=$fileName");
      
      // The PDF source is in original.pdf
      //readfile($path, true);

      // TEST HEADERS

      $payload = json_encode(array(
        "mensaje" => $mensaje,
        "rutaReporteDescargado" => $path
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

  public function DescargarReporteGenerado($request, $response, $args)
  {
    $directory = './reportes/';
    $archivo = $args['nombreArchivo'];
    $path = $directory . $archivo;
      
      // Verificar si el archivo existe
      if (file_exists($path)) {
          // Definir los encabezados de respuesta para la descarga del archivo
          $response = $response->withHeader('Content-Description', 'File Transfer')
                               ->withHeader('Content-Type', 'application/pdf')
                               ->withHeader('Content-Disposition', 'attachment; filename=' . basename($archivo))
                               ->withHeader('Content-Length', filesize($path));
  
          // Leer el archivo y enviarlo como contenido de respuesta
          $response->getBody()->write(file_get_contents($path));
  
          return $response;
      } else {
          // Si el archivo no existe, responder con un mensaje de error
          return $response->withStatus(404)->write('Archivo no encontrado');
      }
  }

}
