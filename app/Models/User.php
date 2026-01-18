<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Notifications\PasswordResetNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'login_attempts',
        'locked_until',
    ];

    /**
     * Attributes that are not mass assignable.
     * Sensitive fields like role and is_active must be assigned explicitly.
     */
    protected $guarded = ['id', 'role', 'is_active'];

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

    /**
     * Generate a secure random password.
     * Returns both plain text (for sending to user) and hashed (for storage).
     */
    public static function generateSecurePassword(int $length = 12): array
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $digits = '0123456789';
        $special = '!@#$%^&*';

        $all = $uppercase . $lowercase . $digits . $special;

        // Ensure at least one character from each category
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $digits[random_int(0, strlen($digits) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        // Fill the rest randomly
        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        // Shuffle the password
        $password = str_shuffle($password);

        return [
            'plain' => $password,
            'hashed' => bcrypt($password),
        ];
    }

    /**
     * Reset password and send notification to user.
     * Returns the plain text password (for admin display if needed).
     */
    public function resetPasswordSecurely(): string
    {
        $passwordData = self::generateSecurePassword();

        $this->update([
            'password' => $passwordData['hashed'],
            'login_attempts' => 0,
            'locked_until' => null,
        ]);

        // Send notification to user
        $this->notify(new PasswordResetNotification($passwordData['plain']));

        return $passwordData['plain'];
    }
}
