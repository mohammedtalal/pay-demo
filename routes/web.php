<?php


Route::get('/','PaypalController@view');
Route::post('payments/with-paypal','PaypalController@payWithPaypal')->name('pay');

Route::get('status','PaypalController@status')->name('status');
Route::get('canceled','PaypalController@canceled')->name('canceled');
