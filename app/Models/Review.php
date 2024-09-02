<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['mediaable_id', 'mediaable_type', 'user_id', 'rating', 'text'];

    public function mediaable()
    {
        return $this->morphTo();
    }
}
