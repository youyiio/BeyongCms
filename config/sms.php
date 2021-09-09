<?php


/**
 * 示例配置文件
 *
 * 可以配置在 sms.php 或 config.php 文件中,
 */
return [
    'driver'      => 'jiguang', // 服务提供商, 支持 aliyun|tencent|jiguang 三种
    'key'         => '', // 短信服务key
    'secret'      => '', // 短信服务secret
    'SDKAppID'    => '', // 腾讯短信平台需要
    'actions'     => [
        'register' => [
            'sign' => '', // 腾讯短信平台为签名串!!!,其他平台为签名id;
            'template' => '',
            'params' => ['code' => 'xxxx'],
        ],
        'login' => [
            'sign' => '',
            'template' => '',
            'params' => ['code' => 'xxxx']
        ]
    ],

    'debug' => true,
    'log_driver' => '', //\beyong\sms\log\File::class,
    'log_path' => __DIR__ . '/log'
];
