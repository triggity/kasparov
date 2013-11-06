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
		mysql_query("DELETE FROM Schedule_Permissions WHERE CampusID=$IdNum");
		mysql_query("DELETE FROM Schedule_Data WHERE CampusID=$IdNum");
		mysql_query("DELETE FROM TimeCards_Members WHERE CampusID=$IdNum");


		$IsUser=0; //Is this person a user 

	} else if ("Update"==$Action) {
		$query="UPDATE Users SET ";
		//IT Staff?
		if (($myuserdata["IsIT"]=="Y") && ($ITStaff!=1) ) {$query .= "ITStaff='N', ";} else if (($myuserdata["IsIT"]=="N") && ($ITStaff==1) ) {$query .= "ITStaff='Y', ";}

		//LINC Staff?
		if (($myuserdata["IsLINC"]=="Y") && ($LINCStaff!=1) ) {$query .= "LINCStaff='N', ";} else if (($myuserdata["IsLINC"]=="N") && ($LINCStaff==1) ) {$query .= "LINCStaff='Y', ";}

		//Admin?
		if (($myuserdata["IsAdmin"]=="Y") && ($Admin!=1) ) {$query .= "Administrator='N', ";} else if (($myuserdata["IsAdmin"]=="N") && ($Admin==1) ) {$query .= "Administrator='Y', ";}

		//TA?
		if (($myuserdata["IsTA"]=="Y") && ($TA!=1) ) {$query .= "TA='N', ";} else if (($myuserdata["IsTA"]=="N") && ($TA==1) ) {$query .= "TA='Y', ";}

		//HelpDesk?
		if (($myuserdata["IsHelpDesk"]=="Y") && ($HelpDesk!=1) ) {$query .= "HelpDesk='N', ";} else if (($myuserdata["IsHelpDesk"]=="N") && ($HelpDesk==1) ) {$query .= "HelpDesk='Y', ";}

		//FieldSupport?
		if (($myuserdata["IsFieldSupport"]=="Y") && ($FieldSupport!=1) ) {$query .= "FieldSupport='N', ";} else if (($myuserdata["IsFieldSupport"]=="N") && ($FieldSupport==1) ) {$query .= "FieldSupport='Y', ";}

		$query .= "UserName='$UserName' WHERE CampusID=".$IdNum;
	//echo $query;
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

		$x=mysql_query("UPDATE Locations_Data SET CampusID=$CID WHERE CampusID=$IdNum");

		$x=mysql_query("UPDATE Schedule_Data SET CampusID=$CID WHERE CampusID=$IdNum");

		$x=mysql_query("UPDATE Schedule_Permissions SET CampusID=$CID WHERE CampusID=$IdNum");

		$x=mysql_query("UPDATE TimeCards_Data SET CampusID=$CID WHERE CampusID=$IdNum");

		$x=mysql_query("UPDATE TimeCards_Members SET CampusID=$CID WHERE CampusID=$IdNum");

		//back in business
		$x=mysql_query("UNLOCK TABLES");

		$IdNum=$CID;
	} else if ("Add TC"==$Action) { //Add this user to some time card's membership table

		$x=mysql_query("INSERT INTO TimeCards_Members (CampusID,TimeCard,Admin,ExtraInfo,PendingRegAdj,PendingOTAdj) VALUES ($IdNum,$TC,'$Admin','$Extra','$RegAdj','$OTAdj')");

	} else if ("Edit TC"==$Action) { //Edit a user's time card

		$x=mysql_query("UPDATE TimeCards_Members SET
				Admin='$Admin',
				ExtraInfo='$Extra',
				PendingRegAdj='$RegAdj',
				PendingOTAdj='$OTAdj'
			WHERE
				CampusID=$IdNum AND
				TimeCard=$TC");

	} else if ("TCDelete"==$Action) { //Delete a users time card

		$x=mysql_query("DELETE FROM TimeCards_Members WHERE CampusID=$IdNum AND TimeCard=$TC");

	} else if ("Add Schedule"==$Action) { //Add this user to schedule's permission table
		if ($SHView=="OK")
			$Fl="View";
		else
			$Fl="";

		if ($SHView=="OK" && $SHMember=="OK")
			$Fl.=",Member";
		else if ($SHMember=="OK")
			$Fl="Member";

		if (($SHView=="OK" || $SHMember=="OK") && $SHAdmin=="OK")
			$Fl.=",Admin";
		else if ($SHAdmin=="OK")
			$Fl="Admin";

		$x=mysql_query("INSERT INTO Schedule_Permissions (CampusID,Schedule,Flags) VALUES ($IdNum,$SH,'$Fl')");

	} else if ("Edit Schedule"==$Action) { //Edit this user's to schedule's permission table
		if ($SHView=="OK")
			$Fl="View";
		else
			$Fl="";

		if ($SHView=="OK" && $SHMember=="OK")
			$Fl.=",Member";
		else if ($SHMember=="OK")
			$Fl="Member";

		if (($SHView=="OK" || $SHMember=="OK") && $SHAdmin=="OK")
			$Fl.=",Admin";
		else if ($SHAdmin=="OK")
			$Fl="Admin";

		$x=mysql_query("UPDATE Schedule_Permissions SET Flags='$Fl' WHERE CampusID=$IdNum AND Schedule=$SH");

	} else if ("SHDelete"==$Action) { //Delete this user from schedule's permission table

		$x=mysql_query("DELETE FROM Schedule_Permissions WHERE CampusID=$IdNum AND Schedule=$SH");
		$x=mysql_query("UPDATE Schedule_Data SET Schedule='-1' WHERE CampusID=$IdNum AND Schedule=$SH");

	}

}


//Print out the name & link to their ClientInfo page
echo "<A HREF=\x22clientinfo.php?SID=$SID&CID=$IdNum\x22>";
echo FriendlyName($myuserdata["First"], $myuserdata["Nick"], $myuserdata["Last"]);
echo "</A>";


?>
</H2>

<?

//echo "<B>This Login:</B> ".$myuserdata["LatestLogin"]."<BR><B>Previous Login:</B> ".$myuserdata["LastLogin"]."<BR><BR>";

if ("Y"==$userdata["IsAdmin"]) {

echo "<B>Latest Login:</B> ".$myuserdata["LatestLogin"]."<BR><B>Previous Login:</B> ".$myuserdata["LastLogin"]."<BR><BR>";

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
<TR><TD colspan=2 align=center>Whose Call Tickets to Show</TD><TD colspan=3 align=center>Accessable Features</TD><TD rowspan=2 align=center valign=bottom><INPUT TYPE = CHECKBOX NAME="Admin" VALUE=1 <? echo (($myuserdata["IsAdmin"]=="Y")?"CHECKED":""); ?>><font color=#FF0000>Admin</font></TD><TD>User Name</TD><TD rowspan=2>&nbsp;<INPUT Type=Submit NAME=Action VALUE="Update">&nbsp;</TD></TR>
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


<!--Schedules-->
<BR><BR>
<FORM METHOD=POST>
<INPUT TYPE=HIDDEN NAME="SID" VALUE="<?=$SID?>">
<INPUT TYPE=HIDDEN NAME="IdNum" VALUE="<?=$IdNum?>">
<?

	$color=MakeTable(mysql_query("
		SELECT
			Schedule_Info.Name,
			Flags,
			concat('<A HREF=myinfo.php?SID=$SID&IdNum=$IdNum&Action=SHEdit&SH=',Schedule,'>Edit</A> | <A HREF=myinfo.php?SID=$SID&IdNum=$IdNum&Action=SHDelete&SH=',Schedule,'>Drop</A>') AS 'Options' 
		 FROM
			Schedule_Info,
			Schedule_Permissions
		WHERE
			Schedule_Info.ID=Schedule_Permissions.Schedule AND
			CampusID=$IdNum
		"),1,1,0,1,"Schedule Permissions");

	if ("SHEdit"==$Action) {
		$x=mysql_query("SELECT
				Name,
				Flags
			FROM
				Schedule_Permissions,
				Schedule_Info
			WHERE
				CampusID=$IdNum AND
				Schedule=$SH AND
				ID=Schedule
			");
	} else {
		$x=mysql_query("SELECT '',''");
	}

	?><TR>

	<TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>

	<? if ('SHEdit'==$Action) {
		echo mysql_result($x,0,0);
		?><INPUT NAME=SH VALUE=<?=$SH?> TYPE=HIDDEN><?
	} else { ?>

		<SELECT NAME="SH">
		<? $y=mysql_query("SELECT ID, Name FROM Schedule_Info ORDER BY Name");
			for ($i=0;$i<mysql_num_rows($y);$i++) {
				?><OPTION VALUE=<?=mysql_result($y,$i,0)?>><?=mysql_result($y,$i,1)?></OPTION><?
			}
		?>
		</SELECT>
	<? } ?>
	</TD>

	<TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
		<INPUT TYPE=CHECKBOX NAME="SHView" VALUE="OK" <?=(strpos(mysql_result($x,0,1),"View")===false?'':'CHECKED')?>>View&nbsp;&nbsp;&nbsp; 
		<INPUT TYPE=CHECKBOX NAME="SHMember" VALUE="OK" <?=(strpos(mysql_result($x,0,1),"Member")===false?'':'CHECKED')?>>Member&nbsp;&nbsp;&nbsp; 
		<INPUT TYPE=CHECKBOX NAME="SHAdmin" VALUE="OK"  <?=(strpos(mysql_result($x,0,1),"Admin")===false?'':'CHECKED')?>>Admin
	</TD>


	<TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>><?
		if ("SHEdit"==$Action) {
			?><INPUT NAME="TC" VALUE="<?=$TC?>" TYPE=HIDDEN>
			<INPUT NAME="Action" VALUE="Edit Schedule" TYPE=SUBMIT><?
		} else {
			?><INPUT NAME="Action" VALUE="Add Schedule" TYPE=SUBMIT><?
		}
	?>
	</TD>

	</TR></FORM></TABLE>


<!--Time Cards-->
<BR><BR>
<FORM METHOD=POST>
<INPUT TYPE=HIDDEN NAME="SID" VALUE="<?=$SID?>">
<INPUT TYPE=HIDDEN NAME="IdNum" VALUE="<?=$IdNum?>">
<?

	$color=MakeTable(mysql_query("
		SELECT
			TimeCards_Info.Name,
			Admin,
			ExtraInfo,
			PendingRegAdj as 'Pend Reg',
			PendingOTAdj as 'Pend OT',
			concat('<A HREF=timecards.php?SID=$SID&IdNum=$IdNum&TC=',TimeCard,'>View</A> | <A HREF=myinfo.php?SID=$SID&IdNum=$IdNum&Action=TCEdit&TC=',TimeCard,'>Edit</A> | <A HREF=myinfo.php?SID=$SID&IdNum=$IdNum&Action=TCDelete&TC=',TimeCard,'>Drop</A>') AS 'Options' 
		 FROM
			TimeCards_Info,
			TimeCards_Members
		WHERE
			TimeCards_Info.ID=TimeCards_Members.TimeCard AND
			CampusID=$IdNum
		"),1,1,0,1,"Time Cards");

	if ("TCEdit"==$Action) {
		$x=mysql_query("SELECT
				Name,
				Admin,
				ExtraInfo,
				PendingRegAdj,
				PendingOTAdj
			FROM
				TimeCards_Members,
				TimeCards_Info
			WHERE
				CampusID=$IdNum AND
				TimeCard=$TC AND
				ID=TimeCard
			");
	} else {
		$x=mysql_query("SELECT '','N','',0.00,0.00");
	}

	?><TR>

	<TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>

	<? if ('TCEdit'==$Action) {
		echo mysql_result($x,0,0);
		?><INPUT NAME=TC VALUE=<?=$TC?> TYPE=HIDDEN><?
	} else { ?>

		<SELECT NAME="TC">
		<? $y=mysql_query("SELECT ID, Name FROM TimeCards_Info ORDER BY Name");
			for ($i=0;$i<mysql_num_rows($y);$i++) {
				?><OPTION VALUE=<?=mysql_result($y,$i,0)?>><?=mysql_result($y,$i,1)?></OPTION><?
			}
		?>
		</SELECT>
	<? } ?>
	</TD>

	<TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
		<SELECT NAME="Admin">
			<OPTION>Y</OPTION>
			<OPTION <?=(('N'==mysql_result($x,0,1))?'SELECTED':'')?>>N</OPTION>
		</SELECT>
	</TD>

	<TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
		<INPUT NAME="Extra" VALUE="<?=mysql_result($x,0,2)?>" SIZE=16 TYPE=TEXT>
	</TD>

	<TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
		<INPUT NAME="RegAdj" VALUE="<?=mysql_result($x,0,3)?>" SIZE=5 TYPE=TEXT>
	</TD>

	<TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
		<INPUT NAME="OTAdj" VALUE="<?=mysql_result($x,0,4)?>" SIZE=5 TYPE=TEXT>
	</TD>

	<TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>><?
		if ("TCEdit"==$Action) {
			?><INPUT NAME="TC" VALUE="<?=$TC?>" TYPE=HIDDEN>
			<INPUT NAME="Action" VALUE="Edit TC" TYPE=SUBMIT><?
		} else {
			?><INPUT NAME="Action" VALUE="Add TC" TYPE=SUBMIT><?
		}
	?>
	</TD>

	</TR></FORM></TABLE>

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


<? //Collect All the user's information at one from the DB

//Start with what Schedule's we're on
//$MySchedules=mysql_query("SELECT Name, ID, TimeQuantum, Holiday, ColorCode,DayStart,DayEnd FROM Schedule_Permissions, Schedule_Info WHERE CampusID='$IdNum' AND Flags&3 AND Schedule=ID ORDER BY TimeQuantum DESC, ID");

//MakeTable($MySchedules,1,1,1,1,"X");

$Earliest=96; //set to longest day
$Latest=0; //set to shortest day
$SmallestBreak=12*4; //Smallest period is 12 hours.
$ScheduleInfo["-1"]="Y";
$ScheduleColor["-1"]=$color_unassigned_schedule;
$Holiday=0; //Are we on a schedue that has holidays?
for ($i=0;$i<mysql_num_rows($MySchedules);$i++) { //Compute ranges for building the time table

  if ((mysql_result($MySchedules,$i,"Flg")&1)==1) { //are we a member?

	if (mysql_result($MySchedules,$i,"DayStart")<$Earliest) $Earliest=mysql_result($MySchedules,$i,"DayStart"); //Update earliest possable hour
	if (mysql_result($MySchedules,$i,"DayEnd")>$Latest) $Latest=mysql_result($MySchedules,$i,"DayEnd"); //Update latest possable hour
	if ($SmallestBreak>mysql_result($MySchedules,$i,2)) { //gotta find a smaller value...
		if (floor($SmallestBreak/24)*24==$SmallestBreak && floor(mysql_result($MySchedules,$i,2)/24)*24==mysql_result($MySchedules,$i,2)) {
			$SmallestBreak=24;
		} else if (floor($SmallestBreak/16)*16==$SmallestBreak && floor(mysql_result($MySchedules,$i,2)/16)*16==mysql_result($MySchedules,$i,2)) {
			$SmallestBreak=16;
		} else if (floor($SmallestBreak/12)*12==$SmallestBreak && floor(mysql_result($MySchedules,$i,2)/12)*12==mysql_result($MySchedules,$i,2)) {
			$SmallestBreak=12;
		} else if (floor($SmallestBreak/8)*8==$SmallestBreak && floor(mysql_result($MySchedules,$i,2)/8)*8==mysql_result($MySchedules,$i,2)) {
			$SmallestBreak=8;
		} else if (floor($SmallestBreak/6)*6==$SmallestBreak && floor(mysql_result($MySchedules,$i,2)/6)*6==mysql_result($MySchedules,$i,2)) {
			$SmallestBreak=6;
		} else if (floor($SmallestBreak/4)*4==$SmallestBreak && floor(mysql_result($MySchedules,$i,2)/4)*4==mysql_result($MySchedules,$i,2)) {
			$SmallestBreak=4;
		} else if (floor($SmallestBreak/2)*2==$SmallestBreak && floor(mysql_result($MySchedules,$i,2)/2)*2==mysql_result($MySchedules,$i,2)) {
			$SmallestBreak=2;
		} else {
			$SmallestBreak=1;
		}
	}
	if ("Y"==mysql_result($MySchedules,$i,"Holiday")) {
		$Holiday=1;
	}

	$ScheduleInfo[mysql_result($MySchedules,$i,1)]="Y";
	$ScheduleColor[mysql_result($MySchedules,$i,1)]=mysql_result($MySchedules,$i,"ColorCode");

	$schedule_colors.="<TR><TD></TD><TD BGCOLOR=#".mysql_result($MySchedules,$i,"ColorCode").">".mysql_result($MySchedules,$i,0)."</TD></TR>";

  }
}

//echo "Earliest: $Earliest ...  Latest: $Latest ... Smallest: $SmallestBreak <BR>";

//And now on to the content....
$Content=mysql_query("SELECT Time,Available,Schedule,Day FROM Schedule_Data WHERE CampusID=$IdNum");

//MakeTable($Content,1,1,1,1,"Entries");

$AvailableHrs=0.0;
$WorkingHrs=0.0;

for ($i=0;$i<mysql_num_rows($Content);$i++) {

	if ((mysql_result($Content,$i,0) < $Earliest) || (mysql_result($Content,$i,0) > $Latest)) { //Out of range
		$x=mysql_query("DELETE FROM Schedule_Data WHERE CampusID=$IdNum AND Time=".mysql_result($Content,$i,0));
	} else if ($ScheduleInfo[mysql_result($Content,$i,2)]!="Y" && mysql_result($Content,$i,1)=="N"){ //Unnecissary...
		$x=mysql_query("DELETE FROM Schedule_Data WHERE CampusID=$IdNum AND Time=".mysql_result($Content,$i,0)." AND Day=".mysql_result($Content,$i,3)); //so we clear it

	} else { //If we got here everything is OK
		$MyOK[mysql_result($Content,$i,0)][mysql_result($Content,$i,3)]=mysql_result($Content,$i,1); //Availability

		if ("Y"==mysql_result($Content,$i,1)) {
			$AvailableHrs+=0.25;
		}

		$MyColor[mysql_result($Content,$i,0)][mysql_result($Content,$i,3)]=$ScheduleColor[mysql_result($Content,$i,2)]; //ColorCodes
		if (mysql_result($Content,$i,2)>0) {
			$WorkingHrs+=0.25;
		}

	}

}

if ("Update Schedule"==$Action) { //Are we updating the schedule?
	for ($theday=0;$theday<8;$theday++) //for each day
	for ($thetime=($Earliest%$SmallestBreak);$thetime<96;$thetime+=$SmallestBreak) { //and each hour
		$theindex=$theday+$thetime*8;
		//echo "T($theindex) =  ".$GLOBALS["T".$theindex]."&nbsp;&nbsp;&nbsp;MyOK($thetime,$theday) = ".$MyOK[$thetime][$theday]."<BR>";
		if ("Y"==$MyOK[$thetime][$theday] && 1!=$GLOBALS["T".$theindex]) { //lost an available time
			for ($i=0;$i<$SmallestBreak;$i++) { //make sure we handle all hours correctly
				mysql_query("UPDATE Schedule_Data SET Available='N' WHERE CampusID=$IdNum AND Day=$theday AND Time=".($thetime+$i));
				$AvailableHrs-=0.25;
			}
			$MyOK[$thetime][$theday]="N";
		} else if ("Y"!=$MyOK[$thetime][$theday] && 1==(int)$GLOBALS["T".$theindex]) { //gained an available time
			//echo "ADDED!!!! $theday, $thetime <BR>\n";
			for ($i=0;$i<$SmallestBreak;$i++) { //make sure we handle all hours correctly
				mysql_query("
					INSERT INTO
						Schedule_Data
					(
						Available,
						CampusID,
						Day,
						Time,
						Schedule
					) 
					VALUES
					(
						'Y',
						$IdNum,
						$theday,
						".($thetime+$i).",
						-1
					)
				");
				mysql_query("UPDATE Schedule_Data SET Available='Y' WHERE CampusID=$IdNum AND Day=$theday AND Time=".($thetime+$i));
				$AvailableHrs+=0.25;
			}
			$MyOK[$thetime][$theday]="Y";
		}
	}
	//Clean up
	$z=mysql_query("DELETE FROM Schedule_Data WHERE Available='N' AND Schedule=(-1)");
}

?>


<B>Currently you have <?=$AvailableHrs?> hours marked as available. You are scheduled to work <?
/*$z = mysql_query("SELECT COUNT(*) FROM Schedule where CampusID =
\"".$IdNum."\" AND Position!='UNASSIGNED'");
echo ((float)mysql_result($z,0,0)/2);*/
echo $WorkingHrs;
?> of those.</B>
<TABLE border=0 cellspacing=6><TR><TD valign=top rowspan=3>
<TABLE border=0 cellspacing=2 cellpadding=3>
<TR><TD>Colors:</TD><TD BGCOLOR=#<?=$color_unassigned_schedule?>>Unassigned</TD></TR>
<?=$schedule_colors?>
</TABLE>
<BR>
</TD>
<TD rowspan=3>
<FORM METHOD=GET>
<INPUT TYPE=HIDDEN NAME="IdNum" VALUE=<? echo $IdNum; ?>>
<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?echo $SID;?>>
<TABLE border=1><TR><TD>Time</TD><TD>Su</TD><TD>M</TD><TD>Tu</TD><TD>W</TD><TD>Th</TD><TD>F</TD><TD>Sa</TD><?=((1==$Holiday)?"<TD>Hol</TD>":"")?></TR>
<?


//for ($i=0; $i<mysql_num_rows($x) ; $i++) {
//
//$times[mysql_result($x,$i,0)]=mysql_result($x,$i,1);
//
//}

$hr=((int)(($Earliest/4)+5)%12);
$min=($Earliest % 4)*15;
$mydisplaytime=(($hr==0)?"12":$hr).":".(($min==0)?"00":$min);

//loop through the hours in the day
for ($thetime=$Earliest; $thetime<=$Latest; $thetime+=$SmallestBreak) {
	?><TR><TD align=center BGCOLOR=<?=(((($thetime+20)%96)<48)?"EAEAEA":"DEDEDE")?>><?
	//set up the time to display
	echo $mydisplaytime;
	$hr=((int)((($thetime+$SmallestBreak)/4)+5)%12);
	$min=(($thetime+$SmallestBreak) % 4)*15;
	$mydisplaytime=(($hr==0)?"12":$hr).":".(($min==0)?"00":$min);
	echo " - ".$mydisplaytime;
	?></TD><?

	//Loop through the days of the week
	for ($theday=0;$theday<(7+$Holiday);$theday++) {
		?><TD BGCOLOR=<?=((""==$MyColor[$thetime][$theday])?$color_unassigned_schedule:($MyColor[$thetime][$theday]))?>>
		<INPUT NAME="T<?=($thetime*8+$theday)?>" TYPE=CHECKBOX VALUE="1" <?=(("Y"==$MyOK[$thetime][$theday])?"CHECKED":"")?>>
		</TD><?
	}

	?></TR><?
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
