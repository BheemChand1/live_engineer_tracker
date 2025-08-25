<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'priority',
        'due_date',
        'customer_name',
        'customer_phone',
        'customer_address',
        'device_type',
        'estimated_hours',
        'status',
        'engineer_id',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
    ];

    /**
     * Get the engineer assigned to the task.
     */
    public function engineer(): BelongsTo
    {
        return $this->belongsTo(Engineer::class, 'engineer_id', 'user_id');
    }

    /**
     * Check if the task is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date < now() && $this->status !== 'completed';
    }

    /**
     * Get the duration of the task (if completed).
     */
    public function getDuration(): ?string
    {
        if ($this->started_at && $this->completed_at) {
            $diff = $this->started_at->diff($this->completed_at);
            return $diff->format('%h hours %i minutes');
        }
        return null;
    }

    /**
     * Get priority color for UI.
     */
    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'in-progress' => 'primary',
            'pending' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Scope for pending tasks.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for in-progress tasks.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in-progress');
    }

    /**
     * Scope for completed tasks.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for overdue tasks.
     */
    public function scopeOverdue($query)
    {
        return $query->whereNotNull('due_date')
                     ->where('due_date', '<', now())
                     ->where('status', '!=', 'completed');
    }
}
