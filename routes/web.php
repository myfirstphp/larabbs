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

Route::get('/', 'PagesController@root')->name('root');


//the frame auto create route
//Email Verification Routes 默认是不展开,要展开的话需要加上['verify' => true]
Auth::routes(['verify' => true]);

Route::resource('users', 'UsersController', ['only' => ['show', 'update', 'edit']]);



