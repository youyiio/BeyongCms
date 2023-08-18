<?php

namespace app\api\controller\app;

use app\api\controller\Base;
use app\common\library\ResultCode;
use app\common\model\cms\AdModel;
use app\common\model\cms\AdSlotModel;
use app\common\model\ConfigModel;
use think\facade\Validate;

class Config
{
    //查询应用信息
    public function base()
    {
        $ConfigModel = new ConfigModel();

        $fields = 'id,name,group,key,value,value_type,remark';
        $list = $ConfigModel->field($fields)->select();

        $returnData = parse_fields($list, 1);
        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!', $returnData);
    }

    //查询状态字典
    public function status($name)
    {
        $ConfigModel = new ConfigModel();
        $list = $ConfigModel->where('group', '=', $name . '_status')->field('key,value')->select();

        if (empty($list)) {
            return ajax_return(ResultCode::E_DATA_NOT_FOUND, '数据未找到');
        }

        $returnData = [];
        foreach ($list as $val) {
            $returnData[] = [
                $val['key'] => $val['value']
            ];
        }

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功', $returnData);
    }

    public function carousel()
    {
        $params = request()->put();

        //数据验证git
        $validate = new Validate();
        $rule = ([
            'slot' => 'require',
            'limit' => 'require|integer'
        ]);
        $validate = Validate::rule('ca')->rule($rule);
        if (!$validate->check($params)) {
            return ajax_error(ResultCode::E_PARAM_VALIDATE_ERROR, $validate->getError());
        };

        $slot = $params["slot"];
        $limit = $params["limit"];

        $AdSlotModel = new AdSlotModel();
        $adSlot = $AdSlotModel->where(['name' => $slot])->find();
        if (!$adSlot) {
            return ajax_error(ResultCode::E_DATA_NOT_FOUND, "广告slot不存在!");
        }

        $slotId = $adSlot->id;
        $results = AdModel::hasWhere('adServings', ['slot_id' => $slotId])->order('sort asc')->limit($limit)->select();

        return ajax_success($results);
    }
}
