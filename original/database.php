<?
if($databaseIncluded!='Y'){
$bob = "jane";
function db_logout($db_handle) {

//	mysql_close($db_handle);

}

//passed IP as x.x.x.x notation string, returns a 32 bit unsigned integer
function iptoint ($myip) {

	$parts = explode(".",$myip);
	$result = ((((((int)$parts[0] <<8) +(int)$parts[1]) <<8) + (int)$parts[2]) << 8) + (int)$parts[3];

	return $result;
}
function iptoint2 ($myip) {

	$parts = explode(".",$myip);
	$result = ((((((int)$parts[0] <<8) +(int)$parts[1]) <<8) + (int)$parts[2]) << 8) + (int)$parts[3];

	return $result;
}
function inttoip ($myint) { // reverse

	return (($myint >> 24)&255).".".(($myint >> 16)&255).".".(($myint >> 8)&255).".".($myint&255);

}

function db_reconnect($db_handle) { //this function doesn't seem to work
echo $db_handle;
		mysql_close($db_handle);
	//if ($GLOBALS["nonpersistent"]>0) {
	//	//mysql_close($db_handle);
	//	$hdb=mysql_connect($GLOBALS["database_url"],$GLOBALS["database_uname"],$GLOBALS["database_upass"]);
	//} else { //persistent connection unless otherwise noted to speed things
		$hdb=mysql_pconnect($GLOBALS["database_url"],$GLOBALS["database_uname"],$GLOBALS["database_upass"]);
	//}
echo "::".$hdb;
	return $hdb;
}

if ($nonpersistent>0) {
	$hdb=mysql_connect($database_url,$database_uname,$database_upass);
} else { //persistent connection unless otherwise noted to speed things
	$hdb=mysql_pconnect($database_url,$database_uname,$database_upass);
	//echo $database_uname;
}

if ($hdb < 1) { ?><H1>Uh oh, database connection failure!!!</H1><? exit; }
if (mysql_select_db($database_name,$hdb)==0) { ?>Database selection failure!!!</H1><? exit; }
}
$databaseIncluded='Y';
//mysql_query("set old_passwords='On'");
?>
