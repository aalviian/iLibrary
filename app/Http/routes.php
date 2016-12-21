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

	Route::get('guest/{book}/borrow', [
		'middleware'=>['auth','role:member'],
		'as'=>'guest.books.borrow',
		'uses'=>'GuestController@borrow'
	]);

	Route::get('mail', 'HomeController@mail');

	Route::put('books/{book}/return', [
		'middleware'=>['auth','role:member'],
		'as'=>'guest.books.return',
		'uses'=>'GuestController@returned'
	]);

	Route::group(['prefix'=>'admin', 'middleware'=>['auth','role:admin']], function () {
		Route::resource('authors', 'AuthorsController');
		Route::resource('books', 'BooksController');
	});
});


