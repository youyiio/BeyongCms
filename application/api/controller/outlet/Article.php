<?php

namespace app\api\controller\outlet;

use app\api\middleware\JWTOptionalCheck;
use app\common\library\ResultCode;
use app\common\model\cms\ArticleDataModel;
use app\common\model\cms\ArticleMetaModel;
use app\common\model\cms\ArticleModel;
use app\common\model\cms\CategoryArticleModel;
use app\common\model\cms\CategoryModel;
use app\common\model\cms\CommentModel;

class Article extends Base
{
    protected $middleware = [
        JWTOptionalCheck::class
    ];

    //查询文章列表
    public function timeline()
    {
        $params = $this->request->put();
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? 10;
        $orders = $params['orders'] ?? [];
        $filters = $params['filters'] ?? [];
        $cid = $filters['cid'] ?? 0;
        $cname = $filters['cname'] ?? '';

        $ArticleModel = new ArticleModel();
        $fields = 'id,title,keywords,thumb_image_id,post_time,update_time,create_time,is_top,status,read_count,sort,author';
        if (empty($cid) && !empty($cname)) {
            $category = CategoryModel::where(['name' => $cname])->find();
            if (!empty($category)) {
                $cid = $category['id'];
            } else {
                $cid = -1;
            }
        }

        $order = [];
        if ($orders && !empty($orders['isTop'])) {
            $order['is_top'] = $orders['isTop'] == 'desc' ? 'desc' : 'asc';
        }
        $order['post_time'] = 'desc';

        $where[] = ['status', '=', ArticleModel::STATUS_PUBLISHED];

        if ($cid) {
            $childs = CategoryModel::getChild($cid);
            $cids = $childs['ids'];
            $cids[] = $cid;
            $fields = 'cms_article.id,title,keywords,thumb_image_id,post_time,update_time,create_time,is_top,status,read_count,sort,author';
            $list = ArticleModel::hasWhere('CategoryArticle', [['category_id', 'in', $cids]], $fields)->where($where)->order($order)->paginate($size, false, ['page' => $page]);
        } else {
            $list = $ArticleModel->where($where)->field($fields)->order($order)->paginate($size, false, ['page' => $page]);
        }

        //添加缩略图和分类
        $CategoryArticleModel = new CategoryArticleModel();
        foreach ($list as $art) {
            $art['thumbImage'] = findThumbImage($art);
            $categorysIds = CategoryArticleModel::where('article_id', '=', $art['id'])->column('category_id');
            $art['categorys'] = CategoryModel::where('id', 'in', $categorysIds)->field('id,name,title')->select();
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

    // 最新文章列表
    public function latest()
    {
        $params = $this->request->put();
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? 10;
        $filters = $params['filters'] ?? '';
        $cid = $filters['cid'] ?? 0;
        $cname = $filters['cname'] ?? '';

        $ArticleModel = new ArticleModel();
        $fields = 'id,title,keywords,thumb_image_id,post_time,update_time,create_time,is_top,status,read_count,sort,author';
        if (empty($cid) && !empty($cname)) {
            $category = CategoryModel::where(['name' => $cname])->find();
            if (!empty($category)) {
                $cid = $category['id'];
            } else {
                $cid = -1;
            }
        }

        $where[] = ['status', '=', ArticleModel::STATUS_PUBLISHED];
        $order = 'post_time desc';
        if ($cid) {
            $childs = CategoryModel::getChild($cid);
            $cids = $childs['ids'];
            $cids[] = $cid;
            $fields = 'cms_article.id,title,keywords,thumb_image_id,post_time,update_time,create_time,is_top,status,read_count,sort,author';
            $list = ArticleModel::hasWhere('CategoryArticle', [['category_id', 'in', $cids]], $fields)->where($where)->order($order)->paginate($size, false, ['page' => $page]);
        } else {
            $list = $ArticleModel->where($where)->field($fields)->order($order)->paginate($size, false, ['page' => $page]);
        }

        //添加缩略图和分类
        $CategoryArticleModel = new CategoryArticleModel();
        foreach ($list as $art) {
            $art['thumbImage'] = findThumbImage($art);

            $categotyIds = $CategoryArticleModel->where('article_id', '=', $art['id'])->column('category_id');
            $categorys = [];
            foreach ($categotyIds as $cateId) {
                $CategoryModel = new CategoryModel();
                $categorys[] = $CategoryModel->where('id', '=', $cateId)->field('id,name,title')->find();
            }
            $art['categorys'] = $categorys;
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

    //查询热门文章
    public function hottest()
    {
        $params = $this->request->put();
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? 10;
        $filters = $params['filters'] ?? '';
        $cid = $filters['cid'] ?? 0;
        $cname = $filters['cname'] ?? '';

        $ArticleModel = new ArticleModel();
        if (empty($cid) && !empty($cname)) {
            $category = CategoryModel::where(['name' => $cname])->find();
            if (!empty($category)) {
                $cid = $category['id'];
            } else {
                $cid = -1;
            }
        }

        $where[] = ['status', '=', ArticleModel::STATUS_PUBLISHED];
        $order = 'read_count desc';
        $fields = 'id,title,keywords,thumb_image_id,post_time,update_time,create_time,is_top,status,read_count,sort,author';
        if ($cid) {
            $childs = CategoryModel::getChild($cid);
            $cids = $childs['ids'];
            $cids[] = $cid;
            $fields = 'cms_article.id,title,keywords,thumb_image_id,post_time,update_time,create_time,is_top,status,read_count,sort,author';
            $list = ArticleModel::hasWhere('CategoryArticle', [['category_id', 'in', $cids]], $fields)->where($where)->order($order)->paginate($size, false, ['page' => $page]);
        } else {
            $list = $ArticleModel->where($where)->field($fields)->order($order)->paginate($size, false, ['page' => $page]);
        }

        //添加缩略图和分类
        $CategoryArticleModel = new CategoryArticleModel();
        foreach ($list as $art) {
            $art['thumbImage'] = findThumbImage($art);

            $categotyIds = $CategoryArticleModel->where('article_id', '=', $art['id'])->column('category_id');
            $categorys = [];
            foreach ($categotyIds as $cateId) {
                $CategoryModel = new CategoryModel();
                $categorys[] = $CategoryModel->where('id', '=', $cateId)->field('id,name,title')->find();
            }
            $art['categorys'] = $categorys;
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

    //查询文章内容
    public function query($aid)
    {
        $ArticleModel = new ArticleModel();

        $fields = 'id,title,keywords,description,content,read_count,thumb_image_id,comment_count,author,status,create_time,post_time,update_time';
        $art = $ArticleModel->where('id', $aid)->field($fields)->find();

        if (!$art) {
            return ajax_error(ResultCode::E_DATA_NOT_FOUND, '文章不存在');
        }

        //文章分类
        $data = $art->categorys()->select();
        $categorys = [];
        if (!empty($data)) {
            foreach ($data as $val) {
                $categorys[] = [
                    'id' => $val['id'],
                    'name' => $val['name'],
                    'title' => $val['title'],
                ];
            }
        }
        //文章标签
        $articleMetaModel = new ArticleMetaModel();
        $tags = $articleMetaModel->_metas($art['id'], 'tag');
        //缩略图
        $thumbImage = findThumbImage($art);
        //附加图片
        $metaImages = findMetaImages($art);
        //附加文件
        $metaFiles = findMetaFiles($art);

        //返回数据
        $returnData = parse_fields($art->toArray(), 1);
        $returnData['tags'] = $tags;
        $returnData['categorys'] = $categorys;
        $returnData['thumbImage'] = $thumbImage;
        $returnData['metaImages'] = $metaImages;
        $returnData['metaFiles'] = $metaFiles;

        return ajax_return(ResultCode::ACTION_SUCCESS, '查询成功!', $returnData);
    }

    //查询文章评论
    public function comments($aid)
    {
        $article = ArticleModel::get($aid);
        if (empty($article)) {
            return ajax_return(ResultCode::E_PARAM_ERROR, '文章不存在');
        }

        $params = $this->request->put();
        $page = $params['page'] ?: 1;
        $size = $params['size'] ?: 5;
        $filters = $params['filters'] ?? '';
        $keyword = $filters['keyword'] ?? '';

        //查询评论
        $CommentModel = new CommentModel();
        $where = [
            ['content', 'like', '%' . $keyword . '%'],
            ['article_id', '=', $aid],
            ['status', '=', CommentModel::STATUS_PUBLISHED]
        ];

        $list = $CommentModel->where($where)->paginate($size, false, ['page' => $page]);

        return ajax_return(ResultCode::ACTION_SUCCESS, '查询成功!', to_standard_pagelist($list));
    }

    //查询相关推荐
    public function related($aid)
    {
        $params = $this->request->put();
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? 10;
        $filters = $params['filters'] ?? '';
        $cid = $filters['cid'] ?? 0;
        $cname = $filters['cname'] ?? '';

        if (empty($cid) && !empty($cname)) {
            $category = CategoryModel::where(['name' => $cname])->find();
            if (!empty($category)) {
                $cid = $category['id'];
            } else {
                $cid = -1;
            }
        }

        $where[] = ['article_a_id', '=', $aid];
        $whereOr[] = ['article_b_id', '=', $aid];
        $field = 'id,article_a_id,article_b_id,title_similar,content_similar';
        $ArticleDataModel = new ArticleDataModel();
        $dataList = $ArticleDataModel->where($where)->whereOr($whereOr)->field($field)->order('title_similar desc,content_similar desc')->limit(100)->select();
        $ids = [];
        foreach ($dataList as $articleData) {
            if ($articleData['article_a_id'] == $aid) {
                $ids[] = $articleData['article_b_id'];
            } else {
                $ids[] = $articleData['article_a_id'];
            }
        }

        $where = [];
        $where[] = ['status', '=', ArticleModel::STATUS_PUBLISHED];
        $where[] = ['id', 'in', $ids];
        $order = 'read_count desc';
        $fields = 'id,title,keywords,thumb_image_id,post_time,update_time,create_time,is_top,status,read_count,sort,author';
        $ArticleModel = new ArticleModel();
        if ($cid) {
            $childs = CategoryModel::getChild($cid);
            $cids = $childs['ids'];
            $fields = 'cms_article.id,title,keywords,thumb_image_id,post_time,update_time,create_time,is_top,status,read_count,sort,author';
            $list = ArticleModel::hasWhere('CategoryArticle', [['category_id', 'in', $cids]], $fields)->where($where)->order($order)->paginate($size, false, ['page' => $page]);
        } else {
            $list = $ArticleModel->where($where)->field($fields)->order($order)->paginate($size, false, ['page' => $page]);
        }

        //添加缩略图和分类
        $CategoryArticleModel = new CategoryArticleModel();
        foreach ($list as $art) {
            $art['thumbImage'] = findThumbImage($art);

            $categotyIds = $CategoryArticleModel->where('article_id', '=', $art['id'])->column('category_id');
            $categorys = [];
            foreach ($categotyIds as $cateId) {
                $CategoryModel = new CategoryModel();
                $categorys[] = $CategoryModel->where('id', '=', $cateId)->field('id,name,title')->find();
            }
            $art['categorys'] = $categorys;
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

    //搜索文章
    public function search()
    {
        $params = $this->request->put();
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? 10;
        $orders = $params['orders'] ?? [];
        $filters = $params['filters'] ?? [];
        $cid = $filters['cid'] ?? 0;
        $cname = $filters['cname'] ?? '';
        $keyword = $filters['keyword'] ?? '';

        $ArticleModel = new ArticleModel();
        $fields = 'id,title,keywords,thumb_image_id,post_time,last_update_time,create_time,is_top,status,read_count,sort,author';
        if (empty($cid) && !empty($cname)) {
            $category = CategoryModel::where(['title_en' => $cname])->find();
            if (!empty($category)) {
                $cid = $category['id'];
            } else {
                $cid = -1;
            }
        }

        $order = [];
        if ($orders && !empty($orders['isTop'])) {
            $order['is_top'] = $orders['isTop'] == 'desc' ? 'desc' : 'asc';
        }
        $order['post_time'] = 'desc';
        //dump($order);

        $where[] = ['status', '=', ArticleModel::STATUS_PUBLISHED];
        if ($keyword) {
            $where[] = ['title|keywords', 'like', '%' . $keyword . '%'];
        }

        if ($cid) {
            $childs = CategoryModel::getChild($cid);
            $cids = $childs['ids'];
            $cids[] = $cid;
            $fields = 'cms_article.id,title,keywords,thumb_image_id,post_time,last_update_time,create_time,is_top,status,read_count,sort,author';
            $list = ArticleModel::hasWhere('CategoryArticle', [['category_id', 'in', $cids]], $fields)->where($where)->order($order)->paginate($size, false, ['page' => $page]);
        } else {
            $list = $ArticleModel->where($where)->field($fields)->order($order)->paginate($size, false, ['page' => $page]);
        }

        //添加缩略图和分类
        $CategoryArticleModel = new CategoryArticleModel();
        foreach ($list as $art) {
            $art['thumbImage'] = findThumbImage($art);
            $categorysIds = CategoryArticleModel::where('article_id', '=', $art['id'])->column('category_id');
            $art['categorys'] = CategoryModel::where('id', 'in', $categorysIds)->field('id,title_cn,title_en')->select();
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
}
