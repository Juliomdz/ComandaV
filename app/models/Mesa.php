<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mesa extends Model
{
    use SoftDeletes; // delete de forma lógica

    protected $table = 'mesa';
    protected $primaryKey = 'id';
    public $incrementing = true;
    // public $timestamps = false;

    const CREATED_AT = 'fechaAlta';
    const DELETED_AT = 'fechaBaja';
    const UPDATED_AT = 'fechaModificacion';

    protected $fillable = [
        'idMesaEstado',
        'codigo',
        'descripcion',
        'fechaAlta', 'fechaModificacion', 'fechaBaja'
    ];

    public function MesaEstado()
    {
        return $this->belongsTo(MesaEstado::class, 'idMesaEstado');
    }

    public function ListPedido()
    {
        return $this->hasMany(Pedido::class, 'idMesa');
    }

    static public function GenerarCodigoAlfanumerico($strength = 5)
    {
      $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
   
      do{
        $input_length = strlen($permitted_chars);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) 
        {
          $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
          $random_string .= $random_character;
        }
  
        $obj = self::where('codigo', $random_string)->first();
      }while($obj !== null);
      
      return $random_string;
    }

}