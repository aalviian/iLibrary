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
Route::group(['middleware' => 'web'], function () {
	Route::get('/', 'HomeController@index');
    Route::get('/home', 'GuestController@index');
    Route::auth();

    Route::get('auth/verify/{token}', 'Auth\RegisterController@verify');

    Route::get('/mail', 'HomeController@mail');

	Route::get('guest/{book}/borrow', [
		'middleware'=>['auth','role:member'],
		'as'=>'guest.books.borrow',
		'uses'=>'GuestController@borrow'
	]);

	Route::put('guest/{book}/return', [
		'middleware'=>['auth','role:member'],
		'as'=>'guest.books.return',
		'uses'=>'GuestController@returned'
	]);

	Route::get('settings/profile', 'SettingsController@profile');
	Route::get('settings/profile/edit','SettingsController@edit');
	Route::post('settings/profile','SettingsController@update');

	Route::group(['prefix'=>'admin', 'middleware'=>['auth','role:admin']], function () {
		Route::resource('authors', 'AuthorsController');
		Route::resource('books', 'BooksController');
	});
});


