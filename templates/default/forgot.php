<?php 
if (!defined('IN_SAESPOT')) exit('error: 403 Access Denied'); 

echo '
<div class="title"><i class="fa fa-angle-double-right"></i> ',$title,'</div>
<div class="main-box">
<p class="red" style="margin-left:60px;">';
foreach($errors as $error){
    echo '› ',$error,' <br/>';
}
echo '</p>
<form action="',$_SERVER["REQUEST_URI"],'" method="post">
<p><label>用户名： <input type="text" name="name" class="name sl w200" value="',htmlspecialchars($name),'" /></label></p>
<p><label>邮　箱： <input type="text" name="email" class="email sl w200" value="" /></label></p>
<p><input type="submit" value=" ',$title,' " name="submit" class="textbtn" style="margin-left:60px;" /> </p>';
if($url_path == 'login'){
    if($options['close_register'] || $options['close']){
        echo '<p class="grey fs12">&nbsp;&nbsp;<i class="fa fa-ban"></i> 网站暂时停止注册';
    }else{
        echo '<p class="grey fs12">&nbsp;&nbsp;<i class="fa fa-user-plus"></i> 还没来过？<a href="/sigin">现在注册</a> ';
    }
}else{
    echo '<p class="grey fs12">&nbsp;&nbsp; 哦~~又想起来了？！<a href="/login">现在登录</a> ';
}
echo '</p>
</form>
</div>';
echo'
<script type="text/javascript" charset="utf-8">
$(\'.name\').contip({
    trigger: \'focus\',
    align: \'right\',
	radius: 2,
    bg: \'#EFEFEF\',
	color: \'#000\',
    fade: 180,
    rise: 3,
    html: \'请输入您的用户名！\'
});
$(\'.email\').contip({
    trigger: \'focus\',
    align: \'right\',
	radius: 2,
    bg: \'#EFEFEF\',
	color: \'#000\',
    fade: 180,
    rise: 3,
    html: \'请填写设置的邮箱地址！\'
});
</script>';
?>
