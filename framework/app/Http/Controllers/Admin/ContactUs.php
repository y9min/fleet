<?php
/*
@copyright

Fleet Manager v7.1.2

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\MessageModel;
use Illuminate\Http\Request;
use Auth;

class ContactUs extends Controller {
	public function __construct() {
		$this->middleware('permission:Inquiries list');
	}

    public function index() {
        // Yamz-only access check
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

		$data['messages'] = MessageModel::orderBy('id', 'desc')->get();
		return view('contactus', $data);
	}
	
	// Single delete method
    public function destroy($id)
    {
        // Yamz-only access check
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }
     
        $message = MessageModel::findOrFail($id);

        // Check if the message exists and delete it
        if ($message) {
            $message->delete();
            return redirect()->back()->with('success', 'Message deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Message not found.');
        }
    }

    // Bulk delete method
    public function bulkDelete(Request $request)
    {
        // Yamz-only access check
        $user = Auth::user();
        if ($user->email !== 'yamzahmed@hotmail.com') {
            abort(403, 'Access denied.');
        }

        $ids = $request->input('ids');

   
        // Check if IDs array is not empty
        if (!empty($ids)) {
            // Delete messages where ID is in the array
            MessageModel::whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', 'Messages deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'No messages selected for deletion.');
        }
    }
}
