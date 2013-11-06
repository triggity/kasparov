<?
include "config.php";
include "database.php";
include "login.php";

$title="Search for: ".$Phrase;
include "header.php";

MustLogIn(0);

$mode="Search";
$Bits=28;
//$Bits=30;
$news_sort=1;
//12 posts per page
$news_first_limit=8;
$news_next_limit=12;


include "news.php";
	

if (0==$news_items_shown) {
?><H2>Nothing found</H2><?
}

include "footer.php";
db_logout($hdb);


?>
