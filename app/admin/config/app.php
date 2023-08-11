<?php

return [

    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => 'public/success', //Env::get('think_path') . 'tpl/dispatch_jump.tpl',
    'dispatch_error_tmpl'    => \think\facade\App::getAppPath() . 'admin/view/public/error', //Env::get('think_path') . 'tpl/dispatch_jump.tpl',
    //'exception_tmpl'         => 'public/500.html', //Env::get('think_path') . 'tpl/think_exception.tpl',
];
