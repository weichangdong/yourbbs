<?php
define('IN_SAESPOT', 1);
define('CURRENT_DIR', pathinfo(__FILE__, PATHINFO_DIRNAME));

include(CURRENT_DIR . '/config.php');
include(CURRENT_DIR . '/common.php');

if (!$cur_user) exit('error: 401 login please');
if ($cur_user['flag']==0){
    header("content-Type: text/html; charset=UTF-8");
    exit('error: 403 该帐户已被禁用');
}else if($cur_user['flag']==1){
    header("content-Type: text/html; charset=UTF-8");
    exit('error: 401 该帐户还在审核中');
}

$act = isset($_GET['act']) ? $_GET['act'] : '';
$tid = isset($_GET['id']) ? intval($_GET['id']) : '0';
$page = isset($_GET['page']) ? intval($_GET['page']) : '1';

// 处理操作
if($act && $tid > 0){
    if($act == 'add'){
        // 添加关注
         $DBS->query("INSERT ignore INTO `yunbbs_follow`(`ID`, `UserID`, `ObjID`, `Type`, `FollowTime`) VALUES (null,$cur_uid,$tid,0, $timestamp)");
         $new_mid = $DBS->insert_id();
         echo 1;
    }else if($act == 'del'){
        // 取消关注
        $DBS->unbuffered_query("Delete from yunbbs_follow WHERE ObjID='$tid' and UserID='$cur_uid' and Type=0");
       echo 1;
    }else if($act == 'isfo'){
        //  是否关注过此用户
        $table_followCount = $DBS->fetch_one_array("select count(1) as count from yunbbs_follow WHERE ObjID='$tid' and UserID='$cur_uid' and Type=0");

        echo $table_followCount['count'];
        
        
    }
    exit();
}

// 获取我的关注的用户发布的帖子数量
$query_sql = "SELECT count(1) as count
    FROM `yunbbs_follow` f
    inner join `yunbbs_users` u ON f.ObjID=u.id
    inner join `yunbbs_articles` a ON a.uid=u.id
    LEFT JOIN `yunbbs_categories` c ON c.id=a.cid
    LEFT JOIN `yunbbs_users` ru ON a.ruid=ru.id
	WHERE f.UserID=$cur_uid and Type=0";

$table_followCount = $DBS->fetch_one_array($query_sql);

$total_follow = $table_followCount['count'];

// 处理正确的页数
// 第一页是1
$total_page = ceil($total_follow/$options['list_shownum']);

if($page<=0 || $total_page == 0){
     $page = 1;
}elseif($page>$total_page){
    $page = $total_page;
}

$query_sql = "SELECT a.id,a.cid,a.uid,a.ruid,a.title,a.addtime,a.edittime,a.comments,a.isred,c.name as cname,u.avatar as uavatar,u.name as author,ru.name as rauthor
    FROM `yunbbs_follow` f
    inner join `yunbbs_users` u ON f.ObjID=u.id
    inner join `yunbbs_articles` a ON a.uid=u.id
    LEFT JOIN `yunbbs_categories` c ON c.id=a.cid
    LEFT JOIN `yunbbs_users` ru ON a.ruid=ru.id
	WHERE f.UserID=$cur_uid and Type=0 
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

// 页面变量
$title = '我关注的用户';
$pagefile = CURRENT_DIR . '/templates/default/'.$tpl.'followuser.php';
$newest_nodes = get_newest_nodes();

include(CURRENT_DIR . '/templates/default/'.$tpl.'layout.php');

?>
