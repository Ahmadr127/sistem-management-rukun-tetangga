<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'nik',
        'username',
        'email',
        'password',
        'role_id',
        'rt_id',
        'organization_unit_id',
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
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->role->name === $role;
        }
        return $this->role->id === $role->id;
    }

    public function hasPermission($permission): bool
    {
        return $this->role->hasPermission($permission);
    }

    /**
     * @return BelongsTo<OrganizationUnit, $this>
     */
    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }

    public function isSuperAdmin(): bool
    {
        // Superadmin = rt_id null AND has permission to manage RT OR role admin/superadmin
        if (is_null($this->rt_id)) {
            // Check via role name for backwards compat
            $roleName = $this->role?->name;
            if (in_array($roleName, ['admin', 'superadmin', 'super_admin'])) {
                return true;
            }
            // Also treat null rt_id with high privilege permission as superadmin
            if ($this->hasPermission('manage_rt') || $this->hasPermission('manage_users')) {
                return true;
            }
        }
        return false;
    }

    public function isRtUser(): bool
    {
        return !is_null($this->rt_id);
    }
}
