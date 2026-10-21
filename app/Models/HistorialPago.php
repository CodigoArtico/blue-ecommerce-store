<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialPago extends Model
{
    use HasFactory;

    protected $table = 'historial_pagos';

    protected $fillable = [
        'user_web_id',
        'order_id',
        'metodo_pago_id',
        'monto_pagado',
        'fecha_pago',
        'estado',
    ];

    public function userWeb()
    {
        return $this->belongsTo(UserWeb::class, 'user_web_id');
    }

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'order_id');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }
}
