<?php

namespace app\api\controller\admin;

use app\common\library\ResultCode;
use app\common\model\ConfigModel;
use app\common\model\UserModel;
use think\facade\Validate;

class Config extends Base
{
    public function __construct()
    {
        parent::initialize();
    }

    public function list()
    {
        $params = request()->put();
        $page = $params['page'] ?? '1';
        $size = $params['size'] ?? '10';
        $filters = $params['filters'];

        $where = [];
        if (!empty($filters['keyword'])) {
            $where[] = ['name', 'like', '%' . $filters['keyword'] . '%'];
        }
        if (!empty($filters['group'])) {
            $where[] = ['group', '=', $filters['group']];
        }
        if (!empty($filters['key'])) {
            $where[] = ['key', 'like', '%' . $filters['key'] . '%'];
        }

        $ConfigModel = new ConfigModel();
        $list = $ConfigModel->where($where)->paginate($size, false, ['page' => $page])->toArray();

        $returnData['current'] = $list['current_page'];
        $returnData['pages'] = $list['last_page'];
        $returnData['size'] = $list['per_page'];
        $returnData['total'] = $list['total'];
        $returnData['records'] = parse_fields($list['data'], 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!', $returnData);
    }

    //查询字典信息
    public function query()
    {
        $params = request()->put();
        $key = $params['key'] ?? '';
        $group = $params['group'] ?? '';

        if (empty($key) && empty($group)) {
            return ajax_return(ResultCode::E_PARAM_EMPTY, '参数为空');
        }

        $where = [];
        if (!empty($key)) {
            $where[] = ['key', '=', $key];
        }
        if (!empty($group)) {
            $where[] = ['group', '=', $group];
        }

        $ConfigModel = new ConfigModel();
        $fields = 'id,name,group,key,value,value_type,status,sort,remark';

        $list = $ConfigModel->where($where)->field($fields)->select();

        $returnData = parse_fields($list->toArray(), 1);
        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功', $returnData);
    }

    //查询状态字典
    public function status($name)
    {
        $ConfigModel = new ConfigModel();
        $list = $ConfigModel->where('group', '=', $name . '_status')->field('key,value')->select();

        if ($list->isEmpty()) {
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

    //新增字典
    public function create()
    {
        $params = request()->put();
        $params = parse_fields($params);
        $rule = [
            'name' => 'require',
            'group' => 'require',
            'key' => 'require',
            'value' => 'require',
            'value_type' => 'require',
        ];

        $validate = Validate::rule('create')->rule($rule);
        if (!$validate->check($params)) {
            ajax_return(ResultCode::E_PARAM_ERROR, '操作成功!', $validate->getError());
        }

        $user = $this->user_info;
        $userInfo = UserModel::find($user->uid);
        $params['create_by'] = $userInfo['nickname'] ?? '';
        $params['create_time'] = date_time();
        $params['status'] = $params['status'] ?? ConfigModel::STATUS_ACTIVED;
        $Config = new ConfigModel();
        $id = $Config->insertGetId($params);
        if (!$id) {
            return ajax_return(ResultCode::E_DB_ERROR, '新增失败!');
        }

        $list = ConfigModel::find($id);
        $returnData = parse_fields($list->toArray(), 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功', $returnData);
    }

    //编辑字典
    public function edit()
    {
        $params = request()->put();
        $id = $params['id'];

        $config = ConfigModel::find($id);
        if (!$config) {
            return ajax_return(ResultCode::E_ACCESS_NOT_FOUND, '数据未找到!');
        }

        $params = parse_fields($params);
        $res = $config->update($params, ['id' => $id]);

        if (!$res) {
            return ajax_return(ResultCode::E_DB_ERROR, '操作失败!');
        }

        $returnData = parse_fields($config, 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功', $returnData);
    }

    //删除字典
    public function delete($id)
    {
        $config = ConfigModel::find($id);
        if (!$config) {
            return ajax_return(ResultCode::E_DATA_NOT_FOUND, '字典不存在!');
        }

        $res = $config->delete();
        if (!$res) {
            return ajax_return(ResultCode::E_DB_ERROR, '操作失败!');
        }

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!');
    }
}