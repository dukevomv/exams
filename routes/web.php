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

Route::group(['middleware' => 'auth'], function () {
	Route::get('/home', 'HomeController@home');
	Route::get('/test', 'HomeController@test');
	Route::get('/settings', 'HomeController@settings');

	Route::group(['prefix' => 'lessons'], function () {
		Route::group(['namespace' => 'Professor'], function () {
			Route::get('/', 'LessonController@index')->name('lessons_index');
			Route::post('subscribe', 'LessonController@subscribe');
			Route::post('create', 'LessonController@create');
		});
	});

	Route::group(['prefix' => 'segments'], function () {
		Route::group(['namespace' => 'Professor'], function () {
			Route::get('/', 'SegmentController@index')->name('segments_index');
			Route::get('create', 'SegmentController@updateView');
			Route::get('{id}/edit', 'SegmentController@updateView');
			Route::get('{id}/preview', 'SegmentController@preview');
			Route::get('{id}/delete', 'SegmentController@delete');

			Route::post('create', 'SegmentController@update');
			Route::post('{id}/edit', 'SegmentController@update');
		});
	});

	Route::group(['prefix' => 'tests'], function () {
		Route::group(['namespace' => 'Professor'], function () {
			Route::get('/', 'TestController@index')->name('tests_index');
			Route::get('create', 'TestController@updateView');

			Route::post('create', 'TestController@create');
			Route::post('delete', 'TestController@delete');
		});
	});

});