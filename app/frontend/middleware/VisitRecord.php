<?php

namespace app\frontend\middleware;

use think\facade\Session;
use think\Request;
use app\frontend\service\VisitService;

class VisitRecord
{

    public function handle(Request $request, \Closure $next)
    {
        $this->recordVisit($request);
        
        $response = $next($request);
        
        return $response;
    }
    
    protected function recordVisit(Request $request)
    {
        $visitService = new VisitService();
        // 1. 记录用户在线状态
        $visitService->recordOnline($request);
        
        // 2. 记录访问统计
        $visitService->recordVisit($request);
    }
    
    // protected function recordOnline(Request $request)
    // {
    //     $sessionId = Session::getId();
    //     $userId = Session::get('user_id', 0);
    //     $now = time();
        
    //     // 清理过期在线记录（默认5分钟）
    //     UserOnline::where('last_activity', '<', $now - 300)->delete();
        
    //     // 查找或创建在线记录
    //     $online = UserOnline::where('session_id', $sessionId)->find();
        
    //     if ($online) {
    //         $online->save([
    //             'last_activity' => $now,
    //             'user_id' => $userId
    //         ]);
    //     } else {
    //         UserOnline::create([
    //             'user_id' => $userId,
    //             'session_id' => $sessionId,
    //             'ip_address' => $request->ip(),
    //             'user_agent' => $request->server('HTTP_USER_AGENT', ''),
    //             'last_activity' => $now
    //         ]);
    //     }
    // }
    
    // protected function recordVisit(Request $request)
    // {
    //     $today = date('Y-m-d');
    //     $sessionId = Session::getId();
        
    //     // 获取今日统计
    //     $stats = VisitStatistics::getTodayStats();
        
    //     // 增加访问次数
    //     $stats->visit_count += 1;
        
    //     // 检查是否是今日首次访问
    //     $key = 'visited_' . $today;
    //     if (!Session::get($key)) {
    //         Session::set($key, true);
    //         $stats->unique_visitor += 1;
    //     }
        
    //     $stats->save();
    // }
}