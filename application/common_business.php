<?php
/**
 * Created by VSCode.
 * User: cattong
 * Date: 2018-06-12
 * Time: 11:31
 */

/**
 * 业务类sitemap生成 钩子
 * @param \app\common\library\LibSitemap $sitemap
 * @param int $pageSize 查询每页最多数量
 * @param int $startPage 起始页
 * @param int $endPage  结束页
 */
function sitemap_xml_hook(\app\common\library\LibSitemap $sitemap, $pageSize=800, $startPage=1, $endPage=50)
{

}

/**
 * 业务类sitemap链接统计 钩子
 *
 */
function sitemap_xml_count_hook()
{
    return 0;
}