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
});

//手机APP
//注册
Route::get('/register','Pass\PassController@register');
Route::post('/doreg','Pass\PassController@doreg');
//登录
Route::get('/login','Pass\PassController@login');
Route::post('/dologin','Pass\PassController@dologin');
//PC端
Route::get('/pcregister','Pass\PassPcController@register');
Route::post('/pcdoreg','Pass\PassPcController@doreg');
//登录
Route::get('/pclogin','Pass\PassPcController@login');
Route::post('/pcdologin','Pass\PassPcController@dologin');


