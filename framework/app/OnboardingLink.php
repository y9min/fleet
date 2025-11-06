<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OnboardingLink extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'onboarding_links';
    
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'company_id',
        'token',
        'link',
        'expires_at',
        'is_used',
        'used_at',
        'is_active',
        'usage_count',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_used' => 'boolean',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    // Relationship with User
    public function createdBy()
    {
        return $this->belongsTo(\App\Model\User::class, 'created_by');
    }

    // Relationship with Company
    public function company()
    {
        return $this->belongsTo(\App\Model\Company::class, 'company_id');
    }

    // Scope for active links
    public function scopeActive($query)
    {
        // Use raw SQL with explicit boolean comparison for PostgreSQL compatibility
        return $query->whereRaw('is_active IS TRUE');
    }

    // Scope for non-expired links
    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    // Scope for unused links
    public function scopeUnused($query)
    {
        return $query->where('is_used', false);
    }

    // Increment usage count
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    // Check if link is expired
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // Mark link as used
    public function markAsUsed()
    {
        $this->update([
            'is_used' => true,
            'used_at' => now()
        ]);
    }

    /**
     * Ensure is_active is always stored as a true boolean for PostgreSQL
     */
    public function setIsActiveAttribute($value)
    {
        $this->attributes['is_active'] = (bool) $value;
    }

    /**
     * Ensure is_used is always stored as a true boolean for PostgreSQL
     */
    public function setIsUsedAttribute($value)
    {
        $this->attributes['is_used'] = (bool) $value;
    }

    /**
     * Force boolean casting on insert for PostgreSQL compatibility
     * Use DB::raw() to bypass PDO integer conversion
     */
    protected function performInsert(\Illuminate\Database\Eloquent\Builder $query)
    {
        // Ensure boolean values are properly cast for PostgreSQL
        if (array_key_exists('is_active', $this->attributes)) {
            $boolValue = $this->attributes['is_active'] ? true : false;
            $this->attributes['is_active'] = \DB::raw($boolValue ? 'TRUE' : 'FALSE');
        }
        if (array_key_exists('is_used', $this->attributes)) {
            $boolValue = $this->attributes['is_used'] ? true : false;
            $this->attributes['is_used'] = \DB::raw($boolValue ? 'TRUE' : 'FALSE');
        }
        return parent::performInsert($query);
    }

    /**
     * Force boolean casting on update for PostgreSQL compatibility
     * Use DB::raw() to bypass PDO integer conversion
     */
    protected function performUpdate(\Illuminate\Database\Eloquent\Builder $query)
    {
        // Ensure boolean values are properly cast for PostgreSQL
        if (array_key_exists('is_active', $this->attributes)) {
            $boolValue = $this->attributes['is_active'] ? true : false;
            $this->attributes['is_active'] = \DB::raw($boolValue ? 'TRUE' : 'FALSE');
        }
        if (array_key_exists('is_used', $this->attributes)) {
            $boolValue = $this->attributes['is_used'] ? true : false;
            $this->attributes['is_used'] = \DB::raw($boolValue ? 'TRUE' : 'FALSE');
        }
        return parent::performUpdate($query);
    }
}
