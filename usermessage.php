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
//
//// 处理操作
if($act && $tid > 0){
     //获取需要操作的消息数据
    $user_msg = $DBS->fetch_one_array("SELECT * FROM yunbbs_messages WHERE ID=$tid");
 //   if($act == 'setread'){
//         设置为已读，消息存在，并且消息是发给自己的
//        if($user_msg && $user_msg['ToUID'] == $cur_uid){
//            $DBS->unbuffered_query("UPDATE yunbbs_messages SET IsRead=1 WHERE ID='$tid'");
//            echo 1;
//        }else{
//            echo 0;
//        }
//        exit();
//    }else 
    if($act == 'del'){
        // 删除，只有发送消息的人才可以删除
        if($user_msg && $user_msg['FromUID'] == $cur_uid){
            $DBS->unbuffered_query("Delete from yunbbs_messages WHERE ID=$tid");
        }
    }
}

// 获取发送给我的未读私信的数量
$table_msgCount = $DBS->fetch_one_array("SELECT count(distinct ReferID) as count FROM `yunbbs_messages` where ToUID=$cur_uid or FromUID=$cur_uid");

$total_msg = $table_msgCount['count'];

// 处理正确的页数
// 第一页是1
$total_page = ceil($total_msg/$options['list_shownum']);

if($page<=0 || $total_page == 0){
     $page = 1;
}elseif($page>$total_page){
    $page = $total_page;
}

$query_sql = "SELECT *,count(1) as count FROM `yunbbs_messages`
                where fromuid=$cur_uid or touid=$cur_uid
                group by referid
                order by IsRead,id desc limit ".($page-1)*$options['list_shownum'].",".$options['list_shownum'];

$query = $DBS->query($query_sql);
$messagedb=array();
while ($message = $DBS->fetch_array($query)) {
    // 格式化内容
    if($message['IsRead'] == '0' && $message['ToUID'] == $cur_uid){
         $message['Title'] = "<span class=\"label label-success\">未读</span>";
     }else{
        $message['Title']='';
     }
    $message['AddTime'] = showtime($message['AddTime']);
    $messagedb[] = $message;
}
unset($message);
$DBS->free_result($query);

// 页面变量
$title = '私信';
$newest_nodes = get_newest_nodes();

$pagefile = CURRENT_DIR . '/templates/default/'.$tpl.'usermessage.php';

include(CURRENT_DIR . '/templates/default/'.$tpl.'layout.php');

?>
