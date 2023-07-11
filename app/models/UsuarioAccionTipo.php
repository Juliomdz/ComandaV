<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioAccionTipo extends Model
{
    // Identificadores
    const Login = 1;
    const Alta = 2;
    const Baja = 3;
    const Modificacion = 4;
    const CargaFoto = 5;
    const CargaCSV = 6;
    const DescargaCSV = 7;

    use SoftDeletes; // delete de forma lógica

    protected $table = 'usuarioAccionTipo';
    protected $primaryKey = 'id';
    public $incrementing = true;
    // public $timestamps = false;

    const CREATED_AT = 'fechaAlta';
    const DELETED_AT = 'fechaBaja';
    const UPDATED_AT = 'fechaModificacion';

    protected $fillable = [
        'tipo',
        'fechaAlta', 'fechaModificacion', 'fechaBaja'
    ];
    
    public function listUsuarioAccion()
    {
        return $this->hasMany(UsuarioAccion::class, 'idUsuarioAccionTipo');
    }
}