<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Log;

class UserController extends Controller
{
	public function index(Request $request) {
		$users = User::query();

		if($request->input('role','') != '')
			$users->where('role',$request->role);

		if($request->input('approved','') != '')
			$users->where('approved',$request->approved);

		if($request->input('search','') != '')
			$users->search($request->search);

		$users = $users->paginate(10);
		
		return view('users.index',['users'=>$users]);
	}

	public function toggleApprove(Request $request) {
		$user = User::find($request->input('user',''));
		$user->approved = !$user->approved;
		$user->save();
		return $user;
	}
}
