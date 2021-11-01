<?php 
namespace app\api\controller;

use app\common\library\ResultCode;
use app\common\model\AuthRuleModel;

class Menu extends Base
{
    public function list()
    {
        $AuthRuleModel = new AuthRuleModel();
        $list = $AuthRuleModel->getTreeDataBelongsTo('tree', 'id','title', 'id', 'pid', 'admin');

        $returnData = $list;
        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功', $returnData);
    }
}