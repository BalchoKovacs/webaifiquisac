<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;

    public $table = 'users';
    protected $dates = ['deleted_at'];

    public $fillable = [
        'tipo_usuario',
        'area',
        'name',
        'telefono',
        'dni',
        'email',
        'password'
    ];

    function tipo(){
        return $this->belongsTo('App\Models\TipoUsuario','tipo_usuario','id');
    }
    function funcion_area(){
        return $this->belongsTo('App\Models\Area','area','id');
    }    

}
