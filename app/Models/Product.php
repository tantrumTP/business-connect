<?php

namespace App\Models;

use App\Traits\HandlePathAliasTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HandlePathAliasTrait;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'price',
        'category',
        'availability',
        'warranty',
        'status'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediaable');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function getOriginalPath(): string
    {
        return "/api/products/{$this->id}";
    }
}
