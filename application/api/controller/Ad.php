<?php
namespace app\api\controller;

// 不需要认证的话继承Base
use app\api\controller\Base;
use app\common\library\ResultCode;
use app\common\model\cms\AdModel;
use app\common\model\cms\AdSlotModel;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;
use think\Validate;

class Ad extends Base
{
    //查询轮播图
    public function carousel() 
    {
        $params = $this->request->put();

        //数据验证
        $validate = new Validate();
        $validate->rule([
            'slot' => 'require',
            'limit' => 'require|integer'
        ]);
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
        $results = AdModel::has('adServings', ['slot_id' => $slotId])->order('sort asc')->limit($limit)->select();

        return ajax_success($results);
    }

    //查询广告列表
    public function list() 
    {
        $params = $this->request->put();

        //数据验证
        $validate = new Validate();
        $validate->rule([
            'page' => 'require|integer',
            'size' => 'require|integer'
        ]);
        if (!$validate->check($params)) {
            return ajax_error(ResultCode::E_PARAM_VALIDATE_ERROR, $validate->getError());
        };

        $page = $params["page"];
        $size = $params["size"];
        $query = $params["filters"];

        $AdModel = new AdModel();

        $pageConfig = [
            'page' => $page,
            'query' => $query
        ];
        $list = $AdModel->alias('a')->join('sys_image b', 'a.image_id = b.id')->paginate($size, false, $pageConfig);
    
        if (!$list) {
            return ajax_error(ResultCode::E_DATA_NOT_FOUND, "广告不存在!");
        }

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!', to_standard_pagelist($list));
    }

    //查询广告插槽
    public function slots()
    {
        $adSlot = new AdSlotModel();
        $list = $adSlot->select();
        
        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!', $list);

    }

    //新增广告
    public function create()
    {

    }

    //编辑广告
    public function audit()
    {

    }

    public function delete($id)
    {
        $ad = AdModel::get($id);
        if (!$ad) {
            $this->error('分类不存在!');
        }

        $ad->delete();

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!');
    }
}