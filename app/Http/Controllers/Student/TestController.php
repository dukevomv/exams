<?php

namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Segments\Segment;
use App\Models\Lesson;
use App\Models\Test;

use Carbon\Carbon;
use Log;
use Auth;
class TestController extends Controller
{
	public function register($id = null) {
		$user_id = Auth::id();
		$test = Test::where('id',$id)
						->where('status','published')
						->where('scheduled_at','>=',Carbon::now())
						->with('users')
						->first();
		if(is_null($test))
			return back()->with(['error'=>'You can not register to this test.']);
		if($test->users->contains($user_id))
			return back()->with(['error'=>'Already registered to this test.']);
		$test->register();
		return redirect('tests/'.$id.'/lobby');
	}

	public function live($id = null) {
		$user_id = Auth::id();
		$test = Test::where('id',$id)
						->where('status','started')
						->with(['segments'=>function($query){
							$query->withTaskAnswers();
						}])
						->first();
		if(is_null($test))
			return redirect('tests')->with(['error'=>'The test is not live right now.']);
		return view('tests.live',['test'=>$test]);
	}
}
