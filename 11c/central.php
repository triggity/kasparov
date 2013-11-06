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
Welcome to the updated Kasparov schedule!
<p />
The system is currently going through updates. The code has been tested many many times but there are probably a bunch of bugs that I haven't found. If you think you found a bug, or if you have comments/suggestions/opinions/anything, feel free to let me know and I'll work on it. Not many people look at this so your feedback really matters!
<p />
If you want to help test out the next versions, check out <a href="http://kasparov.scu.edu:8081/helpdesk/beta/">http://kasparov.scu.edu:8081/helpdesk/beta/</a>. You don't have to do anything special; just use it like you normally would and tell me what you think. I'm only one person so any extra pairs of eyes would help me out a lot.
<p />
Oh, and if you want to go to the page that you used to see when you first login, click on "Helpdesk Main" on the menu to the left. I've never used it though so hopefully this change won't really affect anyone.
<p />
-James T.
<p />
P.S. I like to have the username box selected by default on a login page (like if you go to gmail.com, I can start typing my username without having to click on the box because it's already selected). I don't know if anyone noticed but our login page didn't do that. Now it does! (if you have javascript)
<p />
<hr />
<p />

<u>Current versions</u><br />
Stable: <?=constant('VERSION_STABLE')?><br />
Beta: <?=constant('VERSION_BETA')?><br />
Dev: <?=constant('VERSION_DEVELOPMENT')?><br />
Unstable: <?=constant('VERSION_UNSTABLE')?><br />
<br />
<a href=

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
