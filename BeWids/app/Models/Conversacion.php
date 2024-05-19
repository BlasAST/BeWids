<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversacion extends Model
{
    use HasFactory;
    protected $fillable=['emisor',
    'receptor',
    'ultimo_mensaje'];


    public function mensajes()
    {
        return $this->hasMany(Mensaje::class);
    }
    public function participantes()
    {
        return $this->belongsTo(Participantes::class);
    }
}
