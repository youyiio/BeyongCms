<?php

use think\facade\Route;


Route::group('api', function () {
    
    Route::rule("sign/login", 'api/Sign/login', 'post|get');
    Route::rule("sign/register", 'api/Sign/register', 'post|get');
    Route::rule("sign/logout", 'api/Sign/logout', 'post|get');
    Route::rule("sign/reset", 'api/Sign/reset', 'post|get');

    Route::rule("user/getInfo", 'api/User/getInfo', 'get');

    Route::rule("article/create", 'api/Article/create', 'post');
    Route::rule("article/:aid", 'api/Article/query', 'get');    
    Route::rule("article/:aid", 'api/Article/edit', 'post');
    Route::rule("article/delete", 'api/Article/delete', 'delete');
    Route::rule("article/publish", 'api/Article/publish', 'post');
    Route::rule("article/audit", 'api/Article/audit', 'post');
    Route::rule("article/comments/:id", 'api/Article/comments', 'get|post');

    Route::rule("article/list", 'api/Article/list', 'get|post');

    Route::rule("comment/list", 'api/Comment/list', 'get|post');
    Route::rule("comment/:cid", 'api/Comment/query', 'get');
    Route::rule("comment/create", 'api/Comment/create', 'post');
    Route::rule("comment/audit", 'api/Comment/audit', 'post');
    Route::rule("comment/delete", 'api/Comment/delete', 'delete');

    Route::rule("category/list", 'api/Category/list', 'get|post');
    Route::rule("category/create", 'api/Category/create', 'post');
    Route::rule("category/edit", 'api/Category/edit', 'post');
    Route::rule("category/setStatus", 'api/Category/setStatus', 'post');
    Route::rule("category/:id", 'api/Category/delete', 'delete');

    Route::rule("ad/list", 'api/Ad/list', 'get|post');
    Route::rule("ad/slots", 'api/Ad/slots', 'get');
    Route::rule("ad/create", 'api/Ad/create', 'post');
    Route::rule("ad/edit", 'api/Ad/edit', 'post');
    Route::rule("ad/:id", 'api/Ad/delete', 'delete');
    
    Route::rule("sms/sendCode", 'api/Sms/sendCode', 'post');
    Route::rule("sms/login", 'api/Sms/login', 'post');

    Route::rule("ad/carousel", 'api/Ad/carousel', 'get');

    // 定义miss路由
    Route::miss('api/Base/miss');


})->ext(false)->header('Access-Control-Allow-Headers', 'token')
->allowCrossDomain()->pattern(['aid' => '\d+']);