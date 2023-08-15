<?php

/**
 * Created by VSCode.
 * User: cattong
 * Date: 2019-06-05
 * Time: 10:38
 */

namespace app\common\middleware;

use app\common\model\ActionLogModel;
use think\facade\Request;

use app\common\logic\ActionLogLogic;

class SpiderCheck
{
    public function handle($request, \Closure $next)
    {
        $userAgent = Request::header('user-agent');
        $params = $_REQUEST;
        if (isset($params['password'])) {
            $params['password'] = '********';
        }

        if (strlen($userAgent) > 255) {
            $userAgent = substr($userAgent, 0, 255);
        }

        //登录日志
        $actionLog = new ActionLogLogic();
        $actionLog->addLog(0, ActionLogModel::ACTION_ACCESS, $userAgent, $params);

        return $next($request);
    }
}