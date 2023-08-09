<?php

namespace app\frontend\controller;

use app\common\controller\BaseController;
use think\facade\View;

class Base extends BaseController
{

    //空操作：系统在找不到指定的操作方法的时候，会定位到空操作
    public function _empty()
    {
        return View::fetch('public/404');
    }

    public function initialize()
    {
        parent::initialize();
        if (!session('uid') && !session('visitor')) {
            $ip = request()->ip(0, true);
            $visitor = '游客-' . ip_to_address($ip, 'province,city');
            session('visitor', $visitor);
        }
    }
}
