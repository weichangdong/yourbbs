<?php 
if (!defined('IN_SAESPOT')) exit('error: 403 Access Denied'); 
ob_start();

echo '
<!doctype html>
<html lang="zh-cmn-Hans">
<head>
<meta charset="utf-8">
<title>',$title,'</title>';
if(isset($meta_kws) && $meta_kws){
    echo '<meta name="keywords" content="',$meta_kws,'," />';
}
if(isset($meta_des) && $meta_des){
    echo '<meta name="description" content="',$meta_des,'" />';
}
echo '<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<link href="/static/default/style.css" rel="stylesheet" type="text/css" />
<link href="//cdn.bootcss.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<script src="',$options['jquery_lib'],'" type="text/javascript"></script>';
if(isset($t_obj)){
echo '
<link rel="stylesheet" href="/static/highlight/styles/github-gist.css">
<script src="/static/highlight/highlight.pack.js"></script>
<script>hljs.initHighlightingOnLoad();</script>';
}
echo'
<script src="/static/js/jquery.contip.js" type="text/javascript"></script>
<script src="/static/js/jquery.lazylinepainter-1.5.1.min.js" type="text/javascript"></script>';
if($options['head_meta']){
    echo $options['head_meta'];
}
if(isset($canonical)){
    echo '<link rel="canonical" href="http://',$_SERVER['HTTP_HOST'],$canonical,'" />';
}

echo '
</head>
<body>
<div class="header-wrap">
    <div class="header">
        <div class="logo"><a href="/" name="top">',htmlspecialchars($options['name']),'</a></div>
		<div class="scbox">
			<form action="/search/?p=" role="search" method="get" id="searchform" target="_blank">
				<input class="search-input" type="text" maxlength="30" name="q" id="q"><i class="fa fa-search"></i>
			</form>
		</div>
        <div class="banner">';
        
if($cur_user){ 
    echo '<a href="/follow/nodes"><i class="fa fa-archive"></i>关注的节点</a>
          <a href="/follow/user"><i class="fa fa-user"></i>关注的用户</a>
		  <a href="/favorites"><i class="fa fa-star"></i>收藏的帖子</a>
		  <a id="translateLink"><i class="fa fa-globe"></i>繁體中文</a>';
}else{
    echo '<a href="/login" rel="nofollow"><i class="fa fa-sign-in"></i>登录</a>
	<a href="/sigin" rel="nofollow"><i class="fa fa-user-plus"></i>注册</a>
	<a id="translateLink" rel="nofollow"><i class="fa fa-globe"></i>繁體中文</a>';
}
echo '       </div>
        <div class="c"></div>
    </div>
    <!-- header end -->
</div>

<div class="main-wrap">
    <div class="main">
        <div class="main-content">';
        
include($pagefile);

echo '       </div>
        <!-- main-content end -->
        <div class="main-sider">';

include(CURRENT_DIR . '/templates/default/sider.php');

echo '       </div>
        <!-- main-sider end -->
        <div class="c"></div>
    </div>
    <!-- main end -->
    <div class="c"></div>
</div>';

echo '
<div class="footer-wrap">
    <div class="footer">
	<div class="sep10"></div>
	<div class="sep10"></div>
	<i class="fa fa-heart"></i> Lovingly made by <a href="http://youbbs.git.yangzhennan.com/" target="_blank">EOEN</a><div class="sep5"></div>
	<i class="fa fa-asterisk fa-spin"></i> Proudly Powered by <a href="http://youbbs.sinaapp.com/" target="_blank">YouBBS</a><div class="footericp">';
if($options['icp']){
    echo '<div class="sep5"></div><i class="fa fa-university"></i> <a href="http://www.miibeian.gov.cn/" target="_blank" rel="nofollow">',$options['icp'],'</a>';
}

if($options['show_debug']){
    $mtime = explode(' ', microtime());
    $totaltime = number_format(($mtime[1] + $mtime[0] - $starttime), 6);
    echo '<div class="sep5"></div><i class="fa fa-leaf"></i> Processed in ',$totaltime,' second(s), ',$DBS->querycount,' queries';
}
echo '    </div></div>
    <!-- footer end -->
</div>

<script src="/static/js/jquery.lazyload.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
$(function() {
    $(".main-box img").lazyload({
        //placeholder : "/static/grey.gif",
        //effect : "fadeIn"
    });
});
</script>
';
if($options['analytics_code']){
    echo $options['analytics_code'];
}

echo '
<script type="text/javascript">
$(function(){
	function backtop(){
		var backtop = $("<a class=\'backTop\'>∧</a>")
		$("body").append(backtop);
		var fn = function(){
		$(\'html,body\').animate({
			scrollTop: \'0px\'
			}, 200);
			return false;
		}
		$(\'.backTop\').bind(\'click\',fn);
		$(window).scroll(function () {
			var scrollt = $(window).scrollTop();
			if ( scrollt > 100 ){
				$(".backTop").fadeIn("slow");
			} else {
				$(".backTop").fadeOut("slow");
			}
		});
	}
	backtop();
});
</script>
<script src="/static/js/emojify.js"></script>
<script>
emojify.setConfig({
    emojify_tag_type : \'div\',
    only_crawl_id    : null,
    img_dir          : \'http://www.emoji-cheat-sheet.com/graphics/emojis\',
    ignored_tags     : { 
        \'SCRIPT\'  : 1,
        \'TEXTAREA\': 1,
        \'PRE\'     : 1,
        \'CODE\'    : 1
    }
});
emojify.run();
</script>
<script src="/static/js/Language.js" type="text/javascript"></script>
<script type="text/javascript">
translateInitilization();
</Script>
<script type="text/javascript">
$(document).ready(function(){
  $("#close").click(function(){
    $("#closes").remove();
  });
});
</script>
</body>
</html>';

$_output = ob_get_contents();
ob_end_clean();

// 304
if(!$options['show_debug']){
    $etag = md5($_output);
    if($_SERVER['HTTP_IF_NONE_MATCH'] == $etag){
        header("HTTP/1.1 304 Not Modified");
        header("Status: 304 Not Modified");
        header("Etag: ".$etag);
        exit;    
    }else{
        header("Etag: ".$etag);
    }
}

echo $_output;

?>
