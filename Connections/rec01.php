<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_rec01 = "localhost";
$database_rec01 = "recebiveis";
$username_rec01 = "root";
$password_rec01 = "";
$rec01 = mysql_pconnect($hostname_rec01, $username_rec01, $password_rec01) or trigger_error(mysql_error(),E_USER_ERROR); 
?>