<?php

namespace app\api\controller;

use think\Controller;

class Base extends Controller
{
    public function miss() {
        return \json([
            'errno' => \ERRNO['PARAMERR'],
            'msg'   => '访问接口不存在或参数错误']);
    }
}
