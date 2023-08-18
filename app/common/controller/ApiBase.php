<?php

namespace app\common\controller;

use app\api\library\RolePermission;
use app\common\library\ResultCode;
use think\facade\Request;
use app\common\exception\JwtException;

/**
 * Trait api接口 Base Controller 组件
 * 使用方法：use \app\common\controller\ApiBase;
 * @package app\common\controller
 */
trait ApiBase
{
    // 用户信息
    protected $user_info;

    public function initialize()
    {
        $url = strtolower(Request::url());
        if (in_array($url, config('jwt.jwt_action_excludes'))) {
            return true;
        }

        $authorization = Request::header('authorization');
        if (!$authorization) {
            throw new JwtException(ResultCode::E_TOKEN_EMPTY, 'TOKEN参数缺失！', 'E_TOKEN_EMPTY');
        }

        $type = substr($authorization, 0, 6);
        if ($type !== 'Bearer') {
            throw new JwtException(ResultCode::E_TOKEN_INVALID, 'TOKEN类型错误！', 'E_TOKEN_INVALID');
        }

        $token = Request::header('authorization');

        // 验证是否登录
        if (is_null($token)) {
            header('Content-Type:application/json; charset=utf-8');
            $response = json_encode([
                'code'  => ResultCode::E_TOKEN_EMPTY,
                'message' => '用户未登陆'
            ]);
            exit($response);
        }

        // 验证登录是否过期
        $user_info = \getJWT($token);
        if (is_null($user_info)) {
            header('Content-Type:application/json; charset=utf-8');

            $response = json_encode([
                'code'  => ResultCode::E_TOKEN_EXPIRED,
                'message' => '登录已过期'
            ]);
            exit($response);
        }

        // 存储用户信息
        $this->user_info = $user_info;

        //Api权限验证      
        if (config('jwt.jwt_auth_on') !== 'off') {
            $uid = $user_info->uid;
            $permission = app('http')->getName() . '/' . request()->controller() . '/' . request()->action();
            $permission = strtolower($permission);
            $rolePermission = new RolePermission();
            $module = app('http')->getName();
            //$module = $module == 'api' ? 'api' : 'admin';
            if (!$rolePermission->checkPermission($uid, $permission, $module, 'path')) {
                $response = json_encode([
                    'code'  => ResultCode::E_ACCESS_LIMIT,
                    'message' => "$permission 没有访问权限"
                ]);
                exit($response);
            }
        }
    }
}