<?
include "config.php";
include "database.php";
include "login.php";

$title="Logout "+$userdata["UserName"];

if (0==$IsLoggedIn) {

	include "header.php";
	?>
	<H1>Um...Right...</H1><BR>
	You're aren't logged in, so I have no idea why you're trying to log out. Since you're logged out already, you probably want to either login, using the Login box to the left, or go to the main page by <A HREF="index.php">clicking here</A>.
<? } else {

	$IsLoggedIn=0;
	include "header.php";

	//Do Log out stuff
	$x=mysql_query("DELETE FROM Sessions WHERE SessionID=$SID") or die("Could not delete expired sessions"); 	

	?>
	<H1>Logged Out</H1><BR>
	<? echo $fulluname; ?>, you are now logged out of the system. <A HREF="index.php">Click here</A> to return to the main page.
	<?

}

include "footer.php";
db_logout($hdb);
?>

