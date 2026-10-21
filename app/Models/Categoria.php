<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'categoria_productos') //aqui podemos agregar el nombre real de la base de datos DEL PIVOT
            ->withTimestamps();
    }

    public function caracteristica()
    {
        return $this->belongsTo(Caracteristica::class);
    }

    protected $fillable = ['caracteristica_id'];
}
