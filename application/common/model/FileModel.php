<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/9
 * Time: 14:55
 */

namespace app\common\model;


use think\Model;

class FileModel extends Model
{
    protected $name = CMS_PREFIX . 'file';

    protected $pk = 'id';

    public function getFullFileUrlAttr($value, $data)
    {
        $switch = 'false';//get_config('oss_switch');
        if ($switch !== 'true') {
            $fullImageUrl = url_add_domain($data['file_url']);
            $fullImageUrl = str_replace('\\', '/', $fullImageUrl);
        } else {
            $fullImageUrl = $data['oss_image_url'];
        }

        return $fullImageUrl;
    }
}