<?php

namespace app\common\behavior;

use think\facade\Request;
use think\facade\Env;
use think\facade\View;

class DefaultBehavior
{

    public function handle($event)
    {
        //hack swoole下，cms view::config导致其他模块延迟共用
        $viewPath = app_path() . DIRECTORY_SEPARATOR . app('http')->getName() . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
        View::config('view_path', $viewPath);
    }
}
