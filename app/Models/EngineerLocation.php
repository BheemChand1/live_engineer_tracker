<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngineerLocation extends Model
{
    protected $fillable = [
        'engineer_id',
        'latitude',
        'longitude',
        'recorded_at'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'recorded_at' => 'datetime',
    ];

    /**
     * Get the engineer that owns the location.
     */
    public function engineer(): BelongsTo
    {
        return $this->belongsTo(Engineer::class);
    }

    /**
     * Get the location as a formatted string.
     */
    public function getLocationString(): string
    {
        return "{$this->latitude}, {$this->longitude}";
    }

    /**
     * Scope for recent locations.
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('recorded_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope for today's locations.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('recorded_at', today());
    }
}
