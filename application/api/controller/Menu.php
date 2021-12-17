<?php 
namespace app\api\controller;

use app\common\library\ResultCode;
use app\common\model\AuthRuleModel;
use beyong\commons\data\Tree;
use think\Validate;

class Menu extends Base
{
    public function list()
    {
        $params = $this->request->put();
        $page = $params['page'];
        $size = $params['size'];

        $filters = $params['filters'];
        $keyword = $filters['keyword']?? '';
        $pid = $filters['pid']?? 0;
        $depth = $filters['depth']?? 1;

        $where = [];
        $where[] = ['belongs_to', '=', 'admin'];
        if (!empty($keyword)) {
            $where[] = ['title', 'like', '%'.$keyword.'%'];
        }
        
        $AuthRuleModel = new AuthRuleModel();
        $list = $AuthRuleModel->where($where)->order('id asc')->select();
     
        // 获取树形或者list数据
        $data = getTree($list, $pid, 'id', 'pid', $depth);
        if (isset($filters['struct']) && $filters['struct'] === 'list') {
            $data = getList($list, $pid, 'id', 'pid', $depth);
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

    //新增菜单
    public function create()
    {
        $params = $this->request->put();

        $validate = Validate::make([
            'pid' => 'require|integer',
            'name' => 'unique:'. config('database.prefix') . 'sys_auth_rule,name',
            'title' => 'require',
            'type' => 'require|integer',
           
        ]);
        if (!$validate->check($params)) {
            return ajax_return(ResultCode::ACTION_FAILED, '操作失败!', $validate->getError());
        }
        
        $AuthRuleModel = new AuthRuleModel();
        $res = $AuthRuleModel->save($params);
        $id = $AuthRuleModel->id;
        if (!$res) {
            return ajax_return(ResultCode::ACTION_FAILED, '操作失败!');
        }

        $returnData = AuthRuleModel::get($id);
        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!', $returnData);
    }

    //编辑菜单
    public function edit()
    {
        $params = $this->request->put();

        $validate = Validate::make([
            'id' => 'require',
            'name' => 'unique:'. config('database.prefix') . 'sys_auth_rule,name',
            'title' => 'require',
            'type' => 'require|integer',
           
        ]);
        if (!$validate->check($params)) {
            return ajax_return(ResultCode::ACTION_FAILED, '操作失败!', $validate->getError());
        }

        $menu = AuthRuleModel::get($params['id']);
     
        $menu->name = $params['name']?? '';
        $menu->title = $params['title']?? '';
        $menu->icon = $params['icon']?? '';
        $menu->type = $params['type']?? '';
        $menu->is_menu = $params['isMenu']?? '';
        $menu->sort = $params['sort']?? '';
        $menu->belongs_to = $params['belongsTo']?? '';
        $res = $menu->save();

        if (!$res) {
            return ajax_return(ResultCode::ACTION_FAILED, '操作失败!');
        }

        $data = AuthRuleModel::get($params['id']);
        $returnData = parse_fields($data, 1);
        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!', $returnData);
    }

    //删除菜单
    public function delete($id)
    {
        $menu = AuthRuleModel::get($id);
        if (!$menu) {
            return ajax_return(ResultCode::E_DATA_NOT_FOUND, '菜单不存在!');
        }

        $res = $menu->delete();
        if (!$res) {
            return ajax_return(ResultCode::ACTION_FAILED, '操作失败!');
        }

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!');
    }
}