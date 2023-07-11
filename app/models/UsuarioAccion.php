<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioAccion extends Model
{
    use SoftDeletes;

    protected $table = 'usuarioaccion';
    protected $primaryKey = 'id';
    public $incrementing = true;
    //public $timestamps = false;

    const CREATED_AT = 'fechaAlta';
    const DELETED_AT = 'fechaBaja';
    const UPDATED_AT = 'fechaModificacion';

    protected $fillable = [
        'idUsuario', 
        'idUsuarioAccionTipo', 
        'idPedido', 
        'idPedidoDetalle', 
        'idMesa', 
        'idProducto', 
        'idArea', 
        'hora', 
        'fechaAlta', 'fechaModificacion', 'fechaBaja'
    ];

    public function UsuarioAccionTipo()
    {
        return $this->belongsTo(UsuarioAccionTipo::class,'idUsuarioAccionTipo');
    }

    public function Usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }

    public function Pedido()
    {
        return $this->belongsTo(Pedido::class, 'idPedido');
    }
    
    public function PedidoDetalle()
    {
        return $this->belongsTo(PedidoDetalle::class, 'idPedidoDetalle');
    }

    public function Mesa()
    {
        return $this->belongsTo(Mesa::class, 'idMesa');
    }
    
    public function Producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto');
    }

    public function Area()
    {
        return $this->belongsTo(Area::class, 'idArea');
    }

}