<?php

/**
 * 示例配置文件
 *
 * 可以配置在 sms.php 或 config.php 文件中,
 */
 
return [
    'driver'      => env('sms.driver', 'jiguang'), // 服务提供商, 支持 aliyun|tencent|jiguang 三种
    'key'         => env('sms.key', ''), // 短信服务key
    'secret'      => env('sms.secret', ''), // 短信服务secret
    'SDKAppID'    => env('sms.SDKAppID', ''), // 腾讯短信平台需要
    'actions'     => [
        'register' => [
            'sign' => env('sms.register_sign', ''),
            'template' => env('sms.register_template', ''),
            'params' => ['code' => ''],
        ],
        'login' => [
            'sign' => env('sms.login_sign', ''),
            'template' => env('sms.login_template', ''),
            'params' => ['code' => '']
        ],
        'reset_password' => [
            'sign' => env('sms.reset_password_sign', ''),
            'template' => env('sms.reset_password_template', ''),
            'params' => ['code' => '']
        ]
    ],

    'debug' => true,
    'log_driver' => \beyong\sms\log\File::class, //\beyong\sms\log\File::class,
    'log_path' => runtime_path() . 'log' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR,
];
