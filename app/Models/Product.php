<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'price',
        'media',
        'availability',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediaable');
    }
}
