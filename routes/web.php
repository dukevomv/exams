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

Route::get('/', 'HomeController@home')->middleware('confirms_otp');

Auth::routes();

Route::get('/now', function () {
    return \Carbon\Carbon::now();
});

if (config('app.demo.enabled')) {
    Route::group(['prefix' => 'demo'], function () {
        Route::get('/', 'DemoController@index');
        Route::post('generate', 'DemoController@generate');
        Route::post('switch-role/{role}', 'DemoController@switchRole');
    });
}

if (config('app.trial.enabled')) {
    Route::group(['prefix' => 'trial'], function () {
        Route::get('/', 'TrialController@index');
        Route::post('generate', 'TrialController@generate');
        Route::post('switch-role/{role}', 'TrialController@switchRole');
        Route::post('send-login-code', 'TrialController@sendLoginCode');
    });
}

Route::get('/otp', 'HomeController@viewOTP');
Route::get('/otp/resend', 'HomeController@resendOTP');
Route::post('/otp', 'HomeController@submitOTP');

Route::get('public/tests/{testId}/invited-students/{inviteUuid}/preview', 'TestController@previewInvitation')->name('test.invitation.preview');
Route::post('public/tests/{testId}/invited-students/{inviteUuid}/accept', 'TestController@acceptInvitation')->name('test.invitation.accept');
Route::post('public/tests/{testId}/invited-students/{inviteUuid}/login-code', 'TestController@sendLoginCodeForInvitation')->name('test.invitation.login-code');


Route::group(['middleware' => ['auth']], function () {
    Route::get('/settings', 'HomeController@settings');
    Route::group(['middleware' => ['confirms_otp']], function () {
        Route::post('/settings', 'HomeController@updateSettings')->middleware('can:updateProfileDetails');
        Route::post('/settings/otp', 'HomeController@updateOTPSetting')->middleware('can:updateOTPSetting');

        Route::group(['middleware' => ['can:navigate']], function () {
            Route::get('/home', 'HomeController@home');
            Route::get('/test', 'HomeController@test');

            Route::group(['prefix' => 'users', 'middleware' => 'can:accessUsers'], function () {
                Route::get('/', 'UserController@index')->name('users_index');
                Route::post('toggle-approve', 'UserController@toggleApprove');
                Route::get('{id}/delete', 'UserController@destroy');
                Route::post('{id}/delete', 'UserController@destroy');
                Route::get('{id}/restore', 'UserController@restore');
                Route::post('{id}/restore', 'UserController@restore');
            });

            Route::group(['middleware' => 'can:viewStatistics'], function () {
                Route::get('statistics', 'HomeController@statistics');
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

            Route::group(['prefix' => 'segments', 'middleware' => 'can:accessSegments'], function () {
                Route::get('/', 'SegmentController@index')->name('segments_index');
                Route::get('/sidebar', 'SegmentController@sidebarIndex');
                Route::get('create', 'SegmentController@updateView');
                Route::get('{id}/edit', 'SegmentController@updateView');
                Route::get('{id}/preview', 'SegmentController@preview');
                Route::get('{id}/delete', 'SegmentController@delete');

                Route::post('update', 'SegmentController@update');
                Route::post('{id}/edit', 'SegmentController@update');//might be deprecated
            });

            Route::group(['prefix' => 'tasks', 'middleware' => 'can:accessSegments'], function () {
                //images
                Route::post('{id}/images', 'ImageController@uploadOnTask');
                Route::post('{id}/images/{image_id}', 'ImageController@update');
                Route::post('{id}/images/{image_id}/remove', 'ImageController@remove');
            });

            Route::group(['prefix' => 'tests', 'middleware' => 'can:accessTests'], function () {
                //will proceed the statuses from /tests/id page
                // and will provide required fields for schedules and test
                //draft -> published -> started -> finished -> graded
                //Route::post('{id}/proceed', 'TestController@proceed');

                //professors
                Route::group(['namespace' => 'Professor', 'middleware' => 'can:customizeTests'], function () {
                    Route::get('create', 'TestController@updateView')->middleware('can:createTests');
                    Route::get('{id}/edit', 'TestController@updateView');
                    Route::get('{id}/delete', 'TestController@delete');
                    Route::get('{id}/export-csv', 'TestController@exportCSV');

                    Route::post('update', 'TestController@update');
                    Route::post('{id}/edit', 'TestController@update');
                    Route::get('{testId}/invited-students', 'TestController@inviteStudentsList');
                    Route::post('{testId}/invited-students', 'TestController@inviteStudents');
                    Route::get('{testId}/invited-students/{id}/delete', 'TestController@removeInvitedStudent');
                    Route::get('{testId}/invited-students/{id}/send-invite', 'TestController@sendInvitation');
                    Route::get('{testId}/invited-students/{uuid}/send-invite', 'TestController@sendInvitation');

                    Route::post('{id}/start', 'TestController@start');
                    Route::post('{id}/finish', 'TestController@finish');
                    Route::post('{id}/auto-calculate', 'TestController@autoCalculateGrades');
                    Route::post('{id}/publish-grades', 'TestController@publishGrades');

                    Route::get('{id}/users/{userId}', 'TestController@userPreview');
                    Route::post('{id}/users/{userId}/grade-task', 'TestController@gradeUserTask');
                    Route::post('{id}/users/{userId}/auto-grade', 'TestController@autoGrade');
                    Route::post('{id}/users/{userId}/publish-grade', 'TestController@publishGrade');
                });

                //students
                Route::group(['namespace' => 'Student', 'middleware' => 'can:takeTests'], function () {
                    Route::post('{id}/register', 'TestController@register');
                    Route::post('{id}/leave', 'TestController@leave');

                    Route::post('{id}/submit', 'TestController@submit');
                    Route::post('{id}/submit-final', 'TestController@submit_final');
                });

                Route::get('/', 'TestController@index')->name('tests_index');
                Route::get('{id}', 'TestController@preview');
            });
        });
        });
});