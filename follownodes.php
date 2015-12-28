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
         $DBS->query("INSERT ignore INTO `yunbbs_follow`(`ID`, `UserID`, `ObjID`, `Type`, `FollowTime`) VALUES (null,$cur_uid,$tid,1, $timestamp)");
         $new_mid = $DBS->insert_id();
         echo 1;
        
    }else if($act == 'del'){
        // 取消关注
        $DBS->unbuffered_query("Delete from yunbbs_follow WHERE ObjID='$tid' and UserID='$cur_uid' and Type=1");
       echo 1;
    }else if($act == 'isfo'){
        //  是否关注过此话题
        $table_followCount = $DBS->fetch_one_array("select count(1) as count from yunbbs_follow WHERE ObjID='$tid' and UserID='$cur_uid' and Type=1");

        echo $table_followCount['count'];
        
    }
    exit();
}

// 获取tag数据
$total_articles = 0;
//$all_articles = array();

$tag_sql = "SELECT t.* FROM `yunbbs_categories` t
            inner join `yunbbs_follow` f on t.id=f.ObjID
             WHERE f.UserID=$cur_uid and Type=1";
                                     
$tagquery = $DBS->query($tag_sql);
$tagsdb=array();

while ($tag = $DBS->fetch_array($tagquery)) {
   
    $total_articles+=$tag['articles'];
    //$all_articles[] = $tag['ids'];
    
}

unset($tag);
$DBS->free_result($tagquery);

// 处理正确的页数
// 第一页是1
$total_page = ceil($total_articles/$options['list_shownum']);

if($page<=0 || $total_page == 0){
     $page = 1;
}elseif($page>$total_page){
    $page = $total_page;
}
$articledb=array();

// 获取文章列表
if($total_articles > 0){

  //  $from_i = $options['list_shownum']*($page-1);
//    $to_i = $from_i + $options['list_shownum'];
    
 //   $all_articles = array_unique(explode(',',implode(',',$all_articles)));
//    
//    $id_arr = array_slice( $all_articles , $from_i, $to_i);
//    
//    $ids = implode(',', $id_arr);
    //exit($ids);
         
    $query_sql = "SELECT a.id,a.uid,a.cid,a.ruid,a.title,a.addtime,a.edittime,a.comments,a.isred,c.name as cname,u.avatar as uavatar,u.name as author,ru.name as rauthor
        FROM `yunbbs_follow` f
        inner join `yunbbs_articles` a on f.ObjID=a.cid
        LEFT JOIN `yunbbs_categories` c ON c.id=a.cid
        LEFT JOIN `yunbbs_users` u ON a.uid=u.id
        LEFT JOIN `yunbbs_users` ru ON a.ruid=ru.id
        WHERE f.UserID=$cur_uid and Type=1
        ORDER BY `edittime` DESC LIMIT ".($page-1)*$options['list_shownum'].",".$options['list_shownum'];
//AND `cid` > '1'
    $query = $DBS->query($query_sql);
    // 按id添加顺序排列
   // foreach($id_arr as $aid){
//        $articledb[$aid] = '';
//    }

    while ($article = $DBS->fetch_array($query)) {
       
        // 格式化内容
        $article['addtime'] = showtime($article['addtime']);
        $article['edittime'] = showtime($article['edittime']);
        $articledb[$article['id']] = $article;
    }
   
    unset($article);
    $DBS->free_result($query);
}

// 页面变量
$title = '我关注的话题';
$pagefile = CURRENT_DIR . '/templates/default/'.$tpl.'follownodes.php';
$newest_nodes = get_newest_nodes();

include(CURRENT_DIR . '/templates/default/'.$tpl.'layout.php');

?>
