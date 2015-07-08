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

Route::get('/',array('as'=>'index','uses'=>'UsersController@loginForm'));
Route::get('/auth/login',array('as'=>'login.form','uses'=>'UsersController@loginForm'));
Route::get('/login',array('as'=>'login.form','uses'=>'UsersController@loginForm'));
Route::post('/',array('as'=>'login.action','uses'=>'UsersController@loginAction'));
Route::get('/logout',array('as'=>'logout','uses'=>'UsersController@logout'));
Route::get('/panel',array('as'=>'panel','uses'=>'UsersController@panel'));
Route::get('/resend', array('as'=>'users.resendConfEmailForm', 'uses' => 'UsersController@resendConfEmailForm'));
Route::post('/resend', array('as'=>'users.resendConfEmailAction', 'uses' => 'UsersController@resendConfEmailAction'));
Route::get('/forget', array('as'=>'users.forgetPasswordForm', 'uses' => 'UsersController@forgetPasswordForm'));
Route::post('/forget', array('as'=>'users.forgetPasswordAction', 'uses' => 'Auth\PasswordController@postEmail'));
Route::get('/forgetConfirm/{token}', array('as'=>'users.forgetPasswordConfirmForm', 'uses' => 'UsersController@forgetPasswordConfirmForm'));
Route::post('/forgetConfirm/{token}', array('as'=>'users.forgetPasswordConfirmAction', 'uses' => 'Auth\PasswordController@postReset'));

Route::get('/fotFound',array('as' => 'notFound','uses' => 'UsersController@notFound'));
Route::get('/permissionsDenied',array('as' => 'permissionsDenied','uses' => 'UsersController@permissionsDenied'));
Route::get('/visitorOnly',array('as' => 'visitorOnly','uses' => 'UsersController@visitorOnly'));

Route::resource('users', 'UsersController');
Route::put('/users/{id}/changeEmail',array('as'=>'users.updateEmail', 'uses' => 'UsersController@updateEmail'));
Route::put('/users/{id}/changePassword',array('as'=>'users.updatePass', 'uses' => 'UsersController@updatePass'));
Route::put('/users/{id}/changeDetails',array('as'=>'users.updateDetails', 'uses' => 'UsersController@updateDetails'));

Route::resource('sponsors', 'SponsorsController');
Route::delete('/sponsors/{id}/activate',array('as'=>'sponsors.activate','uses'=>'SponsorsController@activate'));
Route::delete('/sponsors/{id}/{pid}/suspend',array('as'=>'sponsors.suspend','uses'=>'SponsorsController@suspend'));
Route::get('/sponsors/join/{url}',array('as'=>'sponsors.join','uses'=>'SponsorsController@join'));
Route::post('/sponsors/register/{url}',array('as'=>'sponsors.register','uses'=>'SponsorsController@register'));
Route::get('/sponsors/{hash}/confirm',array('as'=>'sponsors.confirm','uses'=>'SponsorsController@confirm'));
Route::get('/mySponsors',array('as'=>'mySponsors','uses'=>'SponsorsController@mySponsors'));
Route::get('/sponsors/{id}/projects',array('as'=>'sponsors.projects','uses'=>'SponsorsController@projects'));

Route::resource('recipients', 'RecipientsController');
Route::get('/join/{url}',array('as'=>'recipients.join','uses'=>'RecipientsController@join'));
Route::post('/recipients/register/{url}',array('as'=>'recipients.register','uses'=>'RecipientsController@register'));
Route::get('/recipients/of/{id}',array('as'=>'recipients.perProject','uses'=>'RecipientsController@perProject'));
Route::post('/recipients/{id}/accept',array('as'=>'recipients.accept','uses'=>'RecipientsController@accept'));
Route::post('/recipients/{id}/deny',array('as'=>'recipients.deny','uses'=>'RecipientsController@deny'));
Route::get('/recipients/{hash}/confirm',array('as'=>'recipients.confirm','uses'=>'RecipientsController@confirm'));
Route::get('/myRecipients',array('as'=>'myRecipients','uses'=>'RecipientsController@myRecipients'));

Route::resource('coordinators', 'CoordinatorsController');
Route::get('/coordinators/{id}/projects',array('as'=>'projects.perCoordinator','uses'=>'ProjectsController@perCoordinator'));

Route::resource('projects', 'ProjectsController');
Route::get('/projects/{id}/sponsors',array('as'=>'sponsors.perProject','uses'=>'SponsorsController@perProject'));
Route::get('/projects/{id}/recipients',array('as'=>'recipients.perProject','uses'=>'RecipientsController@perProject'));
Route::delete('/projects/{id}/withdraw',array('as'=>'projects.withdraw','uses'=>'ProjectsController@withdraw'));
Route::delete('/projects/{id}/close',array('as'=>'projects.close','uses'=>'ProjectsController@close'));
Route::delete('/projects/{id}/open',array('as'=>'projects.open','uses'=>'ProjectsController@open'));
Route::get('/myProject',array('as'=>'myProject','uses'=>'ProjectsController@myProject'));
Route::get('/joinProject',array('as'=>'joinProject','uses'=>'ProjectsController@joinProject'));
Route::get('/joinProject/{id}',array('as'=>'joinProjectAction','uses'=>'ProjectsController@joinProjectAction'));

Route::resource('payments', 'PaymentsController');
Route::get('/myTransactions',array('as'=>'myTransactions','uses'=>'PaymentsController@myTransactions'));
Route::get('/payments/add/{pid}/{uid}',array('as'=>'payments.add','uses'=>'PaymentsController@add'));
Route::get('/payments/request/{pid}/{uid}',array('as'=>'payments.request','uses'=>'PaymentsController@request'));
Route::post('/payments/save/{pid}/{uid}',array('as'=>'payments.save','uses'=>'PaymentsController@save'));
Route::delete('/payments/accept/{pid}',array('as'=>'payments.accept','uses'=>'PaymentsController@accept'));
Route::delete('/payments/reject/{pid}',array('as'=>'payments.reject','uses'=>'PaymentsController@reject'));

Route::resource('projects.invitations', 'InvitationsController');
Route::resource('settings', 'SettingsController');
Route::resource('projects.spends', 'SpendsController');
Route::get('/myActivities',array('as'=>'myActivities','uses'=>'SpendsController@myActivities'));

//Route::get('/', function () {
//    return view('welcome');
//});
