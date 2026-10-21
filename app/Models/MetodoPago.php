<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    use HasFactory;

    protected $table = 'metodo_pagos'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $fillable = [
        'nombre',  // Si tienes un campo para el nombre del método de pago
        'descripcion', // Si tienes un campo para la descripción
        // otros campos si es necesario
    ];

    public function historialPagos()
    {
        return $this->hasMany(HistorialPago::class, 'metodo_pago_id');
    }
}
