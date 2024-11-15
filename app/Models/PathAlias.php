<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PathAlias extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'alias', 'language', 'status'];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

}
