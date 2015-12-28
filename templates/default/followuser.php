<?php 
if (!defined('IN_SAESPOT')) exit('error: 403 Access Denied'); 

echo '
<div class="title">
       <i class="fa fa-angle-double-right"></i> 我关注的用户 （',$total_page,'）';
echo '    <div class="c"></div>
</div>

<div class="main-box home-box-list">';
if($articledb){
	
foreach($articledb as $article){
echo '
<div class="post-list">
    <div class="item-avatar"><a href="/user/',$article['uid'],'">';
if(!$is_spider){
    echo '<img src="/avatar/large/',$article['uavatar'],'.png" alt="',$article['author'],'" />';
}else{
    echo '<img src="/static/grey.gif" data-original="/avatar/large/',$article['uavatar'],'.png" alt="',$article['author'],'" />';
}
echo '    </a></div>
    <div class="item-content">
        <h1><a href="/topics/',$article['id'],'">',$article['title'],'</a></h1>
        <span class="item-date"><i class="fa fa-archive"></i> <a href="/nodes/',$article['cid'],'">',$article['cname'],'</a>&nbsp;&nbsp;<i class="fa fa-user"></i> <a href="/user/',$article['uid'],'">',$article['author'],'</a>';
if($article['comments']){
    echo '&nbsp;&nbsp;<i class="fa fa-clock-o"></i> ',$article['edittime'],'&nbsp;&nbsp;<i class="fa fa-user-secret"></i> 最后回复来自 <a href="/user/',$article['ruid'],'">',$article['rauthor'],'</a>';
}else{
    echo '&nbsp;&nbsp;<i class="fa fa-clock-o"></i> ',$article['addtime'];
}
echo '        </span>
    </div>';
if($article['comments']){
    $gotopage = ceil($article['comments']/$options['commentlist_num']);
    if($gotopage == 1){
        $c_page = '';
    }else{
        $c_page = '/'.$gotopage;
    }
    echo '<div class="item-count"><a href="/topics/',$article['id'],$c_page,'#reply',$article['comments'],'">',$article['comments'],'</a></div>';
}
echo '    <div class="c"></div>
</div>';

}


if($total_follow > $options['list_shownum']){ 
echo '<div class="pagination">';
if($page>1){
echo '<a href="/follow/user?page=',$page-1,'" class="float-left"><i class="fa fa-angle-double-left"></i> 上一页</a>';
}
echo '<div class="pagediv">';
$begin = $page-4;
$begin = $begin >=1 ? $begin : 1;
$end = $page+4;
$end = $end <= $total_page ? $end : $total_page;

if($begin > 1)
{
	echo '<a href="/follow/user?page=1" class="float-left">1</a>';
	echo '<a class="float-left">...</a>';
}
for($i=$begin;$i<=$end;$i++){
	
	if($i != $page){
		echo '<a href="/follow/user?page=',$i,'" class="float-left">',$i,'</a>';
	}else{
		echo '<a class="float-left pagecurrent">',$i,'</a>';
	}
}
if($end < $total_page)
{
	echo '<a class="float-left">...</a>';
	echo '<a href="/follow/user?page=',$total_page,'" class="float-left">',$total_page,'</a>';
}

echo '</div>';
if($page<$total_page){
echo '<a href="/follow/user?page=',$page+1,'" class="float-right">下一页 <i class="fa fa-angle-double-right"></i></a>';
}
echo '<div class="c"></div>
</div>';
}

}else{
    echo '<p>&nbsp;&nbsp;&nbsp;您还没有关注过任何一个用户 或者 关注的用户还没有发布过帖子</p>';
}

echo '</div>';


?>

