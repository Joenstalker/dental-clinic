<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail 
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'number',
        'address',
        'dob',
        'userRole',
        'password',
        'status',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dob' => 'date',
        'password' => 'hashed',
    ];

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->userRole === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->userRole, $roles);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->userRole === 'admin';
    }

    /**
     * Check if user is patient
     */
    public function isPatient(): bool
    {
        return $this->userRole === 'patient';
    }

    /**
     * Check if user is dentist
     */
    public function isDentist(): bool
    {
        return $this->userRole === 'dentist';
    }

    /**
     * Check if user is assistant
     */
    public function isAssistant(): bool
    {
        return $this->userRole === 'assistant';
    }

    /**
     * Check if user can manage users (admin or assistant)
     */
    public function canManageUsers(): bool
    {
        return $this->hasAnyRole(['admin', 'assistant']);
    }

    /**
     * Check if user owns a resource
     */
    public function owns($resource): bool
    {
        if (!$resource) {
            return false;
        }

        // Check if resource has user_id
        if (isset($resource->user_id)) {
            return $this->id === $resource->user_id;
        }

        return false;
    }

    /**
     * Get the profile picture URL
     */
    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture) {
            return asset('storage/profile_pictures/' . $this->profile_picture);
        }
        
        // Default avatar based on role
        return asset('img/user.png');
    }

    /**
     * Get profile picture or default
     */
    public function getProfilePicture(): string
    {
        return $this->profile_picture_url;
    }
}
