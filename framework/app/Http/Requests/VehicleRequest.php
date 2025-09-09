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

class VehicleRequest extends FormRequest {

        public function authorize() {
                if (Auth::user()->user_type == "S" || Auth::user()->user_type == "O") {
                        return true;
                } else {
                        abort(404);
                }
        }

        public function rules() {
                // dd($this->request->get("_method"));
                if($this->request->get("_method") == 'PATCH'){
                        $id = \Request::get("id") ?: 'NULL';
                        return [
                                'make_name' => 'required',
                                'model_name' => 'required',
                                'year' => 'nullable|numeric|digits:4',
                                'engine_type' => 'required',
                                'horse_power' => 'nullable|integer',
                                'color_name' => 'nullable',
                                'lic_exp_date' => 'nullable|date|date_format:Y-m-d',
                                'reg_exp_date' => 'nullable|date|date_format:Y-m-d',
                                'license_plate' => 'required|unique:vehicles,license_plate,' . $id . ',id,deleted_at,NULL',
                                'int_mileage' => 'nullable|alpha_num',
                                'vehicle_image' => 'nullable|mimes:jpg,png,jpeg|max:5120',
                                'icon' => 'nullable|mimes:jpg,png,jpeg|max:5120',
                                'average' => 'nullable|numeric',
                                'type_id' => 'nullable|integer',
                                'traccar_device_id' => 'nullable',
                        ];
                }
                else{
                        return [
                                'make_name' => 'required',
                                'model_name' => 'required',
                                'year' => 'nullable|numeric|digits:4',
                                'engine_type' => 'required',
                                'horse_power' => 'nullable|integer',
                                'color_name' => 'nullable',
                                'lic_exp_date' => 'nullable|date|date_format:Y-m-d',
                                'reg_exp_date' => 'nullable|date|date_format:Y-m-d',
                                'license_plate' => 'required|unique:vehicles,license_plate,NULL,id,deleted_at,NULL',
                                'int_mileage' => 'nullable|alpha_num',
                                'vehicle_image' => 'nullable|mimes:jpg,png,jpeg|max:5120',
                                'icon' => 'nullable|mimes:jpg,png,jpeg|max:5120',
                                'average' => 'nullable|numeric',
                                'type_id' => 'nullable|integer',
                                'traccar_device_id' => 'nullable',
                        ];
                }
        }
}
