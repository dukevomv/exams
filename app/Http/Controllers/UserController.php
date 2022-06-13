<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Log;

class UserController extends Controller {

    public function index(Request $request) {
        $users = User::query();

        if ($request->input('role', '') != '') {
            $users->where('role', $request->role);
        }

        if ($request->input('approved', '') != '') {
            $users->where('approved', $request->approved);
        }

        if ($request->input('search', '') != '') {
            $users->search($request->search);
        }

        if ($request->input('deleted', '0') != '0') {
            $users->onlyTrashed();
        }

        $users = $users->paginate(10);

        return view('users.index', ['users' => $users]);
    }

    public function toggleApprove(Request $request) {
        $user = User::find($request->input('user', ''));
        $user->approved = !$user->approved;
        $user->save();
        return $user;
    }
    
    public function destroy($id = null) {
        $user = User::where('id', $id)->first();
        if (is_null($id) || is_null($user)) {
            return back()->with(['error' => 'User cannot be deleted.']);
        }
        $user->delete();
        return back()->with(['success' => 'User deleted successfully']);
    }
    
    
    public function restore($id = null) {
        $user = User::onlyTrashed()->where('id', $id)->first();
        if (is_null($id) || is_null($user)) {
            return back()->with(['error' => 'User cannot be restored.']);
        }
        $user->restore();
        return back()->with(['success' => 'User deletion restored successfully']);
    }
}
