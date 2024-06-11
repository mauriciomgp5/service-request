<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'service_request_id',
        'user_id',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeLike($query)
    {
        return $query->where('type', 'like');
    }

    public function scopeUnlike($query)
    {
        return $query->where('type', 'unlike');
    }

    public function scopeByUser($query, $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeByServiceRequest($query, $serviceRequest)
    {
        return $query->where('service_request_id', $serviceRequest->id);
    }
}
