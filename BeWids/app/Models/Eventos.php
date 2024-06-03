<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eventos extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'titulo', 'descripcion', 'inicio', 'fin', 'horario', 'horas', 'dias',
        'precio', 'calle', 'cp', 'localidad', 'lugar', 'conex', 'latitud',
        'longitud', 'edad', 'url', 'categoria', 'api'
    ];
}
