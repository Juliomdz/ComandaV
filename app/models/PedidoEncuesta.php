<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PedidoEncuesta extends Model
{
    use SoftDeletes;

    protected $table = 'pedidoEncuesta';
    protected $primaryKey = 'id';
    public $incrementing = true;
    //public $timestamps = false;

    const CREATED_AT = 'fechaAlta';
    const DELETED_AT = 'fechaBaja';
    const UPDATED_AT = 'fechaModificacion';

    protected $fillable = [
        'idMesa', 
        'idPedido', 
        'puntajeMesa', 
        'puntajeRestaurante',
        'puntajeMozo', 
        'puntajeCocinero', 
        'comentario', 
        'fechaAlta', 'fechaModificacion', 'fechaBaja'
    ];

    public function Mesa()
    {
      return $this->belongsTo(Mesa::class, 'idMesa');
    }
    public function Pedido() 
    { 
      return $this->belongsTo(Pedido::class, 'idPedido'); 
    }
    
}