<?php

namespace App\Model;

use App\Model\BaseUuidModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class CitiesModel extends BaseUuidModel {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'cities';
	protected $fillable = ['city', 'cost', 'image', 'other', 'slug'];
}
