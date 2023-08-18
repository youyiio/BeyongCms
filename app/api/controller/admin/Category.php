<?php

namespace app\api\controller\admin;

use app\common\library\ResultCode;
use app\common\model\cms\CategoryModel;

class Category extends Base
{
    public function list()
    {
        $params = request()->put();

        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? 10;
        $filters = $params['filters'] ?: '';
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
        $list = $CategoryModel->where($where)->select()->toArray();

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
        $records =  array_slice($data, $start, $size); //读取数据
        //返回数据
        $returnData['current'] = $page;
        $returnData['pages'] = $pages;
        $returnData['size'] = $size;
        $returnData['total'] = $total;
        $returnData['records'] = parse_fields($records, 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!', $returnData);
    }

    //新增分类
    public function create()
    {
        $params = request()->put();

        if (empty($params['name'])) {
            return ajax_return(ResultCode::E_DATA_NOT_FOUND, "参数错误!");
        }

        if (isset($params['pid']) && !is_numeric($params['pid'])) {
            return ajax_return(ResultCode::E_DATA_NOT_FOUND, "参数错误!");
        }

        //查看分类名是否已存在
        $CategoryModel = new CategoryModel();
        $name = $CategoryModel->where('name', $params['name'])->limit(1)->select();
        if (count($name) >= 1) {
            return ajax_return(ResultCode::E_PARAM_ERROR, "分类名已存在!");
        }

        $categoryModel = new CategoryModel();
        $params['status'] = CategoryModel::STATUS_ONLINE;
        $categoryModel->save($params);
        if (!$categoryModel->id) {
            return ajax_return(ResultCode::ACTION_FAILED, "操作失败!");
        }

        $data = CategoryModel::find($categoryModel->id);
        $data = $data->toArray();
        $returnData = parse_fields($data, 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, "操作成功!", $returnData);
    }

    //编辑分类
    public function edit()
    {
        $params = request()->put();

        $id = $params['id'];
        $category = CategoryModel::find($id);
        if (!$category) {
            return ajax_return(ResultCode::E_DATA_NOT_FOUND, '分类不存在!');
        }
        if (empty($params['name'])) {
            return ajax_return(ResultCode::E_DATA_NOT_FOUND, "参数错误!");
        }
        if (isset($params['pid']) && !is_numeric($params['pid'])) {
            return ajax_return(ResultCode::E_DATA_NOT_FOUND, "参数错误!");
        }

        $res = $category->update($params, ['id' => $id]);
        if (!$res) {
            return ajax_return(ResultCode::ACTION_FAILED, "操作失败!");
        }

        $data = CategoryModel::find($id);
        $data = $data->toArray();

        $returnData = parse_fields($data, 1);
        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!', $returnData);
    }

    //上线下线分类
    public function setStatus()
    {
        $params = request()->put();

        $id = $params['id'];
        $category = CategoryModel::find($id);
        if (!$category) {
            return ajax_return(ResultCode::E_PARAM_ERROR, '分类不存在!');
        }

        $status = $params['status'];
        if (!is_numeric($params['status']) || !in_array($status, [CategoryModel::STATUS_OFFLINE, CategoryModel::STATUS_ONLINE])) {
            return ajax_return(ResultCode::E_PARAM_ERROR, "参数错误!");
        }

        $category->status = $status;
        $category->save();

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!');
    }

    public function delete($id)
    {
        $category = CategoryModel::find($id);
        if (!$category) {
            return ajax_return(ResultCode::E_DATA_NOT_FOUND, '分类不存在!');
        }

        $res = $category->delete();
        if (!$res) {
            return ajax_return(ResultCode::ACTION_FAILED, '操作失败!');
        }

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!');
    }
}
