<?php

namespace app\frontend\service;

use think\facade\Cache;
use think\facade\Session;
use think\facade\Request;

class VisitService
{
    protected $onlineKey = 'online_users';
    protected $todayVisitsKey = 'visits:';
    protected $timeout = 300; // 5分钟
    
    /**
     * 记录用户在线
     */
    public function recordOnline()
    {
        $sessionId = Session::getId();
        $userId = Session::get('user_id', 0) ?: 'guest_' . $sessionId;
        $now = time();
        
        $data = [
            'user_id' => $userId,
            'ip' => Request::ip(),
            'user_agent' => Request::server('HTTP_USER_AGENT', ''),
            'last_activity' => $now,
            'login_time' => $now
        ];
        
        // 存储用户在线信息
        Cache::store('redis')->hSet($this->onlineKey, $sessionId, json_encode($data));
        
        // 设置过期时间自动清理
        Cache::store('redis')->expire($this->onlineKey, $this->timeout);
        
        // 清理过期用户
        $this->cleanExpiredUsers();
    }
    
    /**
     * 清理过期用户
     */
    protected function cleanExpiredUsers()
    {
        $allUsers = Cache::store('redis')->hGetAll($this->onlineKey);
        $now = time();
        
        foreach ($allUsers as $sessionId => $userData) {
            $data = json_decode($userData, true);
            if ($now - $data['last_activity'] > $this->timeout) {
                Cache::store('redis')->hDel($this->onlineKey, $sessionId);
            }
        }
    }
    
    /**
     * 获取在线用户数
     */
    public function getOnlineCount()
    {
        $this->cleanExpiredUsers();
        return Cache::store('redis')->hLen($this->onlineKey);
    }
    
    /**
     * 获取在线用户列表
     */
    public function getOnlineUsers()
    {
        $this->cleanExpiredUsers();
        $users = Cache::store('redis')->hGetAll($this->onlineKey);
        
        $result = [];
        foreach ($users as $data) {
            $result[] = json_decode($data, true);
        }
        
        return $result;
    }
    
    /**
     * 记录访问统计
     */
    public function recordVisit()
    {
        $today = date('Ymd');
        $sessionId = Session::getId();
        
        // 总访问量
        $totalKey = 'total_visits';
        Cache::store('redis')->incr($totalKey);
        
        // 今日访问量
        $todayKey = 'today_visits:' . $today;
        Cache::store('redis')->incr($todayKey);
        Cache::store('redis')->expire($todayKey, 86400);
        
        // 独立访客（基于Session）
        $uniqueKey = 'unique_visitors:' . $today;
        if (!Cache::store('redis')->sIsMember($uniqueKey, $sessionId)) {
            Cache::store('redis')->sAdd($uniqueKey, $sessionId);
            Cache::store('redis')->expire($uniqueKey, 86400);
        }
    }
    
    /**
     * 获取统计信息
     */
    public function getStatistics()
    {
        $today = date('Ymd');
        
        return [
            'total_visits' => Cache::store('redis')->get('total_visits') ?: 0,
            'today_visits' => Cache::store('redis')->get('today_visits:' . $today) ?: 0,
            'unique_visitors' => Cache::store('redis')->sCard('unique_visitors:' . $today) ?: 0,
            'online_users' => $this->getOnlineCount()
        ];
    }
}