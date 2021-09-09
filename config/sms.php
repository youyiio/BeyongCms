<?php

/**
 * 示例配置文件
 *
 * 可以配置在 sms.php 或 config.php 文件中,
 */

 use think\facade\Env;
 
return [
    'driver'      => '', // 服务提供商, 支持 aliyun|tencent|jiguang 三种
    'key'         => '', // 短信服务key
    'secret'      => '', // 短信服务secret
    'SDKAppID'    => '', // 腾讯短信平台需要
    'actions'     => [
        'register' => [
            'sign' => '',
            'template' => '',
            'params' => ['code' => ''],
        ],
        'login' => [
            'sign' => '',
            'template' => '',
            'params' => ['code' => '']
        ]
    ],

    'debug' => true,
    'log_driver' => \beyong\sms\log\File::class, //\beyong\sms\log\File::class,
    'log_path' => Env::get('runtime_path') . 'log' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR,
];
