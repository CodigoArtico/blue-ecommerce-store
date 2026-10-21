<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'stock',
        'fecha_vencimiento',
        'img_path',
        'img_url',
        'medida',
        'estado',
        'destacado',
        'marca_id',
        'presentacione_id'
    ];

    public function compras()
    {
        return $this->belongsToMany(Compra::class, 'compra_productos')->withTimestamps()
            ->withPivot('cantidad', 'precio_compra', 'precio_venta');
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'categoria_productos')->withTimestamps();
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function presentacione()
    {
        return $this->belongsTo(Presentacione::class);
    }

    public function precioVentaReciente()
    {
        return $this->compras()->latest('compras.created_at')->value('precio_venta');
    }


    public function handleUploadImage($image)
    {
        $file = $image;
        $name = time() . $file->getClientOriginalName();
        // $file->move(public_path() . '/img/productos/',$name);
        Storage::putFileAs('/public/productos/', $file, $name, 'public');

        return $name;
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'producto_id');
    }
}
