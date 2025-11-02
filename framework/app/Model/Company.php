<?php

namespace App\Model;

use App\Model\BaseUuidModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends BaseUuidModel
{
    use SoftDeletes;
    
    protected $table = 'companies';
    protected $fillable = [
        'name',
        'description',
        'email',
        'phone',
        'address',
        'is_active',
        'stripe_customer_id',
        'stripe_subscription_id',
        'stripe_subscription_item_id',
        'subscription_status'
    ];
    
    protected $dates = ['deleted_at'];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    /**
     * Set the is_active attribute to ensure it's always a boolean for PostgreSQL
     */
    public function setIsActiveAttribute($value)
    {
        // Explicitly convert to boolean to ensure PostgreSQL receives proper boolean type
        $this->attributes['is_active'] = (bool) $value;
    }
    
    /**
     * Perform a model insert operation.
     * Override to ensure boolean values are properly handled for PostgreSQL
     */
    protected function performInsert(\Illuminate\Database\Eloquent\Builder $query)
    {
        // Ensure is_active is explicitly cast to boolean before insert
        if (isset($this->attributes['is_active'])) {
            $this->attributes['is_active'] = (bool) $this->attributes['is_active'];
        }
        
        return parent::performInsert($query);
    }
    
    /**
     * Perform a model update operation.
     * Override to ensure boolean values are properly handled for PostgreSQL
     */
    protected function performUpdate(\Illuminate\Database\Eloquent\Builder $query)
    {
        // Ensure is_active is explicitly cast to boolean before update
        if (isset($this->attributes['is_active'])) {
            $this->attributes['is_active'] = (bool) $this->attributes['is_active'];
        }
        
        return parent::performUpdate($query);
    }
    
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function vehicles()
    {
        return $this->hasMany(VehicleModel::class);
    }
    
    public function bookings()
    {
        return $this->hasMany(Bookings::class);
    }
}
