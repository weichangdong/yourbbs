<?php 
if (!defined('IN_SAESPOT')) exit('error: 403 Access Denied'); 

echo '
<div class="title">
    <div class="float-left">
        <i class="fa fa-angle-double-right"></i> 最新发布的主题
    </div>';
	if($cur_user['notic']){
            $notic_n = count(array_unique(explode(',', $cur_user['notic'])))-1;
	echo'<span class="nopic"><a href="/notifications"><i class="fa fa-bell"></i> 您有',$notic_n,'条消息</a></span>';
	}
echo '    <div class="c"></div>
</div>

<div class="main-box home-box-list">';

foreach($articledb as $article){
echo '
<div class="post-list">
    <div class="item-avatar"><a href="/user/',$article['uid'],'">
    <img src="/avatar/normal/',$article['uavatar'],'.png" alt="',$article['author'],'" />
    </a></div>
    <div class="item-content count',$article['comments'],'">
        <h1><a href="/topics/',$article['id'],'">',$article['title'],'</a></h1>
        <span class="item-date"><i class="fa fa-archive"></i> <a href="/nodes/',$article['cid'],'">',$article['cname'],'</a>';
if($article['comments']){
    echo '&nbsp;&nbsp;<i class="fa fa-user"></i> <a href="/user/',$article['ruid'],'">',$article['rauthor'],'</a>&nbsp;&nbsp;<i class="fa fa-clock-o"></i> ',showtime($article['edittime']),'回复';
}else{
    echo '&nbsp;&nbsp;<i class="fa fa-user"></i> <a href="/user/',$article['uid'],'">',$article['author'],'</a>&nbsp;&nbsp;<i class="fa fa-clock-o"></i> ',showtime($article['addtime']),'发表';
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


if(count($articledb) == $options['home_shownum']){ 

// 处理正确的页数
$table_status = $DBS->fetch_one_array("SHOW TABLE STATUS LIKE 'yunbbs_articles'");
$taltol_article = $table_status['Auto_increment'] -1;
$taltol_page = ceil($taltol_article/$options['list_shownum']);

echo '<div class="pagination">';

echo '<div class="pagediv" style="margin-left:78px;">';
$page=1;
$begin = $page-4;
$begin = $begin >=1 ? $begin : 1;
$end = $page+4;
$end = $end <= $taltol_page ? $end : $taltol_page;

if($begin > 1)
{
	echo '<a href="/page/1" class="float-left">1</a>';
	echo '<a class="float-left">...</a>';
}
for($i=$begin;$i<=$end;$i++){
	
	if($i != $page){
		echo '<a href="/page/',$i,'" class="float-left">',$i,'</a>';
	}else{
		echo '<a class="float-left pagecurrent">',$i,'</a>';
	}
}
if($end < $taltol_page)
{
	echo '<a class="float-left">...</a>';
	echo '<a href="/page/',$taltol_page,'" class="float-left">',$taltol_page,'</a>';
}

echo '</div>';

echo '<a href="/page/2" class="float-right">下一页 &raquo;</a>';
echo '<div class="c"></div>
</div>';
}

echo '</div>';

if(isset($bot_nodes)){
echo '
<div class="title">热门分类</div>
<div class="main-box main-box-node">
<span class="btn">';
foreach( $bot_nodes as $k=>$v ){
    echo '<a href="/',$k,'">',$v,'</a>';
}
echo '
</span>
<div class="c"></div>

</div>';
}

?>

