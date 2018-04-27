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

Route::get('/test',function (){
//    $redis=new \Redis();
//    $redis->connect('127.0.0.1','6379');
////        echo 1;
//    $redis->set('name','zhangsan');
//    echo '<pre>';
//    var_dump($redis->get('name'));exit;
//    \Illuminate\Support\Facades\Redis::set('name','zhangsan');
//    return '';
});
