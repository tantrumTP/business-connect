<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = ['mediaable_id', 'mediaable_type', 'type', 'file_path', 'caption'];

    public function mediaable()
    {
        return $this->morphTo();
    }
}
