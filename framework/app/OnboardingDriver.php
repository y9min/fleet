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
        'vehicle_id',
        'scheme',
        'insurance_selection',
        'custom_data',
        'form_data',
        'status',
        'unique_token',
        'license_expiry',
        'address',
        'emergency_contact',
        'emergency_phone',
        'company_id' // Added to link submissions to company
    ];

    protected $casts = [
        'custom_data' => 'array',
        'form_data' => 'array'
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

    // Relationship with vehicle
    public function vehicle()
    {
        return $this->belongsTo(\App\Model\VehicleModel::class, 'vehicle_id');
    }
    
    /**
     * Override newQuery to handle the case where database uses BIGINT but model expects UUID
     */
    public function newEloquentBuilder($query)
    {
        return new \Illuminate\Database\Eloquent\Builder($query);
    }
    
    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'id';
    }

    // Get onboarding link
    public function getOnboardingLinkAttribute()
    {
        return url('/driver-onboarding/' . $this->unique_token);
    }

    // File accessors
    public function getLicenseUrlAttribute()
    {
        if (!$this->license_upload_path) {
            return null;
        }
        
        // Check if S3 is configured, otherwise use local storage
        $useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');
        
        if ($useS3) {
            $s3BaseUrl = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_REGION') . '.amazonaws.com/';
            // Handle both old and new path formats
            if (strpos($this->license_upload_path, 'onboarding/documents/') === 0) {
                // Old format: onboarding/documents/filename
                return $s3BaseUrl . $this->license_upload_path;
            } else {
                // New format: filename (stored in uploads/onboarding/)
                return $s3BaseUrl . 'uploads/onboarding/' . $this->license_upload_path;
            }
        } else {
            // Local storage - handle both old and new formats
            if (strpos($this->license_upload_path, 'onboarding/documents/') === 0) {
                // Old format: onboarding/documents/filename
                return asset('storage/' . $this->license_upload_path);
            } else {
                // New format: filename (stored in uploads/onboarding/)
                return asset('uploads/onboarding/' . $this->license_upload_path);
            }
        }
    }

    public function getInsuranceUrlAttribute()
    {
        if (!$this->insurance_upload_path) {
            return null;
        }
        
        // Check if S3 is configured, otherwise use local storage
        $useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');
        
        if ($useS3) {
            $s3BaseUrl = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_REGION') . '.amazonaws.com/';
            // Handle both old and new path formats
            if (strpos($this->insurance_upload_path, 'onboarding/documents/') === 0) {
                // Old format: onboarding/documents/filename
                return $s3BaseUrl . $this->insurance_upload_path;
            } else {
                // New format: filename (stored in uploads/onboarding/)
                return $s3BaseUrl . 'uploads/onboarding/' . $this->insurance_upload_path;
            }
        } else {
            // Local storage - handle both old and new formats
            if (strpos($this->insurance_upload_path, 'onboarding/documents/') === 0) {
                // Old format: onboarding/documents/filename
                return asset('storage/' . $this->insurance_upload_path);
            } else {
                // New format: filename (stored in uploads/onboarding/)
                return asset('uploads/onboarding/' . $this->insurance_upload_path);
            }
        }
    }

}
