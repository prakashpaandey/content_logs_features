<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
        'status',
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
     * Get the clients for the user.
     */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Get the contents for the user.
     */
    public function contents()
    {
        return $this->hasMany(Content::class);
    }

    /**
     * Get the monthly targets for the user.
     */
    public function monthlyTargets()
    {
        return $this->hasMany(MonthlyTarget::class);
    }

    /**
     * Get the permissions for the user.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is pending approval.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if user is deactivated.
     */
    public function isDeactivated()
    {
        return $this->status === 'deactivated';
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission($permissionSlug)
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }
}
