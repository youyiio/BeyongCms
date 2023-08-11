<?php
// 应用中间件定义文件
return [
    // 主题初始化
    \app\frontend\middleware\ThemeInit::class,
    \app\common\middleware\FromMark::class,
    \app\common\middleware\SpiderCheck::class,
];
