<?php 
namespace app\api\controller;

use app\common\library\ResultCode;
use app\common\model\AuthGroupModel;

class Role extends Base
{
    public function list()
    {
        $params = $this->request->put();
        $page = $params['page'];
        $size = $params['size'];

        $filters = $params['filters'];
        $keyword = $filters['keyword'];

        $where = [];
        $fields = 'id,title,status';
        if (!empty($keyword)) {
            $where[] = ['title', 'like', '%'.$keyword.'%'];
        }

        $AuthGroupModel = new AuthGroupModel();
        $list = $AuthGroupModel->where($where)->field($fields)->paginate($size, false, ['page'=>$page]);

        $list = $list->toArray();
        //返回数据
        $returnData['current'] = $list['current_page'];
        $returnData['pages'] = $list['last_page'];
        $returnData['size'] = $list['per_page'];
        $returnData['total'] = $list['total'];
        $returnData['records'] = parse_fields($list['data'], 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!', $returnData);
    }

    //新增角色
    public function create()
    {
        $params = $this->request->put();
        
        $name = $params['name']?? '';
        $remark = $params['remark']?? '';

        if (empty($name)) {
            return ajax_return(ResultCode::E_DATA_VALIDATE_ERROR, '操作失败!');
        }

        $AuthGroupModel = new AuthGroupModel();
        $result = $AuthGroupModel->save(['title'=>$name]);
        // $result = $AuthGroupModel->save([['title'=>$name], ['remark'=>$remark]]);
        if (!$result) {
            return ajax_return(ResultCode::E_DB_ERROR, '操作失败!');
        }

        $id = $AuthGroupModel->id;
        $returnData = $AuthGroupModel->where('id', '=' ,$id)->find();

        return ajax_return(ResultCode::E_DB_ERROR, '操作成功!', $returnData);
    }

    //编辑角色
    public function audit()
    {
        $params = $this->request->put();

        $id = $params['id'];

        $role = AuthGroupModel::get($id);

        if (!$role) {
            return ajax_return(ResultCode::E_PARAM_ERROR, '角色不存在!');
        }

        $role->name = $params['name'];
        // $role->remark = $params['remark'];
        $role->save();
        
    }
}