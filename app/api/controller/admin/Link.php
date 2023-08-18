<?php

namespace app\api\controller\admin;

use app\common\library\ResultCode;
use app\common\model\cms\LinkModel;
use think\facade\Validate;

class Link extends Base
{
    public function list()
    {
        $params = request()->put();

        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? 10;
        $filters = $params['filters'] ?? '';

        $where = [];
        $fields = 'id,title,url,sort,status,start_time,end_time,create_time';
        if (isset($filters['keyword'])) {
            $where[] = ['title', 'like', '%' . $filters['keyword'] . '%'];
        }
        if (isset($filters['status']) && $filters['status'] !== '') {
            $where[] = ['status', '=', $filters['status']];
        }

        $LinkModel = new LinkModel();
        $list = $LinkModel->where($where)->field($fields)->order('sort asc')->paginate($size, false, ['page' => $page])->toArray();

        //返回数据
        $returnData['current'] = $list['current_page'];
        $returnData['pages'] = $list['last_page'];
        $returnData['size'] = $list['per_page'];
        $returnData['total'] = $list['total'];
        $returnData['records'] = parse_fields($list['data'], 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功', $returnData);
    }

    //新增链接
    public function create()
    {
        $params = request()->put();

        $rule = [
            'title' => 'require',
            'url' => 'require|url',
            'sort' => 'integer',
        ];
        $validate = Validate::rule('edit')->rule($rule);
        if (!$validate->check($params)) {
            return ajax_return(ResultCode::ACTION_FAILED, '操作失败!', $validate->getError());
        }


        $params = parse_fields($params);
        $LinkModel = new LinkModel();
        $res = $LinkModel->save($params);
        $id = $LinkModel->id;
        if (!$res) {
            return ajax_return(ResultCode::E_DB_ERROR, '操作失败!');
        }
        cache('links', null);

        $list = LinkModel::find($id);

        $returnData = parse_fields($list->toArray(), 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功', $returnData);
    }

    //编辑链接
    public function edit()
    {
        $params = request()->put();

        $link = LinkModel::find($params['id']);
        if (!$link) {
            return ajax_return(ResultCode::E_PARAM_ERROR, '友链不存在!');
        }

        $rule = [
            'title' => 'require',
            'url' => 'require|url',
            'sort' => 'integer',
        ];
        $validate = Validate::rule('edit')->rule($rule);
        if (!$validate->check($params)) {
            return ajax_return(ResultCode::E_PARAM_ERROR, '参数错误!');
        }

        $params = parse_fields($params);
        $res = $link->update($params);

        if (!$res) {
            return ajax_return(ResultCode::E_DB_ERROR, '编辑失败!');
        }

        $returnData = parse_fields($link->toArray(), 1);
        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功', $returnData);
    }

    //删除链接
    public function delete($id)
    {
        $link = LinkModel::find($id);
        if (!$link) {
            return ajax_return(ResultCode::E_DATA_NOT_FOUND, '友链不存在!');
        }

        $res = $link->delete();
        if (!$res) {
            return ajax_return(ResultCode::ACTION_FAILED, '操作失败!');
        }

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!');
    }
}