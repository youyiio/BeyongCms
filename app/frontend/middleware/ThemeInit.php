<?php

/**
 * Created by VSCode.
 * User: cattong
 * Date: 2018-06-12
 * Time: 15:18
 */

namespace app\frontend\middleware;

use think\facade\Config;
use think\facade\View;
use think\Paginator;

/**
 * 主题设置，主题切换
 * Class ThemeBehavior
 * @package app\frontend\behavior
 */
class ThemeInit
{
    public function handle($request, \Closure $next)
    {
        //读取当前主题详细信息
        $config = get_theme_config('cms');

        /*根据配置和来访设备类型自动切换为电脑主题或手机主题。 start */
        $header = request()->header();
        $isWechat = isset($header['user-agent']) && preg_match('/micromessenger/', strtolower($header['user-agent']));
        if (request()->isMobile() || $isWechat) {
            $template = "mobile";
        } else {
            $template = "pc";
        }

        //设置所有主题的存放路径
        $themePath = root_path()  . 'public' . DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR . $config['package_name'] . DIRECTORY_SEPARATOR;
        $viewPath = $themePath . 'tpl' . DIRECTORY_SEPARATOR;
        $paginateFile = $themePath . 'paginate.php';
        if (isset($config['responsive']) && $config['responsive'] == true) {
            $viewPath .=  $template . DIRECTORY_SEPARATOR;
            $paginateFile = $themePath . 'paginate_' . $template . '.php';
        }

        //使用容器修改
        View::config(['view_path' => $viewPath]);

        //如果分页配置存在时，加载分页配置
        if (file_exists($paginateFile)) {
            $paginateConfig = include $paginateFile;
            $paginateConfig = array_merge(Config::get('paginate'), $paginateConfig);

            // 动态注册自定义分页器
            Paginator::maker(function ($items, $listRows, $currentPage, $total, $simple, $options) use ($paginateConfig) {
                $paginateDriver = $paginateConfig['type'];
                return new $paginateDriver($items, $listRows, $currentPage, $total, $simple, $options);
            });
        }

        return $next($request);
    }
}
