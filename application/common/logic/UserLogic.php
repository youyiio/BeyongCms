<?php
namespace app\common\logic;

use think\Model;
use app\common\exception\ModelException;
use app\common\library\ResultCode;
use app\common\model\UserModel;
use app\common\model\api\TokenModel;
use app\common\model\RoleModel;
use app\common\model\UserRoleModel;
use beyong\commons\utils\StringUtils;
use think\facade\Cookie;

class UserLogic extends Model
{

    public function register($mobile, $password, $nickname = '', $email = '', $account = '', $status = UserModel::STATUS_ACTIVED)
    {
        $UserModel = new UserModel();
        $user = $UserModel->createUser($mobile, $password, $nickname, $email, $account, $status);
        if (!$user) {
            $this->error = $UserModel->error;
            return false;
        }

        // 添加普通会员角色
        $RoleModel = new RoleModel();
        $role = $RoleModel->where('name', 'member')->find();
        if ($role) {
            $UserRoleModel = new UserRoleModel();
            $UserRoleModel->save(['uid' => $user['id'], 'role_id' => $role->id]);
        }

        return $user;
    }

    /**
     * @param $account 邮箱或手机号
     * @param $password 密码
     * @param $ip
     * @return Model
     * @throws ModelException
     */
    public function login($account, $password, $ip='127.0.0.1')
    {
        $UserModel = new UserModel();
        $user = $UserModel->checkUser($account, $password);
        if (!$user) {
            throw new ModelException(ResultCode::E_USER_NOT_EXIST, '帐号不正确');
        }

        switch ($user->status) {
            case UserModel::STATUS_APPLY:
                throw new ModelException(ResultCode::E_USER_STATE_NOT_ACTIVED, '用户未激活');
                break;
            case UserModel::STATUS_FREEZED:
                throw new ModelException(ResultCode::E_USER_STATE_FREED, '用户已冻结');
                break;
            case UserModel::STATUS_DELETED:
                throw new ModelException(ResultCode::E_USER_STATE_DELETED, '用户已删除');
                break;
            default:
                break;
        }

        $userId = $user['id'];
        $user->markLogin($userId, $ip);

        return $user;
    }

    public function logout($userId, $accessId, $deviceId)
    {
        //$PushTokenModel = new PushTokenModel();
        //return $PushTokenModel->logout($userId, $accessId, $deviceId);
    }

    public function modifyPassword($userId, $oldPassword, $newPassword)
    {
        $UserModel = new UserModel();
        $user = $UserModel->find($userId);
        if (!$user) {
            throw new ModelException(ResultCode::E_USER_NOT_EXIST, '用户不存在!');
        }

        $tempPassword = encrypt_password($oldPassword, $user['salt']);
        if ($tempPassword != $user->password) {
            throw new ModelException(ResultCode::E_DATA_VALIDATE_ERROR, '原始密码不正确!');
        }

        $newPassword = encrypt_password($newPassword, $user['salt']);

        $data['id'] = $userId;
        $data['password'] = $newPassword;
        $result = $UserModel->isUpdate(true)->save($data);
        if ($result == false) {
            return false;
        }

        $user = $UserModel->find($userId);

        return $user;
    }

    public function findOrCreateToken($userId, $accessId, $deviceId)
    {
        $TokenModel = new TokenModel();
        $tokenInfo = $TokenModel->findByUserId($userId, $accessId, $deviceId);
        if (!$tokenInfo) {
            $tokenInfo = $TokenModel->createTokenInfo($userId, $accessId, $deviceId);
        }

        if ($tokenInfo['status'] == TokenModel::STATUS_DISABLED || $tokenInfo['status'] == TokenModel::STATUS_EXPIRED) {
            $userTokenInfo = $TokenModel->updateTokenInfo($userId, $accessId, $deviceId);
        }
        if (strtotime($tokenInfo['expire_time']) < time()) {
            $where['uid'] = $userId;
            $where['access_id'] = $accessId;
            $where['device_id'] = $deviceId;
            $TokenModel->where($where)->setField('status', TokenModel::STATUS_EXPIRED);

            $tokenInfo = $TokenModel->updateTokenInfo($userId, $accessId, $deviceId);
        }

        return $tokenInfo;
    }

    public function fillUserStuff(&$user, $accessId, $deviceId)
    {
        $userId = $user['id'];

        $user['device_id'] = $deviceId;

        $tokenInfo = $this->findOrCreateToken($userId, $accessId, $deviceId);
        if ($tokenInfo) {
            $user['token'] = $tokenInfo['token'];
            $user['expire_time'] = $tokenInfo['expire_time'];
        }

        return $user;
    }

    //新增用户
    public function createUser($mobile, $password, $nickname = '', $email = '', $account = '', $status = UserModel::STATUS_ACTIVED)
    {
        $UserModel = new UserModel();
        if (empty($nickname)) {
            $nickname = $mobile;
        }
        if ($UserModel->findByMobile($mobile)) {
            throw new ModelException(ResultCode::E_USER_MOBILE_HAS_EXIST, '手机号已经存在');
        }
        if (empty($email)) {
            $email = $mobile .'@' . StringUtils::getRandString(6) . '.com';
        } else if ($UserModel->findByEmail($email)) {
            throw new ModelException(ResultCode::E_USER_EMAIL_HAS_EXIST, '邮箱已经存在');
        }
        if (empty($account)) {
            $account = StringUtils::getRandString(12);
        } else if ($UserModel->where(['account' => $account])->find()) {
            throw new ModelException(ResultCode::E_USER_ACCOUNT_HAS_EXIST, '帐号已经存在');
        }

        $salt = StringUtils::getRandNum(24);
        $user = new UserModel();
        $user->mobile = $mobile;
        $user->email = $email;
        $user->account = $account;
        $user->password = encrypt_password($password, $salt);
        $user->salt = $salt;
        $user->status = $status;
        $user->nickname = $nickname;
        $currentTime = date('Y-m-d H:i:s');
        $user->register_time = $currentTime;

        //设置来源及入口url
        if (Cookie::has('from_referee') || Cookie::has('entrance_url')) {
            $user->from_referee = sub_str(Cookie::get('from_referee'), 0, 250);
            $user->entrance_url = sub_str(Cookie::get('entrance_url'), 0, 250);
        }

        $user->save();
        return $user->id;
    }

    //编辑用户
    public function editUser($uid, $params)
    {
        unset($params['password']); //防止修改密码
        $user = UserModel::get($uid);

        if (isset($params['mobile']) && $user['mobile'] != $params['mobile']) {
            if ($user->findByMobile($params['mobile'])) {
                throw new ModelException(ResultCode::E_USER_MOBILE_HAS_EXIST, '手机号已经存在');
            }
        }
        if (isset($params['email']) && $user['email'] != $params['email']) {
            if ($user->findByEmail($params['email'])) {
                throw new ModelException(ResultCode::E_USER_MOBILE_HAS_EXIST, '邮箱已经存在');
            }
        }
        if (isset($params['account']) && $user['account'] != $params['account']) {
            if ($user->findByAccount($params['account'])) {
                throw new ModelException(ResultCode::E_USER_MOBILE_HAS_EXIST, '账户已经存在');
            }
        }

        $res = $user->isUpdate(true)->allowField(true)->save($params);

        return $res;
    }
    
}
