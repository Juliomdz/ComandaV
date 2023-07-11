<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoTipo extends Model
{
    const Comida = 1;
    const Bebida = 2;

    use SoftDeletes;

    protected $table = 'productoTipo';
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

    public function listProducto()
    {
        return $this->hasMany(Producto::class, 'idProductoTipo');
    }

}