<?php

namespace app\api\controller\app;

use app\api\controller\Base;
use app\common\model\cms\CategoryModel;
use app\common\model\cms\LinkModel;
use app\common\library\ResultCode;
use app\common\model\cms\AdModel;
use app\common\model\cms\AdSlotModel;
use think\Validate;

class Cms extends Base
{
    //查询分类列表
    public function categoryList()
    {
        $params = $this->request->put();

        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? 10;
        $filters = $params['filters'] ?? '';
        $pid = $filters['pid'] ?? 0;
        $depth = $filters['depth'] ?? 1;
        $struct = $filters['struct'] ?? '';

        $where = [];
        if (!empty($filters['startTime'])) {
            $where[] = ['create_time', '>=', $filters['startTime'] . '00:00:00'];
        }
        if (!empty($filters['endTime'])) {
            $where[] = ['create_time', '<=', $filters['endTime'] . '23:59:59'];
        }

        $CategoryModel = new CategoryModel();
        $list = $CategoryModel->where($where)->select();

        // 获取树形或者list数据
        if ($struct === 'list') {
            $data = getList($list, $pid, 'id', 'pid');
        } else {
            $data = getTree($list, $pid, 'id', 'pid', $depth);
        }

        //分页
        $total = count($data);  //总数
        $pages = ceil($total / $size); //总页数
        $start = ($page - 1) * $size;
        $records =  array_slice($data, $start, $size);
        //返回数据
        $returnData['current'] = $page;
        $returnData['pages'] = $pages;
        $returnData['size'] = $size;
        $returnData['total'] = $total;
        $returnData['records'] = parse_fields($records, 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!', $returnData);
    }

    //查询友链列表
    public function linkList()
    {
        $params = $this->request->put();

        $limit = $params['limit'] ?? 5;

        $where = [];
        $fields = 'id,title,url,sort,status,start_time,end_time,create_time';
        if (isset($filters['keyword'])) {
            $where[] = ['title', 'like', '%' . $filters['keyword'] . '%'];
        }
        if (isset($filters['status']) && $filters['status'] !== '') {
            $where[] = ['status', '=', $filters['status']];
        }

        $LinkModel = new LinkModel();

        $list = $LinkModel->where($where)->field($fields)->limit($limit)->select()->toArray();

        //返回数据
        $returnData = parse_fields($list, 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功', $returnData);
    }

    //查询轮播图
    public function carousel()
    {
        $params = $this->request->put();

        $slot = $filters["slot"] ?? '';
        $limit = $params["limit"] ?? 5;

        $AdSlotModel = new AdSlotModel();
        $adSlot = $AdSlotModel->where(['name' => $slot])->find();
        if (!$adSlot) {
            return ajax_error(ResultCode::E_DATA_NOT_FOUND, "广告slot不存在!");
        }

        $slotId = $adSlot->id;
        $results = AdModel::has('adServings', ['slot_id' => $slotId])->with('image')->order('sort asc')->limit($limit)->select();
        foreach ($results as $ad) {
            $ad->image['full_image_url'] = $ad->image->fullImageUrl;
        }

        $results = $results->toArray();
        return ajax_success(parse_fields($results, 1));
    }
}
