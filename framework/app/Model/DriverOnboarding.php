
<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DriverOnboarding extends Model
{
    protected $table = 'driver_onboarding';
    
    protected $fillable = [
        'onboarding_id', 'name', 'email', 'phone', 'license_number',
        'drivers_license_file', 'pco_license_file', 'insurance_file',
        'custom_fields', 'status', 'unique_link', 'submitted_at'
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'submitted_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->onboarding_id)) {
                $model->onboarding_id = 'ONB-' . strtoupper(Str::random(8));
            }
            if (empty($model->unique_link)) {
                $model->unique_link = Str::random(32);
            }
        });
    }

    public function getDocumentsAttribute()
    {
        $docs = [];
        if ($this->drivers_license_file) $docs[] = 'Drivers License';
        if ($this->pco_license_file) $docs[] = 'PCO License';
        if ($this->insurance_file) $docs[] = 'Insurance';
        return $docs;
    }
}
