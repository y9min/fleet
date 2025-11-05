<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerfController extends Controller
{
    public function store(Request $request)
    {
        \Log::notice('[PERF-CLIENT]', $request->all());
        return response()->noContent();
    }
}

