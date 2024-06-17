<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Infousuario extends Model
{
    use HasFactory;
     protected $fillable=[
        'id_user',
        'nombre',
        'fecha_nacimiento',
        'descripcion',
        'numero_contacto',
        'provincia',
     ];
}
