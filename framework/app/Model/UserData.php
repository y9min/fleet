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

class UserData extends BaseUuidModel {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = "users_meta";
	
	protected $fillable = [
		'user_id', 'key', 'value', 'type'
	];
}
