<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'context',
        'action',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function loggable()
    {
        return $this->morphTo();
    }
}
