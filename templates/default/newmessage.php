<?php 
if (!defined('IN_SAESPOT')) exit('error: 403 Access Denied'); 

echo '
<div class="title">
    <i class="fa fa-angle-double-right"></i> <a name="message"></a>与 <a href="/user/',$c_obj['id'],'">',$c_obj['name'],'</a> 的私信 （',$total_msg,'）
</div>

<div class="main-box home-box-list">';

if($messagedb){

foreach($messagedb as $message){ //<a href="/user/',$message['uid'],'">
echo '
<div class="post-list">';
	if($message['FromUID'] == $cur_uid){ // 如果是我发的
		echo'<div class="item-avatar">
			<a href="/user/',$message['FromUID'],'"><img src="/avatar/normal/',$cur_user['avatar'],'.png">    </a>
		</div>';
	}else{
		echo'<div class="item-avatar">
			<a href="/user/',$message['FromUID'],'"><img src="/avatar/normal/',$message['avatar'],'.png">    </a>
		</div>';
	}
echo'
	<div class="commont-data">
        <div class="commont-content">
            <p>',$message['Content'],'</p>
        </div>
        <div class="commont-data-date">
            <div class="float-left">';
				if($message['FromUID'] == $cur_uid){ // 如果是我发的
					echo '<i class="fa fa-user"></i> 我发给<a href="/user/',$message['ToUID'],'">',$message['ToUName'],'</a>&nbsp;&nbsp; <i class="fa fa-clock-o"></i> ',$message['AddTime'],'&nbsp;&nbsp; <i class="fa fa-trash"></i> <a href="/newmessage/'.$cid.'?act=del&tid=',$message['ID'],'">删除</a>';
				}else{
					echo '<i class="fa fa-user"></i> 来自<a href="/user/',$message['FromUID'],'">',$message['FromUName'],'</a>&nbsp;&nbsp; <i class="fa fa-clock-o"></i> ',$message['AddTime'],'</span>';
				}
			echo'
			</div>
            <div class="float-right">',$message['Title'],'</div>
                <div class="c"></div>
            </div>
            <div class="c"></div>
        </div>
		<div class="c"></div>
	</div>';
}

$pageurl = '/newmessage/'.$cid.'?page=';
if($total_msg > $options['list_shownum']){ 
echo '<div class="pagination">';
if($page>1){
echo '<a href="',$pageurl,$page-1,'" class="float-left"><i class="fa fa-angle-double-left"></i> 上一页</a>';
}
echo '<div class="pagediv">';
$begin = $page-4;
$begin = $begin >=1 ? $begin : 1;
$end = $page+4;
$end = $end <= $total_page ? $end : $total_page;

if($begin > 1)
{
	echo '<a href="'.$pageurl.'1" class="float-left">1</a>';
	echo '<a class="float-left">...</a>';
}
for($i=$begin;$i<=$end;$i++){
	
	if($i != $page){
		echo '<a href="',$pageurl,$i,'" class="float-left">',$i,'</a>';
	}else{
		echo '<a class="float-left pagecurrent">',$i,'</a>';
	}
}
if($end < $total_page)
{
	echo '<a class="float-left">...</a>';
	echo '<a href="',$pageurl,$total_page,'" class="float-left">',$total_page,'</a>';
}

echo '</div>';
if($page<$total_page){
echo '<a href="',$pageurl,$page+1,'" class="float-right">下一页 <i class="fa fa-angle-double-right"></i></a>';
}
echo '<div class="c"></div>
</div>';
}

}else{
    echo '<p>&nbsp;&nbsp;&nbsp;暂无私信</p>';
}

echo '</div>';



echo '
<a name="newmsg"></a>
<form action="',$_SERVER["REQUEST_URI"],'" method="post">
<input type="hidden" name="formhash" value="',$formhash,'" />
<div class="title"><i class="fa fa-angle-double-right"></i> 发送私信</div>
<div class="main-box">';
if($tip){
    echo '<div id="closes" class="redbox"><i class="fa fa-info-circle"></i> ',$tip,'<span id="close"><i class="fa fa-times"></i></span></div>';
}
echo '
<p class="newp" style="display:none;"><input type="text" name="title" value="',htmlspecialchars($p_title),'" class="sll" placeholder="请填写标题"/></p>
<p class="newp"><textarea id="id-content" name="content" class="mll tallll">',htmlspecialchars($p_content),'</textarea></p>
';

echo'<p><div class="float-right" style="padding-right: 5px;"><input type="submit" value=" 发送私信 " name="submit" class="textbtn" /></div><div class="c"></div></p>';

echo '
</form>

</div>';

echo '
<script type="text/javascript">
$(document).ready(function(){
    var target=$("#btnFollow");
    $.ajax({
        type: "GET",
        url: "/follow/user?act=isfo&id="+target.attr("data-id"),
        success: function(msg){
            if(msg == 1){
                target.text("已关注");
            }
       }
    });
    
    target.click(function(){
        if(target.text() == "关注TA"){
            $.ajax({
                type: "GET",
                url: "/follow/user?act=add&id="+target.attr("data-id"),
                success: function(msg){
                    if(msg == 1){
                        target.text("已关注");
                    }
               }
            });
        }else{
            $.ajax({
                type: "GET",
                url: "/follow/user?act=del&id="+target.attr("data-id"),
                success: function(msg){
                    if(msg == 1){
                        target.text("关注TA");
                    }
               }
            });
        }
    });
});
</script>
';

?>
