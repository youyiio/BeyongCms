<?php 
namespace app\api\controller\app;

use app\api\controller\Base;
use app\common\library\ResultCode;
use app\common\model\cms\ArticleModel;
use app\common\model\cms\CategoryArticleModel;
use app\common\model\cms\CategoryModel;
use app\common\model\ImageModel;

class Article extends Base
{
    public function timeLine()
    {
        $params = $this->request->put();
        $page = $params['page'];
        $size = $params['size'];
        $filters = $params['filters'];
        $ArticleModel = new ArticleModel();

        $where = [];
        $fields = 'id,title,keywords,thumb_image_id,post_time,update_time,create_time,is_top,status,read_count,sort,author';
        if (isset($filters['cid']) && $filters['cid'] > 0) {
            $childs = CategoryModel::getChild($filters['cid']);
            $childCateIds = $childs['ids'];
            array_push($childCateIds, $filters['cid']);

            $fields = 'ArticleModel.id,title,keywords,thumb_image_id,post_time,update_time,create_time,is_top,status,read_count,sort,author';
            $ArticleModel = ArticleModel::hasWhere('CategoryArticle', [['category_id','in',$childCateIds]], $fields)->group([]); //hack:group用于清理hasmany默认加group key
        }

        $where[] = ['status', '=', ArticleModel::STATUS_PUBLISHED];
        $list = $ArticleModel->where($where)->field($fields)->order('post_time, desc')->paginate($size, false, ['page'=>$page]);

        //添加缩略图和分类
        $CategoryArticleModel = new CategoryArticleModel();
        foreach ($list as $art) {
            $art['thumbImage'] = $this->findThumbImage($art);

            $categotyIds = $CategoryArticleModel->where('article_id', '=', $art['id'])->column('category_id');
            $categotys = [];
            foreach ($categotyIds as $cateId) {
                $CategoryModel = new CategoryModel();
                $categotys[] = $CategoryModel->where('id', '=', $cateId)->field('id,name,title')->find();
            }
            $art['categorys'] = $categotys;
        }

        $list = $list->toArray();
        //返回数据
        $returnData['current'] = $list['current_page'];
        $returnData['pages'] = $list['last_page'];
        $returnData['size'] = $list['per_page'];
        $returnData['total'] = $list['total'];
        $returnData['records'] = parse_fields($list['data'], 1);

        return ajax_return(ResultCode::ACTION_SUCCESS, '查询成功!', $returnData);
    }

    //查找文章的缩略图
    public function findThumbImage($art)
    {
        $thumbImage = [];
        if (empty($art['thumb_image_id']) || $art['thumb_image_id'] == 0) {
            return $thumbImage;
        }

        $ImageModel = new ImageModel();
        $thumbImage = $ImageModel::get($art['thumb_image_id']);
    
        if (empty($thumbImage)) {
            return $thumbImage;
        }

        //完整路径
        $thumbImage['fullImageUrl'] = $ImageModel->getFullImageUrlAttr('',$thumbImage);
        $thumbImage['FullThumbImageUrlAttr'] = $ImageModel->getFullThumbImageUrlAttr('',$thumbImage);
        unset($thumbImage['remark']);
        unset($thumbImage['image_size']);
        unset($thumbImage['thumb_image_size']);
        unset($art['thumb_image_id']);

        $thumbImage = parse_fields($thumbImage->toArray(),1);
        
        return $thumbImage;
    }
}