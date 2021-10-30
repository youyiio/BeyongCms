<?php 
namespace app\api\controller;

use app\common\library\ResultCode;
use app\common\model\ActionLogModel;

class Log extends Base 
{
    //日志列表
    public function list()
    {
        $ActionLogModel = new ActionLogModel();

        $parmas = $this->request->put();

        $page = $parmas['page'];
        $size = $parmas['size'];
        $filters = $parmas['filters'];
        
        
        $action = $filters['action']??'';
        $startTime = $filters['startime']??'';
        $endTime = $filters['endtime']??'';
        $key = $filters['keyword']??'';
        if (!(empty($startTime) && empty($endTime))) {
            $startTime  = date('Y-m-d',strtotime('-31 day'));
            $endTime   = date('Y-m-d');
        }

        $startDatetime = date('Y-m-d 00:00:00', strtotime($startTime));
        $endDatetime = date('Y-m-d 23:59:59', strtotime($endTime));

        $where = [
            ['remark', 'like', "%{$key}%"],
            ['action', '=', $action],
            ['create_time', 'between', [$startDatetime, $endDatetime]],
        ];
        if ($action == '' && $key == '') {
            $where = [
                ['create_time', 'between', [$startDatetime, $endDatetime]],
            ];
        } else if ($action == ''){
            $where = [
                ['remark', 'like', "%{$key}%"],
                ['create_time', 'between', [$startDatetime, $endDatetime]],
            ];
        }

        $fields = 'id, uid,action, module, ip, remark, data, create_time';
       
        halt($where);
        $list = $ActionLogModel->where($where)->field($fields)->order('id desc')->paginate($size, false, ['page'=>$page]);

        $list = $list->toArray();
        //返回数据
        $returnData['current'] = $list['current_page'];
        $returnData['pages'] = $list['last_page'];
        $returnData['size'] = $list['per_page'];
        $returnData['total'] = $list['total'];
        $returnData['records'] = parse_fields($list['data'], 1);
        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功', $returnData);
    }
}