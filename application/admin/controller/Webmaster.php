<?php
namespace app\admin\controller;

use think\facade\Env;
use think\facade\Log;

use app\frontend\controller\Base;
use app\common\model\cms\ArticleModel;
use app\common\model\cms\CategoryModel;
use app\common\model\ConfigModel;

use app\common\library\LibSitemap;
use think\facade\Cache;

/**
 * 站长工具
 * Webmaster class
 */
class Webmaster extends Base
{
    private $config = [
        "domain" => '',
        "xml_file" => "_sitemap", //不带后缀
    ];

    public function index()
    {
        return $this->fetch('webmaster/index');
    }

    //站长设置
    public function baidu()
    {
        $tab = input('param.tab', 'setting');
        if ($tab == 'setting') {
            $zhanzhang_site = input("post.zhanzhang_site", '');
            $zhanzhang_token = input("post.zhanzhang_token", '');
    
            $ConfigModel = new ConfigModel();
            $ConfigModel->where('key', 'zhanzhang_site')->setField('value', $zhanzhang_site);
            $ConfigModel->where('key', 'zhanzhang_token')->setField('value', $zhanzhang_token);
    
            cache('config', null);

            $this->success('操作成功');
        } else if ($tab == 'push-urls') {
            $zhanzhang_site = get_config('zhanzhang_site', '');
            $zhanzhang_token = get_config('zhanzhang_token', '');
            if (empty($zhanzhang_site) || empty($zhanzhang_token)) {
                $this->error("zhanzhang_site 或 zhanzhang_token  未配置！！！");
            }

            $urls = input("post.urls", '');
            if (empty($urls)) {
                $this->error("urls地址不能为空!");
            }

            $api = "http://data.zz.baidu.com/urls?site=$zhanzhang_site&token=$zhanzhang_token";
            $output = http_post($api, $urls);
            //dump($output);
            Log::info($output);

            $res = json_decode($output, true);
            Log::info($res);
            if (isset($res['error'])) {
               $this->error($res['message']);
            }
            if (isset($res['success'])) {
                $this->success("操作成功, 成功 {$res['success']} 个");
            }
            
        }        

    }

    //分析sitemap分割方式
    public function sitemapInfo($pageSize) 
    {

        $data = $this->_sitemapInfo($pageSize);

        $this->success("ok", null, $data);
    }

    //sitemap xml生成工具
    public function sitemap($pageSize, $startPage=1, $endPage=50)
    {
        $xmlFileName = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . $this->config['xml_file'];
        //清除旧的文件
        if (file_exists($xmlFileName . LibSitemap::SITEMAP_SEPERATOR . LibSitemap::INDEX_SUFFIX . LibSitemap::SITEMAP_EXT)) {
            foreach(glob($xmlFileName . "*") as $file) {
                unlink($file);
            }
        }

        // 计算生成时间
        $costTimeStart = millisecond();

        $sitemapInfo = $this->_sitemapInfo($pageSize);

        $sitemap = new LibSitemap($this->config['domain'] ? $this->config['domain'] : config('url_domain_root'));
        $sitemap->setXmlFile($xmlFileName);	 // 设置xml文件（可选）
        $sitemap->setDomain($this->config['domain'] ? $this->config['domain'] : config('url_domain_root')); // 设置自定义的根域名（可选）
        $sitemap->setIsSchemeMore(true);	// 设置是否写入额外的Schema头信息（可选）

        $lastPage = $sitemapInfo['lastPage'];
        $hookLastPage = $sitemapInfo['hookLastPage'];
        $cmsLastPage = $lastPage - $hookLastPage;
        $currentPage = $startPage;

        $sitemap->setCurrentMaxItems($pageSize);
        $sitemap->setCurrentSitemap($currentPage);

        if ($currentPage <= 1) {
            //生成index 首页
            $sitemap->addItem(url('frontend/Index/index', null, false, get_config('domain_name')), 1, "hourly", date_time());
            $sitemap->addItem(url('frontend/Index/business', null, false, get_config('domain_name')), 1, "monthly", date_time());
            $sitemap->addItem(url('frontend/Index/team', null, false, get_config('domain_name')), 1, "monthly", date_time());
            $sitemap->addItem(url('frontend/Index/partner', null, false, get_config('domain_name')), 1, "monthly", date_time());
            $sitemap->addItem(url('frontend/Index/about', null, false, get_config('domain_name')), 1, "monthly", date_time());
            $sitemap->addItem(url('frontend/Index/contact', null, false, get_config('domain_name')), 1, "monthly", date_time());
            

            //生成栏目item
            $CategoryModel = new CategoryModel();
            $resultSet = $CategoryModel->where(['status' => CategoryModel::STATUS_ONLINE])->order('sort asc')->select();
            foreach ($resultSet as $category) {
                $priority = LibSitemap::$PRIORITY[1];
                $loc = url('cms/Article/articleList', ['cid' => $category->id], false, get_config('domain_name'));
                $sitemap->addItem($loc, $priority, "daily", date_time());

                $loc = url('cms/Article/articleList', ['cname' => $category->name], false, get_config('domain_name'));
                $sitemap->addItem($loc, $priority, "daily", date_time());
            }

            $sitemap->forceEndSitemap();

            $currentPage += 1;
        }

        while ($currentPage <= $cmsLastPage && $currentPage <= $endPage) {
            //生成文章item
            $ArticleModel = new ArticleModel();
            $where = [
                'status' => ArticleModel::STATUS_PUBLISHED
            ];
            $resultSet = $ArticleModel->where($where)->order('id asc')->page($currentPage - 1, $pageSize)->select();
            foreach ($resultSet as $article) {
                $priority = LibSitemap::$PRIORITY[2];
                $loc = url('cms/Article/viewArticle', ['aid' => $article->id], false, get_config('domain_name'));
                $sitemap->addItem($loc, $priority, "weekly", $article->update_time);
            }

            $currentPage++;

            if (count($resultSet) < $pageSize || $currentPage > $cmsLastPage || $currentPage > $endPage) {
                $sitemap->forceEndSitemap();
            }
        }
        

        //sitemap_xml_hook 函数来实现hook sitemap，提供外部的url项目写入
        //外部建议，把sitemap_xml_hook函数定义在common_business.php中
        if (function_exists('sitemap_xml_hook')) {
            if ($currentPage > $cmsLastPage && $currentPage <= $endPage) {
                sitemap_xml_hook($sitemap, $pageSize, $currentPage - $cmsLastPage, $endPage - $cmsLastPage);
            }
        }

        $sitemap->endSitemap();


        //生成sitemap index;
        $sitemapLoc = url('cms/Sitemap/xml', null, false, get_config('domain_name'));
        $sitemapLoc = substr($sitemapLoc, 0, strlen($sitemapLoc) - 4);
        $sitemap->createSitemapIndex($sitemapLoc);        
        

        // 计算生成的时间
        $costTime = millisecond() - $costTimeStart;
        $costTime= sprintf('%01.2f', $costTime);

        $this->success("生成sitemap成功，用时 : $costTime (ms)");
    }

    //下载sitemap.txt
    public function sitemapTxt($pageSize)
    {
        $sitemapInfo = $this->_sitemapInfo($pageSize);
        $lastPage = $sitemapInfo['lastPage'];
        $hookLastPage = $sitemapInfo['hookLastPage'];

        $sitemapLoc = url('cms/Sitemap/xml', null, false, get_config('domain_name'));
        $urls = [];
        for ($i = 1; $i <= $lastPage; $i++) {
            $urls[] = $sitemapLoc . LibSitemap::SITEMAP_SEPERATOR . $i . LibSitemap::SITEMAP_EXT;
        }

        $filename = "sitemap.txt";
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: p_w_upload; filename="' . $filename . '"');
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Pragma:public");

        echo implode("\n", $urls);
    }

    private function _sitemapInfo($pageSize) 
    {
        $data = Cache::get('sitemap_info', []);
        if (!empty($data)) {
            return $data;
        }

        $pageSize = intval($pageSize);

        //urls总数
        $total = 0;
        //每页数量
        $listRows = $pageSize;
        //总页数
        $lastPage = 0;
        $hookTotal = 0;
        $hookLastPage = 0;

        //生成index 首页
        $total += 6;

        //生成栏目item
        $CategoryModel = new CategoryModel();
        $total += $CategoryModel->where(['status' => CategoryModel::STATUS_ONLINE])->count();
        $lastPage += 1; //独立一页

        //生成文章item
        $ArticleModel = new ArticleModel();
        $where = [
            'status' => ArticleModel::STATUS_PUBLISHED
        ];
        $articleCount = $ArticleModel->where($where)->count();
        $total += $articleCount;

        $lastPage += (int) ceil($articleCount / $listRows);        

        //sitemap_xml_count_hook 函数来实现业务类链接统计
        if (function_exists('sitemap_xml_count_hook')) {
            $hookTotal = sitemap_xml_count_hook();
            $total += $hookTotal;

            $hookLastPage = (int) ceil($hookTotal / $listRows);
            $lastPage += $hookLastPage;
        }

        $data = [
            "pageSize" => $pageSize,
            "total" => $total,
            "currentPage" => 1,
            "lastPage" => $lastPage,
            "hookTotal" => $hookTotal,
            "hookLastPage" => $hookLastPage
        ];

        //Cache::set("sitemap_info", $data, 20);

        return $data;
    }
}