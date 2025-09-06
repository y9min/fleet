<?php
/*
@copyright
Fleet Manager v7.1.2
Copyright (C) 2017-2025 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>
 */
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Auth as Login;

class Testcontroller extends Controller {
	public function fetch() {
		return Auth::user();
	}
	public function login() {
		Login::loginUsingId(1);
		$user = Login::user();
		return view('sample');
	}
}
