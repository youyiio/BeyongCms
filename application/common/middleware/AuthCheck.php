<?php

namespace app\common\middleware;

use app\api\library\RolePermission;
use app\common\exception\JwtException;
use app\common\library\ResultCode;

class AuthCheck
{

    public function handle($request, \Closure $next)
    {

        $user_info = session('jwt_payload_data');
        if (empty($user_info)) {
            throw new JwtException(ResultCode::E_TOKEN_EXPIRED, '登录已过期！', 'E_TOKEN_EXPIRED');
        }

        $uid = $user_info->uid;

        $permission = request()->module() . ":" . request()->controller() . ':' . request()->action();
        $permission = strtolower($permission);
        $rolePermission = new RolePermission();
        $module = config('jwt.api_module') ?? "api";
        if (!$rolePermission->checkPermission($uid, $permission, $module, 'permission')) {
            throw new JwtException(ResultCode::E_ACCESS_LIMIT, "$permission 没有访问权限", 'E_ACCESS_LIMIT');
        }

        return $next($request);
    }
}
