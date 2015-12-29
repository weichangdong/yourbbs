<?php
define('IN_SAESPOT', 1);
define('CURRENT_DIR', pathinfo(__FILE__, PATHINFO_DIRNAME));

include(CURRENT_DIR . '/config.php');
include(CURRENT_DIR . '/common.php');

if (!$cur_user) exit('error: 401 login please');
if ($cur_user['flag']==0){
    exit('error: 403 Access Denied');
}else if($cur_user['flag']==1){
    exit('error: 401 Access Denied');
}

$cid = intval($_GET['cid']);
$page = isset($_GET['page']) ? intval($_GET['page']) : '1';
$act = isset($_GET['act']) ? $_GET['act'] : '';

$tid = isset($_GET['tid']) ? intval($_GET['tid']) : '0';

// 处理操作
if($act && $tid > 0){
    // 获取需要操作的消息数据
  //  $user_msg = $DBS->fetch_one_array("SELECT * FROM yunbbs_messages WHERE ID='".$tid."'");
//    if($act == 'setread'){
//        // 设置为已读，消息存在，并且消息是发给自己的
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
        $DBS->unbuffered_query("Delete from yunbbs_messages WHERE FromUID = $cur_uid and ID='$tid'");
        
    }
}

if($cid){
    $query = "SELECT id,name,flag,avatar,url,articles,replies,regtime,about FROM yunbbs_users WHERE id='$cid'";
}else{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    include(CURRENT_DIR . '/404.html');
    exit;
    
}

$c_obj = $DBS->fetch_one_array($query);
if($c_obj){
    if($c_obj['flag'] == 0){
        if(!$cur_user || ($cur_user && $cur_user['flag']<99)){
            //header("content-Type: text/html; charset=UTF-8");
            //exit('该用户已被禁用');
        }
    }
    $openid_user = $DBS->fetch_one_array("SELECT name FROM yunbbs_qqweibo WHERE uid='".$cid."'");
    $weibo_user = $DBS->fetch_one_array("SELECT `openid` FROM `yunbbs_weibo` WHERE `uid`='".$cid."'");
    
    $c_obj['regtime'] = showtime($c_obj['regtime']);
}else{
    exit('404');
}


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(empty($_SERVER['HTTP_REFERER']) || $_POST['formhash'] != formhash() || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) !== preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])) {
    	exit('403: unknown referer.');
    }
    
    $p_title = addslashes(trim($_POST['title']));
    $p_content = addslashes(trim($_POST['content']));
    
    // spam_words
    if($options['spam_words'] && $cur_user['flag']<99){
        $check_con = ' '.$p_title.$p_content;
        $spam_words_arr = explode(",", $options['spam_words']);
        foreach($spam_words_arr as $spam){
            if(strpos($check_con, $spam)){
                // has spam word
                $DBS->unbuffered_query("UPDATE yunbbs_users SET flag='0' WHERE id='$cur_uid'");
                
                exit('403: dont post any spam.');
            }
        }
    }
    
    if($p_content && mb_strlen($p_content,'utf-8') > 0){
            //mb_strlen($p_title,'utf-8')<=$options['article_title_max_len'] && 
            if(mb_strlen($p_content,'utf-8')<=$options['article_content_max_len']){
                $p_title = '';//htmlspecialchars($p_title);
                $p_content = htmlspecialchars($p_content);
                
                $factor = 10000000;
                
                $referid=0;
                
                if($cid > $cur_uid){
                    $referid=$cur_uid*$factor+$cid;
                }else{
                    $referid=$cid*$factor+$cur_uid;
                }
                
                $DBS->query("INSERT INTO yunbbs_messages (ID,FromUID,ToUID,FromUName,ToUName,title,content,addtime,ReferID) VALUES (null,$cur_uid,$cid,'$cur_uname','".$c_obj['name']."', '$p_title', '$p_content', $timestamp,$referid)");
                $new_mid = $DBS->insert_id();
                
                
                $DBS->unbuffered_query("UPDATE yunbbs_users SET lastposttime=$timestamp WHERE id='$cur_uid'");
         
                // 更新u_code
                $cur_user['lastposttime'] = $timestamp;
                //
                $new_ucode = md5($cur_uid.$cur_user['password'].$cur_user['regtime'].$cur_user['lastposttime'].$cur_user['lastreplytime']);
                setcookie("cur_uid", $cur_uid, $timestamp+ 86400 * 365, '/');
                setcookie("cur_uname", $cur_uname, $timestamp+86400 * 365, '/');
                setcookie("cur_ucode", $new_ucode, $timestamp+86400 * 365, '/');
                
                // mentions 没有提醒用户的id
              //  $mentions = find_mentions(' '.$p_title.' '.$p_content, $cur_uname);
//                if($mentions && count($mentions)<=10){
//                    foreach($mentions as $m_name){
//                        $DBS->unbuffered_query("UPDATE yunbbs_users SET notic =  concat('$new_aid,', notic) WHERE name='$m_name'");
//                    }
//                }
                
                
                $p_title = $p_content = '';
                header('location: /newmessage/'.$cid);
                exit;
            }else{
                $tip = '内容'.mb_strlen($p_content,'utf-8').' 太长了';
            }
       }else{
            $tip = '内容 不能留空';
        }
}else{
    $p_title = '';
    $p_content = '';

    $tip = '';

}

// 获取我发送的和发送给的私信的数量
$table_msgCount = $DBS->fetch_one_array("SELECT count(1) as count FROM `yunbbs_messages` 
                    WHERE (FromUID=$cur_uid and ToUID=$cid) OR (FromUID = $cid and ToUID=$cur_uid)");

$total_msg = $table_msgCount['count'];

// 处理正确的页数
// 第一页是1
$total_page = ceil($total_msg/$options['list_shownum']);

if($page<=0 || $total_page == 0){
     $page = 1;
}elseif($page>$total_page){
    $page = $total_page;
}

$query_sql = "SELECT * FROM `yunbbs_messages` 
                WHERE (FromUID=$cur_uid and ToUID=$cid) OR (FromUID = $cid and ToUID=$cur_uid)
                order by id desc limit ".($page-1)*$options['list_shownum'].",".$options['list_shownum'];

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

//  设置此人发给我的消息为已读
$DBS->unbuffered_query("UPDATE yunbbs_messages SET IsRead=1 WHERE FromUID=$cid and ToUID=$cur_uid");

// 页面变量
$title = '发私信';

$pagefile = CURRENT_DIR . '/templates/default/'.$tpl.'newmessage.php';

include(CURRENT_DIR . '/templates/default/'.$tpl.'layout.php');

?>
