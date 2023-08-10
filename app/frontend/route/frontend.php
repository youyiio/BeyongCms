<?php

/**
 * frontend模块路由，针对seo优化进行设置
 * User: cattong
 * Date: 2019-02-18
 * Time: 11:35
 */

use think\facade\Route;

/*****************frontend 通用路由 begin*******************/
//首页
Route::get('index', 'frontend/Index/index');
Route::get('business', 'frontend/Index/business');
Route::get('team', 'frontend/Index/team');
Route::get('partner', 'frontend/Index/partner');
Route::get('about', 'frontend/Index/about');
Route::get('contact', 'frontend/Index/contact');
Route::get('index/:name', 'frontend/Index/__extPage'); //可动态扩充页面

//用户操作
Route::rule('sign/index', 'frontend/Sign/index', 'get,post');
Route::rule('sign/login', 'frontend/Sign/login', 'get,post');
Route::rule('sign/register', 'frontend/Sign/register', 'get,post');
Route::rule('sign/logout', 'frontend/Sign/logout', 'get,post');
Route::rule('sign/forget', 'frontend/Sign/forget', 'get,post');
Route::rule('sign/captcha', 'frontend/Sign/captcha', 'get,post');
Route::rule('sign/sendCode', 'frontend/Sign/sendCode', 'get,post');

/*****************frontend 通用路由 end*******************/
