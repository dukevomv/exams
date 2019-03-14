<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@home');

Auth::routes();

Route::get('/now',function () {return \Carbon\Carbon::now();});
Route::get('/settings', 'HomeController@settings');
Route::post('/settings', 'HomeController@updateSettings')->middleware('auth');

Route::group(['middleware' => ['auth','can:navigate']], function () {
	Route::get('/home', 'HomeController@home');
	Route::get('/test', 'HomeController@test');

	Route::group(['prefix' => 'users','middleware' => 'can:accessUsers'], function () {
		Route::get('/', 'UserController@index')->name('users_index');
		Route::post('toggle-approve', 'UserController@toggleApprove');
	});

	Route::group(['prefix' => 'lessons'], function () {
		Route::get('/', 'LessonController@index')->name('lessons_index');
		Route::get('{id}/approval/request', 'LessonController@requestApproval');
		Route::get('{id}/approval/cancel', 'LessonController@cancelApproval');

		Route::group(['middleware' => 'can:customizeLessons'], function () {
			Route::get('{lesson}', 'LessonController@show');
			Route::post('/', 'LessonController@update');
			Route::get('{id}/delete', 'LessonController@delete');
			Route::get('{id}/users', 'LessonController@getUserApprovals');
			Route::post('users/toggle-approve', 'LessonController@toggleApprove');
		});
	});

	Route::group(['prefix' => 'segments','middleware' => 'can:accessSegments'], function () {
		Route::get('/', 'SegmentController@index')->name('segments_index');
		Route::get('/sidebar', 'SegmentController@sidebarIndex');
		Route::get('create', 'SegmentController@updateView');
		Route::get('{id}/edit', 'SegmentController@updateView');
		Route::get('{id}/preview', 'SegmentController@preview');
		Route::get('{id}/delete', 'SegmentController@delete');

		Route::post('update', 'SegmentController@update');
		Route::post('{id}/edit', 'SegmentController@update');
	});

	Route::group(['prefix' => 'tests'], function () {
		//will proceed the statuses from /tests/id page 
		// and will provide required fields for schedules and test
		//draft -> published -> started -> finished -> graded 
		//Route::post('{id}/proceed', 'TestController@proceed');
		
		//professors
		Route::group(['namespace' => 'Professor','middleware' => 'can:customizeTests'], function () {
			Route::get('create', 'TestController@updateView');
			Route::get('{id}/edit', 'TestController@updateView');
			Route::get('{id}/delete', 'TestController@delete');

			Route::post('update', 'TestController@update');
			Route::post('{id}/edit', 'TestController@update');

			Route::post('{id}/start', 'TestController@start');
			Route::post('{id}/finish', 'TestController@finish');
		});

		//students
		Route::group(['namespace' => 'Student','middleware' => 'can:takeTests'], function () {
			Route::post('{id}/register', 'TestController@register');
			Route::post('{id}/leave', 'TestController@leave');
			
			Route::post('{id}/submit', 'TestController@submit');
			Route::post('{id}/submit-final', 'TestController@submit_final');
		});
		
		Route::get('/', 'TestController@index')->name('tests_index');
		Route::get('{id}', 'TestController@preview');
	});

});