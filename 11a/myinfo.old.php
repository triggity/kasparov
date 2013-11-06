<?
include "config.php";
include "database.php";
include "functions.php";

include "login.php";

$title = "Edit My Info"; //Set Page Title

include "header.php";


MustLogIn();

//Make sure non admins are editing themselves
if ($userdata["IsAdmin"]=="N" || ""==$IdNum) { $IdNum=$CampusID; }


?>

<H1>User Information for:</H1>
<H2><?
//Display Name of user we are working on & Set their info
if ($IdNum==$CampusID) {
	$myuserdata =& $userdata; //refference to currect user info

	$IsUser=2; //The user is me - I can't delete me

} else {
	//Get User's Information
	$myuserdata=GetUserInfo($IdNum);

	$IsUser=(sizeof($myuserdata)>1?1:0); //Is this person a user 

}

if ($userdata["IsAdmin"]=="Y") {


	if ("ADD ".$IdNum==$Action && 0==$IsUser) {
		mysql_query("INSERT INTO Users (CampusID) VALUES ($IdNum)");
		$myuserdata=GetUserInfo($IdNum);

		$IsUser=(sizeof($myuserdata)>1?1:0); //Is this person a user 

	} else if ("Update Memberships"==$Action) {
		//Set bits for message board options
		$BBits=0;
		for ($i=0;$i<count($Boards);$i++) {
			$BBits+=(1<<$Boards[$i]);
		}
		
		//Set bits for admin messagre board options
		$ABBits=0;
		for ($i=0;$i<count($AdminBoards);$i++) {
			$ABBits+=(1<<$AdminBoards[$i]);
		}
		
		$x=mysql_query("UPDATE Users SET BoardBits=$BBits, BoardAdmin=$ABBits WHERE CampusID=$IdNum");
		$myuserdata=GetUserInfo($IdNum);


	} else if ("DELETE"==$Action && 1==$IsUser && $CCID==$IdNum) {
		mysql_query("DELETE FROM Users WHERE CampusID=$IdNum");
		mysql_query("DELETE FROM Schedule WHERE CampusID=$IdNum");


		$IsUser=0; //Is this person a user 

	} else if ("Update"==$Action) {
		$query="UPDATE Users SET ";
		//IT Staff?
		if (($myuserdata["IsIT"]=="Y") && ($ITStaff!=1) ) {$query .= "ITStaff='N', ";} else if (($myuserdata["IsIT"]=="N") && ($ITStaff==1) ) {$query .= "ITStaff='Y', ";}

		//LINC Staff?
		if (($myuserdata["IsLINC"]=="Y") && ($LINCStaff!=1) ) {$query .= "LINCStaff='N', ";} else if (($myuserdata["IsLINC"]=="N") && ($LINCStaff==1) ) {$query .= "LINCStaff='Y', ";}

		//Admin?
		if (($myuserdata["IsAdmin"]=="Y") && ($Admin!=1) ) {$query .= "Admin='N', ";} else if (($myuserdata["IsAdmin"]=="N") && ($Admin==1) ) {$query .= "Admin='Y', ";}

		//TA?
		if (($myuserdata["IsTA"]=="Y") && ($TA!=1) ) {$query .= "TA='N', ";} else if (($myuserdata["IsTA"]=="N") && ($TA==1) ) {$query .= "TA='Y', ";}

		//HelpDesk?
		if (($myuserdata["IsHelpDesk"]=="Y") && ($HelpDesk!=1) ) {$query .= "HelpDesk='N', ";} else if (($myuserdata["IsHelpDesk"]=="N") && ($HelpDesk==1) ) {$query .= "HelpDesk='Y', ";}

		//FieldSupport?
		if (($myuserdata["IsFieldSupport"]=="Y") && ($FieldSupport!=1) ) {$query .= "FieldSupport='N', ";} else if (($myuserdata["IsFieldSupport"]=="N") && ($FieldSupport==1) ) {$query .= "FieldSupport='Y', ";}

		$query .= "UserName='$UserName' WHERE CampusID=".$IdNum;
		$x=mysql_query($query);
		$myuserdata=GetUserInfo($IdNum);

	} else if (("Update Campus ID"==$Action) && ($IdNum!=$CampusID) && ($CID >0 ) && ($CID!=$IdNum) && (mysql_num_rows(mysql_query("SELECT CampusID FROM People WHERE CampusID=$CID"))==0)) {

		//Lock all appropriate table
		$x=mysql_query("LOCK TABLES Addresses WRITE,Buildings WRITE,CallTicket WRITE, Computer WRITE, Email WRITE, LabCounts WRITE, NICs WRITE, PaperTrail WRITE, People WRITE, Queries WRITE, Rooms WRITE, Schedule WRITE, Session WRITE, Users WRITE, Messages WRITE");

		//Log everyone else out.
		$x=mysql_query("DELETE FROM Sessions WHERE SessionID!=$SID");

		//Start the dance!
		$x=mysql_query("UPDATE MiscInfo SET CampusID=$CID WHERE CampusID=$IdNum");
		$x=mysql_query("UPDATE Buildings SET CampusID=$CID WHERE CampusID=$IdNum");
		$x=mysql_query("UPDATE CallTicket SET CampusID=$CID WHERE CampusID=$IdNum");
		$x=mysql_query("UPDATE Computer SET CampusID=$CID WHERE CampusID=$IdNum");
		$x=mysql_query("UPDATE LabCounts SET CampusID=$CID WHERE CampusID=$IdNum");
		$x=mysql_query("UPDATE NICs SET CampusID=$CID WHERE CampusID=$IdNum");
		$x=mysql_query("UPDATE People SET CampusID=$CID WHERE CampusID=$IdNum");
		$x=mysql_query("UPDATE Phones SET CampusID=$CID WHERE CampusID=$IdNum");
		$x=mysql_query("UPDATE Queries SET CampusID=$CID WHERE CampusID=$IdNum");
		$x=mysql_query("UPDATE Rooms SET CampusID=$CID WHERE CampusID=$IdNum");
		$x=mysql_query("UPDATE Schedule SET CampusID=$CID WHERE CampusID=$IdNum");
		$x=mysql_query("UPDATE Users SET CampusID=$CID WHERE CampusID=$IdNum");
		$x=mysql_query("UPDATE Messages SET Author=$CID WHERE Author=$IdNum");
		$x=mysql_query("UPDATE PaperTrail SET Creator_CampusID=$CID WHERE Creator_CampusID=$IdNum");
		$x=mysql_query("UPDATE PaperTrail SET Receiver_CampusID=$CID WHERE Receiver_CampusID=$IdNum");


		//back in business
		$x=mysql_query("UNLOCK TABLES");

		$IdNum=$CID;
	}

}


//Print out the name & link to their ClientInfo page
echo "<A HREF=\x22clientinfo.php?SID=$SID&CID=$IdNum\x22>";
echo FriendlyName($myuserdata["First"], $myuserdata["Nick"], $myuserdata["Last"]);
echo "</A>";


?>
</H2>

<?
if ("Y"==$userdata["IsAdmin"]) {
?>
<!--Admin Only Stuff-->
<BR>
<H2>Administrative Options</H2>
<FORM METHOD=GET>

<INPUT TYPE=HIDDEN NAME="IdNum" VALUE=<? echo $IdNum; ?>>
<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?echo $SID;?>>

<!-- Update CampusID -->
CampusID#&nbsp;&nbsp;<INPUT NAME=CID VALUE="<? echo $IdNum; ?>">&nbsp;&nbsp;<INPUT TYPE=SUBMIT NAME="Action" VALUE="Update Campus ID"><BR>
<TABLE BGCOLOR=#FFFF00><TR><TD BGCOLOR=#FFFF00><B><BLINK><font color=#FF0000>WARNING</font></BLINK></B> Updating the CampusID will temporarily lock the entire database, and log-out all users. Avoid doing this during normal operating hours. Also DO NOT Press the STOP button while this operation is being performed as it could corrupt the database. <B><BLINK><font color=#FF0000>WARNING</font></BLINK></B></TD></TR></TABLE><BR>

<?
/*-----------------If User isn't in DB, give ADD Option---------------------*/
if (0==$IsUser) {

?>
User <?=$IdNum?> is not listed as a user of this system.<BR><BR>

<INPUT TYPE=HIDDEN NAME="SID" VALUE="<?=$SID?>">
<INPUT TYPE=HIDDEN NAME="IdNum" VALUE="<?=$IdNum?>">
<INPUT TYPE=SUBMIT NAME="Action" VALUE="ADD <?=$IdNum?>">
</FORM>
<?

include "footer.php";
db_logout($hdb);
exit;


/*-------------If User is in DB and NOT you, give DEL Option----------------*/
} else if (1==$IsUser) {

?>
<BR><BR>

<INPUT TYPE=HIDDEN NAME="SID" VALUE="<?=$SID?>">
<INPUT TYPE=HIDDEN NAME="IdNum" VALUE="<?=$IdNum?>">
Delete User<BR>Confirm CampusID by entering it in the text box below:<BR>
<INPUT TYPE=TEXT NAME="CCID">&nbsp;&nbsp;<INPUT TYPE=SUBMIT NAME="Action" VALUE="DELETE"><BR>
<TABLE BGCOLOR=#FFFF00><TR><TD BGCOLOR=#FFFF00><B>This removes this person from the Users table, and deletes all their schedule entries.</B></TD></TR></TABLE><BR><BR>

<?


}
/*-------------Show rest of options for users in DB----------------*/

?>


<!-- Edit Permissions -->
Permissions and User Name
<TABLE border=1>
<TR><TD colspan=2 align=center>Whose Call Tickets to Show</TD><TD colspan=3 align=center>Schedules and Operations</TD><TD rowspan=2 align=center valign=bottom><INPUT TYPE = CHECKBOX NAME="Admin" VALUE=1 <? echo (($myuserdata["IsAdmin"]=="Y")?"CHECKED":""); ?>><font color=#FF0000>Admin</font></TD><TD>User Name</TD><TD rowspan=2>&nbsp;<INPUT Type=Submit NAME=Action VALUE="Update">&nbsp;</TD></TR>
<TR><TD align=center><INPUT TYPE = CHECKBOX NAME="ITStaff" VALUE=1 <? echo (($myuserdata["IsIT"]=="Y")?"CHECKED":""); ?>>IT Saff</TD><TD align=center><INPUT TYPE = CHECKBOX NAME="LINCStaff" VALUE=1 <? echo (($myuserdata["IsLINC"]=="Y")?"CHECKED":""); ?>>LINC Staff</TD><TD align=center><INPUT TYPE = CHECKBOX NAME="TA" VALUE=1 <? echo (($myuserdata["IsTA"]=="Y")?"CHECKED":""); ?>>Lab TA</TD><TD align=center><INPUT TYPE = CHECKBOX NAME="HelpDesk" VALUE=1 <? echo (($myuserdata["IsHelpDesk"]=="Y")?"CHECKED":""); ?>>Help Desk</TD><TD align=center><INPUT TYPE = CHECKBOX NAME="FieldSupport" VALUE=1 <? echo (($myuserdata["IsFieldSupport"]=="Y")?"CHECKED":""); ?>>Field Support</TD><TD><INPUT TYPE=TEXT NAME="UserName" VALUE="<? echo $myuserdata["UserName"]; ?>"></TD></TR>
</TABLE>

</FORM><BR>

<!--Edit Membership Tables-->
Memberships:
<TABLE border=0>
<FORM>
<TR><TD>Message Board Writting:</TD><TD>Message Board Admin:</TD><TD></TD></TR>
<TR><TD valign=top>
	<SELECT NAME="Boards[]" SIZE=8 MULTIPLE>
	<?
		$groups=mysql_query("
			SELECT Name, Num
			FROM Boards
		");
		for ($i=0;$i<mysql_num_rows($groups);$i++) { //generate list of boards
			echo "<OPTION VALUE=\x22".mysql_result($groups,$i,"Num")."\x22 ";
			if (((1<<mysql_result($groups,$i,"Num")) & $myuserdata["BoardBits"]) >0) { //Is user already a member?
				echo "SELECTED";
			}
			echo ">";
			echo mysql_result($groups,$i,"Name");
			echo "</OPTION>";
		}
	?>
	</SELECT>
</TD>
<TD valign=top>
	<SELECT NAME="AdminBoards[]" SIZE=8 MULTIPLE>
	<?
		$groups=mysql_query("
			SELECT Name, Num
			FROM Boards
		");
		for ($i=0;$i<mysql_num_rows($groups);$i++) { //generate list of boards
			echo "<OPTION VALUE=\x22".mysql_result($groups,$i,"Num")."\x22 ";
			if (((1<<mysql_result($groups,$i,"Num")) & $myuserdata["BoardAdmin"]) >0) { //Is user already a member?
				echo "SELECTED";
			}
			echo ">";
			echo mysql_result($groups,$i,"Name");
			echo "</OPTION>";
		}
	?>
	</SELECT>
</TD>
<TD valign=top>

<INPUT TYPE=HIDDEN NAME="IdNum" VALUE=<? echo $IdNum; ?>>
<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?echo $SID;?>>

<INPUT TYPE=SUBMIT NAME="Action" VALUE="Update Memberships">
</TD>
</TR>
</FORM>
</TABLE>


<? }  //end admin only part?>


<!--E-Mail Address-->
<BR>
<H2>E-Mail Address</H2>
<?
	if ("Update E-Mail"==$Action) { //Updating Password?
		$x=mysql_query("UPDATE Users SET EMail='$EM' WHERE CampusID=$IdNum");
	}
?>
<FORM METHOD=POST>
<INPUT TYPE=HIDDEN NAME="IdNum" VALUE=<? echo $IdNum; ?>>
<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?echo $SID;?>>
<TABLE border=0>
<TR><TD align=right>Primary E-Mail Address:</TD><TD><INPUT TYPE=TEXT NAME="EM" SIZE=20 VALUE="<?=mysql_result(mysql_query("Select EMail FROM Users WHERE CampusID=$IdNum"),0,0)?>"></TD></TR>
<TR><TD></TD><TD align=right><INPUT TYPE=SUBMIT NAME="Action" VALUE="Update E-Mail"></TD></TR>
</TABLE>
</FORM>


<!--User Password Stuff-->
<BR>
<H2>Password</H2>
<?
	if ("Update Password"==$Action) { //Updating Password?
		if ($N42==$R42) { //Both Passwords the same?
			if ($userdata["IsAdmin"]=="Y" || mysql_num_rows(mysql_query("SELECT CampusID FROM Users WHERE Password=PASSWORD('".$C42."') AND CampusID=$IdNum"))>0) { //Admin or Valid Current PW
				$x=mysql_query("UPDATE Users SET Password=PASSWORD('".$N42."') WHERE CampusID=$IdNum");
				echo "Password Changed";
			} else {
				echo "Unable to change password - perhaps your mistyped your current password.";
			}
		} else {
			echo "New passwords don't match. Perhaps you made a typo?";
		}
	} else {
		echo "Use this the change your password to something different.";
	}
?>
<FORM METHOD=POST>
<INPUT TYPE=HIDDEN NAME="IdNum" VALUE=<? echo $IdNum; ?>>
<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?echo $SID;?>>
<TABLE border=0>
<?if ($userdata["IsAdmin"]=="N") { ?>
<TR><TD align=right>Current Password:</TD><TD><INPUT TYPE=PASSWORD NAME="C42"></TD></TR>
<? } ?>
<TR><TD align=right>New Password:</TD><TD><INPUT TYPE=PASSWORD NAME="N42"></TD></TR>
<TR><TD align=right>Re-Type New Password:</TD><TD><INPUT TYPE=PASSWORD NAME="R42"></TD></TR>
<TR><TD></TD><TD align=right><INPUT TYPE=SUBMIT NAME="Action" VALUE="Update Password"></TD></TR>
</TABLE>
</FORM>




<!--Scheduling Information-->
<BR>
<H2>Schedule & Availability:</H2>
Check times when you are available to work, and click one of the "Update Schedule" buttons to save information into database. Times you are already scheduled are color coded.<BR>
<B>Currently you have <?
$x = mysql_query("SELECT Time,Position FROM Schedule where CampusID = \"".$IdNum."\" ORDER BY Time");
echo ((float)mysql_num_rows($x)/2);
?> hours marked as available. You are scheduled to work <?
$z = mysql_query("SELECT COUNT(*) FROM Schedule where CampusID = \"".$IdNum."\" AND Position!='UNASSIGNED'");
echo ((float)mysql_result($z,0,0)/2);
?> of those.</B>
<TABLE border=0 cellspacing=6><TR><TD valign=top rowspan=3>
<TABLE border=0 cellspacing=2 cellpadding=3>
<TR><TD>Colors:</TD><TD BGCOLOR=#BBBBBB>Unassigned</TD></TR>
<TR><TD></TD><TD BGCOLOR=#00FF00>Kenna Lab TA</TD></TR>
<TR><TD></TD><TD BGCOLOR=#FFFF00>Orradre Lab TA</TD></TR>
<TR><TD></TD><TD BGCOLOR=#FF0000>Help Desk</TD></TR>
<TR><TD></TD><TD BGCOLOR=#0000FF>RCC On Call</TD></TR>
</TABLE>
<BR>
</TD>
<TD rowspan=3>
<FORM METHOD=GET>
<INPUT TYPE=HIDDEN NAME="IdNum" VALUE=<? echo $IdNum; ?>>
<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?echo $SID;?>>
<TABLE border=1><TR><TD>Time</TD><TD>Su</TD><TD>M</TD><TD>Tu</TD><TD>W</TD><TD>Th</TD><TD>F</TD><TD>Sa</TD></TR>
<?


for ($i=0; $i<mysql_num_rows($x) ; $i++) {

$times[mysql_result($x,$i,0)]=mysql_result($x,$i,1);

}

for ($ho=0; $ho<38; $ho++) {
	$hour=($ho+14) % 48;
	?>
	<TR>
	<?
	$t=$hour/2;
	echo "<TD align=center>".floor($t);
	if (((double)$t)==((double)floor($t)))
		{ echo ":00-".floor($t).":30</TD>"; }
		else
		{ echo ":30-".((floor($t)+1)%24).":00</TD>"; }
	for ($day=0; $day<7; $day++) {
		$t=($day + ($hour*7));
		if ($Action=="Update Schedule") {

			if ((1==$GLOBALS["TIME".$t]) && (""==$times[$t])) {
				$x=mysql_query("INSERT INTO Schedule (CampusID,Time) VALUES(".$IdNum.",".$t.")");
				$times[$t]="UNASSIGNED";
			} else if (0==$GLOBALS["TIME".$t] && (""!=$times[$t])) {
				$x=mysql_query("DELETE FROM Schedule WHERE CampusID=".$IdNum." AND Time=".$t);
				$times[$t]="";
			}
		}

		?><TD BGCOLOR=#<?
		switch ($times[$t]) {
			case "RCC":
				?>0000FF<?
				break;
			case "HELPDESK":
				?>FF0000<?
				break;
			case "KENNA":
				?>00FF00<?
				break;
			case "ORRADRE":
				?>FFFF00<?
				break;
			default:
				?>BBBBBB<?
		}

		?>><?
		//Display Check box if time is unassigned.
		if (""==$times[$t] || "UNASSIGNED"==$times[$t]) {
			?><INPUT TYPE=CHECKBOX NAME="TIME<?
			echo $t;
			?>" VALUE="1" <?
			if (""!=$times[$t]) { echo "CHECKED"; }
			?>><?
		} else {
			?><INPUT TYPE=HIDDEN NAME="TIME<?
			echo $t;
			?>" VALUE="1">&nbsp;&nbsp;<?
		}
		?></TD><?
	}
	?>
	</TR>
	<?
}

?></TABLE>
</TD>
<TD valign=top>
<P ALIGN=right><INPUT TYPE=SUBMIT NAME="Action" VALUE="Update Schedule"></P>
</TD></TR>
<TR><TD valign=middle>
<P ALIGN=right><INPUT TYPE=SUBMIT NAME="Action" VALUE="Update Schedule"></P>
</TD></TR>
<TR><TD valign=bottom>
<P ALIGN=right><INPUT TYPE=SUBMIT NAME="Action" VALUE="Update Schedule"></P>
</TD>
</FORM>

</TD></TR></TABLE>
<?

include "footer.php";
db_logout($hdb);
?>
