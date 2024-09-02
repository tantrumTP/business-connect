<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'type', 'file_path', 'caption'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
