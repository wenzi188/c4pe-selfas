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

/*Route::get('/', function () {
    return view('welcome');
});*/


Auth::routes(['register' => false, 'reset' => false]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'HomeController@index')->name('home');
Route::get('/formList/{id}', 'HomeController@formList')->name('home.formList');
Route::get('/form/{oid}/{qid}/{pid}', 'FormController@index')->name('form.index');
Route::post('/form/{qid}/{pid}/store', 'FormController@store')->name('form.store');


Route::get('/questionnaireList', 'QuestionnaireController@index')->name("questionnaire.list");
Route::get('/admin', 'QuestionnaireController@index')->name("questionnaire.list");

Route::get('/questionnaire/{id}/edit', 'QuestionnaireController@edit')->name("questionnaire.edit");
Route::post('/questionnaire/{id}/store', 'QuestionnaireController@store')->name("questionnaire.store");
Route::get('/questionnaire/{qid}/{pid}/statistics', 'QuestionnaireController@statistics')->name('questionnaire.statistics');
Route::get('/questionnaire/{qid}/{pid}/downloadCSV', 'QuestionnaireController@downloadCSV')->name('questionnaire.downloadCSV');
Route::post('/questionnaire/{id}/remove', 'QuestionnaireController@remove')->name("questionnaire.remove");

Route::get('/periodList/{id}', 'QuestionnaireController@periodList')->name("questionnaire.periodList");
Route::post('/period/{qid}/{pid}/store', 'QuestionnaireController@periodStore')->name("period.store");
Route::get('/period/{qid}/{pid}/delete', 'QuestionnaireController@periodDelete')->name("period.delete");

Route::get('users', 'UserController@index')->name('user.index');
Route::get('user/{user}/edit', 'UserController@edit')->name('user.edit');
Route::post('user/{user}/update', 'UserController@update')->name('user.update');


Route::post('user/{user}/updatePassword', 'UserController@updatePassword')->name('user.updatePassword');
Route::post('user/{user}/destroy', 'UserController@destroy')->name('user.destroy');  // for admin only
Route::get('user/{user}/editSettings', 'UserController@editSettings')->name('user.editSettings');
Route::post('user/{user}/updateSettings', 'UserController@updateSettings')->name('user.updateSettings');
Route::post('user/{user}/deleteFromSettings', 'UserController@deleteFromSettings')->name('user.deleteFromSettings');

Route::get('/impressum', 'HomeController@impressum')->name('home.impressum');