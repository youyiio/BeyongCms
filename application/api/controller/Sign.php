<?php
namespace app\api\controller;

use app\common\library\ResultCode;
use app\common\logic\ActionLogLogic;
use app\common\logic\UserLogic;
use app\common\model\AuthGroupAccessModel;
use app\common\model\UserModel;
use Firebase\JWT\JWT;
use think\facade\Cache;
use youyi\util\PregUtil;
use youyi\util\StringUtil;

class Sign extends Base
{
    //use \app\common\controller\SignTrait; //使用trait

    // 注册
    public function register()
    {
        if ($this->request->method() != 'POST') {
            return ajax_error(ResultCode::SC_FORBIDDEN, '非法访问！请检查请求方式！');
        }
        
        //请求的body数据
        $params = $this->request->put();

        //$needParams = ['account', 'nickname', 'mobile', 'email', 'password', 'repassword'];

        $check = validate('User')->scene('register')->check($params);
        if ($check !== true) {
            return ajax_error(ResultCode::E_DATA_VERIFY_ERROR, validate('User')->getError());
        }

        $UserLogic = new UserLogic();

        $mobile = StringUtil::getRandNum(11);
        $email = $mobile .'@' . StringUtil::getRandString(6) . '.com';
        if (PregUtil::isMobile($params['username'])) {
            $mobile = $params['username'];
        } else if (PregUtil::isEmail($params['username'])) {
            $email = $params['username'];
        }
        
        $nickname = isset($params['nickname']) ? $params['nickname'] : '用户' . substr($mobile, 5);

        $user  = $UserLogic->register($mobile, $params['password'], $nickname, $email, '', UserModel::STATUS_ACTIVED);
        if (!$user) {
            return ajax_error(ResultCode::E_DB_OPERATION_ERROR, $UserLogic->getError());
        }

        $UserModel = new UserModel();
        //完善用户资料
        $profileData = [
            'id' => $user['id'],
            'head_url' => '/static/cms/image/head/0002.jpg',
            'referee' => 1, //$data['referee'], //推荐人
            'register_ip' => request()->ip(0, true),
            'from_referee' => cookie('from_referee'),
            'entrance_url'     => cookie('entrance_url'),
        ];
        $UserModel->where('id', $user['id'])->setField($profileData);

        //权限初始化
        $group[] = [
            'uid' => $user->id,
            'group_id' => config('user_group_id')
        ];
        $AuthGroupAccessModel = new AuthGroupAccessModel();
        $AuthGroupAccessModel->insertAll($group);

        $returnData = [
            'uid' => $user->id,
            'account' => $user->account,
            'nickname' => $user->nickname,
            'mobile' => $user->mobile,
            'email' => $user->email,
            'status' => $user->status,
            'registerTime' => $user->register_time
        ];

        return ajax_return(ResultCode::ACTION_SUCCESS, '注册成功', $returnData);
    }

    //登录
    public function login()
    {
        if ($this->request->method() != 'POST') {
            return ajax_error(ResultCode::SC_FORBIDDEN, '非法访问！请检查请求方式！');
        }

        $needParams = ['username', 'password'];
        
        $params = $params = $this->request->put();;

        //登录次数判断
        $tryLoginCountMark = $params['username'] . '_try_login_count';
        $tryLoginCount = Cache::get($tryLoginCountMark);
        if ($tryLoginCount > 5) {
           return ajax_error(ResultCode::E_USER_STATE_FREED, '登录错误超过5次,账号被临时冻结1天');
        }
        if ($tryLoginCount >= 5) {
           Cache::set($tryLoginCountMark, $tryLoginCount + 1, strtotime(date('Y-m-d 23:59:59'))-time());
           
           return ajax_error(ResultCode::E_USER_STATE_FREED, '登录错误超过5次,账号被临时冻结1天');
        }

        //初始化登录错误次数
        Cache::remember($tryLoginCountMark, function () {
            return 0;
        }, strtotime(date('Y-m-d 23:59:59')) - time());

        try {
            $UserLogic = new UserLogic();
            $user = $UserLogic->login($params['username'], $params['password'], request()->ip(0, true));
            if (!$user) {                
                Cache::inc($tryLoginCountMark);

                return ajax_error(ResultCode::E_UNKNOW_ERROR, $UserLogic->getError());
            }
        } catch(\Exception $e) {            
            Cache::inc($tryLoginCountMark);

            throw $e;
        }

        //登录成功清除
        Cache::rm($tryLoginCountMark);     

        $uid = $user['id'];
        $ActionLogLogic = new ActionLogLogic();
        $ActionLogLogic->addLog($uid, \ApiCode::E_USER_LOGIN_ERROR, '登录');

        $payload = [
            'iss' => 'jwt_admin',  //该JWT的签发者
            'aud' => 'jwt_api', //接收该JWT的一方，可选
            'iat' => time(),  //签发时间
            'exp' => time() + config('jwt.jwt_expired_time'),  //过期时间
//            'nbf' => time() + 60,  //该时间之前不接收处理该Token
            'sub' => 'domain',  //面向的用户
            'jti' => md5(uniqid('JWT') . time()),  //该Token唯一标识
            'data' => [
                'uid' => $uid,
            ]
        ];
        $token = JWT::encode($payload, config('jwt.jwt_key'), config('jwt.jwt_alg'));

        $data = [
            // 'uid' => $uid,
            // 'account' => $user->account,
            // 'nickname' => $user->nickname,
            // 'mobile' => $user->mobile,
            // 'email' => $user->email,
            // 'status' => $user->status,
            // 'registerTime' => $user->register_time,
            'token' => 'Bearer ' . $token
        ];

        return ajax_success($data);
    }
}