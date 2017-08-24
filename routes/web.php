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
	Route::get('/settings', 'HomeController@settings');

	Route::group(['prefix' => 'lessons'], function () {
		Route::group(['namespace' => 'Professor'], function () {
			Route::get('/', 'LessonController@index')->name('lessons_index');
			Route::post('subscribe', 'LessonController@subscribe');
			Route::post('create', 'LessonController@create');
			Route::post('delete', 'LessonController@delete');
		});
	});

});