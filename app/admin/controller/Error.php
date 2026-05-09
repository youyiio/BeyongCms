<?php
/**
 * Created by VSCode.
 * User: cattong
 * Date: 2018-03-16
 * Time: 12:20
 */

namespace app\admin\controller;

use think\facade\View;

/**
 * 空控制器,空操作
 * 'empty_controller'       => 'Error',
 */
class Error
{
    public function __call($method, $args)
    {
        return View::fetch('public/404');
    }
}