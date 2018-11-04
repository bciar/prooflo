<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register the API routes for your application as
| the routes are automatically authenticated using the API guard and
| loaded automatically by this application's RouteServiceProvider.
|
 */


Route::group(['middleware' => 'auth:api', 'prefix' => 'auth'], function () {
    Route::post('login_by_user_id','AuthController@authenticateByUserId');
    Route::get('getCurrentRole/{project_id}','AuthController@getCurrentRole');
    Route::get('check','AuthController@check');
});

Route::group(['middleware' => 'auth:api'], function (){
    Route::get('bootstrap', 'BootstrapController@bootstrap');
});

Route::group(['prefix' => 'files'], function () {
    Route::post('upload', 'FileController@upload');
    Route::delete('delete/{id}', 'FileController@deleteFile');
    Route::get('get_file/{file_id}', 'FileController@getById');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'projects'], function () {
    Route::get('bootstrap', 'BootstrapController@bootstrap');
    Route::resource('/', 'ProjectController');
    Route::post('projects/send', 'ProjectController@send');

    Route::post('create', 'ProjectController@store');
/*         Route::post('sendProofs', 'ProjectController@sendProofs'); */
    Route::get('{project_id}/revisions', 'ProjectController@getRevisionVersions');
    Route::get('/{project_id}/collaborators', 'ProjectController@getCollaborators');
    Route::get('{revision_id}/partial_proofs', 'RevisionController@getPartialProofs');
    Route::get('load/{project_id}/{revison_id}', 'ProjectController@loadInitialRevision');
    Route::get('send_project/{project_id}/{user_type}', 'ProjectController@sendProject');
    Route::post('submit_decision', 'ProjectController@submitDecision');
    Route::post('send_invite', 'ProjectController@sendCollaboratorInvite');
    Route::get('get_user_rol/{project_id}', 'ProjectController@getCurrentUserRol');
    Route::get('sendBackRevision/{project_id}/{user_type}', 'ProjectController@sendBackRevision');

});

Route::group(['middleware' => 'auth:api', 'prefix' => 'proof'], function () {
    Route::post('save', 'ProofController@saveProofState');
    Route::post('create_issue', 'ProofController@createIssue');
    Route::delete('delete_issue/{issue_id}', 'ProofController@deleteIssue');
    Route::post('add_comment', 'ProofController@addComment');
    Route::delete('delete_comment/{comment_id}', 'ProofController@deleteComment');
    Route::get('change_proof_status/{proof_id}/{status}', 'ProofController@changeProofStatus');
    Route::get('change_issue_status/{issue_id}/{status}', 'ProofController@changeIssueStatus');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'revisions'], function () {
    Route::post('create', 'RevisionController@create');
    Route::get('get_by_project/{project_id}', 'RevisionController@getRevisionsByProject');
    Route::get('load_revision_by_id/{revision_id}', 'RevisionController@loadRevisionById');
    /* Route::get('change_revision_status/{project_id}/{version}/{status}', 'RevisionController@changeRevisionStatus'); */
});
