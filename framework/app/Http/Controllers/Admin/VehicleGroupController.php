<?php
/*
@copyright
Fleet Manager v7.1.2
Copyright (C) 2017-2025 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>
 */
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleGroupRequest;
use App\Model\VehicleGroupModel;
use App\Model\VehicleModel;
use Auth;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleGroupController extends Controller {
	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:VehicleGroup add', ['only' => ['create', 'store']]);
		$this->middleware('permission:VehicleGroup edit', ['only' => ['edit', 'update']]);
		$this->middleware('permission:VehicleGroup delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:VehicleGroup list', ['only' => ['index', 'fetch_data']]);
	}
	public function index() {
		return view('vehicle_groups.index');
	}
	public function fetch_data(Request $request) {
		if ($request->ajax()) {
			// Debug: Log user information
			\Log::info('Vehicle Group fetch_data called', [
				'user_id' => Auth::user()->id,
				'user_type' => Auth::user()->user_type,
				'group_id' => Auth::user()->group_id,
				'has_permission' => Auth::user()->can('VehicleGroup list')
			]);
			
			try {
				if (Auth::user()->user_type == "S" || Auth::user()->group_id == null) {
					$vehicle_groups = VehicleGroupModel::query();
				} else {
					$vehicle_groups = VehicleGroupModel::where(function($query) {
						$query->where('user_id', Auth::user()->id)
							  ->orWhere('id', Auth::user()->group_id);
					})->distinct();
				}
				
				// Debug: Log the query results
				$groups_data = $vehicle_groups->get();
				\Log::info('Vehicle Groups query results', [
					'count' => $groups_data->count(),
					'groups' => $groups_data->pluck('name', 'id')->toArray()
				]);
				
				return DataTables::eloquent($vehicle_groups)
					->addColumn('check', function ($vehicle) {
						return '<input type="checkbox" name="ids[]" value="' . $vehicle->id . '" class="checkbox" id="chk' . $vehicle->id . '">';
					})
					->addColumn('vehicle_count', function ($vehicle) {
						$v = DB::table('vehicles')
							->where('group_id', $vehicle->id)->where('deleted_at', null)
							->count('group_id');
						return $v;
					})
					->addColumn('user_count', function ($vehicle) {
						$v = DB::table('users')->where('group_id', $vehicle->id)->where('deleted_at', null)->count('group_id');
						return $v;
					})
					->addColumn('action', function ($vehicle) {
						return view('vehicle_groups.list-actions', ['row' => $vehicle]);
					})
					->addIndexColumn()
					->rawColumns(['action', 'check'])
					->make(true);
			} catch (\Exception $e) {
				\Log::error('Vehicle Groups fetch_data error: ' . $e->getMessage());
				return response()->json(['error' => 'Failed to fetch data: ' . $e->getMessage()], 500);
			}
		}
		return response()->json(['error' => 'Not an AJAX request'], 400);
	}
	public function create() {
		return view('vehicle_groups.create');
	}
    public function store(VehicleGroupRequest $request) {
        $group = new VehicleGroupModel();
        $group->name = $request->get('name');
        $group->description = $request->get('description');
        // Note column may not exist in some installations; avoid setting it
        $group->user_id = Auth::user()->id;
        $group->save();

        // Optional: Assign selected vehicles to this group if provided
        $vehicleIds = $request->get('vehicleIds');
        if (is_array($vehicleIds) && count($vehicleIds) > 0) {
            VehicleModel::whereIn('id', $vehicleIds)->update(['group_id' => $group->id]);
        }

        return redirect()->route('vehicle_group.index')->with('success', 'Vehicle group created successfully!');
    }
	public function edit($id) {
		$index['data'] = VehicleGroupModel::where('id', $id)->first();
		return view('vehicle_groups.edit', $index);
	}
    public function update(VehicleGroupRequest $request, $id = null) {
        $targetId = $id ?? $request->get('id');
        $group = $targetId ? VehicleGroupModel::find($targetId) : null;
        if (!$group) {
            return back()->withErrors('Vehicle group not found.');
        }
        $group->name = $request->get('name');
        $group->description = $request->get('description');
        // Avoid assigning to non-existent column
        $group->save();
        return redirect()->route('vehicle_group.index')->with('success', 'Vehicle group updated successfully!');
    }
	public function destroy(Request $request) {
		VehicleGroupModel::find($request->get('id'))->delete();
		return redirect()->route('vehicle_group.index')->with('success', 'Vehicle group deleted successfully!');
	}
	public function bulk_delete(Request $request) {
		VehicleGroupModel::whereIn('id', $request->ids)->delete();
		return back()->with('success', 'Selected vehicle groups deleted successfully!');
	}
}
