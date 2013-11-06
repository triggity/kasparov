<?
include "config.php";
include "database.php";
include "functions.php";
include "login.php";

//$news_date_limit=ereg_replace("[^0-9]","",$userdata["LastLogin"]); //convert last login time to MySQL timestamp by removing all non numeric characters

//echo $news_date_limit."\n\n";

include "header.php";

MustLogIn();

$result = mysql_query("SELECT * FROM Users WHERE CampusID = $CampusID AND lastlogin < lastschedchange");

if(mysql_num_rows($result) > 0) {
?>
<script language="javascript">
	alert('Your schedule has been modified since your last login. Please check your schedule.');
</script>
<?
}

?>
<p style="font-size:250%; color:green">
<strong>
Welcome to the Kasparov scheduling system!
</strong>
<p />

<?
/* Old central.php -Removed by JT
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
*/

include "footer.php";
db_logout($hdb);

?>
