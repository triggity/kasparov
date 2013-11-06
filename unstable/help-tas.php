<?
include "config.php";
include "database.php";
include "login.php";
$title = "Lab TA Manual";
include "header.php";

$mode="List";
$Bits=1<<7;
//$Bits=30;
$news_sort=0;
//12 posts per page
$news_first_limit=8;
$news_next_limit=12;
$news_showauthor = 0; //1=show name of author, 0=don't show author's name

$news_priority[0] = "&nbsp;";
$news_priority[1] = "&nbsp;";
$news_priority[2] = "&nbsp;";
$news_priority[3] = "&nbsp;";

include "news.php";
	

include "footer.php";
db_logout($hdb);


?>
