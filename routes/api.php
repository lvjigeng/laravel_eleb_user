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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//商家列表接口
Route::get('/shops','ApiController@shops');
//商家详情接口
Route::get('/detail','ApiController@detail');
//短信
Route::get('/sms','ApiController@sms');
//注册接口
Route::post('/regist','ApiController@regist');
//登录接口
Route::post('/loginCheck','ApiController@loginCheck');
//修改密码接口
Route::post('/changePassword','ApiController@changePassword');
//忘记密码接口
Route::post('/forgetPassword','ApiController@forgetPassword');
//地址列表接口
Route::get('/addressList','ApiController@addressList');
//添加地址接口
Route::post('/addAddress','ApiController@addAddress');
//修改回显地址接口
Route::get('/address','ApiController@address');
//修改保存地址接口
Route::post('/editAddress','ApiController@editAddress');
//添加购物车址接口
Route::post('/addCart','ApiController@addCart');
//获取购物车数据接口
Route::get('/cart','ApiController@cart');
//添加订单接口
Route::post('/addOrder','ApiController@addOrder');
//订单详情接口
Route::get('/order','ApiController@order');
//订单列表接口
Route::get('/orderList','ApiController@orderList');






