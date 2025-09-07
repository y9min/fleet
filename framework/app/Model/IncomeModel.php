<?php

/*
@copyright

Fleet Manager v7.1.2

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomeModel extends Model {
        use SoftDeletes;
        protected $dates = ['deleted_at'];
        protected $fillable = [
                'vehicle_id', 'user_id', 'amount', 'driver_amount', 'income_cat', 'mileage', 'income_date', 'income_id', 'tax_percent', 'tax_charge_rs',
        ];

        // Map 'date' attribute to 'income_date' column
        public function getDateAttribute()
        {
                return $this->income_date;
        }

        public function setDateAttribute($value)
        {
                $this->attributes['income_date'] = $value;
        }
        protected $table = "income";

        public function category() {
                return $this->hasOne("App\Model\IncCats", "id", "income_cat")->withTrashed();
        }

        public function vehicle() {
                return $this->hasOne("App\Model\VehicleModel", "id", "vehicle_id")->withTrashed();
        }
}
