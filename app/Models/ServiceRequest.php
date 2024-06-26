<?php

namespace App\Models;

use App\Enums\ServiceRequestSectorEnum;
use App\Enums\ServiceRequestStatusEnum;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ServiceRequestPriorityEnum;
use App\Observers\ServiceRequestObserver;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([ServiceRequestObserver::class])]
class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'sector',
        'priority',
        'scheduled_at',
        'started_at',
        'completed_at',
        'assigned_to',
        'status',
        'attachments',
        'created_by',
        'approved_at'
    ];

    protected $casts = [
        'status' => ServiceRequestStatusEnum::class,
        'priority' => ServiceRequestPriorityEnum::class,
        'sector' => ServiceRequestSectorEnum::class,
        'attachments' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function logs()
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}
