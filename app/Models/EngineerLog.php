<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngineerLog extends Model
{
    protected $fillable = [
        'engineer_id',
        'login_at',
        'logout_at'
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    /**
     * Get the engineer that owns the log.
     */
    public function engineer(): BelongsTo
    {
        return $this->belongsTo(Engineer::class);
    }

    /**
     * Get the duration of the session.
     */
    public function getDuration(): ?string
    {
        if ($this->logout_at) {
            $diff = $this->login_at->diff($this->logout_at);
            return $diff->format('%h hours %i minutes');
        }
        return 'Active';
    }

    /**
     * Get the work duration in minutes.
     */
    public function getDurationInMinutes(): int
    {
        $logout = $this->logout_at ?: now();
        return $this->login_at->diffInMinutes($logout);
    }

    /**
     * Scope for today's logs.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('login_at', today());
    }

    /**
     * Scope for active sessions.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('logout_at');
    }
}
