<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'direccion',
        'telefono',
        'correoElectronico',
        'serviciosProductos', // Considera usar JSON o una tabla relacionada
        'precios', // Considera usar JSON o una tabla relacionada
        'horariosAtencion', // Considera usar JSON
        'fotos', // Considera usar JSON o una tabla relacionada
        'videos', // Considera usar JSON o una tabla relacionada
        'sitioWeb',
        'redesSociales', // Considera usar JSON
        'reseñas', // Considera usar JSON o una tabla relacionada
        'calificaciones', // Considera usar JSON o una tabla relacionada
        'caracteristicas', // Considera usar JSON
        'zonasCubiertas' // Considera usar JSON
    ];

    // Si usas JSON, recuerda castear los campos
    protected $casts = [
        'serviciosProductos' => 'array',
        'precios' => 'array',
        'horariosAtencion' => 'array',
        'fotos' => 'array',
        'videos' => 'array',
        'redesSociales' => 'array',
        'reseñas' => 'array',
        'calificaciones' => 'array',
        'caracteristicas' => 'array',
        'zonasCubiertas' => 'array',
    ];
}
