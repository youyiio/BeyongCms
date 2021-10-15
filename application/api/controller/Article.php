<?php
namespace app\api\controller;

use app\admin\controller\Image;
use app\api\controller\Base;
use app\common\library\ResultCode;
use app\common\model\BaseModel;
use app\common\model\cms\ArticleMetaModel;
use app\common\model\cms\ArticleModel;
use app\common\model\ImageModel;
use app\common\model\UserModel;

// 文章相关接口
class Article extends Base
{
    //查询列表
    public function list($page=1, $size=10)
    {
        $where = [
            "status" => ArticleModel::STATUS_PUBLISHED
        ];
        $fields = 'id,title,thumb_image_id,post_time,update_time,create_time,is_top,status,read_count,sort,relateds';
        $order = [
            'sort' => 'desc',
            'post_time' => 'desc',
        ];
        $pageConfig = [
            'query' => ['page' => $page]
        ];

        $ArticleModel = new ArticleModel();
        $list = $ArticleModel->where($where)->field($fields)->order($order)->paginate($size, false, $pageConfig);

        return ajax_success(to_standard_pagelist($list));
    }

    // crud 增删查改
    public function query($aid) 
    {
        $article = ArticleModel::get($aid);

        return ajax_success($article);
    }

    public function create() 
    {
        $payloadData = session('jwt_payload_data');
        if (!$payloadData) {
            return ajax_error(ResultCode::ACTION_FAILED, 'TOKEN自定义参数不存在！');
        }
        $uid = $payloadData->uid;
        if (!$uid) {
            return ajax_error(ResultCode::E_USER_NOT_EXIST, '用户不存在！');
        }

        //请求的body数据
        $params = $this->request->put();
        $check = validate('Article')->scene('create')->check($params);
        if ($check !== true) {
            return ajax_error(ResultCode::E_DATA_VERIFY_ERROR, validate('Article')->getError());
        }
        
        $articleModel = new ArticleModel();
        //新增文章
        if (get_config('article_audit_switch') === 'false') {
            $status = $articleModel::STATUS_PUBLISHED;
        } else {
            $status = $articleModel::STATUS_PUBLISHING;        
        }
     
        $userModel = new UserModel();
        $author = $userModel->where('id', $uid)->value('nickname');
        $data = [
            'uid' => $uid,
            'title' => $params['title'],
            'category_ids' => $params['category_ids'],
            'tags' => $params['tags'],
            'status' => $status,
            'description' => $params['description'],
            'keywords' => $params['keywords'],
            'content' => remove_xss($params['content']),
            'post_time' => date_time(),
            'author' => $author,
            'thumb_image_id' => isset($params['thumb_image_id']) ? $params['thumb_image_id'] : ''
        ];
        
        $res = $articleModel->add($data);
        if (!$res) {
            return ajax_return(ResultCode::ACTION_FAILED, '创建失败',$articleModel->getError());
        }

        //返回数据
        $articleId = $articleModel->id;
        $article = ArticleModel::get($articleId);
        $articleMetaModel = new ArticleMetaModel();
        $tags = $articleMetaModel->_metas($articleId, 'tag');
        
        if (empty($article['thumb_image_id'])) {
            $fullThumbImageUrl = '';
        } else {
            $imageModel = new ImageModel();
            $thumbImageUrl = $imageModel->where('id', $article['thumb_image_id'])->value('thumb_image_url');
            
            $switch = get_config('oss_switch');
            if ($switch !== 'true') {
                $fullThumbImageUrl = url_add_domain($thumbImageUrl);
                $fullThumbImageUrl = str_replace('\\', '/', $fullThumbImageUrl);
            } else {
                $fullThumbImageUrl = $data['oss_image_url'];
            }
        }

        $returnData = [
            'id' => $article['id'],
            'title' => $article['title'],
            'keywords' => $article['keywords'],
            'description' => $article['description'],
            'content' => $article['content'],
            'author' => $article['author'],
            'status' => $article['status'],
            'createTime' => $article['create_time'],
            'postTime' => $article['post_time'],
            'updateTime' => $article['update_time'],
            'tags' => $tags,
            'readCount' => 0,
            'commentCount' => 0,
            'fullThumbImageUrl' => $fullThumbImageUrl
        ];

        return ajax_return(ResultCode::ACTION_SUCCESS, '创建成功', $returnData);
    }

    public function edit($aid) 
    {
        $data = input("post.");
        $article = ArticleModel::update($data, ["id" => $aid]);

        return ajax_success($article);
    }

    public function delete($aid) 
    {
        $data = [
            "status" => ArticleModel::STATUS_DELETED
        ];
        $res = ArticleModel::update($data, $aid);

        return ajax_return(ResultCode::ACTION_SUCCESS, '删除成功');
    }
}