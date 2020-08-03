<?php

use Illuminate\Support\Facades\Route;

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
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::match(['GET', 'POST'], 'phone/verify', array('as' => 'phone.verify', 'uses' => 'UserController@verify'));
Route::match(['POST'], 'user/update', array('as' => 'user.update', 'uses' => 'UserController@update'));
Route::match(['POST'], 'user/resend', array('as' => 'user.resend', 'uses' => 'UserController@resend'));
