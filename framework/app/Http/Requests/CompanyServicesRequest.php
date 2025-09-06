<?php

/*
@copyright

Fleet Manager v7.1.2

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class CompanyServicesRequest extends FormRequest {

	public function authorize() {
		return (Auth::user()->user_type == "S" || Auth::user()->user_type == "O");
	}

	public function rules() {
		return [
			'title' => 'required|max:54',
			'description' => 'required|max:93',
			'image' => 'mimes:png|dimensions:max_width=880px,max_height=555px',
		];
	}

	public function messages() {
		return [
			'image.dimensions' => 'Icon Image dimensions must be 880x555.',
		];
	}
}
