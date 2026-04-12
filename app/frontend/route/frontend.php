<?php

/**
 * frontend模块路由，针对seo优化进行设置
 * User: cattong
 * Date: 2019-02-18
 * Time: 11:35
 */

use think\facade\Route;

Route::pattern([
    'id' => '\d+',
    'uid' => '\d+',
    'aid' => '\d+',
    'cid' => '\d+',
    'cname' => '\w+',
    'csubname' => '\w+'
]);

/*****************frontend 通用路由 begin*******************/
//首页
Route::get('index', 'Index/index');
Route::get('business', 'Index/business');
Route::get('team', 'Index/team');
Route::get('partner', 'Index/partner');
Route::get('about', 'Index/about');
Route::get('contact', 'Index/contact');
Route::get('index/:name', 'Index/__extPage'); //可动态扩充页面

//用户操作
Route::rule('sign/index', 'Sign/index', 'get,post');
Route::rule('sign/login', 'Sign/login', 'get,post');
Route::rule('sign/register', 'Sign/register', 'get,post');
Route::rule('sign/logout', 'Sign/logout', 'get,post');
Route::rule('sign/forget', 'Sign/forget', 'get,post');
Route::rule('sign/captcha', 'Sign/captcha', 'get,post');
Route::rule('sign/sendCode', 'Sign/sendCode', 'get,post');

/*****************frontend 通用路由 end*******************/


/*****************Cms 通用路由 begin*******************/

//文章
Route::get('list/index', 'Article/index');
Route::get('list/:cid', 'Article/articleList');
Route::get('list/:cname/[:csubname]', 'Article/articleList'); //:cname 为必须参数，csubname加[]为可选参数

Route::get('articles/:cname/:aid', 'Article/viewArticle'); //与以下规则，不能对换；只匹配最先匹配，而非最优配置
Route::get('article/:aid', 'Article/viewArticle');
Route::get('tag/:tag', 'Article/tag')->pattern(['tag' => '\w+']);

//搜索
Route::rule('search/[:q]/[:p]', 'Search/index', 'get,post')->pattern(['q' => '\w+', 'p' => '\d+']);

//站点地图
Route::get('sitemap.xml', 'Sitemap/xml');
Route::get('sitemap-[:id].xml', 'Sitemap/xml');
Route::get('sitemap', 'Sitemap/html');
/*****************Cms 通用路由 end*******************/
