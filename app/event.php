<?php
// 事件定义文件
return [
    'bind'      => [],

    'listen'    => [
        'AppInit'  => ['app\common\thinkphp\AppInit'],
        'HttpRun'  => [
            'app\common\event\LogRun',
        ],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
    ],

    'subscribe' => [],
];
