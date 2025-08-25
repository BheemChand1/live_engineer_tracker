<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Engineer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'skills',
        'status',
        'user_id'
    ];

    protected $casts = [
        'status' => 'string',
        'skills' => 'array',
    ];

    /**
     * Get the user associated with the engineer.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tasks assigned to the engineer.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'engineer_id', 'user_id');
    }

    /**
     * Get the tasks assigned to the engineer (alias for tasks).
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'engineer_id', 'user_id');
    }

    /**
     * Get the logs for the engineer.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(EngineerLog::class);
    }

    /**
     * Get the locations for the engineer.
     */
    public function locations(): HasMany
    {
        return $this->hasMany(EngineerLocation::class);
    }

    /**
     * Get the latest location for the engineer.
     */
    public function latestLocation()
    {
        return $this->hasOne(EngineerLocation::class)->latestOfMany('recorded_at');
    }

    /**
     * Check if the engineer is currently active (logged in).
     */
    public function isActiveToday(): bool
    {
        return $this->logs()
            ->whereDate('login_at', today())
            ->whereNull('logout_at')
            ->exists();
    }

    /**
     * Get today's work hours for the engineer.
     */
    public function getTodayWorkHours(): int
    {
        $todayLogs = $this->logs()
            ->whereDate('login_at', today())
            ->get();

        $totalMinutes = 0;
        foreach ($todayLogs as $log) {
            $logout = $log->logout_at ?: now();
            $totalMinutes += $log->login_at->diffInMinutes($logout);
        }

        return round($totalMinutes / 60, 1);
    }
}
