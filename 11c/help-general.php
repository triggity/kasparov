<?
include "config.php";
//$color_table_lt_bg="990000";
//$color_table_dk_bg="990000";
//$color_page_lt_bg="990000";

include "database.php";
include "login.php";
$title = "General User Guide";

$body_style="font-family: Arial, Helvetica, sans-serif";

include "header.php";

?>
<STYLE TYPE="text/css">
<!--
TD {font-family:  Arial, Helvetica, sans-serif}
H3 {font-family: Verdana,Arial,Helvetica,sans-serif}
H2 {font-family: Verdana,Arial,Helvetica,sans-serif}
H1 {font-family: Verdana,Arial,Helvetica,sans-serif}
-->
</STYLE>
<?

$mode="List";
$Bits=1<<5;
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
