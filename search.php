<?php
define('IN_SAESPOT', 1);
define('CURRENT_DIR', pathinfo(__FILE__, PATHINFO_DIRNAME));

include(CURRENT_DIR . '/config.php');
include(CURRENT_DIR . '/common.php');

if(!isset($_GET['q'])){
    header('location: /');
    exit;
}
$page=1;
if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}

$keyword = addslashes($_GET['q']);

// 处理正确的页数
$table_status = $DBS->fetch_one_array("select count(1) as count from `yunbbs_articles` where title like '%".$keyword."%'");

$taltol_article = $table_status['count'];

$taltol_page = ceil($taltol_article/$options['list_shownum']);
if($page<=0){
     $page = 1;
}elseif($page>$taltol_page){
    $page = $taltol_page;
}
$articledb=array();
if($taltol_article > 0){
    $query_sql = "SELECT a.id,a.cid,a.uid,a.ruid,a.title,a.addtime,a.edittime,a.comments,a.isred,c.name as cname,u.avatar as uavatar,u.name as author,ru.name as rauthor
        FROM `yunbbs_articles` a 
        LEFT JOIN `yunbbs_categories` c ON c.id=a.cid
        LEFT JOIN `yunbbs_users` u ON a.uid=u.id
        LEFT JOIN `yunbbs_users` ru ON a.ruid=ru.id
    	WHERE `cid` > '1' and `title` like '%${keyword}%'
        ORDER BY `edittime` DESC LIMIT ".($page-1)*$options['list_shownum'].",".$options['list_shownum'];
    $query = $DBS->query($query_sql);
    $articledb=array();
    while ($article = $DBS->fetch_array($query)) {
        // 格式化内容
        if($article['isred'] == '1'){
             $article['title'] = "<span class=\"label label-success\">推荐</span>".$article['title'];
         }
        $article['addtime'] = showtime($article['addtime']);
        $article['edittime'] = showtime($article['edittime']);
        $articledb[] = $article;
    }
    unset($article);
    $DBS->free_result($query);
}

// 页面变量
$title = $options['name'].' - page '.$page;

$site_infos = get_site_infos();
$newest_nodes = get_newest_nodes();
if(count($newest_nodes)==$options['newest_node_num']){
    $bot_nodes = get_bot_nodes();
}

$show_sider_ad = "1";
//$links = get_links();
$meta_kws = htmlspecialchars(mb_substr($options['name'], 0, 6, 'utf-8'));
if($options['site_des']){
    $meta_des = htmlspecialchars(mb_substr($options['site_des'], 0, 150, 'utf-8')).' - page '.$page;
}

$pagefile = CURRENT_DIR . '/templates/default/'.$tpl.'search.php';

include(CURRENT_DIR . '/templates/default/'.$tpl.'layout.php');

?>
