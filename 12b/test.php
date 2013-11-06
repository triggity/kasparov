<?
$database_url = "localhost";
$database_name = "scu_tech_help";
$database_uname = "stdsvc";
$database_upass = "skippy4u";
$hdb=mysql_connect($database_url,$database_uname,$database_upass);
print $hdb;
if(mysql_select_db($database_name)) echo "Connected to localhost.";
else die ("Connection failed!");

phpinfo();
print $REMOTE_IP;
?>
