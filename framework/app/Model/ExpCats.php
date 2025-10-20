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

class ExpCats extends BaseUuidModel {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable = [
		'name', 'user_id', 'cost', 'frequancy', 'type',
	];
	protected $table = "expense_cat";
	public function expense() {
		return $this->hasMany("App\Model\Expense", "expense_type", "id")->withTrashed();
	}
}
