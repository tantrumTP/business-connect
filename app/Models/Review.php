<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['rating', 'content','reviewable_id', 'reviewable_type', 'user_id'];

    public function reviewable()
    {
        return $this->morphTo();
    }
}
