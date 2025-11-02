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
				$auth = Auth::user();
				
				$users = User::with(['company'])
					->where(function ($query) {
						$query->where('user_type', 'O')
							->orWhere('user_type', 'S');
					});
				
				// Company scoping - yamz sees everything, others only see their company
				if ($auth->email !== 'yamzahmed@hotmail.com') {
					// Filter by company_id - only show users from the same company
					if (!is_null($auth->company_id)) {
						$users = $users->where('company_id', $auth->company_id);
					} else {
						// User without company sees no users
						$users = $users->whereRaw('1=0');
					}
				}
				// If yamz (yamzahmed@hotmail.com), show all users without company filter
				
				return DataTables::eloquent($users)
					->addColumn('company', function ($user) {
						return $user->company ? $user->company->name : 'No Company';
					})
					->editColumn('created_at', function ($user) {
						return $user->created_at->format('M d, Y');
					})
					->addColumn('action', function ($user) {
						$auth = Auth::user();
						
						$buttons = '';
						
						// Edit button - opens modal
						$buttons .= '<button type="button" class="btn btn-sm btn-primary edit-user-btn" data-user-id="' . $user->id . '" data-toggle="modal" data-target="#editUserModal" style="margin-right: 5px;">';
						$buttons .= '<i class="fas fa-edit"></i> Edit';
						$buttons .= '</button>';
						
						// Delete button - not super admin (id=1)
						if ($user->id != 1) {
							$buttons .= '<button type="button" class="btn btn-sm btn-danger" data-id="' . $user->id . '" data-toggle="modal" data-target="#myModal">';
							$buttons .= '<i class="fas fa-trash"></i> Delete';
							$buttons .= '</button>';
							
							// Hidden form for delete functionality
							$deleteUrl = url("admin/users/" . $user->id);
							$buttons .= '<form method="POST" action="' . $deleteUrl . '" style="display:none;" id="form_' . $user->id . '">';
							$buttons .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
							$buttons .= '<input type="hidden" name="_method" value="DELETE">';
							$buttons .= '<input type="hidden" name="id" value="' . $user->id . '">';
							$buttons .= '</form>';
						}
						
						return $buttons;
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
		$profileImage = $user->getMeta('profile_image');
		if ($profileImage && file_exists('./uploads/' . $profileImage) && !is_dir('./uploads/' . $profileImage)) {
			unlink('./uploads/' . $profileImage);
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
	
	public function getEditData($id) {
		$user = User::with(['company', 'roles'])->findOrFail($id);
		
		return response()->json([
			'id' => $user->id,
			'name' => $user->name,
			'email' => $user->email,
			'role_id' => $user->roles->first() ? $user->roles->first()->id : null,
		]);
	}
	public function update(Request $request) {
		// Handle validation for both modal and form formats
		if ($request->has('name') && !$request->has('first_name')) {
			// Modal format - validate separately
			$request->validate([
				'name' => 'required|string|max:255',
				'email' => 'required|email|unique:users,email,' . $request->get('id'),
				'password' => 'nullable|string|min:8|confirmed',
			]);
		} else {
			// Form format - use EditUserRequest validation via type hint will be lost, so validate manually
			$rules = [
				'first_name' => 'required',
				'last_name' => 'required',
				'email' => 'required|email|unique:users,email,' . $request->get('id'),
				'profile_image' => 'nullable|mimes:jpg,png,jpeg|max:2084',
			];
			$request->validate($rules);
		}
		
		$user = User::whereId($request->get("id"))->first();
		
		// Handle both modal format (name) and form format (first_name, last_name)
		if ($request->has('name') && !$request->has('first_name')) {
			// Modal format - parse name into first_name and last_name
			$nameParts = explode(' ', $request->get('name'), 2);
			$first_name = $nameParts[0] ?? '';
			$last_name = $nameParts[1] ?? '';
			$user->name = $request->get("name");
		} else {
			// Form format
			$first_name = $request->get("first_name");
			$last_name = $request->get("last_name");
			$user->name = $first_name . " " . $last_name;
		}
		
		$user->email = $request->get("email");
		
		// Only set these if provided (from full edit form, not modal)
		if ($request->has('group_id')) {
			$user->group_id = $request->get("group_id");
		}
		if ($request->has('module')) {
			$user->module = serialize($request->get('module'));
		}
		
		$user->setMeta([
			'first_name' => $first_name,
			'last_name' => $last_name
		]);
		
		// Handle password update (from modal)
		if ($request->filled('password')) {
			$user->password = bcrypt($request->get('password'));
		}
		// Handle role update (only if role_id is provided)
		if ($request->has('role_id')) {
			$oldRole = $user->roles->first();
			if ($oldRole != null) {
				$old = Role::find($oldRole->id);
				if ($old != null) {
					$user->removeRole($old);
				}
			}
			$role = Role::find($request->role_id);
			if ($role) {
				if ($role->name == "Super Admin") {
					$user->user_type = 'S';
				} else {
					$user->user_type = 'O';
				}
				$user->assignRole($role);
			}
		}
		
		$user->save();
		if ($request->file('profile_image') && $request->file('profile_image')->isValid()) {
			$oldProfileImage = $user->getMeta('profile_image');
			if ($oldProfileImage && file_exists('./uploads/' . $oldProfileImage) && !is_dir('./uploads/' . $oldProfileImage)) {
				unlink('./uploads/' . $oldProfileImage);
			}
			$this->upload_file($request->file('profile_image'), "profile_image", $user->id);
		}
		// Return JSON for AJAX requests (modal), redirect for regular form submissions
		if ($request->ajax() || $request->wantsJson()) {
			return response()->json([
				'success' => true,
				'message' => 'User updated successfully.'
			]);
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
			$profileImage = $user->getMeta('profile_image');
			if ($profileImage && file_exists('./uploads/' . $profileImage) && !is_dir('./uploads/' . $profileImage)) {
				unlink('./uploads/' . $profileImage);
			}
			$user->delete();
		}
		return back();
	}
}
