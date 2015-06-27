<?php
define('IN_SAESPOT', 1);
define('CURRENT_DIR', pathinfo(__FILE__, PATHINFO_DIRNAME));

include(CURRENT_DIR . '/config.php');
include(CURRENT_DIR . '/common.php');

// 获取最近文章列表 $articledb
$articledb = $cache->get('home_articledb');

if($articledb === FALSE){
    $query_sql = "SELECT a.id,a.cid,a.uid,a.ruid,a.title,a.addtime,a.edittime,a.comments,a.isred,a.top,c.name as cname,u.avatar as uavatar,u.name as author,ru.name as rauthor
        FROM yunbbs_articles a  
        LEFT JOIN yunbbs_categories c ON c.id=a.cid
        LEFT JOIN yunbbs_users u ON a.uid=u.id
        LEFT JOIN yunbbs_users ru ON a.ruid=ru.id
        WHERE `cid` > '1'
        ORDER BY `top` DESC ,`edittime` DESC LIMIT ".$options['home_shownum'];
	
    $query = $DBS->query($query_sql);
    $articledb = array();
    while ($article = $DBS->fetch_array($query)) {
        // 格式化内容
        if($article['isred'] == '1' && $article['top'] == '1'){
             $article['title'] = "<span class=\"label label-warning\">置顶</span><span class=\"label label-success\">推荐</span>".$article['title'];
         }elseif($article['isred'] == '1'){
             $article['title'] = "<span class=\"label label-success\">推荐</span>".$article['title'];
         }elseif($article['top'] == '1'){
             $article['title'] = "<span class=\"label label-warning\">置顶</span>".$article['title'];
         }	
        //$article['addtime'] = showtime($article['addtime']);
        //$article['edittime'] = showtime($article['edittime']);
        $articledb[] = $article;
    }
    unset($article);
    $DBS->free_result($query);
    
    // set to cache
    $cache->set('home_articledb', $articledb);
}

// 页面变量
$title = $options['name'];

$site_infos = get_site_infos($DBS);
$newest_nodes = get_newest_nodes();
if(count($newest_nodes)==$options['newest_node_num']){
    $bot_nodes = get_bot_nodes();
}

$links = get_links();
$meta_kws = htmlspecialchars(mb_substr($options['name'], 0, 6, 'utf-8'));
if($options['site_des']){
    $meta_des = htmlspecialchars(mb_substr($options['site_des'], 0, 150, 'utf-8'));
}

$pagefile = CURRENT_DIR . '/templates/default/'.$tpl.'home.php';

include(CURRENT_DIR . '/templates/default/'.$tpl.'layout.php');

?>
