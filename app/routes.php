<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// This handles the image generation
Route::get ( '/' , 'LastfmController@leader' );

// We're now handling any BBCode generation forms
Route::get ( '/generator' , function () {
	return View::make ( 'form' );
} );

Route::group ( [ 'before' => 'csrf' ] , function () {
	Route::post ( '/generator' , 'LastfmController@formProcessor' );
} );