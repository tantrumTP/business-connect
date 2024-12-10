<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requestable_id',
        'requestable_type',
        'name',
        'email',
        'phone',
        'message',
        'attended',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestable()
    {
        return $this->morphTo();
    }
}
