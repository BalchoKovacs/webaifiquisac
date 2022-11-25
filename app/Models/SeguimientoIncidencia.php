<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeguimientoIncidencia extends Model
{
    use SoftDeletes;

    public $table = 'seguimiento_incidencia';
    protected $dates = ['deleted_at'];

    public $fillable = [
        'id_incidencia',
        'id_accion',
        'emisor',
        'receptor',
        'detalle',
        'estatus_seg'

    ];
    function incidencia(){
        return $this->belongsTo('App\Models\Incidencia','id_incidencia','id');
    }        
    function accion(){
        return $this->belongsTo('App\Models\Accion','id_accion','id');
    }
    function emisorf(){
        return $this->belongsTo('App\Models\User','emisor','id');
    }
    function receptorf(){
        return $this->belongsTo('App\Models\User','receptor','id');
    }
}