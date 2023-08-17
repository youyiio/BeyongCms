<?php

/**
 * Created by VSCode.
 * User: cattong
 * Date: 2018-03-16
 * Time: 12:31
 */

namespace app\cms\controller;

use think\facade\View;

/**
 * 空控制器,空操作
 * 'empty_controller'       => 'Error',
 */
class Error
{
    public function _empty()
    {
        return View::fetch('public/404');
    }
}
