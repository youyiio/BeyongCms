<?php 
namespace app\api\controller;

use app\common\library\ResultCode;
use app\common\model\AuthGroupModel;

class Role extends Base
{
    public function list()
    {
        $AuthGroupModel = new AuthGroupModel();
        
        $fields = 'id,title,status'; 
        $list = $AuthGroupModel->field($fields)->select();
        $returnData = $list;
        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功', $returnData);
    }
}