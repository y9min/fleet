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

class PartsCategoryModel extends Model {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'parts_category';
	protected $fillable = ['name', 'user_id'];
}
