<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

Route::get('/', 'WelcomeController@show');

Route::get('/home', function () {
    return redirect('/projects');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/projects/{path?}', function () {
        return view('spa-modules.spa-projects.spa-projects');
    })->where('path', '^(?!api).*$');

    Route::get('/loadProofer/{project_hash}/{revision_id}', 'ProjectController@loadProofer');
    /* Route::get('/load_editor_freelancer/{project_id}/{revision_id}', 'ProjectController@loadEditorFreelancer'); */
});

/* Route::get('/load_editor_client/{project_hash}/{revision_id}', 'ProjectController@loadEditorClient');
Route::get('/load_editor_freelancer/{project_id}/{revision_id}', 'ProjectController@loadEditorFreelancer'); */

Route::get('/proofer/{path?}', function () {
    return view('spa-modules.spa-projects.spa-projects');
})->where('path', '^(?!api).*$');

/* Route::get('/proofer_guest/{path?}', function () {
    return view('spa-modules.spa-projects.spa-projects');
})->where('path', '^(?!api).*$');

Route::get('/proofer_freelancer/{path?}', function () {
    return view('spa-modules.spa-projects.spa-projects');
})->where('path', '^(?!api).*$');
 */