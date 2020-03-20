<?php
/**
 * @package Zammad API Wrapper
 * @author  Jordan GOBLET <jordan.goblet.pro@gmail.com>
 */
Route::get('test', function(){
    return view('test::menu');
});

Route::post('createTicket', 'Dogteam\Zammad\Controller\TestController@createTicket')->name('createTicket');
Route::post('find', 'Dogteam\Zammad\Controller\TestController@find')->name('find');
Route::post('search', 'Dogteam\Zammad\Controller\TestController@search')->name('search');
