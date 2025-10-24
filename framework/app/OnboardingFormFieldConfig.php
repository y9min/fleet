<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingFormFieldConfig extends Model
{
    use HasFactory;

    protected $table = 'onboarding_form_field_configs';

    protected $fillable = [
        'field_key',
        'field_label',
        'field_type',
        'is_visible',
        'is_required',
        'sort_order'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_required' => 'boolean'
    ];

    // Field type constants
    const TYPE_TEXT = 'text';
    const TYPE_EMAIL = 'email';
    const TYPE_PHONE = 'phone';
    const TYPE_FILE = 'file';
    const TYPE_VEHICLE_SELECT = 'vehicle_select';
    const TYPE_SCHEME_SELECT = 'scheme_select';

    // Available field types
    public static function getFieldTypes()
    {
        return [
            self::TYPE_TEXT => 'Text Input',
            self::TYPE_EMAIL => 'Email Input',
            self::TYPE_PHONE => 'Phone Input',
            self::TYPE_FILE => 'File Upload',
            self::TYPE_VEHICLE_SELECT => 'Vehicle Selection',
            self::TYPE_SCHEME_SELECT => 'Scheme Selection'
        ];
    }

    // Scope to get visible fields
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    // Scope to get required fields
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    // Scope to get fields in order
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    // Helper methods
    public function isVisible()
    {
        return $this->is_visible;
    }

    public function isRequired()
    {
        return $this->is_required;
    }

    public function isFileUpload()
    {
        return $this->field_type === self::TYPE_FILE;
    }

    public function isVehicleSelect()
    {
        return $this->field_type === self::TYPE_VEHICLE_SELECT;
    }

    public function isSchemeSelect()
    {
        return $this->field_type === self::TYPE_SCHEME_SELECT;
    }

    // Get default field configurations
    public static function getDefaultFields()
    {
        return [
            [
                'field_key' => 'full_name',
                'field_label' => 'Full Name',
                'field_type' => self::TYPE_TEXT,
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'field_key' => 'email',
                'field_label' => 'Email Address',
                'field_type' => self::TYPE_EMAIL,
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'field_key' => 'phone',
                'field_label' => 'Phone Number',
                'field_type' => self::TYPE_PHONE,
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 3
            ],
            [
                'field_key' => 'license_number',
                'field_label' => 'Driver\'s License Number',
                'field_type' => self::TYPE_TEXT,
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 4
            ],
            [
                'field_key' => 'license_file',
                'field_label' => 'Driver\'s License Upload',
                'field_type' => self::TYPE_FILE,
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 5
            ],
            [
                'field_key' => 'insurance_file',
                'field_label' => 'Insurance Document Upload',
                'field_type' => self::TYPE_FILE,
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 6
            ],
            [
                'field_key' => 'vehicle_selection',
                'field_label' => 'Vehicle Selection',
                'field_type' => self::TYPE_VEHICLE_SELECT,
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 7
            ],
            [
                'field_key' => 'scheme_selection',
                'field_label' => 'Scheme Selection',
                'field_type' => self::TYPE_SCHEME_SELECT,
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 8
            ]
        ];
    }

    // Initialize default fields if none exist
    public static function initializeDefaultFields()
    {
        $defaultFields = self::getDefaultFields();
        
        foreach ($defaultFields as $field) {
            // Check if this specific field exists
            $exists = self::where('field_key', $field['field_key'])->exists();
            
            if (!$exists) {
                self::create($field);
            }
        }
    }
}
