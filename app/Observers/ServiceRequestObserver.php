<?php

namespace App\Observers;

use App\Models\ServiceRequest;

class ServiceRequestObserver
{
    /**
     * Handle the ServiceRequest "created" event.
     */
    public function created(ServiceRequest $serviceRequest): void
    {
        $serviceRequest->logs()->create([
            'message' => 'Criado por ' . auth()->user()->name,
            'action' => 'created',
        ]);
    }

    /**
     * Handle the ServiceRequest "updated" event.
     */
    public function updated(ServiceRequest $serviceRequest): void
    {
        //
    }

    /**
     * Handle the ServiceRequest "deleted" event.
     */
    public function deleted(ServiceRequest $serviceRequest): void
    {
        $serviceRequest->logs()->create([
            'message' => 'Deletado por ' . auth()->user()->name,
            'action' => 'deleted',
            'context' => [
                'title' => $serviceRequest->title,
                'description' => $serviceRequest->description,
                'sector' => $serviceRequest->sector,
                'priority' => $serviceRequest->priority,
                'scheduled_at' => $serviceRequest->scheduled_at,
                'started_at' => $serviceRequest->started_at,
                'completed_at' => $serviceRequest->completed_at,
                'assigned_to' => $serviceRequest->assigned_to,
                'status' => $serviceRequest->status,
                'attachments' => $serviceRequest->attachments,
                'created_by' => $serviceRequest->created_by,
                'approved_at' => $serviceRequest->approved_at,
            ],
        ]);
    }

    /**
     * Handle the ServiceRequest "restored" event.
     */
    public function restored(ServiceRequest $serviceRequest): void
    {
        //
    }

    /**
     * Handle the ServiceRequest "force deleted" event.
     */
    public function forceDeleted(ServiceRequest $serviceRequest): void
    {
        //
    }
}
