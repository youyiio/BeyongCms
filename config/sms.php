<?php

/**
 * 示例配置文件
 *
 * 可以配置在 sms.php 或 config.php 文件中,
 */

 use think\facade\Env;
 
return [
    'driver'      => Env::get('sms.driver', 'jiguang'), // 服务提供商, 支持 aliyun|tencent|jiguang 三种
    'key'         => Env::get('sms.key', ''), // 短信服务key
    'secret'      => Env::get('sms.secret', ''), // 短信服务secret
    'SDKAppID'    => Env::get('sms.SDKAppID', ''), // 腾讯短信平台需要
    'actions'     => [
        'register' => [
            'sign' => Env::get('sms.register_sign', ''),
            'template' => Env::get('sms.register_template', ''),
            'params' => ['code' => ''],
        ],
        'login' => [
            'sign' => Env::get('sms.login_sign', ''),
            'template' => Env::get('sms.login_template', ''),
            'params' => ['code' => '']
        ],
        'reset_password' => [
            'sign' => Env::get('sms.reset_password_sign', ''),
            'template' => Env::get('sms.reset_password_template', ''),
            'params' => ['code' => '']
        ]
    ],

    'debug' => true,
    'log_driver' => \beyong\sms\log\File::class, //\beyong\sms\log\File::class,
    'log_path' => Env::get('runtime_path') . 'log' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR,
];
