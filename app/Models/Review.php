<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['rating', 'content','reviewable_id', 'reviewable_type', 'user_id'];

    public function reviewable()
    {
        return $this->morphTo();
    }
}
