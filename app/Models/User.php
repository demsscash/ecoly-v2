<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
        'login_attempts',
        'locked_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'login_attempts' => 'integer',
            'locked_until' => 'datetime',
        ];
    }

    /**
     * Get full name.
     */
    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Check if user is secretary.
     */
    public function isSecretary(): bool
    {
        return $this->role === UserRole::Secretary;
    }

    /**
     * Check if user is teacher.
     */
    public function isTeacher(): bool
    {
        return $this->role === UserRole::Teacher;
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        
        return in_array($this->role->value, $roles);
    }

    /**
     * Check if the account is locked.
     */
    public function isLocked(): bool
    {
        if (!$this->locked_until) {
            return false;
        }

        if ($this->locked_until->isPast()) {
            $this->update([
                'locked_until' => null,
                'login_attempts' => 0,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Get remaining lock minutes.
     */
    public function lockMinutesRemaining(): int
    {
        if (!$this->locked_until) {
            return 0;
        }

        return (int) now()->diffInMinutes($this->locked_until, false);
    }

    /**
     * Increment login attempts.
     */
    public function incrementLoginAttempts(): void
    {
        $attempts = $this->login_attempts + 1;

        $data = ['login_attempts' => $attempts];

        // Lock after 5 failed attempts
        if ($attempts >= 5) {
            $data['locked_until'] = now()->addMinutes(15);
        }

        $this->update($data);
    }

    /**
     * Reset login attempts.
     */
    public function resetLoginAttempts(): void
    {
        $this->update([
            'login_attempts' => 0,
            'locked_until' => null,
        ]);
    }
}
