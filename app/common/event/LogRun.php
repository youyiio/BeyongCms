<?php

/**
 * Created by VSCode.
 * User: cattong
 * Date: 2017-06-20
 * Time: 14:59
 */

namespace app\common\event;

use think\facade\Request;
use think\facade\Log;

/**
 * 日志文件写入
 */
class LogRun
{

    public function handle($event)
    {
        $baseUrl = Request::baseUrl();
        $ip = Request::ip(0, true);

        $datetime = "[" . date_time() . ' ' . (millisecond() % 1000) . "] ";
        $params = $_REQUEST;
        //不输出登陆密码
        if (isset($params['password'])) {
            $params['password'] = '********';
        }

        Log::info($datetime . 'ip=' . $ip . '|path=' . $baseUrl . '|params=' . json_encode($params));
    }
}
