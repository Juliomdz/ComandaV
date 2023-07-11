<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Pedido extends Model
{
    const Comida = 1;
    const Bebida = 2;

    use SoftDeletes;

    protected $table = 'pedido';
    protected $primaryKey = 'id';
    public $incrementing = true;
    //public $timestamps = false;

    const CREATED_AT = 'fechaAlta';
    const DELETED_AT = 'fechaBaja';
    const UPDATED_AT = 'fechaModificacion';

    protected $fillable = [
        'codigo', 
        'idMesa', 
        'idPedidoEstado', 
        'idUsuarioMozo', 
        'idUsuarioSocio',
        'nombreCliente', 
        'foto', 
        'importe', 
        'fechaAlta', 'fechaModificacion', 'fechaBaja'
    ];

    public function Mesa()
    {
      return $this->belongsTo(Mesa::class, 'idMesa');
    }
    public function PedidoEstado() 
    { 
      return $this->belongsTo(PedidoEstado::class, 'idPedidoEstado'); 
    }
    public function UsuarioMozo() 
    { 
      return $this->belongsTo(Usuario::class, 'idUsuarioMozo'); 
    }
    public function UsuarioSocio() 
    { 
      return $this->belongsTo(Usuario::class, 'idUsuarioSocio'); 
    }

    public function ListPedidoDetalle()
    {
        return $this->hasMany(PedidoDetalle::class, 'idPedido');
    }

    static public function GenerarCodigoAlfanumerico($strength = 5)
    {
      $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
      do{
        $input_length = strlen($permitted_chars);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
          $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
          $random_string .= $random_character;
        }
  
        $obj = self::where('codigo', $random_string)->first();
      }while($obj !== null);
      
      return $random_string;
    }
    
}