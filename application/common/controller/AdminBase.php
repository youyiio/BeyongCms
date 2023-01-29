<?php
namespace app\common\controller;

use app\api\library\RolePermission;
use app\common\model\MenuModel;
use think\facade\Cache;
use app\common\model\UserModel;
use think\facade\Config;
use think\facade\Env;
use think\facade\Cookie;
use think\facade\Session;

/**
 * Trait 管理后台Base Controller 组件
 * @package app\common\controller
 */
trait AdminBase
{
    //继承的模块可能不是admin模块下，Session/Cookie/Cache需要主动指定prefix
    //protected $prefix = "admin_";
    protected $sessionConfig = [];
    protected $cookieConfig = [];
    protected $cacheConfig = []; //框架自动根据配置自动加prefix

    protected $uid;

    public function initialize()
    {   
        $adminPath = Env::get('app_path') . DIRECTORY_SEPARATOR . 'admin';
        $sessionConfig = require($adminPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'session.php');
        $cookieConfig = require($adminPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'cookie.php');
        $cacheConfig = require($adminPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'cache.php');
        Config::load($adminPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'cache.php', 'cache');

        //判断登陆session的uid
        $uid = Session::get('uid', $sessionConfig['prefix']);
        if (!$uid) {
            if (request()->isAjax()) {
                $this->error('请重新登陆', url('admin/Sign/login'));
            }
            $this->redirect('admin/Sign/index', ['redirect' => urlencode($this->url())]);
        }
        $this->uid = $uid;

        //实现用户单个端登录，方法: 通过判断cookie和服务器cache的login_hash值
        // $localLoginHash = Cookie::get($uid . CACHE_SEPARATOR . 'login_hash', $cookieConfig['prefix']);
        // $cacheLoginHash = Cache::get($uid . CACHE_SEPARATOR . 'login_hash');
        // if ($localLoginHash != $cacheLoginHash) {
        //     if (request()->isAjax()) {
        //         $this->error('请重新登陆', url('admin/Sign/index'));
        //     } else {
        //         $this->redirect('admin/Sign/index', ['redirect' => urlencode($this->url())]);
        //     }
        // }

        //用户有请求操作时，session时间重置
        $expire = $sessionConfig['expire'];//缓存期限
        Session::set('uid', $uid, $sessionConfig['prefix']);
        Cookie::set('uid', $uid, ["expire" => $expire, "prefix" => $cookieConfig['prefix']]);

        //权限验证
        if (config('cms.auth_on') == 'on') {
            $permission = request()->module() . '/' . request()->controller() . '/' . request()->action();
            $permission = strtolower($permission);
            $rolePermission = new RolePermission();
            $module = request()->module();
            $module = $module == 'api' ? 'api' : 'admin';
            if (!$rolePermission->checkPermission($uid, $permission, $module, 'path')) {
                $this->error(
                    '没有访问权限',
                    'javascript:void(0);'
                );
            }
        }

        //用户信息
        if (Cache::has($uid . '_myself')) {
            $myself = Cache::get($uid . '_myself');
        } else {
            $myself = UserModel::get($uid);
            Cache::set($uid . '_myself', $myself);
        }        
        $this->assign('myself', $myself);

     
        //菜单数据,Cache::tag不支持redis
        if (Cache::has($uid . '_menu')) {
            $menus = Cache::get($uid . '_menu');
        } else {
            $MenuModel = new MenuModel();
            $menus = $MenuModel->getTreeDataBelongsTo('level', 'sort, id', 'path', 'id', 'pid', 'admin');
            Cache::set($uid . '_menu',
                $menus
            );
        }
        $this->assign('menus', $menus);
    }

    //兼容swoole的request->url()处理
    protected function url()
    {
        if ($this->request->isCli()) {
            $scheme = $this->request->header('scheme') ? $this->request->header('scheme') : $this->request->scheme();
            return $scheme . '://' . $this->request->header('x-original-host') . $this->request->server('REQUEST_URI');
        } else {//cgi
            return $this->request->url(true);
        }
    }

    //空操作：系统在找不到指定的操作方法的时候，会定位到空操作
    public function _empty()
    {
        return $this->fetch('admin/public/404');
    }
}