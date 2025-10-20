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

class IncCats extends BaseUuidModel {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable = ['name', 'user_id', 'cost', 'type'];
	protected $table = "income_cat";
	public function income() {
		return $this->hasMany("App\Model\IncomeModel", "income_cat", "id")->withTrashed();
	}
}
