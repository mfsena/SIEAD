<?php 
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_TCC = "localhost";
$database_TCC = "siead";
$username_TCC = "root";
$password_TCC = "";
$TCC = mysql_pconnect($hostname_TCC, $username_TCC, $password_TCC) or trigger_error(mysql_error(),E_USER_ERROR); 
?>