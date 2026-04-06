<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles;

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
        'license_number',
        'license_type',
        'license_status',
        'current_brokerage',
        'mls_membership_option',
        'gamls_access',
        'nar_number',
        'electronic_signature',
        'board_association',
        'ica_signed',
        'policies_accepted',
        'w9_completed',
        'photo_id_uploaded',
        'tax',
        'ssn_ein',
        'city',
        'zip_code',
        'document',
        'privacy_policy',
        'is_admin',
        'stripe_account_id',
        'stripe_customer_id',
        'stripe_subscription_id',
        'payment_method',
        'onboarding_started_at',
        'onboarding_completed_at',
        'current_onboarding_step',
        'onboard_complete',
        'payment_method_id',
        'is_card',
        'is_bank',
        'auto_purchase',
        'docusign_access_token',
        'docusign_refresh_token',
        'docusign_token_expires_at',
        'docusign_account_id',
        'docusign_base_uri',
        'provider',
        'provider_id',
        'provider_refresh_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_created_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'gamls_access' => 'boolean',
            'privacy_policy' => 'boolean',
            'onboard_complete' => 'boolean',
            'auto_purchase' => 'boolean',
            'trial_ends_at' => 'datetime',
            'docusign_access_token' => 'string',
            'docusign_refresh_token' => 'string',
            'docusign_token_expires_at' => 'datetime',
            'stripe_account_id' => 'string',
            'stripe_customer_id' => 'string',
            'payment_method_id' => 'string',
            'is_card' => 'boolean',
            'is_bank' => 'boolean',
        ];
    }

    public function ownerNotifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    // restaurant relationship

    public function tables()
    {
        return $this->belongsToMany(
            RestaurantTable::class,
            // 'staff_table',
            // 'staff_id',
            // 'table_id'
        );
    }

    public function assignedTables()
    {
        return $this->belongsToMany(
            RestaurantTable::class,
            'staff_table_assignments',
            'user_id',
            'restaurant_table_id'
        )
            ->withPivot('notes', 'shift_duration')
            ->withTimestamps();
    }
    public function staffShifts(): HasMany
    {
        return $this->hasMany(StaffShift::class, 'user_id');
    }

    public function membershipPlan()
    {
        $activeSub = $this->activeAgentSubscription;
        if ($activeSub && $activeSub->membershipPlan) {
            return $activeSub->membershipPlan;
        }

        return $this->belongsTo(MembershipPlan::class);
    }

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
