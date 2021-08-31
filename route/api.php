<?php

use think\facade\Route;


Route::group('api', function () {
    
    Route::rule("sign/login", 'api/Sign/login', 'post|get');
    Route::rule("sign/register", 'api/Sign/register', 'post|get');
    Route::rule("sign/logout", 'api/Sign/logout', 'post|get');

    Route::rule("user/getInfo", 'api/User/getInfo', 'get');

    Route::rule("post/create", 'api/Post/create', 'post');
    Route::rule("post/:aid", 'api/Post/query', 'get');    
    Route::rule("post/:aid", 'api/Post/edit', 'post');
    Route::rule("post/:aid", 'api/Post/delete', 'delete');

    Route::rule("post/list/:page", 'api/Post/list', 'get');

    // 定义miss路由
    Route::miss('api/Base/miss');
})->ext(false)->header('Access-Control-Allow-Headers', 'token')
->allowCrossDomain()->pattern(['aid' => '\d+']);