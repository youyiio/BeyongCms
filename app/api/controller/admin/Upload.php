<?php

namespace app\api\controller\admin;

use app\api\controller\admin\Base;
use app\common\library\ResultCode;
use app\common\model\FileModel;
use app\common\model\ImageModel;
use app\common\model\UserModel;
use think\facade\Env;
use think\facade\Validate;

class Upload extends Base
{
    use \app\common\controller\Image;

    public function __construct()
    {
        parent::initialize();
    }
    //图片上传
    public function image()
    {
        $isfile = $_FILES;
        if ($isfile['file']['tmp_name'] == '') {
            return ajax_return(ResultCode::ACTION_FAILED, '请选择上传文件');
        }

        $tmpFile = request()->file('file');
        //图片规定尺寸
        $imgWidth = request()->param('width/d', 0);
        $imgHeight = request()->param('height/d', 0);
        //缩略图尺寸
        $tbWidth = request()->param('thumbWidth/d', 0);
        $tbHeight = request()->param('thumbHeight/d', 0);
        //备注
        $remark = request()->param('remark/s', 0);

        //表单验证
        $check = Validate::check(
            ['file' => $tmpFile],
            ['file' => 'require|image|fileSize:4097152'],
            [
                'file.require' => '请上传图片',
                'file.image' => '不是图片文件',
                'file.fileSize' => '图片太大了',
            ]
        );
        if ($check !== true) {
            return ajax_return(ResultCode::E_PARAM_VALIDATE_ERROR, '参数验证失败！');
        }

        list($width, $height, $type) = getimagesize($tmpFile->getRealPath()); //获得图片宽高类型
        if ($imgWidth > 0 && $imgHeight > 0) {
            if (!($width >= $imgWidth - 10 && $width <= $imgWidth + 10 && $height >= $imgHeight - 10 && $height <= $imgHeight + 10)) {
                return ajax_return(ResultCode::E_PARAM_VALIDATE_ERROR, "图片尺寸不符合要求,原图:宽:$width*高:$height");
            }
        }

        //保存目录
        $filePath = root_path() . 'public' . DIRECTORY_SEPARATOR . 'upload';
        //文件验证&文件move操作
        $rule = ['ext' => 'in:jpg,gif,png,jpeg,bmp,ico,webp'];

        $validate = Validate::rule('image')->rule($rule);
        $data = ['ext' => $tmpFile->extension()];
        if (!$validate->check($data)) {
            return ajax_return(ResultCode::E_PARAM_VALIDATE_ERROR, '参数验证失败！', $validate->getError());
        }

        $file = $tmpFile->move($filePath);

        $dateDir = $filePath . DIRECTORY_SEPARATOR . date('Ymd');
        if (!file_exists($dateDir)) {
            mkdir($dateDir, 0777, true);
        }
        $saveName = date('Ymd') . '/' . md5(uniqid()) . '.' . $tmpFile->extension(); //实际包含日期+名字：如20180724/erwrwiej...dfd.ext
        $imgUrl = $filePath . DIRECTORY_SEPARATOR . $saveName;

        //图片缩放处理
        $image = \think\Image::open($file);
        $quality = get_config('image_upload_quality', 80); //获取图片清晰度设置，默认是80
        $extension = image_type_to_extension($type, false); //png格式时，quality不影响值；jpg|jpeg有效果
        if ($imgWidth > 0 && $imgHeight > 0) {
            //缩放至指定的宽高
            $image->thumb($imgWidth, $imgHeight, \think\Image::THUMB_FIXED); //固定尺寸缩放
            $image->save($imgUrl, $extension, $quality, true);
        }
        $user = $this->user_info;
        $userInfo = UserModel::find($user->uid);
        //插入数据库
        $data = [
            'file_url' => DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . $saveName,
            'file_path' => root_path() . 'public',
            'name' => $tmpFile->getoriginalName(),
            'real_name' => $tmpFile->getoriginalName(),
            'size' => $file->getSize(),
            'remark' => $remark,
            'create_time' => date_time(),
            'create_by' => $userInfo->id
        ];
        //缩略图
        if ($tbWidth > 0 && $tbHeight > 0) {
            $tbImgUrl = $file->getPath() . DIRECTORY_SEPARATOR . 'tb_' . $file->getFilename();
            //缩放至指定的宽高
            $image->thumb($tbWidth, $tbHeight, \think\Image::THUMB_FIXED); //固定尺寸缩放
            $image->save($tbImgUrl, $extension, $quality, true);
            $data['thumb_image_url'] =  DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . dirname($saveName) . DIRECTORY_SEPARATOR . 'tb_' . $file->getFilename();
        }

        if (get_config('oss_switch') === 'true') {
            if (!class_exists('\think\oss\OSSContext')) {
                return ajax_return(ResultCode::ACTION_FAILED, '您启用了OSS存储，却未安装 think-oss 组件，运行 > composer youyiio/think-oss 进行安装！');
            }

            $vendor = get_config('oss_vendor');
            $m = new \think\oss\OSSContext($vendor);
            $ossImgUrl = $m->doUpload($saveName, 'cms');
            $data['oss_image_url'] = $ossImgUrl;
        }

        $ImageModel = new ImageModel();
        $imageId = $ImageModel->insertGetId($data);

        //返回数据=
        $return = $ImageModel->where('id', '=', $imageId)->find()->toArray();

        $return['fullImageUrl'] = $ImageModel->getFullImageUrlAttr('', $return);
        $return['fullThumbImageUrl'] = $ImageModel->getFullThumbImageUrlAttr('', $return);
        $returnData = parse_fields($return, 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功', $returnData);
    }

    //文件上传
    public function file()
    {
        $isfile = $_FILES;
        if (!$isfile) {
            return ajax_return(ResultCode::ACTION_FAILED, '请选择上传文件');
        }

        //通用文件后缀，加强安全;
        $common_file_exts = 'zip,rar,doc,docx,xls,xlsx,ppt,pptx,ppt,pptx,pdf,txt,exe,bat,sh,apk,ipa';
        $exts = request()->param('exts', ''); //文件格式，中间用,分隔
        $remark = request()->param('remark/s', ''); //文件格式，中间用,分隔
        if (empty($exts)) {
            $exts = $common_file_exts;
        } else {
            $exts = strtolower($exts);
            $exts = explode(',', $exts);
            $exts = array_diff($exts, ['php']);
            $exts = implode(',', $exts);
        }


        //文件目录
        $filePath = root_path() . 'public';
        $fileUrl = DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'file';
        $path = $filePath . $fileUrl;

        //不能信任前端传进来的文件名, thinkphp默认使表单里的filename后缀
        $tmpFile = request()->file('file');
        $data = [
            'size' => $tmpFile->getSize(),
            'ext' => $tmpFile->extension()
        ];
        $rule = [
            'size' => "<=:" . 1024 * 1024 * 200,
            'ext' => "in:$exts"
        ];
        $validate = Validate::rule('file')->rule($rule);
        if (!$validate->check($data)) {
            return ajax_return(ResultCode::ACTION_FAILED, $validate->getError());
        }

        $user = $this->user_info;
        $userInfo = UserModel::find($user->uid);
        $file = $tmpFile->move($path);
        $saveName = date('Ymd') . '/' . md5(uniqid()) . '.' . $file->extension(); //实际包含日期+名字：如20180724/erwrwiej...dfd.ext
        $fileUrl = DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'file' . DIRECTORY_SEPARATOR . $saveName;
        $data = [
            'file_url' => DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . $saveName,
            'file_path' => root_path() . 'public',
            'name' => $tmpFile->getoriginalName(),
            'real_name' => $tmpFile->getoriginalName(),
            'size' => $file->getSize(),
            'remark' => $remark,
            'create_time' => date_time(),
            'create_by' => $userInfo->id
        ];

        $FileModel = new FileModel();
        $fileId = $FileModel->insertGetId($data);

        $return = $FileModel->where('id', '=', $fileId)->find()->toArray();

        $return['fullFileUrl'] = $FileModel->getFullFileUrlAttr('', $return);
        unset($return['file_path']);
        $returnData = parse_fields($return, 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, '操作成功', $returnData);
    }
}
