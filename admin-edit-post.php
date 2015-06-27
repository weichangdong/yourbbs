<?php
define('IN_SAESPOT', 1);
define('CURRENT_DIR', pathinfo(__FILE__, PATHINFO_DIRNAME));

include(CURRENT_DIR . '/config.php');
include(CURRENT_DIR . '/common.php');

if (!$cur_user || $cur_user['flag'] < 99) exit('error: 403 Access Denied');

$tid = intval($_GET['tid']);
$query = "SELECT id,cid,title,content,tags,closecomment,visible,isred,top,fop FROM yunbbs_articles WHERE id='$tid'";
$t_obj = $DBS->fetch_one_array($query);
if(!$t_obj){
    exit('404');
}

if($t_obj['closecomment']){
    $t_obj['closecomment'] = 'checked';
}else{
    $t_obj['closecomment'] = '';
}

if($t_obj['visible']){
    $t_obj['visible'] = 'checked';
}else{
    $t_obj['visible'] = '';
}

if($t_obj['top']){
    $t_obj['top'] = 'checked';
}else{
    $t_obj['top'] = '';
}

if($t_obj['isred']){
    $t_obj['isred'] = 'checked';
}else{
    $t_obj['isred'] = '';
}

if($t_obj['fop']){
    $t_obj['fop'] = 'checked';
}else{
    $t_obj['fop'] = '';
}

// 获取1000个热点分类
$query = $DBS->query("SELECT `id`, `name` FROM `yunbbs_categories` ORDER BY  `articles` DESC LIMIT 1000");
$all_nodes = array();
while($node = $DBS->fetch_array($query)) {
    $all_nodes[$node['id']] = $node['name'];
}
if( !array_key_exists($t_obj['cid'], $all_nodes) ){
    $cid = $t_obj['cid'];
    $c_obj = $DBS->fetch_one_array("SELECT id,name FROM yunbbs_categories WHERE id='".$cid."'");
    $all_nodes[$c_obj['id']] = $c_obj['name'];
}

unset($node);
$DBS->free_result($query);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $old_cid = $t_obj['cid'];
    $p_cid = $_POST['select_cid'];
    $p_title = addslashes(trim($_POST['title']));
    $p_content = addslashes(trim($_POST['content']));
    $p_tags = gettags(trim($_POST['tags']));
    $p_closecomment = intval($_POST['closecomment']);
    $p_visible = intval($_POST['visible']);
	$p_top = intval($_POST['top']);
	$p_fop = intval($_POST['fop']);
	$p_isred = intval($_POST['isred']);
    
    if($p_title){
        $oldtags = $t_obj['tags'];
        
        $p_title = htmlspecialchars($p_title);
        $p_content = htmlspecialchars($p_content);
        $DBS->unbuffered_query("UPDATE yunbbs_articles SET cid='$p_cid', title='$p_title', content='$p_content', tags='$p_tags', closecomment='$p_closecomment', visible='$p_visible', isred='$p_isred', top='$p_top', fop='$p_fop' WHERE id='$tid'");
        if($p_cid != $old_cid){
            $DBS->unbuffered_query("UPDATE yunbbs_categories SET articles=articles+1 WHERE id='$p_cid'");
            $DBS->unbuffered_query("UPDATE yunbbs_categories SET articles=articles-1 WHERE id='$old_cid'");
            $cache->clear('hot_nodes');
        }
        
        // set tags
        if($oldtags != $p_tags){
            $old_tags = explode(",", $oldtags);
            $new_tags = explode(",", $p_tags);

            $removed_tags = array_diff($old_tags, $new_tags);
            foreach($removed_tags as $tag){
                $tag_obj  = $DBS->fetch_one_array("SELECT `id`,`articles`,`ids` FROM `yunbbs_tags` WHERE `name`='$tag'");
                if(!empty($tag_obj)) {
                    $ids = ','.$tag_obj['ids'].',';
                    $ids = str_replace(",".$tid.",", ",", $ids);
                    $ids = substr($ids, 1, -1);
                    $DBS->unbuffered_query("UPDATE `yunbbs_tags` SET `articles`=`articles`-1, `ids`='$ids' WHERE `name`='$tag'");
                }
            }

            $newadd_tags = array_diff( $new_tags, $old_tags);
            foreach($newadd_tags as $tag){
                $tag_obj  = $DBS->fetch_one_array("SELECT `id`,`articles`,`ids` FROM `yunbbs_tags` WHERE `name`='$tag'");
                if(empty($tag_obj)) {
                    $DBS->query("INSERT INTO `yunbbs_tags` (`id`,`name`,`articles`,`ids`) VALUES (null,'$tag', '1', '$tid')");
                } else {
                    if($tag_obj['ids']){
                        $ids = $tid.','.$tag_obj['ids'];
                    }else{
                        $ids = $tid;
                    }
                    $DBS->unbuffered_query("UPDATE `yunbbs_tags` SET `articles`=`articles`+1, `ids`='$ids' WHERE `name`='$tag'");
                }
            }
        }
        
        // tag end

        $cache->clear('home_articledb');
        
        header('location: /topics/'.$tid);
        exit;
    }else{
        $tip = '标题 不能留空';
    }
}else{
    $p_title = $t_obj['title'];
    $p_content = $t_obj['content'];
    $p_tags = $t_obj['tags'];
    $tip = '';
}
// 页面变量
$title = '修改帖子 - '.$t_obj['title'];
// 设置回复图片最大宽度
$img_max_w = 650;


$pagefile = CURRENT_DIR . '/templates/default/'.$tpl.'admin-edit-post.php';

include(CURRENT_DIR . '/templates/default/'.$tpl.'layout.php');

?>
