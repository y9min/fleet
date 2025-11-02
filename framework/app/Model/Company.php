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
