<?php
/**
 * frontend模块路由，针对seo优化进行设置
 * User: cattong
 * Date: 2019-02-18
 * Time: 11:35
 */

return [

    /*****************frontend 通用路由 begin*******************/
    //首页
    'index/index' => ['frontend/Index/index', ['method'=>'get']],
    'index/business' => ['frontend/Index/business', ['method'=>'get']],
    'index/team' => ['frontend/Index/team', ['method'=>'get']],
    'index/partner' => ['frontend/Index/partner', ['method'=>'get']],
    'index/about' => ['frontend/Index/about', ['method'=>'get']],
    'index/contact' => ['frontend/Index/contact', ['method'=>'get']],
    'index/:name' => ['frontend/Index/__extPage', ['method'=>'get']], //可动态扩充页面

    /*****************frontend 通用路由 end*******************/
];