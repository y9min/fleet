<?php

/*
@copyright

Fleet Manager v7.1.2

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Auth;
use App\Rules\UniqueContractNumber;
use App\Rules\UniqueEId;
use App\Rules\UniqueLicenceNumber;
use Illuminate\Foundation\Http\FormRequest;

class DriverRequest extends FormRequest {

	public function authorize() {
		if (Auth::user()->user_type == "S" || Auth::user()->user_type == "O") {
			return true;
		} else {
			abort(404);
		}
	}

	public function rules() {
		// Get the ID value and handle empty string for UUID
		$id = \Request::get("id");
		$id = ($id === '' || $id === null) ? null : $id;
		
		if ($this->request->has("edit")) {
			return [

				'first_name' => 'required',
				'last_name' => 'required',
				'address' => 'nullable',
				'phone' => 'required|numeric|digits_between:7,15',
				'email' => $id ? 'required|email|unique:users,email,' . $id : 'required|email|unique:users,email',
				'start_date' => 'date|date_format:Y-m-d',
				'issue_date' => 'date|date_format:Y-m-d',
				'end_date' => 'nullable|date|date_format:Y-m-d',
				'exp_date' => 'required|date|date_format:Y-m-d',
				'driver_image' => 'nullable|mimes:jpg,png,jpeg|max:5120',
				'license_image' => 'nullable|mimes:jpg,png,jpeg|max:5120',
				'documents.*' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx|max:5120',
				'driver_commision_type' => 'nullable',
				'driver_commision' => 'nullable|numeric',
			];
		} else {
			return [
				'emp_id' => ['nullable', new UniqueEId],
				'license_number' => ['nullable', new UniqueLicenceNumber],
				'contract_number' => ['nullable', new UniqueContractNumber],
				'first_name' => 'required',
				'last_name' => 'required',
				'address' => 'nullable',
				'phone' => 'required|numeric|digits_between:7,15',
				'email' => $id ? 'required|email|unique:users,email,' . $id : 'required|email|unique:users,email',
				'exp_date' => 'required|date|date_format:Y-m-d|after:tomorrow',
				'start_date' => 'date|date_format:Y-m-d',
				'issue_date' => 'date|date_format:Y-m-d',
				'end_date' => 'nullable|date|date_format:Y-m-d',
				'driver_image' => 'nullable|mimes:jpg,png,jpeg|max:5120',
				'license_image' => 'nullable|mimes:jpg,png,jpeg|max:5120',
				'documents.*' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx|max:5120',
				'driver_commision_type' => 'nullable',
				'driver_commision' => 'nullable|numeric',
			];
		}
	}
}
