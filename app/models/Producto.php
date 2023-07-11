<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    const Comida = 1;
    const Bebida = 2;

    use SoftDeletes;

    protected $table = 'producto';
    protected $primaryKey = 'id';
    public $incrementing = true;
    //public $timestamps = false;

    const CREATED_AT = 'fechaAlta';
    const DELETED_AT = 'fechaBaja';
    const UPDATED_AT = 'fechaModificacion';

    protected $fillable = [
        'idArea', 
        'idProductoTipo',
        'nombre', 
        'precio', 
        'stock',
        'tiempoEstimado',
        'fechaAlta', 'fechaModificacion', 'fechaBaja'
    ];

    public function ProductoTipo()
    {
        return $this->belongsTo(ProductoTipo::class, 'idProductoTipo');
    }

    public function Area()
    {
        return $this->belongsTo(Area::class, 'idArea');
    }

}