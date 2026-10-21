<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;

    protected $table = 'ordenes';

    protected $fillable = [
        'user_web_id',
        'estadoventa',
        'codigo_orden',
        'identificador_web_venta',
        'estado',
        'total',
        'nombre', // Agregar el campo 'nombre' aquí
        'telefono', // Agregar el campo 'telefono' aquí
        'direccion', // Agregar el campo 
    ];

    public function ordenProductos()
    {
        return $this->hasMany(OrdenProducto::class, 'orden_id');
    }

    public function user()
    {
        return $this->belongsTo(UserWeb::class, 'user_web_id');
    }
}
