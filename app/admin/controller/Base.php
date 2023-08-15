<?php

namespace app\admin\controller;

use app\api\library\RolePermission;
use app\common\controller\BaseController;
use app\common\model\MenuModel;
use think\facade\Cache;
use think\helper\Time;
use app\common\model\UserModel;
use think\facade\Config;
use think\facade\View;

/**
 * 基础控制器
 */
class Base extends BaseController
{
    protected $uid;

    public function initialize()
    {
        //判断登陆session('uid')
        $uid = session('uid');
        if (!$uid) {
            if (request()->isAjax()) {
                $this->error('请重新登陆', app('http')->getName() . '/Sign/login');
            }
            $this->redirect(app('http')->getName() . '/Sign/index');
        }

        $this->uid = $uid;
        //实现用户单个端登录，方法: 通过判断cookie和服务器cache的login_hash值
        $localLoginHash = cookie($uid . CACHE_SEPARATOR . 'login_hash');
        $cacheLoginHash = cache($uid . CACHE_SEPARATOR . 'login_hash');
        if ($localLoginHash != $cacheLoginHash) {
            if (request()->isAjax()) {
                $this->error('请重新登陆', app('http')->getName() . '/Sign/login');
            } else {
                redirect(app('http')->getName() . '/Sign/index');
            }
        }

        //用户有请求操作时，session时间重置
        $expire = Config::get('session.expire'); //缓存期限
        session('uid', $uid);
        cookie('uid', $uid, $expire);

        //权限验证
        if (config('cms.auth_on') == 'on') {
            $permission = app('http')->getName() . '/' . request()->controller() . '/' . request()->action();
            $permission = strtolower($permission);
            $rolePermission = new RolePermission();
            $module = app('http')->getName();
            if (!$rolePermission->checkPermission($uid, $permission, $module, 'path')) {
                $this->error('没有访问权限', 'javascript:void(0);');
            }
        }

        //用户信息
        $myself = UserModel::find($uid);
        $this->assign('myself', $myself);

        //昨日新增用户
        $UserModel = new UserModel();
        $beginYesterday = date("Y-m-d 00:00:00", strtotime("-1 day"));
        $endYesterday = date("Y-m-d 23:59:59", strtotime("-1 day"));
        $yesterdayNewUserCount = $UserModel->cache('yesterdayNewUserCount', time_left())->whereTime('register_time', 'between', [$beginYesterday, $endYesterday])->count();
        $this->assign('yesterdayNewUserCount', $yesterdayNewUserCount);

        //菜单数据,Cache::tag不支持redis
        if (Cache::has($uid . '_menu')) {
            $menus = Cache::get($uid . '_menu');
        } else {
            $MenuModel = new MenuModel();
            $menus = $MenuModel->getTreeDataBelongsTo('level', 'sort, id', 'path', 'id', 'pid', 'admin');
            Cache::set($uid . '_menu', $menus);
        }
        View::assign('menus', $menus);
    }

    //兼容swoole的request->url()处理
    protected function url()
    {
        if ($this->request->isCli()) {
            $scheme = $this->request->header('scheme') ? $this->request->header('scheme') : $this->request->scheme();
            return $scheme . '://' . $this->request->header('x-original-host') . $this->request->server('REQUEST_URI');
        } else { //cgi
            return $this->request->url(true);
        }
    }

    //空操作：系统在找不到指定的操作方法的时候，会定位到空操作
    public function _empty()
    {
        return view('public/404');
    }
}