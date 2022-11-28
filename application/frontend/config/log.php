<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 日志设置
// +----------------------------------------------------------------------
return [
    // 日志记录方式，内置 file socket 支持扩展
    'type'  => 'app\\common\\thinkphp\\log\\driver\\File',
    // 日志保存目录
    'path'  => think\facade\Env::get('runtime_path') . 'log' . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR,
    // 日志记录级别
    'level' => ['error', 'notice', 'info', 'log', 'debug'],
    //独立日志
    'apart_level'   =>  ['error'],
];
