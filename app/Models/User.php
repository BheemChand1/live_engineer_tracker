<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the engineer profile associated with the user.
     */
    public function engineer()
    {
        return $this->hasOne(Engineer::class);
    }

    /**
     * Get the tasks assigned to this user (engineer).
     */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'engineer_id');
    }

    /**
     * Get the current location of the user.
     */
    public function currentLocation()
    {
        return $this->hasOne(EngineerLocation::class)->latest();
    }

    /**
     * Get all location history for the user.
     */
    public function locations()
    {
        return $this->hasMany(EngineerLocation::class);
    }

    /**
     * Get the logs for this user.
     */
    public function logs()
    {
        return $this->hasMany(EngineerLog::class);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is an engineer.
     */
    public function isEngineer(): bool
    {
        return $this->role === 'engineer';
    }
}
