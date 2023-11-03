<?php

namespace app\common\middleware;

use think\facade\Log;
use think\facade\Request;

use Firebase\JWT\JWT;
use app\common\exception\JwtException;
use app\common\library\ResultCode;


class JWTCheck
{

    public function handle($request, \Closure $next)
    {
        $url = strtolower(Request::url());
        $url = str_replace("/api", "", $url);
        if (in_array($url, config('jwt.jwt_action_excludes'))) {
            return $next($request);
        }

        $authorization = Request::header('authorization');

        if (!$authorization) {
            throw new JwtException(ResultCode::E_TOKEN_EMPTY, 'TOKEN参数缺失！', 'E_TOKEN_EMPTY');
        }

        $type = substr($authorization, 0, 6);
        if ($type !== 'Bearer') {
            throw new JwtException(ResultCode::E_TOKEN_INVALID, 'TOKEN类型错误！', 'E_TOKEN_INVALID');
        }

        $token = substr($authorization, 7);
        $payload = null;
        try {
            $payload = JWT::decode($token, config('jwt.jwt_key'), [config('jwt.jwt_alg')]);
        } catch (\Throwable $e) {
            Log::error("jwt decode error:" . $e->getMessage());
        }

        if (is_null($payload) || is_null($payload->data)) {
            throw new JwtException(ResultCode::E_TOKEN_EXPIRED, '登录已过期！', 'E_TOKEN_EXPIRED');
        }

        session('jwt_payload_data', $payload->data);

        return $next($request);
    }
}
