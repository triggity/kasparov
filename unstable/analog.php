<?php   include "config.php";
	include "database.php";
	include "login.php";
	$title="Webserver Statistics";
	include "header.php";
	MustLogin(1);
	//<TABLE border=0 width=640><TR><TD width=*>&nbsp;</TD><TD>
	system("/usr/freeware/bin/analog|sed \x22/html>/ d\x22|sed \x22/body>/ d\x22|sed \x22/<meta/ d\x22|sed \x22/head>/ d\x22|sed \x22/<title>/ d\x22|sed \x22/DOCTYPE HTML/ d\x22");
	//system("/usr/freeware/bin/analog");
	//</TD><TD width=*>&nbsp;</TD></TR></TABLE>
include "footer.php";
?>