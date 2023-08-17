<?php

/**
 * Cms模块路由，针对seo优化进行设置
 * User: cattong
 * Date: 2019-02-18
 * Time: 11:35
 */

use think\facade\Route;

/*****************Cms 通用路由 begin*******************/

Route::pattern([
    'id' => '\d+',
    'uid' => '\d+',
    'aid' => '\d+',
    'cid' => '\d+',
    'cname' => '\w+',
    'csubname' => '\w+'
]);

//文章
Route::get('list/index', 'frontend/Article/index');
Route::get('list/:cid', 'frontend/Article/articleList');
Route::get('list/:cname/[:csubname]', 'frontend/Article/articleList'); //:cname 为必须参数，csubname加[]为可选参数

Route::get('articles/:cname/:aid', 'frontend/Article/viewArticle'); //与以下规则，不能对换；只匹配最先匹配，而非最优配置
Route::get('article/:aid', 'frontend/Article/viewArticle');
Route::get('tag/:tag', 'frontend/Article/tag')->pattern(['tag' => '\w+']);

//搜索
Route::rule('search/[:q]/[:p]', 'frontend/Search/index', 'get,post')->pattern(['q' => '\w+', 'p' => '\d+']);

//站点地图
Route::get('sitemap.xml', 'frontend/Sitemap/xml');
Route::get('sitemap-[:id].xml', 'frontend/Sitemap/xml');
Route::get('sitemap', 'frontend/Sitemap/html');

/*****************Cms 通用路由 end*******************/
