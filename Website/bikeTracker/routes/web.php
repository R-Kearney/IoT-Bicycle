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

Route::get('/', function () {
    return view('welcome');
})->name('welcome');



Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Auth::routes();

Route::get('/home', array(
    'uses' => 'Bike@view'
))->name('home');

Route::put('/updateBike', array(
 'uses' => 'Bike@update'
));

Route::get('/editBike', array(
    'uses' => 'Bike@edit'
))->name('editBike');
