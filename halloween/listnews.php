<?
include "config.php";
include "database.php";
include "login.php";

include "header.php";

MustLogIn(0);

$mode="List";
$Bits=28+(($userdata["IsAdmin"]=="Y")?4096:0);
//$Bits=30;
$news_sort=1;
//12 posts per page
$news_first_limit=8;
$news_next_limit=12;


include "news.php";
	

include "footer.php";
db_logout($hdb);


?>
