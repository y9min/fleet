<?php
/*
@copyright

Fleet Manager v7.1.2

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model\BaseUuidModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatSettingsModel extends BaseUuidModel {
	use HasFactory;
	use SoftDeletes;

	protected $table = 'chat_settings';
	protected $guarded = [];
}
