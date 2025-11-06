<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OnboardingFormFieldConfig extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'onboarding_form_field_configs';
    
    protected $keyType = 'string';
    public $incrementing = false;

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
        return $query->whereRaw('is_visible IS TRUE');
    }

    // Scope to get required fields
    public function scopeRequired($query)
    {
        return $query->whereRaw('is_required IS TRUE');
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
                'field_key' => 'license_expiry',
                'field_label' => 'License Expiry Date',
                'field_type' => self::TYPE_TEXT,
                'is_visible' => false,
                'is_required' => false,
                'sort_order' => 7
            ],
            [
                'field_key' => 'address',
                'field_label' => 'Address',
                'field_type' => self::TYPE_TEXT,
                'is_visible' => false,
                'is_required' => false,
                'sort_order' => 8
            ],
            [
                'field_key' => 'emergency_contact',
                'field_label' => 'Emergency Contact',
                'field_type' => self::TYPE_TEXT,
                'is_visible' => false,
                'is_required' => false,
                'sort_order' => 9
            ],
            [
                'field_key' => 'emergency_phone',
                'field_label' => 'Emergency Phone',
                'field_type' => self::TYPE_PHONE,
                'is_visible' => false,
                'is_required' => false,
                'sort_order' => 10
            ],
            [
                'field_key' => 'vehicle_selection',
                'field_label' => 'Vehicle Selection',
                'field_type' => self::TYPE_VEHICLE_SELECT,
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 11
            ],
            [
                'field_key' => 'scheme_selection',
                'field_label' => 'Scheme Selection',
                'field_type' => self::TYPE_SCHEME_SELECT,
                'is_visible' => true,
                'is_required' => true,
                'sort_order' => 12
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

    /**
     * Ensure is_visible is always stored as a true boolean for PostgreSQL
     */
    public function setIsVisibleAttribute($value)
    {
        $this->attributes['is_visible'] = (bool) $value;
    }

    /**
     * Ensure is_required is always stored as a true boolean for PostgreSQL
     */
    public function setIsRequiredAttribute($value)
    {
        $this->attributes['is_required'] = (bool) $value;
    }

    /**
     * Force boolean casting on insert for PostgreSQL compatibility
     * Use DB::raw() to bypass PDO integer conversion
     */
    protected function performInsert(\Illuminate\Database\Eloquent\Builder $query)
    {
        // Ensure boolean values are properly cast for PostgreSQL
        if (array_key_exists('is_visible', $this->attributes)) {
            $boolValue = $this->attributes['is_visible'] ? true : false;
            $this->attributes['is_visible'] = \DB::raw($boolValue ? 'TRUE' : 'FALSE');
        }
        if (array_key_exists('is_required', $this->attributes)) {
            $boolValue = $this->attributes['is_required'] ? true : false;
            $this->attributes['is_required'] = \DB::raw($boolValue ? 'TRUE' : 'FALSE');
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
        if (array_key_exists('is_visible', $this->attributes)) {
            $boolValue = $this->attributes['is_visible'] ? true : false;
            $this->attributes['is_visible'] = \DB::raw($boolValue ? 'TRUE' : 'FALSE');
        }
        if (array_key_exists('is_required', $this->attributes)) {
            $boolValue = $this->attributes['is_required'] ? true : false;
            $this->attributes['is_required'] = \DB::raw($boolValue ? 'TRUE' : 'FALSE');
        }
        return parent::performUpdate($query);
    }
}
