<?php

/**
 * Created by VSCode.
 * User: cattong
 * Date: 2019-08-05
 * Time: 14:57
 */

namespace app\frontend\controller;

use app\common\model\cms\ArticleModel;

class Search extends Base
{

    //搜索词：q, 分页：p；路由为 search/:q/[:p] 模式
    //返回 list 结果集，page 分页
    public function index($q = '', $p = 1)
    {
        if (empty($q)) {
            return $this->error('请输入搜索词!');
        }

        // 验证搜索词
        $validation = $this->validateSearchTerm($q, [
            'maxLength' => 100,
            'allowWildcards' => true
        ]);

        if (!$validation['valid']) {
           return $this->error('搜索词存在不合法的关键字!');
        }

        // 使用安全的搜索词进行查询
        $q = $validation['term'];
        
        if (true) {
            $this->_searchFromDb($q, $p);
        } else {
            $this->_searchFromES($q, $p);
        }

        $this->assign('q', $q);

        return $this->fetch("search/index");
    }

    //从数据库搜索
    private function _searchFromDb($q = '', $p = '')
    {
        $where = [];
        $where[] = ['status', '=', ArticleModel::STATUS_PUBLISHED];

        $ArticleModel = new ArticleModel();
        $field = 'id,title,description,author,thumb_image_id,post_time,read_count,comment_count';
        $order = 'is_top desc,sort,post_time desc';
        $pageConfig = [
            'var_page' => 'p', //设置分页变量是p
            'query' => input('param.'),
            'page' => $p, //设置分页值
        ];
        $resultSet = $ArticleModel->where($where)->whereLike('title', '%' . $q . '%', 'and')->field($field)->order($order)->paginate(10, false, $pageConfig);

        $this->assign('list', $resultSet);
        $this->assign('page', $resultSet->render());
    }

    //从ElasticSearch搜索
    private function _searchFromES($q = '', $p = '') {}

    //记录用户搜索日志
    private function _searchLog($q = '', $p = 1) {}

    private function validateSearchTerm($input, $options = []) {
        $defaults = [
            'maxLength' => 100,
            'allowWildcards' => false, // 是否允许用户使用 % 和 _
            'allowedChars' => 'a-zA-Z0-9\p{L}\s\-_@.', // 允许的字符正则
            'minLength' => 1
        ];
        
        $options = array_merge($defaults, $options);
        
        // 1. 基本清理
        $term = trim($input ?? '');
        $term = strip_tags($term);
        
        // 2. 长度检查
        if (strlen($term) < $options['minLength']) {
            return ['valid' => false, 'error' => '搜索词太短'];
        }
        
        if (strlen($term) > $options['maxLength']) {
            $term = substr($term, 0, $options['maxLength']);
        }
        
        // 3. 字符过滤
        if ($options['allowWildcards']) {
            $pattern = '/[^' . $options['allowedChars'] . '%_]/u';
        } else {
            $pattern = '/[^' . $options['allowedChars'] . ']/u';
        }
        
        $term = preg_replace($pattern, '', $term);
        
        // 4. 防止过多的空格
        $term = preg_replace('/\s+/', ' ', $term);
        
        return [
            'valid' => true,
            'term' => $term,
            'original' => $input
        ];
    }
}
