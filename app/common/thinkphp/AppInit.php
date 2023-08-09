<?php

/**
 * Created by PhpStorm.
 * User: cattong
 * Date: 2019-04-02
 * Time: 16:50
 */

namespace app\common\thinkphp;

use think\facade\Env;

/**
 * AppInit 事件监听器
 */
class AppInit
{
    private static $BEYONG_CMS_VERSION = '';

    public function handle($event)
    {
        $app = app();

        //加载配置的环境变量
        $envName = Env::get('run.profile');
        $app->loadEnv($envName);

        $app->setRuntimePath($app->getRootPath() . 'data' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR);

        $app->config->load($app->getConfigPath() . 'database.php', 'database');
        $app->config->load($app->getConfigPath() . 'log.php', 'log');

        if (is_file($app->getRootPath() . '.version')) {
            $app->env->load($app->getRootPath() . '.version');
            //读取版本
            AppInit::$BEYONG_CMS_VERSION = $app->env->get('beyongcms_version', 'none');
        }
    }

    /**
     * 获取beyongCms 版本
     * @return string
     */
    public static function beyongCmsVersion()
    {
        return AppInit::$BEYONG_CMS_VERSION;
    }
}
