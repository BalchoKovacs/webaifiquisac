<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incidencia extends Model
{
    use SoftDeletes;

    public $table = 'incidencia';
    protected $dates = ['deleted_at'];

    public $fillable = [
        'id_tipo_incidencia',
        'id_categoria',
        'codigo',
        'id_prioridad',
        'descripcion',
        'id_creador',
        'id_estado'
    ];
    function tipo_incidencia(){
        return $this->belongsTo('App\Models\TipoIncidencia','id_tipo_incidencia','id');
    }   
     function prioridad(){
        return $this->belongsTo('App\Models\TipoIncidencia','id_tipo_incidencia','id');
    } 
    function estado(){
        return $this->belongsTo('App\Models\Accion','id_estado','id');
    }         
    function creador(){
        return $this->belongsTo('App\Models\User','id_creador','id');
    }
}