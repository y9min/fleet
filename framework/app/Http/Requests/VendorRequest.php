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

class VendorRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		if (Auth::user()->user_type == "S" || Auth::user()->user_type == "O") {
			return true;
		} else {
			abort(404);
		}
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|alpha',
			'type' => 'required',
			//'website' => 'required',
			'phone' => 'required',
			'address1' => 'required',
			'email' => 'required',
			'city' => 'required',
			'postal_code' => 'regex:/\d{5}(-\d{0,4})?/|nullable',
			'country' => 'required',
			'photo' => 'nullable|mimes:jpg,png,jpeg|max:5120',
		];
	}
}
