<?
include "config.php";
include "database.php";
include "functions.php";

include "login.php";

$title ="Lab Count Popper";

if (!defined("Header_Included")) $IamOnDuty=0; //set the inital value only if the header.php has not yet been included
include "header.php";

MustLogIn();

$popper=1;


$SubmissionTime=time(); //Capture time NOW
if (($SubmissionTime % (60*30))>840) {

$TimeQuantum=2;
$y=mysql_query("SELECT UNIX_TIMESTAMP(Time),IP,RecordID FROM Locations_Data WHERE CampusID=$CampusID AND FLOOR(UNIX_TIMESTAMP(Time)/".($TimeQuantum*900).") = ".((int)($SubmissionTime/(900*$TimeQuantum)))); //Look if we already submitted this data

if (mysql_num_rows($y)==0) {$popper=2;}
}

if ($popper==2) {
?>
<meta HTTP-EQUIV=Refresh CONTENT='<?=(30*60-($SubmissionTime % (60*30)))?>, "popper.php?notables=1&SID=<?=$SID?>"'>
<script language="JavaScript">
self.resizeTo(900,700);
self.moveTo(0,0);
window.focus();
</script>
Logged in as:<H3><?=$fulluname?></H3><BR><BR><?
include "location.php";

} else {
?>
<script language="JavaScript">
self.resizeTo(320,260);
setTimeout("window.location.href=unescape(window.location.pathname);",30000);
</script>
<meta HTTP-EQUIV=Refresh CONTENT='15'>
<CENTER>
<font face="Arial,Helvetica">
<font size=6>Pop-up Reminders</font><BR>
for<BR>
<font size=5><?=$fulluname?></font><BR>
You may minimize this window, but if you close it, lab count reminders will not automatically appear.
<BR><BR>Server Time:<BR>
<?=date("g:ia j F Y")?>
</font>
</CENTER>
<?
include "footer.php";

}
