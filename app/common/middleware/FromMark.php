<?php

namespace app\common\middleware;

use think\facade\Request;
use think\facade\Cookie;

/**
 * 来源标记
 */
class FromMark
{
    public function handle($request, \Closure $next)
    {
        //首次访问 cookie记录来源
        if (!Cookie::has('from_referee') && !Cookie::has('entrance_url')) {
            $request = Request::instance();
            $fromReferee = $request->server('HTTP_REFERER');
            Cookie::set('from_referee', $fromReferee, 0);
            $fromUrl = $request->url(true);
            Cookie::set('entrance_url', $fromUrl, 0);
        }

        return $next($request);
    }
}
