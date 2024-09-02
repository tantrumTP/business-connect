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
        'name',
        'description',
        'direction',
        'phone',
        'email',
        'services',
        'products',
        'media',
        'hours',
        'website',
        'social_networks',
        'reviews',
        'characteristics',
        'covered_areas'
    ];

    // Si usas JSON, recuerda castear los campos
    protected $casts = [
        'hours' => 'array',
        'social_networks' => 'array',
        'characteristics' => 'array',
        'covered_areas' => 'array',
    ];


    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediaable');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
