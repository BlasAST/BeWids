<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;
    protected $fillable=[
        'emisor',
        'receptor',
        'conversacions_id',
        'read',
        'type',
        'body',
    ];

    public function conversacion()
    {
        return $this->belongsTo(Conversacion::class);
    }
    public function participantes()
    {
        return $this->belongsTo(Participantes::class,'emisor');
    }
}
