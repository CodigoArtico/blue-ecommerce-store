<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    use HasFactory;

    protected $table = 'calificaciones';

    protected $fillable = [
        'producto_id',
        'user_web_id',
        'puntuacion',
        'comentario',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function userWeb()
    {
        return $this->belongsTo(UserWeb::class, 'user_web_id');
    }
}
