<?php

namespace app\api\controller;

use think\Controller;

class Base extends Controller
{
    // 用户信息，来自playload data
    protected $user_info;

    public function miss() {
        return json([
            'errno' => \ERRNO['PARAMERR'],
            'msg'   => '访问接口不存在或参数错误']);
    }

    public function initialize() {
        $this->user_info = session("jwt_payload_data", null);
    }
}
