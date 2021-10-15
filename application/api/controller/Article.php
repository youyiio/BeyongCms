<?php
namespace app\api\controller;

use app\admin\controller\Image;
use app\api\controller\Base;
use app\common\library\ResultCode;
use app\common\model\BaseModel;
use app\common\model\cms\ArticleMetaModel;
use app\common\model\cms\ArticleModel;
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
        $params = $this->request->put();
     
        $check = validate('Article')->scene('list')->check($params);
        if ($check !== true) {
            return ajax_error(ResultCode::E_DATA_VERIFY_ERROR, validate('Article')->getError());
        }
        
        $page = $params['page'];
        $size = $params['size'];
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

        $artModel = new ArticleModel();
        $list = $artModel->where($where)->field($fields)->order($order)->paginate($size, false, $pageConfig);

        return ajax_return(ResultCode::ACTION_SUCCESS, '查询成功!', $list);
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
       
        //文章评论
        $CommentModel = new CommentModel();
        $pageConfig = [
            'type' => '\\app\\common\\paginator\\BootstrapTable',
        ];
        $where = [
            'article_id' => $aid,
            'status' => CommentModel::STATUS_PUBLISHED
        ];
        $comments = $CommentModel->where($where)->order('id desc')->paginate(6, false, $pageConfig);
        
        $returnData = [
            'id' => $art['id'],
            'title' => $art['title'],
            'keywords' => $art['keywords'],
            'description' => $art['description'],
            'content' => $art['content'],
            'author' => $art['author'],
            'status' => $art['status'],
            'createTime' => $art['create_time'],
            'postTime' => $art['post_time'],
            'updateTime' => $art['update_time'],
            'readCount' => $art['read_count'],
            'commentCount' => $art['comment_count'],
            'tags' => $tags,
            'comments' => $comments,
        ];

        return ajax_return(ResultCode::ACTION_SUCCESS, '查询成功!', $returnData);
    }

    //新增文章
    public function create() 
    {
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
        $author = $userModel->where('id', $this->uid)->value('nickname');
        $data = [
            'uid' => $this->uid,
            'title' => $params['title'],
            'category_ids' => $params['category_ids'],
            'tags' => $params['tags'],
            'status' => $status,
            'description' => $params['description'],
            'keywords' => $params['keywords'],
            'content' => remove_xss($params['content']),
            'post_time' => date_time(),
            'author' => $author,
            'thumb_image_id' => isset($params['thumb_image_id'])?: ''
        ];
        
        $res = $articleModel->add($data);
        if (!$res) {
            return ajax_return(ResultCode::E_DB_OPERATION_ERROR, '新增失败',$articleModel->getError());
        }

        //返回数据
        $artId = $articleModel->id;
        $art = ArticleModel::get($artId);

        $articleMetaModel = new ArticleMetaModel();
        $tags = $articleMetaModel->_metas($artId, 'tag');
        
        if (empty($art['thumb_image_id'])) {
            $fullThumbImageUrl = '';
        } else {
            $imageModel = new ImageModel();
            $image = $imageModel->where('id', $art['thumb_image_id'])->find();
            
            $switch = get_config('oss_switch');
            if ($switch !== 'true') {
                $fullThumbImageUrl = url_add_domain($image['thumb_image_url']);
                $fullThumbImageUrl = str_replace('\\', '/', $fullThumbImageUrl);
            } else {
                $fullThumbImageUrl = $image['oss_image_url'];
            }
        }

        $returnData = [
            'id' => $art['id'],
            'title' => $art['title'],
            'keywords' => $art['keywords'],
            'description' => $art['description'],
            'content' => $art['content'],
            'author' => $art['author'],
            'status' => $art['status'],
            'createTime' => $art['create_time'],
            'postTime' => $art['post_time'],
            'updateTime' => $art['update_time'],
            'tags' => $tags,
            'readCount' => 0,
            'commentCount' => 0,
            'fullThumbImageUrl' => $fullThumbImageUrl
        ];

        return ajax_return(ResultCode::ACTION_SUCCESS, '创建成功!', $returnData);
    }

    public function edit($aid) 
    {
        $art = ArticleModel::get($aid);
        if (!$art) {
            return ajax_error(ResultCode::SC_NOT_FOUND, '文章不存在');
        }

        //请求的body数据
        $params = $this->request->put();
        $check = validate('Article')->scene('edit')->check($params);
        if ($check !== true) {
            return ajax_error(ResultCode::E_PARAM_ERROR, '', validate('Article')->getError());
        }
           
        //更新数据
        $res = $art->allowField(true)->isUpdate(true)->save($params);

        if(!$res){
            return ajax_return(ResultCode::E_DB_OPERATION_ERROR, '更新失败', $art->getError());
        }

        //返回数据
        $articleMetaModel = new ArticleMetaModel();
        $tags = $articleMetaModel->_metas($art['id'], 'tag');
        
        if (empty($art['thumb_image_id'])) {
            $fullThumbImageUrl = '';
        } else {
            $imageModel = new ImageModel();
            $image = $imageModel->where('id', $art['thumb_image_id'])->find();
            
            $switch = get_config('oss_switch');
            if ($switch !== 'true') {
                $fullThumbImageUrl = url_add_domain($image['thumb_image_url']);
                $fullThumbImageUrl = str_replace('\\', '/', $fullThumbImageUrl);
            } else {
                $fullThumbImageUrl = $image['oss_image_url'];
            }
        }

        $returnData = [
            'id' => $art['id'],
            'title' => $art['title'],
            'keywords' => $art['keywords'],
            'description' => $art['description'],
            'content' => $art['content'],
            'author' => $art['author'],
            'status' => $art['status'],
            'createTime' => $art['create_time'],
            'postTime' => $art['post_time'],
            'updateTime' => $art['update_time'],
            'readCount' => $art['read_count'],
            'commentCount' => $art['comment_count'],
            'tags' => $tags,
            'fullThumbImageUrl' => $fullThumbImageUrl
        ];

        return ajax_return(ResultCode::ACTION_SUCCESS, '更新成功', $returnData);
    }

    public function delete($aid) 
    {
        $art = ArticleModel::get($aid);

        if (!$art) {
            return ajax_return(ResultCode::SC_NOT_FOUND, '文章不存在');
        }

        $res = ArticleModel::update(['id'=>$aid, 'status'=>ArticleModel::STATUS_DELETED]);
        if (!$res) {
            return ajax_return(ResultCode::E_DB_OPERATION_ERROR, '删除失败', $art->getError());
        }

        return ajax_return(ResultCode::ACTION_SUCCESS, '删除成功');
    }
}