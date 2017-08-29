<?php

namespace App\Http\Controllers\Professor;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class TestController extends Controller
{
	public function index(Request $req) {
		return view('tests.index',[]);
	}
}
