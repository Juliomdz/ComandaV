<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PedidoDetalle extends Model
{
    const Comida = 1;
    const Bebida = 2;

    use SoftDeletes;

    protected $table = 'pedidoDetalle';
    protected $primaryKey = 'id';
    public $incrementing = true;
    //public $timestamps = false;

    const CREATED_AT = 'fechaAlta';
    const DELETED_AT = 'fechaBaja';
    const UPDATED_AT = 'fechaModificacion';

    protected $fillable = [
        'idPedido', 'idProducto', 'idPedidoEstado', 'idUsuarioEncargado',
        'cantidadProducto', 
        'tiempoEstimado', 'tiempoInicio', 'tiempoFin',
        'fechaAlta', 'fechaModificacion', 'fechaBaja'
    ];

    public function GetMinutosTotalPedido(){
      if($this->tiempoInicio != null && $this->tiempoFin != null){

        $arrTiempoFin = explode(":", $this->tiempoFin);

        $date = new DateTime($this->tiempoInicio);
        $date->modify('-'.intval($arrTiempoFin[0]).' hours');
        $date->modify('+'.intval($arrTiempoFin[1]).' minute');
        $date->modify('-'.intval($arrTiempoFin[2]).' second');

        return intval($date->format('i:s'));
      }
      return -1;
    }

    public function Pedido()
    {
      return $this->belongsTo(Pedido::class, 'idPedido'); 
    }
    public function Producto() 
    { 
      return $this->belongsTo(Producto::class, 'idProducto');
    }
    public function PedidoEstado() 
    { 
      return $this->belongsTo(PedidoEstado::class, 'idPedidoDetalleEstado');
    }
    public function UsuarioEncargado() 
    { 
      return $this->belongsTo(Usuario::class, 'idUsuarioEncargado'); 
    }
}