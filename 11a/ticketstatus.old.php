<?
include "config.php";
include "database.php";
include "functions.php";
include "login.php";

$AboutTicket = mysql_query("SELECT concat(IFNULL(People.Last,''),', ',IFNULL(People.First,''),' ', IFNULL(People.Middle,'')) AS Name, CallTicket.CampusID AS CID, CallTicket.JackID AS Jack, CallTicket.ComputerID AS Computer, CallTicket.Location AS Loc FROM People, CallTicket WHERE CallTicket.TicketID='$TID' AND People.CampusID=CallTicket.CampusID");

//MakeTable($AboutTicket,1,1,1,1,"Hello");

if (0==mysql_num_rows($AboutTicket)) {

include "header.php";
?>
<H1>Ticket Not Found</H1>
Ticket <? echo $TID; ?> was not found in the database.
<?

} else if (1==$IsLoggedIn || mysql_result($AboutTicket,0,"CID")==$CID) {

	//Only if we're logged in -- Do any necissary processing.
	if ($IsLoggedIn==1 && ($userdata["IsHelpDesk"]=="Y" || $userdata["IsFieldSupport"]=="Y" || $userdata["IsAdmin"]=="Y")) {

		
		if ("Force Open"==$Action) { //Are we going to force the ticket open
			$Comments="Forced re-openning: ".($Comments);
			$x=mysql_query("INSERT INTO PaperTrail (Creator_CampusID,State,Comment,Department,TicketID,Creation) VALUES($CampusID,'OPEN','$Comments','$Dept',$TID,NOW())");
			$x=mysql_query("UPDATE PaperTrail SET IsLast='N' WHERE ThisID!=LAST_INSERT_ID() AND TicketID=$TID");
		} else if ("Forward"==$Action) {
			$Comments="Forwarded To Other Department: ".($Comments);
			$x=mysql_query("INSERT INTO PaperTrail (Creator_CampusID,State,Comment,Department,TicketID,Creation) VALUES($CampusID,'OPEN','$Comments','$Dept',$TID,NOW())");
			$x=mysql_query("UPDATE PaperTrail SET IsLast='N' WHERE ThisID!=LAST_INSERT_ID() AND TicketID=$TID");
		} else if ("Update Jack"==$Action) {
			$x=mysql_query("UPDATE CallTicket SET JackID='$JID' WHERE TicketID=$TID");
			$AboutTicket = mysql_query("SELECT concat(IFNULL(People.Last,''),', ',IFNULL(People.First,''),' ', IFNULL(People.Middle,'')) AS Name, CallTicket.CampusID AS CID, CallTicket.JackID AS Jack, CallTicket.ComputerID AS Computer, CallTicket.Location AS Loc FROM People, CallTicket WHERE CallTicket.TicketID='$TID' AND People.CampusID=CallTicket.CampusID");
		} else if ("Pick Up"==$Action) {
			$Comments="Picked up Ticket";
			$x=mysql_query("INSERT INTO PaperTrail (Creator_CampusID,Receiver_CampusID,State,Comment,Department,TicketID,Creation) VALUES($CampusID,$CampusID,'ACTIVE','$Comments','".(("Y"==$userdata["IsIT"])?"IT":"LINC")."',$TID,NOW())");
			$x=mysql_query("UPDATE PaperTrail SET IsLast='N' WHERE ThisID!=LAST_INSERT_ID() AND TicketID=$TID");
		} else if ("Add Remark"==$Action) {
			$Comments=($Comments);
			$x=mysql_query("INSERT INTO PaperTrail (Creator_CampusID,Receiver_CampusID,State,Comment,Department,TicketID,Creation) VALUES($CampusID,$CampusID,'ACTIVE','$Comments','".(("Y"==$userdata["IsIT"])?"IT":"LINC")."',$TID,NOW())");
			$x=mysql_query("UPDATE PaperTrail SET IsLast='N' WHERE ThisID!=LAST_INSERT_ID() AND TicketID=$TID");
		} else if ("Completed"==$Action) {
			$Comments=($Comments);
			$x=mysql_query("INSERT INTO PaperTrail (Creator_CampusID,State,Comment,Department,TicketID,Creation) VALUES($CampusID,'COMPLETE','$Comments','".(("Y"==$userdata["IsIT"])?"IT":"LINC")."',$TID,NOW())");
			$x=mysql_query("UPDATE PaperTrail SET IsLast='N' WHERE ThisID!=LAST_INSERT_ID() AND TicketID=$TID");
		} else if ("Update Ticket"==$Action) {
			$Comments="Re-assignment: ".($Comments);
			$x=mysql_query("INSERT INTO PaperTrail (Creator_CampusID,Receiver_CampusID, State,Comment,Department,TicketID,Creation,Appointment) VALUES($CampusID,$TechCampusID,'OPEN','$Comments','$Dept',$TID,NOW(),'".(($Appointment==1)?(date("Y-m-d H:i:00",mktime((("PM"==$HalfDay)?($Hour+12):$Hour),$Minute,0,$Month,$Day,$Year))):"NULL")."')");
			$x=mysql_query("UPDATE PaperTrail SET IsLast='N' WHERE ThisID!=LAST_INSERT_ID() AND TicketID=$TID");
			//echo "INSERT INTO PaperTrail (Creator_CampusID,Receiver_CampusID, State,Comment,Department,TicketID,Creation,Appointment) VALUES($CampusID,$TechCampusID,'OPEN','$Comments','$Dept',$TID,NOW(),'".(($Appointment==1)?(date("Y-m-d H:i:00",mktime((("PM"==$HalfDay)?($Hour+12):$Hour),$Minute,0,$Month,$Day,$Year))):"NULL")."')";


		}
	}

$title = "Ticket ".$TID." for ".mysql_result($AboutTicket,0,"Name");

include "header.php";

?>
<TABLE width=100%><TR><TD valign=top align=left>
<H1>Ticket <? echo $TID; ?></H1>
<H2><?
if (1==$IsLoggedIn) { echo "<A HREF=\x22clientinfo.php?SID=$SID&notables=$notables&CID=".mysql_result($AboutTicket,0,"CID")."\x22>"; }
echo mysql_result($AboutTicket,0,"Name");
if (1==$IsLoggedIn) { ?> </A> <? }
?></H2>
Phone Number(s): <?

$phones=mysql_query("SELECT concat(".sql_phone("Phones.PhNum").",' (', IF(Phones.Hall=0,IF(Phones.Extra=NULL OR Phones.Extra='','',Phones.Extra),IF(Phones.Extra=NULL OR Phones.Extra='',concat(Buildings.Name,' ',Phones.RoomNumber),concat(Phones.Extra, ' - ',Buildings.Name,' ',Phones.RoomNumber))),')') AS 'x' FROM Phones, Buildings WHERE Phones.CampusID=".mysql_result($AboutTicket,0,"CID")." AND Buildings.Number=Phones.Hall");

for ($i=0;$i<mysql_num_rows($phones);$i++) {
	if (0<$i) { ?>, <? }
	echo mysql_result($phones,$i,0);
}

?><BR>
Location: <? echo mysql_result($AboutTicket,0,"Loc"); ?><BR><?
if ("Y"==$userdata["IsAdmin"] || "Y"==$userdata["IsHelpDesk"] || "Y"==$userdata["IsFieldSupport"]) {
	echo "<FORM METHOD=
POST>Jack: <INPUT NAME=\x22JID\x22 VALUE=\x22".mysql_result($AboutTicket,0,"Jack")."\x22 SIZE=12><INPUT TYPE=SUBMIT NAME=\x22Action\x22 VALUE=\x22Update Jack\x22><INPUT TYPE=HIDDEN NAME=SID VALUE=$SID><INPUT TYPE=HIDDEN NAME=TID VALUE=$TID></FORM>\n";
} else {
	echo "Jack: ".mysql_result($AboutTicket,0,"Jack");
}
?></TD><TD valign=top align=right>
<?
if (mysql_result($AboutTicket,0,"Computer")>0) {
	$x=mysql_query("SELECT Brand,Line,Model,OS,OSVer AS 'OS Version' From Computer WHERE ComputerID='".mysql_result($AboutTicket,0,"Computer")."'");
	MakeTableSideways($x,"Computer with problem");
	?><BR><?
}

?></TD></TR></TABLE><?

//The Mac-Daddy of MySQL Queries
	$history=mysql_query("SELECT DATE_FORMAT(PaperTrail.Creation,'%e %b \'%y - %l:%i%p') AS 'Time Stamp', IF(p1.CampusID=0,'UNKNOWN',IF(LENGTH(p1.Nick)>0,concat(p1.Nick,' ',p1.Last),concat(p1.First,' ',p1.Last))) AS 'Created By', PaperTrail.State AS 'State',PaperTrail.Comment AS 'Description', PaperTrail.Department AS 'Dept.', IF(LENGTH(p2.nick)>0,concat(p2.Nick,' ',p2.Last),concat(p2.First,' ',p2.Last)) AS 'For' , IF(PaperTrail.Appointment!='',DATE_FORMAT(PaperTrail.Appointment,'%e %b \'%y - %l:%i%p'),'<CENTER>-</CENTER>') AS 'Appointment' FROM People AS p1, People AS p2, PaperTrail WHERE PaperTrail.TicketID = '$TID' AND p1.CampusID=PaperTrail.Creator_CampusID AND p2.CampusID=PaperTrail.Receiver_CampusID ORDER BY PaperTrail.ThisID");

	MakeTable($history,1,1,1,1,"Ticket History");

	//Only if we're logged in.
	if ($IsLoggedIn==1 && ($userdata["IsIT"] || $userdata["IsLINC"] || $userdata["IsAdmin"])) {

		?><BR><BR><? //some spacing

		$extra=mysql_query("SELECT Receiver_CampusID, ThisID FROM PaperTrail WHERE PaperTrail.TicketID=$TID ORDER BY PaperTrail.ThisID");
		$state=mysql_result($history,mysql_num_rows($history)-1,"State");

		if ("OPEN"==$state) {
			//Stuff to do to an open ticket

			?><HR>This ticket is marked as OPEN. <?
			echo (mysql_result($extra,0,"Receiver_CampusID")>0)?((mysql_result($extra,0,"Receiver_CampusID")==$CampusID)?"It is marked as being assigned to you, thus you should Pick Up the ticket as assist the client as soon as possable, or at the specified appointment time.)":"Though it does appear to be presently assigned to someone, who should assist the client. However it still technically free for anyone to pick up."):"Anyone may pick up this ticket & assist the client. Though that person should be in the ".mysql_result($history, mysql_num_rows($history)-1,4)." department, given that is where the Ticket is currently assigned.";
			?><HR>
			<FORM METHOD=POST>
			Click here to pick up the ticket:
			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<? echo $SID; ?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<? echo $TID; ?>>
			<INPUT TYPE=SUBMIT NAME="Action" Value="Pick Up">
			</FORM>ONLY pick up a ticket immediately before you leave to work on it, and only if it is YOU who are working on it <BR><BR><HR><BR>

			<FORM METHOD=POST>
			Forward to another department<BR>Reason for forwarding:
			<INPUT TYPE=TEXT SIZE=32 NAME="Comments">
			<SELECT NAME="Dept">
			<OPTION VALUE="IT" <? echo ("Y"==$userdata["IsLINC"])?"SELECTED":""; ?>>IT</OPTION>
			<OPTION VALUE="LINC" <? echo ("Y"==$userdata["IsIT"])?"SELECTED":""; ?>>LINC</OPTION>
			</SELECT>
			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<? echo $SID; ?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<? echo $TID; ?>>
			<INPUT TYPE=SUBMIT NAME="Action" Value="Forward">
			</FORM><BR><HR><BR>


			<FORM METHOD=POST>
			Close (i.e. student fixed it themselves)<BR>Reason for pre-mature closure:
			<INPUT TYPE=TEXT SIZE=32 NAME="Comments">
			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<? echo $SID; ?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<? echo $TID; ?>>
			<INPUT TYPE=SUBMIT NAME="Action" Value="Completed">
			</FORM><BR><HR><BR>


			<FORM METHOD=POST>
			Set Appointment Time / Field Support Technician<BR>
			Appointment: 
			<SELECT NAME="Month">
			<? $DayInfo=getdate(); ?>
			<? for ($i=2;$i<14;$i++) { ?>
			<OPTION VALUE=<?
			echo $i-1;
			echo ($DayInfo["mon"]==($i-1))?" SELECTED":" ";
			echo ">";
			echo date("M",gmmktime(0,0,0,$i,0,0));
			?></OPTION>
			<? } ?>
			</SELECT>
			<INPUT TYPE=TEXT SIZE=2 NAME="Day" VALUE=<? echo date("d"); ?>>
			<INPUT TYPE=TEXT SIZE=4 NAME="Year" VALUE=<? echo date("Y"); ?>>
			&nbsp;&nbsp;
			<INPUT TYPE=TEXT SIZE=2 NAME="Hour" align=right VALUE=<? echo date("g"); ?>>
			:
			<INPUT TYPE=TEXT SIZE=2 NAME="Minute" VALUE=<? echo date("i"); ?>>
			<SELECT NAME="HalfDay">
			<OPTION VALUE="AM" <? echo ("AM"==date("A"))?"SELECTED":""; ?>>AM</OPTION>
			<OPTION VALUE="PM" <? echo ("PM"==date("A"))?"SELECTED":""; ?>>PM</OPTION>
			</SELECT>
			<INPUT TYPE=CHECKBOX NAME="Appointment" VALUE="1">Set Appointment Time


<BR>

			Assigned Field Support Tech:
			<SELECT NAME="TechCampusID">
			<OPTION VALUE=0 <? echo (mysql_result($extra,mysql_num_rows($history)-1,0)==0)?"SELECTED":""; ?>>Anyone</OPTION>
			<?
			$y=mysql_query("Select IF(LENGTH(p.nick)>0,concat(p.Nick,' ',p.Last),concat(p.First,' ',p.Last)) ,p.CampusID, concat(p.Last,p.First,p.Middle) AS LastFirst FROM Users as u, People as p WHERE u.CampusID = p.CampusID AND u.".mysql_result($history,mysql_num_rows($history)-1,4)."Staff='Y' AND u.FieldSupport='Y' ORDER BY LastFirst");
			for ($i=0;$i<mysql_num_rows($y);$i++) { ?>
			<OPTION VALUE="<? echo mysql_result($y,$i,1); ?>" <? echo (mysql_result($extra,mysql_num_rows($history)-1,0)==mysql_result($y,$i,1))?"SELECTED":""; ?>><? echo mysql_result($y,$i,0); ?></OPTION>
			<? } ?> 
			</SELECT>
			or Dept: 
			<SELECT NAME="Dept">
			<OPTION VALUE="LINC" <? echo ("Y"==$userdata["IsLINC"])?"SELECTED":""; ?>>LINC</OPTION>
			<OPTION VALUE="IT" <? echo ("Y"==$userdata["IsIT"])?"SELECTED":""; ?>>IT</OPTION>
			</SELECT>
			<BR>
			Remarks (optional): <INPUT TYPE=TEXT SIZE=32 NAME="Comments"><BR>
			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<? echo $SID; ?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<? echo $TID; ?>>
			<INPUT TYPE=SUBMIT NAME="Action" Value="Update Ticket">
			</FORM>

			<?
		} else if ("ACTIVE"==$state && (mysql_result($extra,mysql_num_rows($history)-1,0)==$CampusID || "Y"==$userdata["IsAdmin"])) {
			?><HR>
			<FORM METHOD=POST>
			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<? echo $SID; ?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<? echo $TID; ?>>
			<TABLE>
			<TR><TD colspan=3>Comments / Remarks:</TD></TR>
			<TR>
			<TD rowspan=3><TEXTAREA name="Comments" rows=6 cols=28></TEXTAREA></TD>
			<TD valign=top><INPUT TYPE=SUBMIT NAME="Action" Value="Completed">&nbsp;- Close Ticket</TD></TR>
			<TR><TD valign=middle> <INPUT TYPE=SUBMIT NAME="Action" Value="Add Remark"> but keep ACTIVE</TD></TR>
			<TR><TD valign=bottom> <INPUT TYPE=SUBMIT NAME="Action" Value="Forward"> to 
			<SELECT NAME="Dept">
			<OPTION VALUE="IT" <? echo ("Y"==$userdata["IsLINC"])?"SELECTED":""; ?>>IT</OPTION>
			<OPTION VALUE="LINC" <? echo ("Y"==$userdata["IsIT"])?"SELECTED":""; ?>>LINC</OPTION>
			</SELECT>
			</TD></TR>
			</TABLE>
			</FORM><BR><HR><BR>

			<FORM METHOD=POST>
			Set Appointment Time / Field Support Technician<BR>
			Appointment: 
			<SELECT NAME="Month">
			$DayInfo=getdate();
			<? for ($i=2;$i<14;$i++) { ?>
			<OPTION VALUE=<?
			echo $i;
			echo ($DayInfo["mon"]==($i-1))?" SELECTED":" ";
			echo ">";
			echo date("M",gmmktime(0,0,0,$i,0,0));
			?></OPTION>
			<? } ?>
			</SELECT>
			<INPUT TYPE=TEXT SIZE=2 NAME="Day" VALUE=<? echo date("d"); ?>>
			<INPUT TYPE=TEXT SIZE=4 NAME="Year" VALUE=<? echo date("Y"); ?>>
			&nbsp;&nbsp;
			<INPUT TYPE=TEXT SIZE=2 NAME="Hour" align=right VALUE=<? echo date("g"); ?>>
			:
			<INPUT TYPE=TEXT SIZE=2 NAME="Minute" VALUE=<? echo date("i"); ?>>
			<SELECT NAME="HalfDay">
			<OPTION VALUE="AM" <? echo ("AM"==date("A"))?"SELECTED":""; ?>>AM</OPTION>
			<OPTION VALUE="PM" <? echo ("PM"==date("A"))?"SELECTED":""; ?>>PM</OPTION>
			</SELECT>
			<INPUT TYPE=CHECKBOX NAME="Appointment" VALUE="1">Set Appointment Time

<BR>
			Assigned Field Support Tech:
			<SELECT NAME="TechCampusID">
			<OPTION VALUE=0 <? echo (mysql_result($extra,mysql_num_rows($history)-1,0)==0)?"SELECTED":""; ?>>Anyone</OPTION>
			<?
			$y=mysql_query("Select IF(LENGTH(p.nick)>0,concat(p.Nick,' ',p.Last),concat(p.First,' ',p.Last)) ,p.CampusID, concat(p.Last,p.First,p.Middle) AS LastFirst FROM Users as u, People as p WHERE u.CampusID = p.CampusID AND u.".mysql_result($history,mysql_num_rows($history)-1,4)."Staff='Y' AND u.FieldSupport='Y' ORDER BY LastFirst");
			for ($i=0;$i<mysql_num_rows($y);$i++) { ?>
			<OPTION VALUE="<? echo mysql_result($y,$i,1); ?>" <? echo (mysql_result($extra,mysql_num_rows($history)-1,0)==mysql_result($y,$i,1))?"SELECTED":""; ?>><? echo mysql_result($y,$i,0); ?></OPTION>
			<? } ?> 
			</SELECT>
			or Dept: 
			<SELECT NAME="Dept">
			<OPTION VALUE="LINC" <? echo ("Y"==$userdata["IsLINC"])?"SELECTED":""; ?>>LINC</OPTION>
			<OPTION VALUE="IT" <? echo ("Y"==$userdata["IsIT"])?"SELECTED":""; ?>>IT</OPTION>
			</SELECT>
			<BR>
			Remarks (optional): <INPUT TYPE=TEXT SIZE=32 NAME="Comments"><BR>
			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<? echo $SID; ?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<? echo $TID; ?>>
			<INPUT TYPE=SUBMIT NAME="Action" Value="Update Ticket">
			</FORM>
			<?
		} else if ("ACTIVE"==$state) {
			?><HR><BR>This ticket is currectly ACTIVE. That means the user who marked it active is currently working on the problem. If this probablem has been resolved, forgotten about, or otherwise should no longer be ACTIVE, the Force Open option below can re-open the ticket for work by others or closure.<BR><BR><HR><BR>
			<FORM METHOD=POST>
			Reason for re-opening:
			<INPUT TYPE=TEXT SIZE=32 NAME="Comments">
			<SELECT NAME="Dept">
			<OPTION VALUE="IT" <? echo ("Y"==$userdata["IsIT"])?"SELECTED":""; ?>>IT</OPTION>
			<OPTION VALUE="LINC" <? echo ("Y"==$userdata["IsLINC"])?"SELECTED":""; ?>>LINC</OPTION>
			</SELECT>
			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<? echo $SID; ?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<? echo $TID; ?>>
			<INPUT TYPE=SUBMIT NAME="Action" Value="Force Open">
			</FORM><?
		} else {
			?><HR><BR>This ticket is marked as having been completed. It may, however, be re-opened if the client is experiencing the same problem, or it is determined that the problem wasn't really resolved.<BR><BR><HR><BR>
			<FORM METHOD=POST>
			Reason for re-opening:
			<INPUT TYPE=TEXT SIZE=32 NAME="Comments">
			<SELECT NAME="Dept">
			<OPTION VALUE="IT" <? echo ("Y"==$userdata["IsIT"])?"SELECTED":""; ?>>IT</OPTION>
			<OPTION VALUE="LINC" <? echo ("Y"==$userdata["IsLINC"])?"SELECTED":""; ?>>LINC</OPTION>
			</SELECT>
			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<? echo $SID; ?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<? echo $TID; ?>>
			<INPUT TYPE=SUBMIT NAME="Action" Value="Force Open">
			</FORM>
			<?
		}



/*		if ($state="OPEN" && ($userdata["Is".mysql_result($history,mysql_num_rows($history-1),"Dept")]=="Y" || "Y"==$userdata["IsAdmin"])*/


	} else {


	?><BR><BR><BR><BR><?
	$color=MakeTable(mysql_query("SELECT CallTicket.TicketID, PaperTrail.Comment AS Description, PaperTrail.Creation AS Created FROM CallTicket,PaperTrail WHERE CallTicket.CampusID=$CID AND PaperTrail.TicketID=CallTicket.TicketID AND PaperTrail.IsFirst='Y' AND PaperTrail.TicketID!=$TID ORDER BY TicketID"),1,1,1,3,"Your Other Tickets");

	}


} else {

include "header.php";


if (""!=$Action || $SID>0) {MustLogIn();}
?>
<H1>Wrong CampusID</H1>
The Campus ID Specified (<? echo $CID; ?>) does not match the database entry for ticket <? echo $TID; ?>. If you this this message is in error, and that is your correct Campus ID and ticket number, please contact 408-551-1705 (x1705 on Campus) to verify your ticket number.
<?

}

include "footer.php";
db_logout($hdb);
?>
