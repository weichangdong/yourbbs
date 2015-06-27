<?php

if(!defined('IN_SAESPOT')) exit('Access Denied');

class DB_MySQL  {

    var $querycount = 0;
    var $link;

    function connect($servername, $dbport, $dbusername, $dbpassword, $dbname) {
        $this->link = mysqli_connect($servername, $dbusername, $dbpassword, $dbname, $dbport);
        if (mysqli_connect_errno($this->link)) {
            $this->halt("Failed to connect to MySQL: " . mysqli_connect_error());
            exit();
        }
        
        mysqli_set_charset($this->link, "utf8"); 
        mysqli_select_db($this->link, $dbname);
    }


    function geterrdesc() {
        return mysqli_connect_error();
    }

    function geterrno() {
        return intval(mysqli_connect_errno());
    }

    function insert_id() {
        return ($id = mysqli_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
    }

    function fetch_array($query, $result_type = MYSQL_ASSOC) {
        return mysqli_fetch_array($query, $result_type);
    }

    function query($sql) {
        $query = mysqli_query($this->link, $sql);
        $this->querycount++;
        return $query;
    }

    function unbuffered_query($sql) {
        $query = mysqli_query($this->link, $sql, MYSQLI_USE_RESULT);
        $this->querycount++;
        return $query;
    }

    function select_db($dbname) {
        return mysqli_select_db($dbname, $this->link);
    }

    function fetch_row($query) {
        $query = mysqli_fetch_row($query);
        return $query;
    }

    function fetch_one_array($query) {
        $result = $this->query($query);
        $record = $this->fetch_array($result);
        return $record;
    }

    function num_rows($query) {
        $query = mysqli_num_rows($query);
        return $query;
    }

    function num_fields($query) {
        return mysqli_num_fields($query);
    }

    function result($query, $row) {
        $query = @mysqli_result($query, $row);
        return $query;
    }

    function free_result($query) {
        $query = mysqli_free_result($query);
        return $query;
    }

    function version() {
        return mysqli_get_server_info($this->link);
    }

    function close() {
        return mysqli_close($this->link);
    }

    function halt($msg ='', $sql=''){
        $message = "<html>\n<head>\n";
        $message .= "<meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\">\n";
        $message .= "<style type=\"text/css\">\n";
        $message .=  "body,p,pre {\n";
        $message .=  "font:12px Verdana;\n";
        $message .=  "}\n";
        $message .=  "</style>\n";
        $message .= "</head>\n";
        $message .= "<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#006699\" vlink=\"#5493B4\">\n";

        $message .= "<p>MySQL Database error:</p><pre><b>".htmlspecialchars($msg)."</b></pre>\n";
        $message .= "<b>Mysql error description</b>: ".htmlspecialchars($this->geterrdesc())."\n<br />";
        $message .= "<b>Mysql error number</b>: ".$this->geterrno()."\n<br />";
        $message .= "<b>Date</b>: ".date("Y-m-d @ H:i")."\n<br />";
        $message .= "<b>Script</b>: http://".$_SERVER['HTTP_HOST'].getenv("REQUEST_URI")."\n<br />";

        $message .= "</body>\n</html>";
        @header("content-Type: text/html; charset=UTF-8");
        echo $message;
        exit;
    }
}
?>
