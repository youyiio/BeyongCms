<?php
namespace app\api\controller;

use app\admin\controller\Image;
use app\api\controller\Base;
use app\common\library\ResultCode;
use app\common\logic\ArticleLogic;
use app\common\model\BaseModel;
use app\common\model\cms\ArticleMetaModel;
use app\common\model\cms\ArticleModel;
use app\common\model\cms\CategoryModel;
use app\common\model\cms\CommentModel;
use app\common\model\ImageModel;
use app\common\model\UserModel;


// 文章相关接口
class Article extends Base
{
    //查询列表
    protected $payloadData;
    protected $uid;
    
    public function initialize() {
        
        $this->payloadData = session('jwt_payload_data');
        if (!$this->payloadData) {
            return ajax_error(ResultCode::ACTION_FAILED, 'TOKEN自定义参数不存在！');
        }
        $this->uid = $this->payloadData->uid;
        if (!$this->uid) {
            return ajax_error(ResultCode::E_USER_NOT_EXIST, '用户不存在！');
        }
    }
    
    public function list($page=1, $size=10)
    {
        $ArticleModel = new ArticleModel();

        $params = $this->request->put();
        $check = validate('Article')->scene('list')->check($params);
        if ($check !== true) {
            return ajax_error(ResultCode::E_DATA_VERIFY_ERROR, validate('Article')->getError());
        }
     
        $pages = $params['page']?: 1;
        $size = $params['size']?: 10;
        $filters = $params['filters']?: ''; 

        $where = [];
        $fields = 'id,title,thumb_image_id,post_time,update_time,create_time,is_top,status,read_count,sort';
        if ($filters['keywords']) {
            $where[] = ['keywords', 'like', '%'.$filters['keywords'].'%'];
        }
      
        if ($filters['categoryId'] > 0) {
            $childs = CategoryModel::getChild($filters['categoryId']);
            $childCateIds = $childs['ids'];
            array_push($childCateIds, $filters['categoryId']);

            $fields = 'ArticleModel.id,title,thumb_image_id,post_time,update_time,create_time,is_top,status,read_count,sort';
            $ArticleModel = ArticleModel::hasWhere('CategoryArticle', [['category_id','in',$childCateIds]], $fields)->group([]); //hack:group用于清理hasmany默认加group key
        }

        //文章状态
        $status = $filters['status'];
        if ($status !== '') {
            $where[] = ['status', '=', $status];
        }

        //查询时间
        $queryTimeField = ($status == '' || $status == ArticleModel::STATUS_PUBLISHED) ? 'post_time' : 'create_time';
        if (!empty($filters['startTime'])) {
            $where[] = [$queryTimeField, '>=', $filters['startTime'] . '00:00:00'];
        }
        if (!empty($filters['endTime'])) {
            $where[] = [$queryTimeField, '<=', $filters['endTime'] . '23:59:59'];
        }
      
        $order = [
            'sort' => 'desc',
            'post_time' => 'desc',
        ];
        $pageConfig = [
            'page' => $pages,
            'query' => ''
        ];
        
        $list = $ArticleModel->where($where)->field($fields)->order($order)->paginate($size, false, $pageConfig);

        return ajax_return(ResultCode::ACTION_SUCCESS, '查询成功!', to_standard_pagelist($list));
    }

    // 查询文章内容
    public function query($aid) 
    {
        $art = ArticleModel::get($aid);

        if (!$art) {
            return ajax_error(ResultCode::SC_NOT_FOUND, '文章不存在');
        }

        //文章标签
        $articleMetaModel = new ArticleMetaModel();
        $tags = $articleMetaModel->_metas($art['id'], 'tag');
        //缩略图
        $fullThumbImageUrl = '';
        if (!empty($art['thumb_image_id'])) {
            $field = 'id,image_name,thumb_image_url,image_url,oss_image_url,create_time';
            $ImageModel = new ImageModel();
            $thumbImage = ImageModel::get($art['thumb_image_id']);
            $fullThumbImageUrl = $ImageModel->getFullImageUrlAttr('',$thumbImage);
        }
        
        //附加图片和文件
        $Images = get_image($art->metas('image'));
        $Images = $Images->toArray();
        $metaImages = parse_fields($Images,1);
      
        $metaFiles = get_file($art->metas('file'));
        $metaFiles = $metaFiles->toArray();
        $metaFiles = parse_fields($metaFiles,1);
      
        //返回数据
        $returnData = [
            'id' => $art['id'],
            'title' => $art['title'],
            'keywords' => $art['keywords'],
            'description' => $art['description'],
            'tags' => $tags,
            'fullThumbImageUrl' => $fullThumbImageUrl,
            'content' => $art['content'],
            'readCount' => $art['read_count'],
            'commentCount' => $art['comment_count'],
            'author' => $art['author'],
            'status' => $art['status'],
            'createTime' => $art['create_time'],
            'postTime' => $art['post_time'],
            'updateTime' => $art['update_time'],
            'metaImages' => $metaImages,
            'metaFiles' => $metaFiles
        ];

        return ajax_return(ResultCode::ACTION_SUCCESS, '查询成功!', $returnData);
    }

    //新增文章
    public function create() 
    {
        //请求的body数据
        $params = $this->request->put();

        //新增文章
        $articleModel = new ArticleModel();
        if (get_config('article_audit_switch') === 'false') {
            $status = $articleModel::STATUS_PUBLISHED;
        } else {
            $status = $articleModel::STATUS_PUBLISHING;        
        }
     
        if (empty($params['author'])) {
            $userModel = new UserModel();
            $author = $userModel->where('id', $this->uid)->value('nickname');
        } else {
            $author = $params['author'];
        }
        
        $data = [
            'uid' => $this->uid,
            'title' => $params['title'],
            'description' => $params['description'],
            'keywords' => $params['keywords'],
            'author' => $author,
            'tags' => $params['tags']?: '',
            'content' => remove_xss($params['content']),
            'category_ids' => $params['categoryIds'],
            'thumb_image_id' => $params['thumbImageId']?: '',
            'meta_image_ids' => $params['metaImageIds']?: '',
            'meta_file_ids' => $params['metaFileIds']?: '',
            'status' => $status,
        ];

        $articleLogic = new ArticleLogic();
        $artId = $articleLogic->addArticle($data);
        
        if (!$artId) {
            return ajax_error(ResultCode::E_DB_OPERATION_ERROR, '新增失败',$artId->geterror());
        }

        //返回数据
        $art = ArticleModel::get($artId);

        $articleMetaModel = new ArticleMetaModel();
        $tags = $articleMetaModel->_metas($artId, 'tag');
        
        //缩略图
        $thumbImage = '';
        if (!empty($art['thumb_image_id'])) {
            //$field = 'id,image_name,thumb_image_url,image_url,oss_image_url,create_time';
            $ImageModel = new ImageModel();
            $thumbImage = $ImageModel::get($art['thumb_image_id']);
         
            $thumbImage['fullImageUrl'] = $ImageModel->getFullImageUrlAttr('',$thumbImage);
            $thumbImage['FullThumbImageUrlAttr'] = $ImageModel->getFullThumbImageUrlAttr('',$thumbImage);
        
            $thumbImage = $thumbImage->toArray();
            $thumbImage = parse_fields($thumbImage,1);
        }

        //附件图片和文件
        $Images = get_image($art->metas('image'));
        $Images = $Images->toArray();
        $metaImages = parse_fields($Images,1);
      
        $metaFiles = get_file($art->metas('file'));
        $metaFiles = $metaFiles->toArray();
        $metaFiles = parse_fields($metaFiles,1);
      
        //返回json格式
        $returnData = [
            'id' => $art['id'],
            'title' => $art['title'],
            'keywords' => $art['keywords'],
            'description' => $art['description'],
            'tags' => $tags,
            'thumbImage' => $thumbImage,
            'content' => $art['content'],
            'readCount' => 0,
            'commentCount' => 0,
            'author' => $art['author'],
            'status' => $art['status'],
            'createTime' => $art['create_time'],
            'postTime' => $art['post_time'],
            'updateTime' => $art['update_time'],
            'metaImages' => $metaImages,
            'metaFiles' => $metaFiles
        ];

        return ajax_return(ResultCode::ACTION_SUCCESS, '创建成功!', $returnData);
    }

    public function edit($aid) 
    {
        //请求的body数据
        $params = $this->request->put();
        $params = parse_fields($params,0);
        
        if ($aid !== $params['id']) {
            return ajax_error(ResultCode::E_PARAM_ERROR, '参数错误');
        }
        
        //更新数据
        $articleLogic = new ArticleLogic();
        $res = $articleLogic->editArticle($params);

        if(!$res){
            return ajax_return(ResultCode::E_DB_OPERATION_ERROR, '更新失败');
        }

        //返回数据
        $articleModel = new ArticleModel();
        $fields = 'id,title,keywords,description,content,read_count,comment_count,author,status,create_time,post_time,update_time,thumb_image_id';
        $art = $articleModel->where('id', '=', $aid)->field($fields)->find();
        
        //标签
        $articleMetaModel = new ArticleMetaModel();
        $tags = $articleMetaModel->_metas($aid, 'tag');

        //缩略图
        $thumbImage = '';
        if (!empty($art['thumb_image_id'])) {
            $ImageModel = new ImageModel();
            $thumbImage = $ImageModel::get($art['thumb_image_id']);
        
            $thumbImage['fullImageUrl'] = $ImageModel->getFullImageUrlAttr('',$thumbImage);
            $thumbImage['FullThumbImageUrlAttr'] = $ImageModel->getFullThumbImageUrlAttr('',$thumbImage);
        
            $thumbImage = $thumbImage->toArray();
            $thumbImage = parse_fields($thumbImage,1);
        }
        unset($art['thumb_image_id']);
        //附件图片和文件
        $Images = get_image($art->metas('image'));
        $Images = $Images->toArray();
        $metaImages = parse_fields($Images,1);
      
        $metaFiles = get_file($art->metas('file'));
        $metaFiles = $metaFiles->toArray();
        $metaFiles = parse_fields($metaFiles,1);

        //返回json格式
        $returnData = parse_fields($art->toArray(),1);
        $returnData['tags'] = $tags;
        $returnData['thumbImage'] = $thumbImage;
        $returnData['metaFiles'] = $metaFiles;
        $returnData['metaImages'] = $metaImages;
          
        return ajax_return(ResultCode::ACTION_SUCCESS, '更新成功', $returnData);
    }

    //发布文章
    public function publish()
    {
        $params = $this->request->put();
       
        if (count($params) !== 1) {
            return ajax_return(ResultCode::ACTION_FAILED, '参数错误');
        }

        if (isset($params['id']) && !is_int($params['id'])) {
            return ajax_return(ResultCode::ACTION_FAILED, '参数错误');
        }

        if (isset($params['ids']) && count($params['ids']) == 1) {
            return ajax_return(ResultCode::ACTION_FAILED, '参数错误');
        }

        $data = [
            'status' => ArticleModel::STATUS_PUBLISHING,
            'post_time' => date_time()
        ];
        //审核开关关闭时
        if (get_config('article_audit_switch') === 'false') {
            $data['status'] = ArticleModel::STATUS_PUBLISHED;
        }

        //发布文章
        if (isset($params['id'])) {
            $ids = $params['id'];        
        } 
        if (is_array($params['ids'])) {
            $ids = $params['ids'];        
        }

        $ArticleModel = new ArticleModel();
    
        $success = $ArticleModel->where('id', 'in', $ids)->setField($data);
        $fails = count($ids) - $success;

        $returnData = ['success'=> $success, 'fail' => $fails];
        return ajax_return(ResultCode::ACTION_SUCCESS, '发布文章成功!', $returnData);
        
    }

    //审核文章
    public function audit()
    {
        $params = $this->request->put();
        if (count($params) !== 1) {
            return ajax_return(ResultCode::ACTION_FAILED, '参数错误');
        }

        if (isset($params['id']) && !is_int($params['id'])) {
            return ajax_return(ResultCode::ACTION_FAILED, '参数错误');
        }

        if (isset($params['ids']) && count($params['ids']) == 1) {
            return ajax_return(ResultCode::ACTION_FAILED, '参数错误');
        }

        $data = [
            'status' => ArticleModel::STATUS_PUBLISHED,
            'post_time' => date_time()
        ];

        //发布文章
        if (isset($params['id'])) {
            $ids = $params['id'];        
        } 
        if (is_array($params['ids'])) {
            $ids = $params['ids'];        
        }

        $ArticleModel = new ArticleModel();
    
        $success = $ArticleModel->where('id', 'in', $ids)->setField($data);
        $fails = count($ids) - $success;

        $returnData = ['success'=> $success, 'fail' => $fails];
        return ajax_return(ResultCode::ACTION_SUCCESS, '审核文章成功!', $returnData);
    }


    //删除文章
    public function delete() 
    {
        $params = $this->request->put();
        if (count($params) !== 1) {
            return ajax_return(ResultCode::ACTION_FAILED, '参数错误');
        }

        if (isset($params['id']) && !is_int($params['id'])) {
            return ajax_return(ResultCode::ACTION_FAILED, '参数错误');
        }

        if (isset($params['ids']) && count($params['ids']) == 1) {
            return ajax_return(ResultCode::ACTION_FAILED, '参数错误');
        }

        //删除文章
        if (isset($params['id'])) {
            $ids = $params['id'];        
        } 
        if (is_array($params['ids'])) {
            $ids = $params['ids'];        
        }

        $ArticleModel = new ArticleModel();
        $success = $ArticleModel->where('id', 'in', $ids)->setField('status', '=', ArticleModel::STATUS_DELETED);
        $fails = count($ids) - $success;

        $returnData = ['success'=> $success, 'fail' => $fails];
        return ajax_return(ResultCode::ACTION_SUCCESS, '删除文章成功!', $returnData);
    }

    public function comments($id)
    {
        $article = ArticleModel::get($id);
        
        if (empty($article)) {
            return ajax_return(ResultCode::E_PARAM_ERROR, '文章不存在');
        }

        $params = $this->request->put();
        $pages = $params['page']?: 1;
        $size = $params['size']?: 5;
        $query = $params['filters']?: '';
        //查询评论
        $CommentModel = new CommentModel();
        
        $where['article_id'] = $id;
        $where['status'] = CommentModel::STATUS_PUBLISHED;

        $pageConfig = [
            'pages' => $pages,
            'query' => $query
        ];

        $list = $CommentModel->where($where)->paginate($size, false, $pageConfig);
        //总页数

        return ajax_return(ResultCode::ACTION_SUCCESS, '查询成功!', to_standard_pagelist($list));

    }
}