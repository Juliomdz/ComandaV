<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PedidoEstado extends Model
{
    const Pendiente = 1;
    const En_Preparacion = 2;
    const Listo_Para_Servir = 3;
    const Cancelado = 4;
    const Servido = 5;
    const Cobrado = 6;

    use SoftDeletes;

    protected $table = 'pedidoEstado';
    protected $primaryKey = 'id';
    public $incrementing = true;
    //public $timestamps = false;

    const CREATED_AT = 'fechaAlta';
    const DELETED_AT = 'fechaBaja';
    const UPDATED_AT = 'fechaModificacion';

    protected $fillable = [
        'tipo', 
        'fechaAlta', 'fechaModificacion', 'fechaBaja'
    ];

    public function listPedido()
    {
        return $this->hasMany(Pedido::class, 'idPedidoEstado');
    }

    public function listPedidoDetalle()
    {
        return $this->hasMany(PedidoDetalle::class, 'idPedidoEstado');
    }

}