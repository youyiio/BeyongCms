<?php
// +----------------------------------------------------------------------
// | ThinkPHP 5 [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.zzstudio.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Byron Sampson <xiaobo.sun@gzzstudio.net>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;
use think\facade\Config;
use think\Session;
use think\Request;
use think\Loader;

class Auth
{
    /**
     * @var object 对象实例
     */
    protected static $instance;
    /**
     * 当前请求实例
     * @var Request
     */
    protected $request;

    //默认配置
    // protected $config = [
    //     'auth_on' => 1, // 权限开关
    //     'auth_type' => 1, // 认证方式，1为实时认证；2为登录认证。
    //     'auth_group' => 'cms_auth_group', // 用户组数据表名
    //     'auth_group_access' => 'cms_auth_group_access', // 用户-用户组关系表
    //     'auth_rule' => 'cms_auth_rule', // 权限规则表
    //     'auth_user' => 'member', // 用户信息表
    // ];

    protected $config = [
        'auth_on' => 1, // 权限开关
        'auth_type' => 1, // 认证方式，1为实时认证；2为登录认证。
        'role' => 'sys_role', // 角色表名
        'user_role' => 'sys_user_role', // 用户-用户组关系表
        'role_menu' => 'sys_role_menu', // 角色-权限关系表
        'menu' => 'sys_menu', // 权限规则表
        'user' => 'sys_user', // 用户信息表
    ];
    /**
     * 类架构函数
     * Auth constructor.
     */
    public function __construct($option = [])
    {
        if (empty($option)) {
            //可设置配置项 auth, 此配置项为数组。
            $authConfig = Config::get('auth.');
            if ($authConfig) {
                $this->config = array_merge($this->config, $authConfig);
            }
        } else {
            $this->config = array_merge($this->config, $option);
        }

        // 初始化request
        $this->request = request();
    }

    /**
     * 初始化
     * @access public
     * @param array $options 参数
     * @return \think\Request
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    /**
     * 检查权限
     * @param $name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
     * @param $uid  int           认证用户的id
     * @param int $type 认证类型
     * @param string $mode 执行check的模式
     * @param string $relation 如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
     * @return bool               通过验证返回true;失败返回false
     */
    public function check($name, $uid, $type = 1, $mode = 'url', $relation = 'or')
    {
        if (!$this->config['auth_on']) {
            return true;
        }
        // 获取用户需要验证的所有有效规则列表
        $authList = $this->getAuthList($uid, $type);
        if (is_string($name)) {
            $name = strtolower($name);
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = [$name];
            }
        }
        $list = []; //保存验证通过的规则名
        if ('url' == $mode) {
            $REQUEST = unserialize(strtolower(serialize($this->request->param())));
        }
        foreach ($authList as $auth) {
            $query = preg_replace('/^.+\?/U', '', $auth);
            if ('url' == $mode && $query != $auth) {
                parse_str($query, $param); //解析规则中的param
                $intersect = array_intersect_assoc($REQUEST, $param);
                $auth = preg_replace('/\?.*$/U', '', $auth);
                if (in_array($auth, $name) && $intersect == $param) {
                    //如果节点相符且url参数满足
                    $list[] = $auth;
                }
            } else {
                if (in_array($auth, $name)) {
                    $list[] = $auth;
                }
            }
        }
        if ('or' == $relation && !empty($list)) {
            return true;
        }
        $diff = array_diff($name, $list);
        if ('and' == $relation && empty($diff)) {
            return true;
        }

        return false;
    }

    /**
     * 根据用户id获取用户组,返回值为数组
     * @param  $uid int     用户id
     * @return array       用户所属的用户组 array(
     *     array('uid'=>'用户id','group_id'=>'用户组id','title'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开'),
     *     ...)
     */
    public function getRoles($uid)
    {
        static $roles = [];
        if (isset($roles[$uid])) {
            return $roles[$uid];
        }
        // 转换表名
        $user_role = Loader::parseName($this->config['user_role'], 0);
        $role_menu = Loader::parseName($this->config['role_menu'], 0);
        $role = Loader::parseName($this->config['role'], 0);
        // 执行查询
        $roles = Db::view($user_role, 'uid,role_id')
            ->view($role, 'title', "{$user_role}.role_id={$role}.id", 'LEFT')
            ->view($role_menu, 'menu_id', "{$role_menu}.role_id={$role}.id", 'LEFT')
            ->where("{$user_role}.uid='{$uid}' and {$role}.status='1'")
            ->select();
        $roles[$uid] = $roles ?: [];

        return $roles[$uid];
    }

    /**
     * 获得权限列表
     * @param integer $uid 用户id
     * @param integer $type
     * @return array
     */
    protected function getAuthList($uid, $type)
    {
        //        static $_authList = []; //保存用户验证通过的权限列表
        //        $t = implode(',', (array)$type);
        //        if (isset($_authList[$uid . $t])) {
        //            return $_authList[$uid . $t];
        //        }

        $_authList = [];
        $t = implode(',', (array)$type);

        if (2 == $this->config['auth_type'] && Session::has('_auth_list_' . $uid . $t)) {
            return Session::get('_auth_list_' . $uid . $t);
        }
        //读取用户所属角色组
        $roles = $this->getRoles($uid);
        $ids = []; //保存用户所属用户组设置的所有权限规则id
        foreach ($roles as $key => $g) {
            $ids[$key] = array_push($ids, ($g['menu_id']));
        }
     
        $ids = array_unique($ids);
        if (empty($ids)) {
            $_authList[$uid . $t] = [];
            return [];
        }
        $map = array(
            ['id', 'in', $ids],
            ['type', '=', $type],
            ['status', '=', 1],
            ['belongs_to', '=', 'admin'],
        );
        //读取用户组所有权限规则
        $rules = Db::name($this->config['menu'])->where($map)->field('path')->select();
        //循环规则，判断结果。
        $authList = []; //
        foreach ($rules as $rule) {
            if (!empty($rule['condition'])) {
                //根据condition进行验证
                $user = $this->getUserInfo($uid); //获取用户信息,一维数组
                $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                //dump($command); //debug
                @(eval('$condition=(' . $command . ');'));
                if ($condition) {
                    $authList[] = strtolower($rule['path']);
                }
            } else {
                //只要存在就记录
                $authList[] = strtolower($rule['path']);
            }
        }
        $_authList[$uid . $t] = $authList;
        if (2 == $this->config['auth_type']) {
            //规则列表结果保存到session
            Session::set('_auth_list_' . $uid . $t, $authList);
        }

        return array_unique($authList);
    }

    /**
     * 获得用户资料,根据自己的情况读取数据库
     */
    protected function getUserInfo($uid)
    {
        static $userinfo = [];

        $user = Db::name($this->config['user']);
        // 获取用户表主键
        $_pk = is_string($user->getPk()) ? $user->getPk() : 'uid';
        if (!isset($userinfo[$uid])) {
            $userinfo[$uid] = $user->where($_pk, $uid)->find();
        }

        return $userinfo[$uid];
    }
}
