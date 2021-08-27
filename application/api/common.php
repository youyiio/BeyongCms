<?php

use think\facade\Log;

use Firebase\JWT\JWT;

const ERRNO_MAP = [
    'OK'         => '成功',
    'DBERR'      => '数据库查询错误',
    'NODATA'     => '无数据',
    'DATAEXIST'  => '数据已存在',
    'DATAERR'    => '数据错误',
    'SESSIONERR' => '用户未登录',
    'LOGINERR'   => '用户登录失败',
    'PARAMERR'   => '参数错误',
    'USERERR'    => '用户不存在或未激活',
    'ROLEERR'    => '用户身份错误',
    'PWDERR'     => '密码错误',
    'REQERR'     => '非法请求或请求次数受限',
    'IPERR'      => 'IP受限',
    'THIRDERR'   => '第三方系统错误',
    'IOERR'      => '文件读写错误',
    'SERVERERR'  => '内部错误',
    'UNKOWNERR'  => '未知错误',
];
const ERRNO = [
    'OK'         => '0',
    'DBERR'      => '4001',
    'NODATA'     => '4002',
    'DATAEXIST'  => '4003',
    'DATAERR'    => '4004',
    'SESSIONERR' => '4101',
    'LOGINERR'   => '4102',
    'PARAMERR'   => '4103',
    'USERERR'    => '4104',
    'ROLEERR'    => '4105',
    'PWDERR'     => '4106',
    'REQERR'     => '4201',
    'IPERR'      => '4202',
    'THIRDERR'   => '4301',
    'IOERR'      => '4302',
    'SERVERERR'  => '4500',
    'UNKOWNERR'  => '4501',
];

// 向前端返回JSON数据
function ajaxReturn() {
    // 形参个数
    $args_num = func_num_args();
    // 形参列表
    $args = func_get_args();
    if (1 === $args_num) {
        return json([
            'code' => ERRNO['OK'],
            'message'   => '成功',
            'data'  => $args[0]]);
    }
    if (2 === $args_num) {
        return \json([
            'code' => $args[0],
            'message'   => $args[1]]);
    }
    if (3 === $args_num) {
        return \json([
            'code' => $args[0],
            'message'   => $args[1],
            'data'  => $args[2]]);
    }
    throw new Exception("Error The number of parameters can be one or two or three");
}

// 格式标准的page
function to_standard_pagelist($paginator)
{

    return [
        'total'    => $paginator->total(),
        'size'     => $paginator->listRows(),
        'current'  => $paginator->currentPage(),
        'records'  => $paginator->items(),
    ];
}

// 设置JWT
function setJWT($data) {
    $jwt = new JWT();

    $token = array(
        // "iss"  => "http://example.org", // 签发者
        // "aud"  => "http://example.com", // 认证者
        'iat'  => time(), // 签发时间
        'nbf'  => time(), // 生效时间
        'exp'  => (time() + 60 * 60 * 24 * 7), // 过期时间  7天后的时间戳
        'data' => $data,
    );
    $jwt = $jwt::encode($token, config('jwt_key'), 'HS256');

    return $jwt;
}

// 获取JWT内容
function getJWT($token) {
    $jwt = new JWT();
    $data = null;
    try {
        $jwt_data = $jwt::decode($token, config('jwt_key'), array('HS256'));
        $data     = (array) ($jwt_data->data);
    } catch (\Throwable $e) {
        Log::write($e->getMessage(), 'error');
        return null;
    }

    return $data;
}