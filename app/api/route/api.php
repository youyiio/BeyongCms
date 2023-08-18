<?php

use think\facade\Route;


Route::group(function () {

    //通用公共接口
    Route::rule('config/query', 'api/admin.Config/query', 'post');
    Route::rule('config/:name/status', 'api/admin.Config/status', 'get');
    Route::rule("ad/carousel", 'api/admin.Ad/carousel', 'get');
    Route::rule('image/upload', 'api/admin.Upload/image', 'post');
    Route::rule('file/upload', 'api/admin.Upload/file', 'post');
    Route::rule('user/quickSelect', 'api/admin.User/quickSelect', 'get|post');

    //登录注册相关
    Route::rule("sign/login", 'api/admin.Sign/login', 'post|get');
    Route::rule("sign/register", 'api/admin.Sign/register', 'post|get');
    Route::rule("sign/logout", 'api/admin.Sign/logout', 'post|get');
    Route::rule("sign/reset", 'api/admin.Sign/reset', 'post|get');

    //用户管理相关
    Route::rule("user/:id", 'api/admin.User/query', 'get');
    Route::rule("user/list", 'api/admin.User/list', 'get|post');
    Route::rule("user/create", 'api/admin.User/create', 'post');
    Route::rule("user/edit", 'api/admin.User/edit', 'post');
    Route::rule("user/:id", 'api/admin.User/delete', 'delete');
    Route::rule("user/modifyPassword", 'api/admin.User/modifyPassword', 'post');
    Route::rule("user/freeze", 'api/admin.User/freeze', 'post');
    Route::rule("user/unfreeze", 'api/admin.User/unfreeze', 'post');
    Route::rule("user/addRoles", 'api/admin.User/addRoles', 'post');

    //角色管理相关
    Route::rule("role/list", 'api/admin.Role/list', 'get|post');
    Route::rule("role/create", 'api/admin.Role/create', 'post');
    Route::rule("role/edit", 'api/admin.Role/edit', 'post');
    Route::rule("role/:id", 'api/admin.Role/delete', 'delete');
    Route::rule("role/menus/:id", 'api/admin.Role/menus', 'get');
    Route::rule("role/addMenus/:id", 'api/admin.Role/addMenus', 'post');
    Route::rule("role/users/:id", 'api/admin.Role/users', 'get');

    //文章管理相关
    Route::rule("article/list", 'api/admin.Article/list', 'get|post');
    Route::rule("article/create", 'api/admin.Article/create', 'post');
    Route::rule("article/:aid", 'api/admin.Article/query', 'get');
    Route::rule("article/:aid", 'api/admin.Article/edit', 'post');
    Route::rule("article/delete", 'api/admin.Article/delete', 'delete');
    Route::rule("article/publish", 'api/admin.Article/publish', 'post');
    Route::rule("article/audit", 'api/admin.Article/audit', 'post');
    Route::rule("article/comments/:id", 'api/admin.Article/comments', 'get|post');

    //评论相关
    Route::rule("comment/list", 'api/admin.Comment/list', 'get|post');
    Route::rule("comment/:id", 'api/admin.Comment/query', 'get');
    Route::rule("comment/create", 'api/admin.Comment/create', 'post');
    Route::rule("comment/audit", 'api/admin.Comment/audit', 'post');
    Route::rule("comment/delete", 'api/admin.Comment/delete', 'delete');

    //个人中心相关
    Route::rule("ucenter/getInfo", 'api/admin.Ucenter/getInfo', 'get');
    Route::rule("ucenter/profile", 'api/admin.Ucenter/profile', 'post');
    Route::rule("ucenter/menus", 'api/admin.Ucenter/menus', 'get');
    Route::rule("ucenter/modifyPassword", 'api/admin.Ucenter/modifyPassword', 'post');

    //文章分类相关
    Route::rule("category/list", 'api/admin.Category/list', 'get|post');
    Route::rule("category/create", 'api/admin.Category/create', 'post');
    Route::rule("category/edit", 'api/admin.Category/edit', 'post');
    Route::rule("category/setStatus", 'api/admin.Category/setStatus', 'post');
    Route::rule("category/:id", 'api/admin.Category/delete', 'delete');

    //广告相关
    Route::rule("ad/list", 'api/admin.Ad/list', 'get|post');
    Route::rule("ad/slots", 'api/admin.Ad/slots', 'get');
    Route::rule("ad/create", 'api/admin.Ad/create', 'post');
    Route::rule("ad/edit", 'api/admin.Ad/edit', 'post');
    Route::delete("ad/:id", 'api/admin.Ad/delete');

    Route::rule("sms/sendCode", 'api/admin.Sms/sendCode', 'post');
    Route::rule("sms/login", 'api/admin.Sms/login', 'post');

    //运维管理相关
    Route::rule("server/status", 'api/admin.Server/status', 'get');
    Route::rule("log/list", 'api/admin.Log/list', 'get|post');
    Route::rule("db/tables", 'api/admin.Database/tables', 'get|post');

    //友链相关
    Route::rule("link/list", 'api/admin.Link/list', 'get|post');
    Route::rule("link/create", 'api/admin.Link/create', 'post');
    Route::rule("link/edit", 'api/admin.Link/edit', 'post');
    Route::rule("link/:id", 'api/admin.Link/delete', 'delete');

    //菜单管理相关
    Route::rule("menu/list", 'api/admin.Menu/list', 'get|post');
    Route::rule("menu/create", 'api/admin.Menu/create', 'post');
    Route::rule("menu/edit", 'api/admin.Menu/edit', 'post');
    Route::rule("menu/:id", 'api/admin.Menu/delete', 'delete');

    //字典管理相关
    Route::rule("config/list", 'api/admin.Config/list', 'get|post');
    Route::rule("config/create", 'api/admin.Config/create', 'post');
    Route::rule("config/edit", 'api/admin.Config/edit', 'post');
    Route::rule("config/:id", 'api/admin.Config/delete', 'delete');
    Route::rule("config/query", 'api/admin.Config/query', 'post');

    Route::rule("log/list", 'api/admin.Log/list', 'get|post');

    //移动端通用公共接口
    Route::rule('app/config/:name/status', 'api/app.Config/status', 'get');
    Route::rule('app/config/base', 'api/app.Config/base', 'get');
    Route::rule('app/carousel', 'api/app.Config/carousel', 'post');

    //移动端公共管理相关
    Route::rule('app/article/timeline', 'api/app.Article/timeline', 'get|post');
    Route::rule('app/article/latest', 'api/app.Article/latest', 'get|post');
    Route::rule('app/article/hottest', 'api/app.Article/hotTest', 'get|post');
    Route::rule('app/article/:aid', 'api/app.Article/query', 'get');
    Route::rule('app/article/comments/:aid', 'api/app.Article/comments', 'get|post');
    Route::rule('app/article/related/:aid', 'api/app.Article/related', 'get|post');
    Route::rule('app/category/list', 'api/app.Article/categoryList', 'get|post');
    Route::rule('app/link/list', 'api/app.Article/linkList', 'get|post');

    // 定义miss路由
    Route::miss('api/Base/miss');
})->ext(false)->header('Access-Control-Allow-Headers', 'token')
    ->allowCrossDomain()->pattern(['aid' => '\d+']);
