<?
include "config.php";
include "database.php";
include "functions.php";
include "login.php";


$news_date_limit=ereg_replace("[^0-9]","",$userdata["LastLogin"]); //convert last login time to MySQL timestamp by removing all non numeric characters

//echo $news_date_limit."\n\n";

include "header.php";

MustLogIn();

$mode="List";
$Bits=28+512+1024; //Show default staff boards + lab / linc announcements
$news_sort=0;
$news_first_limit=10; //show a decent amount of the news.
$news_next_limit=20;
include "news.php";


if (0==$news_items_shown) { //only continue if there were no new messages.

if ($userdata["IsFieldSupport"]=="Y" && !(((((int)iptoint($REMOTE_ADDR)>>8) & ((0xFFFFFC))) == 0x81D2C4) && ($userdata["IsHelpDesk"]=="Y"))) {
	include "fieldsupport.php";
} else if ($userdata["IsHelpDesk"]=="Y") {
	include "helpdesk.php";
} else if ($userdata["IsTA"]=="Y") {
	include "location.php";
} else {
	include "queries.php";

}

} else {
   include "footer.php";
   db_logout($hdb);
}

?>
