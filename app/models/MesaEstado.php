<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MesaEstado extends Model
{
    // Identificadores
    const Cliente_Esperando_Pedido = 1;
    const Cliente_Comiendo = 2;
    const Cliente_Pagando = 3;
    const Cerrada = 4;

    use SoftDeletes; // delete de forma lógica

    protected $table = 'mesaEstado';
    protected $primaryKey = 'id';
    public $incrementing = true;
    // public $timestamps = false;

    const CREATED_AT = 'fechaAlta';
    const DELETED_AT = 'fechaBaja';
    const UPDATED_AT = 'fechaModificacion';

    protected $fillable = [
        'descripcionEstado',
        'fechaAlta', 'fechaModificacion', 'fechaBaja'
    ];

    public function listMesa()
    {
        return $this->hasMany(Mesa::class, 'idMesaEstado');
    }

}