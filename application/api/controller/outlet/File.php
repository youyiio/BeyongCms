<?php

namespace app\api\controller\outlet;

use app\api\middleware\JWTOptionalCheck;
use app\common\library\ResultCode;
use app\common\model\FileModel;

class File extends Base
{
    protected $middleware = [
        JWTOptionalCheck::class
    ];

    public function list()
    {
        $params = $this->request->put();
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? 12;
        $orders = $params['orders'] ?? [];
        $filters = $params['filters'] ?? [];

        $FileModel = new FileModel();
        $list = $FileModel->order('create_time desc')->paginate($size, false, ['page' => $page]);
        foreach ($list as $file) {
            $file['full_file_url'] = $file->full_file_url;
        }
        $list = $list->toArray();

        $returnData['current'] = $list['current_page'];
        $returnData['pages'] = $list['last_page'];
        $returnData['size'] = $list['per_page'];
        $returnData['total'] = $list['total'];
        $returnData['records'] = parse_fields($list['data'], 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, '查询成功!', $returnData);
    }
}
