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

class VehicleTypeModel extends BaseUuidModel {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'vehicle_types';
	protected $fillable = ['name', 'display_name', 'icon', 'is_enabled', 'seats'];
	protected $casts = ['is_enabled' => 'boolean'];
}
