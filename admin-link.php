<?php
define('IN_SAESPOT', 1);
define('CURRENT_DIR', pathinfo(__FILE__, PATHINFO_DIRNAME));

include(CURRENT_DIR . '/config.php');
include(CURRENT_DIR . '/common.php');

if (!$cur_user || $cur_user['flag'] < 99) exit('error: 403 Access Denied');

$act = trim($_GET['act']);
$lid = intval($_GET['lid']);
if($lid){
    $query = "SELECT * FROM yunbbs_links WHERE id='$lid'";
    $l_obj = $DBS->fetch_one_array($query);
    if(!$l_obj){
        header('location: /admin-link-list');
        exit;
    }
}

$tip1 = '';
$tip2 = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $action = $_POST['action'];
    if($action=='add'){
        $n_name = trim($_POST['name']);
        $n_url = trim($_POST['url']);
        if($n_name && $n_url){
            if($DBS->query("INSERT INTO yunbbs_links (id,name,url) VALUES (null,'$n_name','$n_url')")){
                $tip1 = '已成功添加';
                $cache->clear('site_links');
            }else{
                $tip1 = '数据库更新失败，修改尚未保存，请稍后再试';
            }
        }else{
            $tip1 = '链接名 和 网址 不能留空';
        }
    }else if($action=='edit'){
        $n_name = trim($_POST['name']);
        $n_url = trim($_POST['url']);
        if($n_name && $n_url){
            if($DBS->unbuffered_query("UPDATE yunbbs_links SET name='$n_name',url='$n_url' WHERE id='$lid'")){
                $l_obj['name'] = $n_name;
                $l_obj['url'] = $n_url;
                $tip2 = '已成功保存';
                $cache->clear('site_links');
            }else{
                $tip2 = '数据库更新失败，修改尚未保存，请稍后再试';
            }
            
        }else{
            $tip2 = '链接名 和 网址 不能留空';
        }
    }
}else{
    if($act == 'del'){
        $DBS->unbuffered_query("DELETE FROM yunbbs_links WHERE id='$lid'");
        $cache->clear('site_links');
    }
    
}

// 获取链接列表
$query_sql = "SELECT * FROM yunbbs_links";
$query = $DBS->query($query_sql);
$linkdb=array();
while ($link = $DBS->fetch_array($query)) {
    $linkdb[] = $link;
}


// 页面变量
$title = '链接管理';


$pagefile = CURRENT_DIR . '/templates/default/'.$tpl.'admin-link.php';

include(CURRENT_DIR . '/templates/default/'.$tpl.'layout.php');

?>
