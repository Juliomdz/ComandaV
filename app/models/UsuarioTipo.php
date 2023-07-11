<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioTipo extends Model
{
    // Identificadores
    const Administrador = 1;
    const Socio = 2;
    const Mozo = 3;
    const Bartender = 4;
    const Cervecero = 5;
    const Cocinero = 6;

    use SoftDeletes; // delete de forma lógica

    protected $table = 'usuarioTipo';
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

    public function listUsuario()
    {
        return $this->hasMany(Usuario::class, 'idUsuarioTipo');
    }

}