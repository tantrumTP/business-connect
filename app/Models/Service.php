<?php

namespace App\Models;

use App\Traits\HandlePathAliasTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, HandlePathAliasTrait, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'price',
        'category',
        'duration',
        'status',
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
        return "/api/services/{$this->id}";
    }
}
