<?php
/*
@copyright
Fleet Manager v7.1.2
Copyright (C) 2017-2025 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>
 */
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\UserRequest;
use App\Model\Hyvikk;
use App\Model\User;
use App\Model\VehicleGroupModel;
use Auth;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Redirect;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;

use App\Traits\FirebasePassword;

class UsersController extends Controller {

	use FirebasePassword;

	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:Users add', ['only' => ['create']]);
		$this->middleware('permission:Users edit', ['only' => ['edit']]);
		$this->middleware('permission:Users delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:Users list');
	}
	public function index() {
		return view("users.index");
	}
	public function fetch_data(Request $request) {
		try {
			if ($request->ajax()) {
				$users = User::with(['company'])
					->where(function ($query) {
						$query->where('user_type', 'O')
							->orWhere('user_type', 'S');
					});
				return DataTables::eloquent($users)
					->addColumn('company', function ($user) {
						return $user->company ? $user->company->name : 'No Company';
					})
					->editColumn('created_at', function ($user) {
						return $user->created_at->format('M d, Y');
					})
					->addColumn('action', function ($user) {
						$auth = Auth::user();
						if (!$auth) {
							return '&nbsp;';
						}
						
						$buttons = '';
						
						// Super Admin or user with edit permission
						$canEdit = ($auth->user_type == 'S') || Gate::allows('Users edit') || $auth->hasPermissionTo('Users edit');
						
						if ($canEdit) {
							$editUrl = url("admin/users/" . $user->id . "/edit");
							$buttons .= '<button type="button" class="btn btn-sm btn-primary" onclick="window.location.href=\'' . $editUrl . '\'" style="margin-right: 5px;">';
							$buttons .= '<i class="fas fa-edit"></i> Edit';
							$buttons .= '</button>';
						}
						
						// Delete button - not super admin (id=1) and user has delete permission
						$canDelete = ($user->id != 1) && (($auth->user_type == 'S') || Gate::allows('Users delete') || $auth->hasPermissionTo('Users delete'));
						
						if ($canDelete) {
							$buttons .= '<button type="button" class="btn btn-sm btn-danger" data-id="' . $user->id . '" data-toggle="modal" data-target="#myModal">';
							$buttons .= '<i class="fas fa-trash"></i> Delete';
							$buttons .= '</button>';
						}
						
						return $buttons ?: '&nbsp;';
					})
					->rawColumns(['action'])
					->make(true);
			}
		} catch (\Exception $e) {
			\Log::error('UsersController::fetch_data error: ' . $e->getMessage());
			\Log::error('Stack trace: ' . $e->getTraceAsString());
			
			if ($request->ajax()) {
				return response()->json([
					'draw' => intval($request->input('draw')),
					'recordsTotal' => 0,
					'recordsFiltered' => 0,
					'data' => [],
					'error' => 'An error occurred while loading users data.'
				], 500);
			}
		}
		
		return response()->json(['error' => 'Invalid request'], 400);
	}
	public function create() {
		$index['groups'] = VehicleGroupModel::all();
		$index['roles'] = Role::get();
		return view("users.create", $index);
	}
	public function destroy(Request $request) {

		$u=User::find($request->get('id'));

		$this->deleteUser($u->email);

		$user = User::find($request->get('id'));
		$user->update([
			'email' => time() . "_deleted" . $user->email,
		]);
		if (file_exists('./uploads/' . $user->profile_image) && !is_dir('./uploads/' . $user->profile_image)) {
			unlink('./uploads/' . $user->profile_image);
		}
		$user->delete();
		return redirect()->route('users.index');
	}
	private function upload_file($file, $field, $id) {
		$destinationPath = './uploads'; // upload path
		$extension = $file->getClientOriginalExtension();
		$fileName1 = Str::uuid() . '.' . $extension;
		$file->move($destinationPath, $fileName1);
		$user = User::find($id);
		$user->setMeta([$field => $fileName1]);
		$user->save();
	}
	public function store(UserRequest $request) {
		$role = Role::find($request->role_id)->toArray();
		if ($role['name'] == "Super Admin") {
			$user_type = 'S';
		} else {
			$user_type = 'O';
		}
		$id = User::create([
			"name" => $request->get("first_name") . " " . $request->get("last_name"),
			"email" => $request->get("email"),
			"password" => bcrypt($request->get("password")),
			"user_type" => $user_type,
			"group_id" => $request->get("group_id"),
			'api_token' => str_random(60),
		])->id;
		$user = User::find($id);
		$user->user_id = Auth::user()->id;
		$user->module = serialize($request->get('module'));
		// $user->language = 'English-en';
		$user->language = Auth::user()->language;
		$user->setMeta([
			'first_name' => $request->get("first_name"),
			'last_name' => $request->get("last_name")
		]);
		$user->save();
		$role = Role::find($request->role_id);
		$user->assignRole($role);
		if ($request->file('profile_image') && $request->file('profile_image')->isValid()) {
			$this->upload_file($request->file('profile_image'), "profile_image", $id);
		}
		return Redirect::route("users.index");
	}
	public function edit($id) {
		$user = User::find($id);
		$groups = VehicleGroupModel::all();
		$roles = Role::get();
		return view("users.edit", compact("user", 'groups', "roles"));
	}
	public function update(EditUserRequest $request) {
		$user = User::whereId($request->get("id"))->first();
		$user->name = $request->get("first_name") . " " . $request->get("last_name");
		$user->email = $request->get("email");
		$user->group_id = $request->get("group_id");
		$user->module = serialize($request->get('module'));
		$user->setMeta([
			'first_name' => $request->get("first_name"),
			'last_name' => $request->get("last_name")
		]);
		$old = Role::find($user->roles->first()->id);
		if ($old != null) {
			$user->removeRole($old);
		}
		// $user->profile_image = $request->get('profile_image');
		$role = Role::find($request->role_id);
		if ($role['name'] == "Super Admin") {
			$user->user_type = 'S';
		} else {
			$user->user_type = 'O';
		}
		$user->save();
		$role = Role::find($request->role_id);
		$user->assignRole($role);
		if ($request->file('profile_image') && $request->file('profile_image')->isValid()) {
			if (file_exists('./uploads/' . $user->profile_image) && !is_dir('./uploads/' . $user->profile_image)) {
				unlink('./uploads/' . $user->profile_image);
			}
			$this->upload_file($request->file('profile_image'), "profile_image", $user->id);
		}
		$modules = unserialize($user->getMeta('module'));
		return Redirect::route("users.index");
	}
	public function bulk_delete(Request $request) {
		$users = User::whereIn('id', $request->ids)->get();
		foreach ($users as $user) {

			$this->deleteUser($user->email);

			$user->update([
				'email' => time() . "_deleted" . $user->email,
			]);
			if (file_exists('./uploads/' . $user->profile_image) && !is_dir('./uploads/' . $user->profile_image)) {
				unlink('./uploads/' . $user->profile_image);
			}
			$user->delete();
		}
		return back();
	}
}
