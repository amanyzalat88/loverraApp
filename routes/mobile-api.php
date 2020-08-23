<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['namespace' => 'Mobileapi'], function () {
	// Users 
		Route::POST('/login', 'CustomerController@login');
		Route::POST('/register', 'CustomerController@create');
		Route::POST('/forgetpassword', 'CustomerController@forgetPassword');
	

	//Category
		Route::GET('/category', 'CategoryController@show');
		Route::GET('/maincategory', 'CategoryController@getMainCategory');
	    Route::GET('/subcategory/{parent}', 'CategoryController@getSubCategory');


	// Prodcut
		Route::GET('/product/{catId}', 'ProductController@show');
		Route::GET('/product/search/{word}/{category_id}', 'ProductController@search');
		Route::GET('/product/detail/{id}', 'ProductController@detail');
	    Route::GET('/latest', 'ProductController@latest');
	    Route::GET('/offers', 'ProductController@offers');
		Route::GET('/soldout', 'ProductController@soldout');
		
	// Slider
	    Route::GET('/slider', 'SliderController@index');

	// Ads
	    Route::GET('/ads/{id}', 'AdsController@index');
	 
	// Payment
	   Route::GET('/payment', 'PaymentMethodController@index');

    //contactus 
		Route::POST('/contactus', 'ContactusController@store');
		
	//setting 
		Route::GET('/setting', 'SettingController@index');
		
	//Gifts 
	Route::GET('/boxes', 'BoxesController@boxes');
	Route::GET('/color', 'BoxesController@color');
	Route::GET('/cards', 'BoxesController@cards');
	Route::DELETE('/gifts/delete', 'BoxesController@delete');

	//soldout
	Route::POST('/soldout', 'SoldoutController@store');

	//country
	Route::GET('/country', 'CountryController@index');
});

Route::group(['middleware' => 'auth:customerapi', 'namespace' => 'Mobileapi'], function () {

	//customer
		Route::POST('/customer', 'CustomerController@store');
		Route::POST('/changepassword', 'CustomerController@changePassword');
		Route::GET('/customer', 'CustomerController@show');
	
	//order
	    Route::POST('/order', 'OrderController@index');
		Route::POST('/order/detail', 'OrderController@show');

	//favorite
		Route::GET('/favorite', 'FavoriteController@index');
		Route::POST('/favorite', 'FavoriteController@store');
		Route::DELETE('/favorite', 'FavoriteController@delete');


	//carts
	   Route::POST('/carts', 'CartController@index');
	   Route::POST('/reorder', 'CartController@reorder');
	   Route::POST('/showcart', 'CartController@show');
	   Route::DELETE('/carts', 'CartController@delete');
	   Route::POST('/carts/count', 'CartController@count');


	//Address 
	   Route::POST('/address', 'CustomerAddressController@store');
	   Route::POST('/addressupdate', 'CustomerAddressController@update');
	   Route::GET('/address', 'CustomerAddressController@index');
	   Route::DELETE('/address', 'CustomerAddressController@delete');

	 //gifts
	 Route::POST('/gifts', 'BoxesController@create');
	 
});



