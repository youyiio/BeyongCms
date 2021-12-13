<?php

namespace app\api\controller;

use app\common\library\ResultCode;
use app\common\model\AuthGroupAccessModel;
use app\common\model\AuthGroupModel;
use app\common\model\AuthRuleModel;
use app\common\model\UserModel;
use think\Validate;

//个人中心
class Ucenter extends Base
{
    // 获取用户信息
    public function getInfo()
    {
        if ($this->request->method() != 'GET') {
            return ajax_error(ResultCode::SC_FORBIDDEN, '非法访问！请检查请求方式！');
        }

        $params = $this->request->put();
        
        $payloadData = session('jwt_payload_data');
        if (!$payloadData) {
            return ajax_error(ResultCode::ACTION_FAILED, 'TOKEN自定义参数不存在！');
        }
        $uid = $payloadData->uid;
        if (!$uid) {
            return ajax_error(ResultCode::E_USER_NOT_EXIST, '用户不存在！');
        }
        $user = UserModel::get(['id' => $uid]);
        if (empty($user)) {
            return ajax_error(ResultCode::E_USER_NOT_EXIST, '用户不存在！');
        }

        $returnData = [
            'uid' => $uid,
            //'account' => $user->account,
            'nickname' => $user->nickname,
            'mobile' => $user->mobile,
            'email' => $user->email,
            //'status' => $user->status,
            'headUrl' => $user->head_url,
            'sex' => $user->sex,
            'registerTime' => $user->register_time,
            'roles' => ['admin']
        ];

        return ajax_success($returnData);
    }
    
    //编辑个人资料
    public function profile()
    {
        $userInfo = $this->user_info;
        $user = UserModel::get($userInfo->uid);

        if (!$user) {
            ajax_return(ResultCode::E_DATA_NOT_FOUND, '用户不存在!');
        }

        $params = $this->request->put();
        $res = $user->isUpdate(true)->allowField(true)->save($params);

        if (!$res) {
            ajax_return(ResultCode::E_DB_ERROR, '操作失败!');
        }
        if (isset($params['description'])) {
            $user->meta('description', $params['description']);
        }

        //返回数据
        $UserModel = new UserModel();
        $data = $UserModel->where('id', $userInfo->uid)->field('id,nickname,head_url')->find();
        $roleIds = AuthGroupAccessModel::where(['uid'=> $userInfo->uid])->column('group_id');

        $AuthGroupModel = new AuthGroupModel();
        $data['roles'] = $AuthGroupModel->where('id', 'in', $roleIds)->field('id,title')->select();
        $data['description'] = $user->metas('description');
        $returnData = parse_fields($data->toArray(), 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!', $returnData);
    }

    //查询权限菜单
    public function menus()
    {
        $userInfo = $this->user_info;
        $user = UserModel::get($userInfo->uid);

        if (!$user) {
            ajax_return(ResultCode::E_DATA_NOT_FOUND, '用户不存在!');
        }

        $roleIds = AuthGroupAccessModel::where(['uid'=> $userInfo->uid])->column('group_id');

        $menus = [];
        $AuthGroupModel = new AuthGroupModel();
        foreach ($roleIds as $id) {
           $rules = $AuthGroupModel->where('id', $id)->value('rules');
           $array = explode(',', $rules);
           $menus = array_merge($menus, $array);
        }
        
        $AuthRuleModel = new AuthRuleModel();
        $list = $AuthRuleModel->where('id', 'in', $menus)->select();

        $list = parse_fields($list, 1);
        $returnData = getTree($list, 0 , 'id', 'pid', 6);

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!', $returnData);
    }

    //修改密码
    public function modifyPassword()
    {
        $params = $this->request->put();
        $validate = Validate::make([
                'oldPassword' => 'require',
                'password' => 'require|length:6,20|alphaDash'
            ]);

        if (!$validate->check($params)) {
            return ajax_error(ResultCode::E_PARAM_VALIDATE_ERROR, $validate->getError());
        }

        $oldPassword = $params['oldPassword'];
        $oldPassword = encrypt_password($oldPassword, get_config('password_key'));

        $uid = $this->user_info;
        $uid = $uid->uid;
        $user = UserModel::get($uid);
        if ($user['password'] !== $oldPassword) {
            return ajax_error(ResultCode::E_PARAM_ERROR, '旧密码不正确');
        }

        $password = encrypt_password($params['password'], get_config('password_key'));
        $res = $user->isUpdate(true)->save(['password' => $password]);
        if (!$res) {
            return ajax_error(ResultCode::E_DB_ERROR, '修改失败!');
        }

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功!', '');
    }
}