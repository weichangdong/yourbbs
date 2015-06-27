<?php
define('IN_SAESPOT', 1);
define('CURRENT_DIR', pathinfo(__FILE__, PATHINFO_DIRNAME));

include(CURRENT_DIR . '/config.php');
include(CURRENT_DIR . '/common.php');

$base_url = 'http://'.$_SERVER['HTTP_HOST'];
$xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n ";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n ";

if($is_spider){
    $id = intval($_GET['id']);

    $table_status = $DBS->fetch_one_array("SHOW TABLE STATUS LIKE 'yunbbs_articles'");
    $post_num = $table_status['Auto_increment'] -1;
    
    $max_num = 39000;

    $from_i = $id;
    $to_i = $from_i + $max_num;
    if($to_i > $post_num){
        $to_i = $post_num + 1;
    }


    for($i = $from_i; $i < $to_i; $i++){
        $xml .= '<url><loc>'.$base_url.'/topics/'.$i."</loc>\n<changefreq>daily</changefreq>\n<priority>1.0</priority></url>\n";
    }
}else{
    $xml .= '<url><loc>'.$base_url.'</loc></url>'."\n ";
}

$xml .= '</urlset>';

header("content-Type: text/xml");
echo $xml;
?>
