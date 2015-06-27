<?php
define('IN_SAESPOT', 1);
define('CURRENT_DIR', pathinfo(__FILE__, PATHINFO_DIRNAME));

include(CURRENT_DIR . '/config.php');
include(CURRENT_DIR . '/common.php');

// 
$via = $_GET['via'];
if($via && $is_mobie){
    setcookie('vtpl', $via, $timestamp+86400 * 365, '/');
}
header('location: /');
exit;

?>
