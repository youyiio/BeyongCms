<?php

namespace app\common\controller;

use app\common\model\FileModel;
use think\facade\Validate;
use beyong\commons\utils\StringUtils;
use think\exception\FileException;

/**
 * 文件上传组件
 * 使用方法，文件控制器中，use \app\common\controller\File,
 * 即会继承这些方法
 */
trait File
{

    /**
     * 通用文件上传
     * 支持参数：file,exts
     */
    public function upload()
    {

        $tmpFile = request()->file('file');
        if (empty($tmpFile)) {
            return $this->result(null, 0, '请选择上传文件', 'json');
        }

        //通用文件后缀，加强安全;
        $common_file_exts = 'zip,rar,doc,docx,xls,xlsx,ppt,pptx,ppt,pptx,pdf,txt,exe,bat,sh,apk,ipa';
        $common_file_exts .= '.pg,gif,png,jpg,jpeg,webp,bmp';
        $exts = request()->param('exts', ''); //文件格式，中间用,分隔

        if (empty($exts)) {
            $exts = $common_file_exts;
        } else {
            $exts = strtolower($exts);
            $exts = explode(',', $exts);
            $exts = array_diff($exts, ['php']);
            $exts = implode(',', $exts);
        }
        $rule = [
            'size' => 1024 * 1024 * 200, //200M
            'ext' => $exts
        ];

        //不能信任前端传进来的文件名, thinkphp默认使表单里的filename后
        $check = Validate::rule('FileUpload', $rule)->message([
            'size' => '尺寸过大', //200M
            'ext' => '文件类型不符合要求'
        ])->check(['size' => $tmpFile->getSize(), 'ext' => $tmpFile->getExtension()]);
        if ($check !== true) {
            return $this->result($check);
        }
        
        //文件目录
        $saveName = StringUtils::getRandString(20) . "." . $tmpFile->getOriginalExtension();
        $saveNameWithPath = date('Ymd') . DIRECTORY_SEPARATOR . $saveName;
        $filePath = root_path() . 'public';
        $fileUrl = DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'file' . DIRECTORY_SEPARATOR . $saveNameWithPath;
        $path = $filePath . $fileUrl;


        $file = null;
        try {
            $movePath = str_replace(DIRECTORY_SEPARATOR . $saveName, '', $path);
            $file = $tmpFile->move($movePath, $saveName);
        } catch (FileException $e) {
            return $this->error($e->getMessage());
        }

        $fileSize = $file->getSize();
        $ext = $file->getExtension();

        //原始上传文件名
        $fileName = $_FILES['file']['name'];
        //存入数据库
        $data = [
            'file_url' => $fileUrl,
            'file_path' => $filePath,
            'size' => $fileSize,
            'ext' => strtolower($ext),
            'name' => $fileName,
            'real_name' => $file->getFileInfo(),
            'create_by' => $this->uid,
            'create_time' => date_time()
        ];
        $FileModel = new FileModel();
        $fileId = $FileModel->insertGetId($data);

        $data['id'] = $fileId;
        $data['ext_icon_url'] = '/static/common/img/format/' . strtolower($ext) . '.png';

        return $this->result($data, 1, '文件上传成功', 'json');
    }

    //上传软件，桌面端软件，如果.exe.zip
    // 文件过大时，需要在php.ini配置post_max_size, upload_max_filesize
    public function uploadSoftware()
    {
        ini_set('memory_limit', '256M');
        //ini_set('post_max_size', '128M');
        //ini_set('upload_max_filesize', '128M');
        $tmpFile = request()->file('file');
        if (empty($tmpFile)) {
            //return $this->error('请选择上传文件');
            return $this->result(null, 0, '请选择上传文件', 'json');
        }
        
        $rule = [
            'ext' => 'zip,rar,exe',
            'size' => 1024 * 1024 * 200, //200M
        ];
        //不能信任前端传进来的文件名, thinkphp默认使表单里的filename后
        $check = Validate::rule('FileUpload', $rule)->message([
            'size' => '尺寸过大', //200M
            'ext' => '文件类型不符合要求'
        ])->check(['size' => $tmpFile->getSize(), 'ext' => $tmpFile->getExtension()]);
        if ($check !== true) {
            return $this->result($check);
        }

        //文件目录
        $saveName = StringUtils::getRandString(20) . "." . $tmpFile->getOriginalExtension();
        $saveNameWithPath = date('Ymd') . DIRECTORY_SEPARATOR . $saveName;
        $filePath = root_path() . 'public';
        $fileUrl = DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'software' . DIRECTORY_SEPARATOR . $saveNameWithPath;
        $path = $filePath . $fileUrl;

        $file = null;
        try {
            $movePath = str_replace(DIRECTORY_SEPARATOR . $saveName, '', $path);
            $file = $tmpFile->move($movePath, $saveName);
        } catch (FileException $e) {
            return $this->error($e->getMessage());
        }

        $version = input('param.version');

        $fileSize = $file->getSize();

        //原始上传文件名
        $originalName = $tmpFile->getOriginalName();

        //存入数据库
        $data = [
            'file_url' => $fileUrl,
            'file_path' => $filePath,
            'file_name' => $saveName,
            'file_size' => $fileSize,
            'create_time' => date_time()
        ];
        $FileModel = new FileModel();
        $fileId = $FileModel->insertGetId($data);

        $data['id'] = $fileId;
        $data['ext'] = $file->getExtension(); //文件后缀

        $this->success('文件上传成功', false, $data);
    }

    //上传应用,移动类应用，如apk, ipa
    public function uploadApp()
    {
        $tmpFile = request()->file('file');
        if (empty($file)) {
            return $this->error('请选择上传文件');
        }
        $rule = [
            'ext' => 'apk,ipa',
            'size' => 1024 * 1024 * 200, //200M
        ];

        //不能信任前端传进来的文件名, thinkphp默认使表单里的filename后
        $check = Validate::rule('FileUpload', $rule)->message([
            'size' => '尺寸过大', //200M
            'ext' => '文件类型不符合要求'
        ])->check(['size' => $tmpFile->getSize(), 'ext' => $tmpFile->getExtension()]);
        if ($check !== true) {
            return $this->result($check);
        }

        $appId = input('param.app_id');
        $version = input('param.version');
        $fileName = $file->getInfo('name');

         //文件目录
        $saveName = StringUtils::getRandString(20) . "." . $tmpFile->getOriginalExtension();
        $saveNameWithPath = $appId . DIRECTORY_SEPARATOR . $version . DIRECTORY_SEPARATOR . $saveName;
        $filePath = root_path() . 'public';
        $fileUrl = DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $saveNameWithPath;
        $path = $filePath . $fileUrl;

        $file = null;
        try {
            $movePath = str_replace(DIRECTORY_SEPARATOR . $saveName, '', $path);
            $file = $tmpFile->move($movePath, $saveName);
        } catch (FileException $e) {
            return $this->error($e->getMessage());
        }


        $fileSize = $file->getSize();

        //原始上传文件名
        $originalName = $tmpFile->getOriginalName();

        //存入数据库
        $data = [
            'file_url' => $fileUrl,
            'file_path' => $filePath,
            'file_name' => $saveName,
            'file_size' => $fileSize,
            'create_time' => date_time()
        ];
        $FileModel = new FileModel();
        $fileId = $FileModel->insertGetId($data);

        $data['id'] = $fileId;
        $data['ext'] = $file->getExtension(); //文件后缀

        $this->success('文件上传成功', false, $data);
    }
}
