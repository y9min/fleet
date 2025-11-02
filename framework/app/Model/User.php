<?php

namespace App\Model;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles, Notifiable, SoftDeletes;
    use HasFactory;
    use HasUuids;
    protected $dates = ['deleted_at'];
    protected $table = "users";
    protected $fillable = [
            'name', 'email', 'password', 'user_type', 'group_id', 'company_id', 'api_token', 'is_active', 'user_id',
    ];

    protected $hidden = ['password', 'remember_token', 'api_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
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
     * Set the is_verified attribute to ensure it's always a boolean for PostgreSQL
     */
    public function setIsVerifiedAttribute($value)
    {
        // Explicitly convert to boolean to ensure PostgreSQL receives proper boolean type
        $this->attributes['is_verified'] = (bool) $value;
    }
    
    /**
     * Perform a model insert operation.
     * Override to ensure boolean values are properly handled for PostgreSQL
     */
    protected function performInsert(\Illuminate\Database\Eloquent\Builder $query)
    {
        // Ensure boolean fields are explicitly cast before insert
        if (isset($this->attributes['is_active'])) {
            $this->attributes['is_active'] = (bool) $this->attributes['is_active'];
        }
        if (isset($this->attributes['is_verified'])) {
            $this->attributes['is_verified'] = (bool) $this->attributes['is_verified'];
        }
        
        return parent::performInsert($query);
    }
    
    /**
     * Perform a model update operation.
     * Override to ensure boolean values are properly handled for PostgreSQL
     */
    protected function performUpdate(\Illuminate\Database\Eloquent\Builder $query)
    {
        // Ensure boolean fields are explicitly cast before update
        if (isset($this->attributes['is_active'])) {
            $this->attributes['is_active'] = (bool) $this->attributes['is_active'];
        }
        if (isset($this->attributes['is_verified'])) {
            $this->attributes['is_verified'] = (bool) $this->attributes['is_verified'];
        }
        
        return parent::performUpdate($query);
    }

    public function user_data() {
            return $this->hasMany("App\Model\UserData", 'user_id', 'id');
    }

    public function bookings() {
            return $this->hasMany('App\Model\Bookings', 'driver_id');
    }

    public function vehicles() {
            return $this->belongsToMany(VehicleModel::class, 'driver_vehicle', 'driver_id', 'vehicle_id')
                    ->using(DriverVehicleModel::class);
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function createApiToken() {
            return $this->api_token = \Illuminate\Support\Str::random(60);
    }

    public function getMeta($key) {
            $meta = UserData::where('user_id', $this->id)->where('key', $key)->first();
            return $meta ? $meta->value : null;
    }

    public function setMeta($attributes) {
            // Accept associative array of key => value
            if (!is_array($attributes)) {
                    return;
            }
            foreach ($attributes as $key => $value) {
                    \App\Model\UserData::updateOrCreate(
                            ['user_id' => $this->id, 'key' => $key],
                            ['value' => $value, 'type' => is_null($value) ? 'string' : gettype($value)]
                    );
            }
    }

    public function updateMeta($key, $value) {
            \App\Model\UserData::updateOrCreate(
                    ['user_id' => $this->id, 'key' => $key],
                    ['value' => $value, 'type' => is_null($value) ? 'string' : gettype($value)]
            );
    }

    public function meta() {
            // Kept for backward compatibility; points to users_meta
            return $this->hasMany(UserData::class, 'user_id', 'id');
    }

    public function metas() {
            return $this->hasMany(UserData::class, 'user_id', 'id');
    }

    public function getUserTypeLabelAttribute() {
			$code = $this->getRawOriginal('user_type');
			switch ($code) {
					case 'B':
							return 'Boss Admin';
					case 'S':
							return 'Super Admin';
					case 'O':
							return 'Office Admin';
					case 'D':
							return 'Driver';
					case 'C':
							return 'Customer';
					default:
							return $code;
			}
	}

	// Helper methods to access metadata fields as attributes
	public function getFirstNameAttribute() {
		return $this->getMeta('first_name');
	}

	public function getMiddleNameAttribute() {
		return $this->getMeta('middle_name');
	}

	public function getLastNameAttribute() {
		return $this->getMeta('last_name');
	}

	public function getAddressAttribute() {
		return $this->getMeta('address');
	}

	public function getPhoneAttribute() {
		return $this->getMeta('phone');
	}

	public function getGenderAttribute() {
		return $this->getMeta('gender');
	}
}