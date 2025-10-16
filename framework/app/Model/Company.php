<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;
    
    protected $table = 'companies';
    protected $fillable = [
        'name',
        'description',
        'email',
        'phone',
        'address',
        'is_active'
    ];
    
    protected $dates = ['deleted_at'];
    
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
