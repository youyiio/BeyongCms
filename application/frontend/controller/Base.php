<?php
namespace app\frontend\controller;

use think\Controller;
use think\facade\Session;

class Base extends Controller
{

    //空操作：系统在找不到指定的操作方法的时候，会定位到空操作
    public function _empty()
    {
        return $this->fetch('public/404');
    }

    public function initialize()
    {
        parent::initialize();
        if (!session('uid') && !Session::has('visitor', config('session.prefix'))) {
            $ip = request()->ip(0, true);
            $visitor = '游客-' . ip_to_address($ip, 'province,city');
            Session::set('visitor', $visitor, config('session.prefix'));
        }
    }

}