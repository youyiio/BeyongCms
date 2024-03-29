<?php

/**
 * Created by VSCode.
 * User: Administrator
 * Date: 2018/5/9
 * Time: 14:55
 */

namespace app\common\model;


use think\Model;

class FileModel extends Model
{
    protected $name = 'sys_file';
    protected $pk = 'id';

    protected $type = [
        'id'    => 'integer'
    ];

    public function getFullFileUrlAttr($value, $data)
    {
        $switch = get_config('oss_switch');
        if ($switch !== 'true') {
            $fullImageUrl = url_add_domain($data['file_url']);
            $fullImageUrl = str_replace('\\', '/', $fullImageUrl);
        } else {
            $fullImageUrl = $data['oss_url'];
        }

        return $fullImageUrl;
    }

    public function getFullImageUrlAttr($value, $data)
    {
        $switch = get_config('oss_switch');
        if ($switch !== 'true') {
            $fullImageUrl = url_add_domain($data['file_url']);
            $fullImageUrl = str_replace('\\', '/', $fullImageUrl);
        } else {
            $fullImageUrl = $data['oss_url'];
        }

        return $fullImageUrl;
    }

    public function getFullThumbImageUrlAttr($value, $data)
    {
        $switch = get_config('oss_switch');
        if ($switch !== 'true') {
            $fullImageUrl = url_add_domain($data['thumb_image_url']);
            $fullImageUrl = str_replace('\\', '/', $fullImageUrl);
        } else {
            $fullImageUrl = $data['oss_url'];
        }

        return $fullImageUrl;
    }

    /**
     * 获取取图片
     * @param $ids，string|array
     * @return array
     */
    public static function getFiles($ids)
    {
        if (empty($ids)) {
            return [];
        }
        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }
        if (!is_array($ids)) {
            return [];
        }

        $FileModel = new FileModel();
        $results = $FileModel->where([['id', 'in', $ids]])->select();
        if (empty($results)) {
            return [];
        }
        $res = [];
        foreach ($results as $v) {
            $res[] = [
                'id'           => $v->id,
                'file_url'     => $v->file_url,
                'full_file_url' => $v->full_file_url,
                'thumb_image_url'    => $v->thumb_image_url,
                'full_thumb_image_url' => $v->full_thumb_image_url,
                'image_url'       => $v->file_url,
                'full_image_url'  => $v->full_file_url,
                'remark' => $v->remark,
            ];
        }

        return $res;
    }
}
