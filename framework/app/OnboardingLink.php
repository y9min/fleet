<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingLink extends Model
{
    use HasFactory;

    protected $table = 'onboarding_links';

    protected $fillable = [
        'token',
        'link',
        'is_active',
        'usage_count',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationship with User
    public function createdBy()
    {
        return $this->belongsTo(\App\Model\User::class, 'created_by');
    }

    // Scope for active links
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Increment usage count
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}
