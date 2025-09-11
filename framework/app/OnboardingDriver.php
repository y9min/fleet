<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OnboardingDriver extends Model
{
    use HasFactory;

    protected $table = 'onboarding_drivers';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'license_number',
        'license_upload_path',
        'insurance_upload_path',
        'custom_data',
        'status',
        'unique_token'
    ];

    protected $casts = [
        'custom_data' => 'array'
    ];

    // Status constants
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Generate unique token for onboarding link
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->unique_token)) {
                $model->unique_token = Str::random(40);
            }
        });
    }

    // Scope to filter by status
    public function scopeSubmitted($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    // Helper methods
    public function isSubmitted()
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    // Get onboarding link
    public function getOnboardingLinkAttribute()
    {
        return url('/driver-onboarding/' . $this->unique_token);
    }

    // File accessors
    public function getLicenseUrlAttribute()
    {
        return $this->license_upload_path ? asset('uploads/onboarding/' . $this->license_upload_path) : null;
    }

    public function getInsuranceUrlAttribute()
    {
        return $this->insurance_upload_path ? asset('uploads/onboarding/' . $this->insurance_upload_path) : null;
    }
}
