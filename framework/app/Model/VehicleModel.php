<?php

/*
@copyright

Fleet Manager v7.1.2

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Model;

use App\Model\DriverVehicleModel;
use App\Model\User;
use App\Model\BaseUuidModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kodeine\Metable\Metable;

class VehicleModel extends BaseUuidModel {
	use Metable;
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = "vehicles";
	protected $metaTable = 'vehicles_meta'; //optional.
	protected $fillable = ['model_name', 'make_name', 'color_name', 'type', 'year', 'engine_type', 'horse_power', 'vin', 'license_plate', 'mileage', 'int_mileage', 'in_service', 'user_id', 'insurance_number', 'documents', 'vehicle_image', 'exp_date', 'reg_exp_date', 'lic_exp_date', 'group_id', 'company_id', 'type_id', 'height', 'length', 'breadth', 'weight'];
	protected $casts = [
		'in_service' => 'boolean',
	];
	
	protected $appends = ['vehicle_status'];

	/**
	 * Ensure in_service is always stored as a true boolean for PostgreSQL
	 */
	public function setInServiceAttribute($value) {
		$this->attributes['in_service'] = (bool) $value;
	}

	/**
	 * Force boolean casting on insert for PostgreSQL compatibility
	 * Use DB::raw() to bypass PDO integer conversion
	 */
	protected function performInsert(\Illuminate\Database\Eloquent\Builder $query) {
		if (array_key_exists('in_service', $this->attributes)) {
			// Get the boolean value
			$boolValue = $this->attributes['in_service'] ? true : false;
			// Use DB::raw() to force PostgreSQL to receive TRUE/FALSE instead of 1/0
			// This bypasses PDO's automatic conversion of booleans to integers
			$this->attributes['in_service'] = \DB::raw($boolValue ? 'TRUE' : 'FALSE');
		}
		return parent::performInsert($query);
	}

	/**
	 * Force boolean casting on update for PostgreSQL compatibility
	 * Use DB::raw() to bypass PDO integer conversion
	 */
	protected function performUpdate(\Illuminate\Database\Eloquent\Builder $query) {
		if (array_key_exists('in_service', $this->attributes)) {
			// Get the boolean value
			$boolValue = $this->attributes['in_service'] ? true : false;
			// Use DB::raw() to force PostgreSQL to receive TRUE/FALSE instead of 1/0
			$this->attributes['in_service'] = \DB::raw($boolValue ? 'TRUE' : 'FALSE');
		}
		return parent::performUpdate($query);
	}

	protected function getMetaKeyName() {
		return 'vehicle_id'; // The parent foreign key
	}
	
	public function getVehicleStatusAttribute() {
		return $this->getMeta('vehicle_status') ?: 'Available';
	}

	public function driver() {
		return $this->hasOne("App\Model\DriverVehicleModel", "vehicle_id", "id");
	}

	public function drivers() {
		return $this->belongsToMany(User::class, 'driver_vehicle', 'vehicle_id', 'driver_id')->using(DriverVehicleModel::class);
	}

	public function income() {
		return $this->hasMany("App\Model\IncomeModel", "vehicle_id", "id")->withTrashed();
	}
	public function expense() {
		return $this->hasMany("App\Model\Expense", "vehicle_id", "id")->withTrashed();
	}

	// public function insurance() {
	// 	return $this->hasOne("App\Model\InsuranceModel", "vehicle_id", "id")->withTrashed();
	// }

	public function acq() {
		return $this->hasMany("App\Model\AcquisitionModel", "vehicle_id", "id");
	}

	public function group() {
		return $this->hasOne("App\Model\VehicleGroupModel", "id", "group_id")->withTrashed();
	}

	public function reviews() {
		return $this->hasMany('App\Model\VehicleReviewModel', 'vehicle_id', 'id');
	}

	public function types() {
		return $this->hasOne("App\Model\VehicleTypeModel", "id", "type_id")->withTrashed();
	}

	public function company() {
		return $this->belongsTo(Company::class);
	}

	/**
	 * Calculate the price based on insurance selection
	 * @param string $insuranceSelection 'with_insurance' or 'without_insurance'
	 * @return float
	 */
	public function calculatePrice($insuranceSelection = 'with_insurance') {
		$basePrice = (float) ($this->getMeta('price') ?: $this->getMeta('vehicle_price') ?: 0);
		$insuranceDiscount = (float) ($this->getMeta('insurance_discount') ?: 0);
		
		if ($insuranceSelection === 'without_insurance' && $insuranceDiscount > 0) {
			return max(0, $basePrice - $insuranceDiscount);
		}
		
		return $basePrice;
	}

	/**
	 * Get the insurance discount amount
	 * @return float
	 */
	public function getInsuranceDiscount() {
		return (float) ($this->getMeta('insurance_discount') ?: 0);
	}

}
