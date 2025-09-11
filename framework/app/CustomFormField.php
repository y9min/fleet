<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFormField extends Model
{
    use HasFactory;

    protected $table = 'custom_form_fields';

    protected $fillable = [
        'field_name',
        'field_type',
        'field_options',
        'is_required',
        'sort_order'
    ];

    protected $casts = [
        'field_options' => 'array',
        'is_required' => 'boolean'
    ];

    // Field type constants
    const TYPE_TEXT = 'text';
    const TYPE_EMAIL = 'email';
    const TYPE_PHONE = 'phone';
    const TYPE_DROPDOWN = 'dropdown';
    const TYPE_DATE = 'date';
    const TYPE_FILE = 'file';
    const TYPE_TEXTAREA = 'textarea';

    // Available field types
    public static function getFieldTypes()
    {
        return [
            self::TYPE_TEXT => 'Text Input',
            self::TYPE_EMAIL => 'Email Input',
            self::TYPE_PHONE => 'Phone Input',
            self::TYPE_DROPDOWN => 'Dropdown Select',
            self::TYPE_DATE => 'Date Picker',
            self::TYPE_FILE => 'File Upload',
            self::TYPE_TEXTAREA => 'Text Area'
        ];
    }

    // Scope to get fields in order
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    // Scope to get required fields
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    // Helper methods
    public function isRequired()
    {
        return $this->is_required;
    }

    public function isDropdown()
    {
        return $this->field_type === self::TYPE_DROPDOWN;
    }

    public function isFileUpload()
    {
        return $this->field_type === self::TYPE_FILE;
    }

    // Get dropdown options
    public function getDropdownOptions()
    {
        if ($this->isDropdown() && isset($this->field_options['options'])) {
            return $this->field_options['options'];
        }
        return [];
    }

    // Get validation rules
    public function getValidationRules()
    {
        $rules = [];
        
        if ($this->is_required) {
            $rules[] = 'required';
        }

        switch ($this->field_type) {
            case self::TYPE_EMAIL:
                $rules[] = 'email';
                break;
            case self::TYPE_PHONE:
                $rules[] = 'regex:/^[0-9+\-\s\(\)]+$/';
                break;
            case self::TYPE_DATE:
                $rules[] = 'date';
                break;
            case self::TYPE_FILE:
                $rules[] = 'file';
                if (isset($this->field_options['max_size'])) {
                    $rules[] = 'max:' . $this->field_options['max_size'];
                }
                if (isset($this->field_options['allowed_types'])) {
                    $rules[] = 'mimes:' . implode(',', $this->field_options['allowed_types']);
                }
                break;
        }

        return implode('|', $rules);
    }
}
