<?php

/*
@copyright

Fleet Manager v7.1.2

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Model;

use App\Model\BaseUuidModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class ServiceReminderModel extends BaseUuidModel {

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable = ['vehicle_id', 'service_id', 'last_date', 'last_meter', 'user_id'];

	/**
	 * Constructor to auto-detect correct table name
	 * Supports both 'service_reminders' (plural) and 'service_reminder' (singular)
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
		
		// Auto-detect table name - prefer plural, fallback to singular
		if (Schema::hasTable('service_reminders')) {
			$this->table = 'service_reminders';
		} elseif (Schema::hasTable('service_reminder')) {
			$this->table = 'service_reminder';
		} else {
			// Default to plural if neither exists (for new installations)
			$this->table = 'service_reminders';
		}
	}

	public function services() {
		return $this->hasOne("App\Model\ServiceItemsModel", "id", "service_id")->withTrashed();
	}

	public function vehicle() {
		return $this->belongsTo("App\Model\VehicleModel", "vehicle_id", "id")->withTrashed();
	}
}
