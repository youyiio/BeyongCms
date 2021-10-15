<?php

use think\facade\Route;


Route::group('api', function () {
    
    Route::rule("sign/login", 'api/Sign/login', 'post|get');
    Route::rule("sign/register", 'api/Sign/register', 'post|get');
    Route::rule("sign/logout", 'api/Sign/logout', 'post|get');
    Route::rule("sign/reset", 'api/Sign/reset', 'post|get');

    Route::rule("user/getInfo", 'api/User/getInfo', 'get');

    Route::rule("Article/create", 'api/Article/create', 'post');
    Route::rule("Article/:aid", 'api/Article/query', 'get');    
    Route::rule("Article/:aid", 'api/Article/edit', 'post');
    Route::rule("Article/:aid", 'api/Article/delete', 'delete');

    Route::rule("post/list", 'api/Post/list', 'get');

    Route::rule("sms/sendCode", 'api/Sms/sendCode', 'post');
    Route::rule("sms/login", 'api/Sms/login', 'post');

    Route::rule("ad/list", 'api/Ad/list', 'get');
    Route::rule("ad/carousel", 'api/Ad/carousel', 'get');

    // 定义miss路由
    Route::miss('api/Base/miss');


})->ext(false)->header('Access-Control-Allow-Headers', 'token')
->allowCrossDomain()->pattern(['aid' => '\d+']);