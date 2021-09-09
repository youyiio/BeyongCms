<?php

/**
 * 示例配置文件
 *
 * 可以配置在 sms.php 或 config.php 文件中,
 */

 use think\facade\Env;
 
return [
    'driver'      => 'jiguang', // 服务提供商, 支持 aliyun|tencent|jiguang 三种
    'key'         => '1c368e6c3bf852cdc9b45d3e', // 短信服务key
    'secret'      => '351a145da81ff2c197d433bf', // 短信服务secret
    'SDKAppID'    => '', // 腾讯短信平台需要
    'actions'     => [
        'register' => [
            'sign' => '	20054',
            'template' => '200819',
            'params' => ['code' => ''],
        ],
        'login' => [
            'sign' => '20054',
            'template' => '200819',
            'params' => ['code' => 'xxxx']
        ]
    ],

    'debug' => true,
    'log_driver' => \beyong\sms\log\File::class, //\beyong\sms\log\File::class,
    'log_path' => Env::get('runtime_path') . 'log' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR,
];
