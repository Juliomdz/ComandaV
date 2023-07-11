<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    // Identificadores
    const Administracion = 1;
    const Salon = 2;
    const Barra_Tragos_Vinos = 3;
    const Barra_Cerveza = 4;
    const Cocina = 5;
    const Candy_Bar = 6;

    use SoftDeletes; // delete de forma lógica

    protected $table = 'area';
    protected $primaryKey = 'id';
    public $incrementing = true;
    // public $timestamps = false;

    const CREATED_AT = 'fechaAlta';
    const DELETED_AT = 'fechaBaja';
    const UPDATED_AT = 'fechaModificacion';

    protected $fillable = [
        'descripcion',
        'fechaAlta', 'fechaModificacion', 'fechaBaja'
    ];

    public function listUsuario()
    {
        return $this->hasMany(Usuario::class, 'idArea');
    }

    public function listProducto()
    {
        return $this->hasMany(Producto::class, 'idArea');
    }
}