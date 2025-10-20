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

class FareSettings extends BaseUuidModel {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable = [
		'key_name', 'key_value', 'type_id',
	];
	protected $table = "fare_settings";

	public static function get($key) {

		return ApiSettings::whereName($key)->first()->key_value;

	}
}
