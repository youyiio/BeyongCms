<?php

namespace app\admin\controller;

use app\common\model\MessageModel;
use think\App;
use think\helper\Time;
use app\common\model\UserModel;
use app\common\model\cms\ArticleModel;

use beyong\echarts\ECharts;
use beyong\echarts\options\YAxis;
use beyong\echarts\Option;
use beyong\echarts\charts\Line;
use beyong\echarts\options\XAxis;
use Jenssegers\Date\Date;
use think\facade\View;

/**
 * 首页控制器
 */
class Index extends Base
{

    public function index()
    {
        return View::fetch('index');
    }

    public function welcome()
    {
        $info = array(
            '操作系统' => PHP_OS,
            '运行环境' => $this->request->server("SERVER_SOFTWARE"),
            'PHP运行方式' => php_sapi_name(),
            'PHP运行版本' => PHP_VERSION,
            'ThinkPHP版本' => App::VERSION,
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time') . '秒',
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
            '服务器域名/IP' => $this->request->server('SERVER_NAME'),
            '剩余空间' => round((disk_free_space(".") / (1024 * 1024)), 2) . 'M'
        );
        $this->assign('info', $info);

        return $this->fetch("welcome");
    }

    public function dashboard()
    {
        list($todayBeginTime, $todayEndTime) = [Date::today(), Date::now()];
        list($curWeekBeginTime, $curWeekEndTime) = [new Date("Monday this week"), new Date("Monday next week")];
        list($curMonthBeginTime, $curMonthEndTime) = [Date::now()->startOfMonth(), Date::now()->endOfMonth()];

        list($yesterdayBeginTime, $yesterdayEndTime) = [Date::yesterday(), Date::today()];
        list($lastWeekBeginTime, $lastWeekEndTime) = [new Date('Monday last week'), new Date('Monday this week')];
        list($lastMonthBeginTime, $lastMonthEndTime) = [new Date('last month'), new Date('this month')];

        $where = [];
        $where[] = ['create_time', 'between', [date_time($todayBeginTime->unix()), date_time($todayEndTime->unix())]];
        $yesterdayWhere[] = ['create_time', 'between', [date_time($yesterdayBeginTime->unix()), date_time($yesterdayEndTime->unix())]];

        $ArticleModel = new ArticleModel();
        $todayCount = $ArticleModel->where($where)->count();
        $yesterdayCount = $ArticleModel->where($yesterdayWhere)->count();
        if ($yesterdayCount === 0) { //除数不能为0
            $dayPercent = $todayCount * 100;
        } else {
            $dayPercent = (($todayCount - $yesterdayCount) / $yesterdayCount) * 100;
        }

        unset($where);
        $where[] = ['create_time', 'between', [date_time($curWeekBeginTime->unix()), date_time($curWeekEndTime->unix())]];
        $lastWeekWhere[] = ['create_time', 'between', [date_time($lastWeekBeginTime->unix()), date_time($lastWeekEndTime->unix())]];
        $curWeekCount = $ArticleModel->where($where)->count();
        $lastWeekCount = $ArticleModel->where($lastWeekWhere)->count();
        if ($lastWeekCount === 0) { //除数不能为0
            $weekPercent = $curWeekCount * 100;
        } else {
            $weekPercent = (($curWeekCount - $lastWeekCount) / $lastWeekCount) * 100;
        }

        unset($where);
        $where[] = ['create_time', 'between', [$curMonthBeginTime, $curMonthEndTime]];
        $lastMonthWhere[] = ['create_time', 'between', [$lastMonthBeginTime, $lastMonthEndTime]];
        $curMonthCount = $ArticleModel->where($where)->count();
        $lastMonthCount = $ArticleModel->where($lastMonthWhere)->count();
        if ($lastMonthCount === 0) { //除数不能为0
            $monthPercent = $curMonthCount * 100;
        } else {
            $monthPercent = (($curMonthCount - $lastMonthCount) / $lastMonthCount) * 100;
        }

        unset($where);
        $where[] = ['status', '=', ArticleModel::STATUS_PUBLISHING];
        $waitForPublishCount = $ArticleModel->where($where)->count();

        unset($where);
        $where[] = ['status', '>=', ArticleModel::STATUS_PUBLISHING];
        $where[] = ['status', '<', ArticleModel::STATUS_PUBLISHED];
        $waitForAuditCount = $ArticleModel->where($where)->count();

        $this->assign('todayCount', $todayCount);
        $this->assign('curWeekCount', $curWeekCount);
        $this->assign('curMonthCount', $curMonthCount);

        $this->assign('waitForPublishCount', $waitForPublishCount);
        $this->assign('waitForAuditCount', $waitForAuditCount);

        $this->assign('dayPercent', $dayPercent);
        $this->assign('weekPercent', $weekPercent);
        $this->assign('monthPercent', $monthPercent);

        return view();
    }

    public function today()
    {

        $where = [];
        $xAxisData = [];
        $yAxisData = [];
        $UserModel = new UserModel();
        for ($hour = 0; $hour < 24; $hour++) {
            $beginTime = mktime($hour, 0, 0, date('m'), date('d'), date('Y'));
            $endTime = mktime($hour, 59, 59, date('m'), date('d'), date('Y'));

            unset($where);
            $where[] = ['status', '>=', UserModel::STATUS_APPLY];
            $where[] = ['register_time', 'between', [date_time($beginTime), date_time($endTime)]];
            $inquiryCount = $UserModel->where($where)->count();

            array_push($xAxisData, $hour . '时');
            array_push($yAxisData, $inquiryCount);
        }

        $xAxis = new XAxis();
        $xAxis->data = $xAxisData;

        $option = new Option();
        $option->xAxis($xAxis);

        $chart = new Line();
        $chart["data"] = $yAxisData;

        $option->series([$chart]);

        $this->success('ok', '', $option);
    }

    public function month()
    {
        $where = [];
        $xAxisData = [];
        $yAxisData = [];
        $UserModel = new UserModel();
        for ($day = 1; $day < date('t'); $day++) {
            $beginTime = mktime(0, 0, 0, date('m'), $day, date('Y'));
            $endTime = mktime(23, 59, 59, date('m'), $day, date('Y'));

            unset($where);
            $where[] = ['status', '>=', UserModel::STATUS_APPLY];
            $where[] = ['register_time', 'between', [date_time($beginTime), date_time($endTime)]];
            $inquiryCount = $UserModel->where($where)->count();

            array_push($xAxisData, $day . '日');
            array_push($yAxisData, $inquiryCount);
        }

        $xAxis = new XAxis();
        $xAxis->data = $xAxisData;

        $option = new Option();
        $option->xAxis($xAxis);

        $chart = new Line();
        $chart["data"] = $yAxisData;

        $option->series([$chart]);

        $this->success('ok', '', $option);
    }

    public function year()
    {
        $where = [];
        $xAxisData = [];
        $yAxisData = [];
        $UserModel = new UserModel();
        for ($month = 1; $month <= 12; $month++) {
            $beginTime = mktime(0, 0, 0, $month, 1, date('Y'));
            $endTime = mktime(23, 59, 59, $month, date("t", strtotime(date('Y') . "-$month")), date('Y'));

            unset($where);
            $where[] = ['status', '>=', UserModel::STATUS_APPLY];
            $where[] = ['register_time', 'between', [date_time($beginTime), date_time($endTime)]];
            $inquiryCount = $UserModel->where($where)->count();

            array_push($xAxisData, $month . '月');
            array_push($yAxisData, $inquiryCount);
        }

        $xAxis = new XAxis();
        $xAxis->data = $xAxisData;

        $option = new Option();
        $option->xAxis($xAxis);

        $chart = new Line();
        $chart["data"] = $yAxisData;

        $option->series([$chart]);

        $this->success('ok', '', $option);
    }
}
