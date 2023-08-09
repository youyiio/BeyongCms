<?php
// 事件定义文件
return [
    'bind'      => [],

    'listen'    => [
        'AppInit'  => ['app\common\thinkphp\AppInit'],
        'HttpRun'  => [
            'app\common\behavior\LogBehavior',
            //'app\\common\\behavior\\SpiderBehavior',
            'app\frontend\behavior\ThemeBehavior',
        ],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
    ],

    'subscribe' => [],
];
