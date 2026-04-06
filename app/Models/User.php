<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory,
        Notifiable,
        // HasApiTokens,
        HasRoles,
        SoftDeletes;


    protected $fillable = [
        'avatar',
        'name',
        'phone',
        'email',
        'email_verified_at',
        'password',
        'otp',
        'otp_created_at',
        'otp_expires_at',
        'reset_token',
        'role',
        'status',
        'tenant_id',
        'store_id',
        'name',
        'email',
        'password',
        'avatar',
        'pin',
        'is_active',
        'is_super_admin',
        'locale',
        'timezone',
        'provider',
        'provider_id',
        'provider_refresh_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'pin',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_created_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'privacy_policy' => 'boolean',
            'is_active'         => 'boolean',
            'is_super_admin'    => 'boolean',
        ];
    }

    public function ownerNotifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    // restaurant relationship

    public function currentMembershipPlan()
    {
        return $this->activeAgentSubscription?->membershipPlan;
    }

    public function getCurrentMembershipPlanAttribute()
    {
        return $this->activeAgentSubscription?->membershipPlan;
    }

    /**
     * Get current plan name (accessor)
     */
    public function getCurrentPlanNameAttribute()
    {
        return $this->current_membership_plan?->name ?? 'No Plan';
    }

    public function scopeAgents($query)
    {
        return $query->where('role', 'agent');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isAgent()
    {
        return $this->role === 'agent';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAvatarAttribute($value): string|null
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (request()->is('api/*') && !empty($value)) {
            return url($value);
        }
        return $value;
    }

    public function getDocumentAttribute($value): string|null
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (request()->is('api/*') && !empty($value)) {
            return url($value);
        }
        return $value;
    }


    // Methods for onboarding status
    public function getOnboardingStep(): int
    {
        return $this->onboardingProgress?->getCurrentStep() ?? 0;
    }

    public function isOnboardingComplete(): bool
    {
        return $this->onboarding_complete ?? false;
    }

    public function getOnboardingPercentage(): int
    {
        return (int) ($this->onboardingProgress?->completion_percentage ?? 0);
    }

    public function hasW9(): bool
    {
        return $this->w9_completed ?? false;
    }

    public function hasPhotoId(): bool
    {
        return $this->photo_id_uploaded ?? false;
    }

    public function hasMLSAccess(): bool
    {
        return $this->mls_membership_option !== null;
    }

    public function getPhotoIdPathAttribute($value): string|null
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (request()->is('api/*') && !empty($value)) {
            return url($value);
        }
        return $value;
    }

    public function getDocumentPathAttribute($value): string|null
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (request()->is('api/*') && !empty($value)) {
            return url($value);
        }
        return $value;
    }
}
