<?php 
if (!defined('IN_SAESPOT')) exit('error: 403 Access Denied'); 

echo '
<div class="title">
    <i class="fa fa-angle-double-right"></i> 私信列表 （',$total_msg,'）
</div>

<div class="main-box home-box-list">';

if($messagedb){

foreach($messagedb as $message){ //<a href="/user/',$message['uid'],'">
echo '
<div class="post-list">';
	if($message['FromUID'] == $cur_uid){ // 如果是我发的
		echo'<div class="item-avatar">
			<a href="/user/',$cur_user['avatar'],'"><img src="/avatar/normal/',$cur_user['avatar'],'.png">    </a>
		</div>';
	}else{
		echo'<div class="item-avatar">
			<a href="/user/',$message['FromUID'],'"><img src="/avatar/normal/',$message['avatar'],'.png">    </a>
		</div>';
	}
	echo'<div class="item-content">';
			if($message['FromUID'] == $cur_uid){
				echo'<h1><a href="/newmessage/'.$message['ToUID'].'#message">',$message['Content'],'</a></h1>';
			}else{
				echo'<h1><a href="/newmessage/'.$message['FromUID'].'#message">',$message['Content'],'</a></h1>';
			}
			if($message['FromUID'] == $cur_uid){ // 如果是我发的
				echo'<span class="item-date"><i class="fa fa-user"></i> 我发给<a href="/user/',$message['ToUID'],'">',$message['ToUName'],'</a>&nbsp;&nbsp;<i class="fa fa-clock-o"></i> ',$message['AddTime'],'&nbsp;&nbsp;<i class="fa fa-trash"></i> <a href="/usermessage/?act=del&id=',$message['ID'],'">删除</a></span>';
			}else{
				echo '<span class="item-date"><i class="fa fa-user"></i> 来自<a href="/user/',$message['FromUID'],'">',$message['FromUName'],'</a>&nbsp;&nbsp;<i class="fa fa-clock-o"></i> ',$message['AddTime'],'&nbsp;&nbsp;<i class="fa fa-comments"></i> <a href="/newmessage/',$message['FromUID'],'#newmsg">回复</a></span>';
			}
	echo'</div>';
    echo'<div class="item-countto">',$message['Title'],'</div>
	<div class="c"></div>
</div>';

}

if($total_msg > $options['list_shownum']){ 
echo '<div class="pagination">';
if($page>1){
echo '<a href="/usermessage/?page=',$page-1,'" class="float-left"><i class="fa fa-angle-double-left"></i> 上一页</a>';
}
echo '<div class="pagediv">';
$begin = $page-4;
$begin = $begin >=1 ? $begin : 1;
$end = $page+4;
$end = $end <= $total_page ? $end : $total_page;

if($begin > 1)
{
	echo '<a href="/usermessage/?page=1" class="float-left">1</a>';
	echo '<a class="float-left">...</a>';
}
for($i=$begin;$i<=$end;$i++){
	
	if($i != $page){
		echo '<a href="/usermessage/?page=',$i,'" class="float-left">',$i,'</a>';
	}else{
		echo '<a class="float-left pagecurrent">',$i,'</a>';
	}
}
if($end < $total_page)
{
	echo '<a class="float-left">...</a>';
	echo '<a href="/usermessage/?page=',$total_page,'" class="float-left">',$total_page,'</a>';
}

echo '</div>';
if($page<$total_page){
echo '<a href="/usermessage/?page=',$page+1,'" class="float-right">下一页 <i class="fa fa-angle-double-right"></i></a>';
}
echo '<div class="c"></div>
</div>';
}

}else{
    echo '<p>&nbsp;&nbsp;&nbsp;暂无未读私信</p>';
}

echo '</div>';

?>
