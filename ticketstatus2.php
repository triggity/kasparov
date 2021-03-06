<?
include "config.php";
include "database.php";
include "functions.php";
include "login.php";

 //Call the DB to get information about the ticket - this call is made again if the ticket is modified
$AboutTicket = mysql_query("
	SELECT
		concat(IFNULL(People.Last,''),', ',IFNULL(People.First,''),' ', IFNULL(People.Middle,'')) AS Name,
		CallTicket.CampusID AS CID,
		CallTicket.JackID AS Jack,
		CallTicket.ComputerID AS Computer,
		CallTicket.Location AS Loc
	FROM
		People, CallTicket
	WHERE
		CallTicket.TicketID='$TID' AND
		People.CampusID=CallTicket.CampusID
");

//MakeTable($AboutTicket,1,1,1,1,"Hello"); //some debugging code

if (0==mysql_num_rows($AboutTicket)) { //if the result has no rows, the ticket wasn't found

include "header.php";
?>
<H1>Ticket Not Found</H1>
Ticket <? echo $TID; ?> was not found in the database.
<?

} else if (1==$IsLoggedIn || mysql_result($AboutTicket,0,"CID")==$CID) { //the ticket must be accessed by a user who is logged in, or that knows the student ID # related to the ticket.

	//Only if we're logged in -- Do any necissary processing.
	if ($IsLoggedIn==1 && ($userdata["IsHelpDesk"]=="Y" || $userdata["IsFieldSupport"]=="Y" || $userdata["IsAdmin"]=="Y")) { //Make sure User has permission to modify the ticket

		
		if ("Update Jack"==$Action) { // 
			$x=mysql_query("UPDATE CallTicket SET JackID='$JID' WHERE TicketID=$TID"); //DB Query to do the update

			 //get the correct about the ticket.
			$AboutTicket = mysql_query("
			SELECT 
				concat(IFNULL(People.Last,''),', ',IFNULL(People.First,''),' ', IFNULL(People.Middle,'')) AS Name,
				CallTicket.CampusID AS CID,
				CallTicket.JackID AS Jack,
				CallTicket.ComputerID AS Computer,
				CallTicket.Location AS Loc
			FROM
				People, CallTicket
			WHERE
				CallTicket.TicketID='$TID' AND
				People.CampusID=CallTicket.CampusID
			");

		} else if ("Update Computer"==$Action) { // 
			$x=mysql_query("UPDATE CallTicket SET ComputerID='$Comp' WHERE TicketID=$TID"); //DB Query to do the update

			 //get the correct about the ticket.
			$AboutTicket = mysql_query("
			SELECT 
				concat(IFNULL(People.Last,''),', ',IFNULL(People.First,''),' ', IFNULL(People.Middle,'')) AS Name,
				CallTicket.CampusID AS CID,
				CallTicket.JackID AS Jack,
				CallTicket.ComputerID AS Computer,
				CallTicket.Location AS Loc
			FROM
				People, CallTicket
			WHERE
				CallTicket.TicketID='$TID' AND
				People.CampusID=CallTicket.CampusID
			");


		} else if ("Update Location"==$Action) { // 
			$x=mysql_query("UPDATE CallTicket SET Location='$Loc' WHERE TicketID=$TID"); //DB Query to do the update

			 //get the correct about the ticket.
			$AboutTicket = mysql_query("
			SELECT 
				concat(IFNULL(People.Last,''),', ',IFNULL(People.First,''),' ', IFNULL(People.Middle,'')) AS Name,
				CallTicket.CampusID AS CID,
				CallTicket.JackID AS Jack,
				CallTicket.ComputerID AS Computer,
				CallTicket.Location AS Loc
			FROM
				People, CallTicket
			WHERE
				CallTicket.TicketID='$TID' AND
				People.CampusID=CallTicket.CampusID
			");



		} else if ("Update Client"==$Action && $IdNum>0 && $userdata["IsAdmin"]=="Y") { // 
			$x=mysql_query("UPDATE CallTicket SET CampusID='$IdNum' WHERE TicketID=$TID"); //DB Query to do the update

			 //get the correct about the ticket.
			$AboutTicket = mysql_query("
			SELECT 
				concat(IFNULL(People.Last,''),', ',IFNULL(People.First,''),' ', IFNULL(People.Middle,'')) AS Name,
				CallTicket.CampusID AS CID,
				CallTicket.JackID AS Jack,
				CallTicket.ComputerID AS Computer,
				CallTicket.Location AS Loc
			FROM
				People, CallTicket
			WHERE
				CallTicket.TicketID='$TID' AND
				People.CampusID=CallTicket.CampusID
			");

		} else if (("Open Ticket"==$Action || "Close Ticket"==$Action || "Transfer Ticket"==$Action || "Set Assignment"==$Action || "Make Appointment"==$Action || "Pick Up Ticket"==$Action || "Add Remark"==$Action)&& (($PreComment." ".$Comment)!=" ")) {
			$Comments=$PreComment." ".$Comment;

			$x=mysql_query("
				INSERT INTO PaperTrail (
					Creator_CampusID,
					Receiver_CampusID, 
					State,
					Comment,
					Department,
					TicketID,
					Creation,
					Appointment
				) VALUES (
					$CampusID,
					$Dest,
					'$NewState',
					'$Comments',
					'$Dept',
					$TID,
					NOW(),
					".(($Appt==1)?("'".date("Y-m-d H:i:00",mktime((("PM"==$HalfDay)?($Hour+12):$Hour),$Minute,0,$Month,$Day,$Year))."'"):"NULL")."
				)
			");

			$x=mysql_query("UPDATE PaperTrail SET IsLast='N' WHERE ThisID!=LAST_INSERT_ID() AND TicketID=$TID");

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
	echo "Jack: ".mysql_result($AboutTicket,0,"Jack");
?></TD><TD valign=top align=right>
<?
if (mysql_result($AboutTicket,0,"Computer")>0) {
	$x=mysql_query("SELECT Brand,Line,Model,OS,OSVer AS 'OS Version' From Computer WHERE ComputerID='".mysql_result($AboutTicket,0,"Computer")."'");
	MakeTableSideways($x,"Computer with problem");
	?><BR><?
}

?></TD></TR></TABLE><?

	//The Mac-Daddy of MySQL Queries
	$history=mysql_query("
		SELECT
			DATE_FORMAT(PaperTrail.Creation,'%e %b \'%y - %l:%i%p') AS 'Time Stamp',
			IF(p1.CampusID=0,'UNKNOWN',IF(LENGTH(p1.Nick)>0,concat(p1.Nick,' ',p1.Last),concat(p1.First,' ',p1.Last))) AS 'Created By',
			PaperTrail.State AS 'State',
			PaperTrail.Comment AS 'Description',
			PaperTrail.Department AS 'Dept',
			IF(LENGTH(p2.nick)>0,concat(p2.Nick,' ',p2.Last),concat(p2.First,' ',p2.Last)) AS 'For',
			IF(PaperTrail.Appointment!='',DATE_FORMAT(PaperTrail.Appointment,'%e %b \'%y - %l:%i%p'),'<CENTER>-</CENTER>') AS 'Appointment'
		FROM
			People AS p1, People AS p2, PaperTrail
		WHERE
			PaperTrail.TicketID = '$TID' AND						p1.CampusID=PaperTrail.Creator_CampusID AND
			p2.CampusID=PaperTrail.Receiver_CampusID
		ORDER BY PaperTrail.ThisID
	");

	MakeTable($history,1,1,1,1,"Ticket History");

	//Only if we're logged in.
	if ($IsLoggedIn==1 && ($userdata["IsIT"] || $userdata["IsLINC"] || $userdata["IsAdmin"])) {

	if (1==$printable) { //if it's "printable" don't show other stuff
	} else {

	//get the current state of the ticket
	$extra=mysql_query("SELECT Receiver_CampusID, ThisID FROM PaperTrail WHERE PaperTrail.TicketID=$TID ORDER BY PaperTrail.ThisID");
	$state=mysql_result($history,mysql_num_rows($history)-1,"State");
	//$foryou=(mysql_result($extra,mysql_num_rows($extra)-1,"Receiver_CampusID")==$IdNum);

	//visual cue grouping via background color.
	?>
	<BR><BR>
	<TABLE WIDTH=100% border=0 cellspacing=0 cellpadding=5><TR><TD BGCOLOR=<?=$color_table_lt_bg?>>
	<TABLE border=0 width=100% cellspacing=6><TR><TD width=50% valign=middle>
	The current state of this ticket is <B><?=$state?></B>.
	<?
		if ("OPEN"==$state) {
			?> It represents a problem which has not yet been resolved. Additionally, no one is currently doing work on this ticket. <?
			if ("Anyone "==mysql_result($history,mysql_num_rows($history)-1,"For")) { //For no one, or assigned?
				?> No one has been assigned to it, so whoever is available should pick it up<?
			} else {
				?> It has been assigned to <?=mysql_result($history,mysql_num_rows($history)-1,"For")?>, who should pick it up<?
			}
			if  ("<CENTER>-</CENTER>"==mysql_result($history,mysql_num_rows($history)-1,"Appointment")) { //do we have an appointment?
				?>.<?
			} else {
				echo " "
.mysql_result($history,mysql_num_rows($history)-1,"Appointment").".";
			}
			?><BR>
			If this ticket has been open for an extended period of time and the client cannot be contacted, the ticket should be closed, and a message should be left on their voicemail reminding them their TicketID (<?=$TID?>) and to call back if they still need assistance.<?
		} else if ("ACTIVE"==$state) {
			?> It represents a problem which has not yet been resolved, however it is currently being worked on.  <?=mysql_result($history,mysql_num_rows($history)-1,"Created By")?> is currently working on the ticket, and should be contacted if this ticket has been active with no updates for a few days. If necissary the ticket may be re-assigned to another staff member.<?
		} else {
			?> Either the problem has been resolved, it has been deemed unresolveable, or the user has been unable to be contacted. If the client experiences the same problem, or calls back about this problem after being unable to be contacted, it should be re-opened. <?
		}
	?>
	</TD>
	<TD width=50% valign=top>
		<H2>Actions:</H2>
		<UL><?
		if ("OPEN"==$state) { ?>
			<LI><A HREF="#PickUp">Pick Up Ticket</A></LI>
			<LI><A HREF="#Appt">Make Appointment</A></LI>
			<LI><A HREF="#Assign">Set Assignment</A></LI>
			<LI><A HREF="#Trans">Transfer to other department</A></LI>
			<LI><A HREF="#Close">Force Closure</A></LI>
		<? } else if ("ACTIVE"==$state) { ?>
			<? if (mysql_result($extra,mysql_num_rows($extra)-1,"Receiver_CampusID")!=$CampusID) { ?>
			<LI><A HREF="#PickUp">Pick Up Ticket</A></LI>
			<? } ?>
			<LI><A HREF="#Close">Close Ticket (completed)</A></LI>
			<LI><A HREF="#Remark">Add Remark (keep active)</A></LI>
			<LI><A HREF="#Appt">Make Appointment</A></LI>
			<LI><A HREF="#Assign">Set Assignment</A></LI>
			<LI><A HREF="#Trans">Transfer to other department</A></LI>
		<? } else { ?>
			<LI><A HREF="#Open">Re-Open</A></LI>
		<? } ?>
			<BR><BR>
			<LI><A HREF="#Jack">Change Associated Jack</A></LI>
			<LI><A HREF="#Loc">Change Associated Location</A></LI>
			<LI><A HREF="#Comp">Change Associated Computer</A></LI>
			<BR></BR>
			<LI><A HREF="ticketstatus.php?SID=<?=$SID?>&TID=<?=$TID?>&notables=1&printable=1">Printable Ticket (reloads page)</A></LI>
		</UL>
	</TD></TR></TABLE>
	
	<BR><BR>

	<? /*----------------------OPEN Ticket------------------*/
	if ("OPEN"==$state || ("ACTIVE"==$state && mysql_result($extra,mysql_num_rows($extra)-1,"Receiver_CampusID")!=$CampusID)) { ?>

		<!-----Pick Up Ticket------>
		<A NAME="PickUp">
		<TABLE border=0 width=100% cellpadding=3 cellspacing=0>
		<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>
			Pick Up Ticket
		<?=$cfont_title?></TD></TR><TR><TD bgcolor=<?=$color_table_dk_bg?>>

		This ticket is currently assigned to <?=mysql_result($history,mysql_num_rows($history)-1,"For")?>. Picking up a ticket declares that you are beginning to do work on this problem. A ticket should be picked up immediately before leaving to assist a client with their problem.<BR><BR> 
		<FORM METHOD=POST>

			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?=$SID?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<?=$TID?>>
			<INPUT TYPE=HIDDEN NAME="Comment" VALUE="Picked up Ticket">
			<INPUT TYPE=HIDDEN NAME="Dest" VALUE="<?=$CampusID?>">
			<INPUT TYPE=HIDDEN NAME="Appt" VALUE="0">
			<INPUT TYPE=HIDDEN NAME="NewState" VALUE="ACTIVE">
			<INPUT TYPE=HIDDEN NAME="Dept" VALUE="<?=(("Y"==$userdata["IsIT"])?"IT":"LINC")?>">
			<CENTER><INPUT TYPE=SUBMIT NAME="Action" VALUE="Pick Up Ticket"></CENTER>

		</FORM>
		</TD></TR></TABLE>
		<BR><BR>

	<?
	}

	if ("ACTIVE"==$state) { ?>

		<!-----Close Ticket------>
		<A NAME="Close">
		<TABLE border=0 width=100% cellpadding=3 cellspacing=0>
		<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>
			Complete / Close Ticket
		<?=$cfont_title?></TD></TR><TR><TD bgcolor=<?=$color_table_dk_bg?>>

		When a ticket has been completed, it should be closed with an explaination of how the problem was resolved. A ticket may also be closed if the problem is deemed unresolveable or the client cannot be contacted.<BR><BR> 
		<FORM METHOD=POST>

			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?=$SID?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<?=$TID?>>
			<CENTER>
			<TABLE border=0 cellspacing=5><TR><TD align=right>
			Standard Reason:
			</TD><TD>
			<SELECT NAME="PreComment">
			<OPTION></OPTION>
			<OPTION>Assisted standard network software/hardware install.</OPTION>
			<OPTION>Client resolved the problem themselves.</OPTION>
			</SELECT>
			</TD></TR><TR><TD valign=top align=right>
			Detailed Reason:</TD><TD>
			<TEXTAREA NAME="Comment" ROWS=5 COLS=40></TEXTAREA>
			</TD></TR></TABLE>
			<INPUT TYPE=HIDDEN NAME="Dest" VALUE="0">
			<INPUT TYPE=HIDDEN NAME="Appt" VALUE="0">
			<INPUT TYPE=HIDDEN NAME="NewState" VALUE="CLOSED">
			<INPUT TYPE=HIDDEN NAME="Dept" VALUE="<?=mysql_result($history,mysql_num_rows($history)-1,"Dept")?>">
			<INPUT TYPE=SUBMIT NAME="Action" VALUE="Close Ticket"></CENTER>

		</FORM>
		</TD></TR></TABLE>
		<BR><BR>


		<!-----Add Remark------>
		<A NAME="Remark">
		<TABLE border=0 width=100% cellpadding=3 cellspacing=0>
		<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>
			Add Remark
		<?=$cfont_title?></TD></TR><TR><TD bgcolor=<?=$color_table_dk_bg?>>

		Anyone can add a remark about an active ticket. It will remain as a ticket for the person currently working on the problem. A remark might be used by the person working on a ticket to declare they had to suspend work on a ticket and will resume later, or by another staff member as a suggesting of what the current field support technician should do.<BR><BR> 
		<FORM METHOD=POST>

			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?=$SID?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<?=$TID?>>
			<CENTER>Remark:<BR>
			<TEXTAREA NAME="Comment" ROWS=5 COLS=40></TEXTAREA>
			<INPUT TYPE=HIDDEN NAME="Dest" VALUE="<?=mysql_result($extra,mysql_num_rows($extra)-1,"Receiver_CampusID")?>">
			<INPUT TYPE=HIDDEN NAME="Appt" VALUE="0">
			<INPUT TYPE=HIDDEN NAME="NewState" VALUE="ACTIVE">
			<INPUT TYPE=HIDDEN NAME="Dept" VALUE="<?=mysql_result($history,mysql_num_rows($history)-1,"Dept")?>">
			<CENTER><INPUT TYPE=SUBMIT NAME="Action" VALUE="Add Remark"></CENTER>

		</FORM>
		</TD></TR></TABLE>
		<BR><BR>


	<?
	}
	if ("OPEN"==$state || "ACTIVE"==$state) { ?>


		<!-----Make Appointment------>
		<A NAME="Appt">
		<TABLE border=0 width=100% cellpadding=3 cellspacing=0>
		<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>
			Make Appointment
		<?=$cfont_title?></TD></TR><TR><TD bgcolor=<?=$color_table_dk_bg?>>

		Appointments should be made if the client cannot be assisted immediately: for example they are going to be unavailable or there is no available RCC.<BR><BR> 
		<FORM METHOD=POST>

			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?=$SID?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<?=$TID?>>

			<CENTER><TABLE border=0 cellspacing=0>
			<TR><TD align=center width=150>Date + Time:</TD><TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD align=center width=150>Field Support Tech:</TD></TR>
			<TR><TD align=center> <!--Select Date/Time-->

			<?DateOpt();?>
			<BR>
			<?TimeOpt();?>

			</TD><TD></TD><TD align=center> <!--Select RCC-->
				<SELECT NAME="Dest">
				<OPTION VALUE=0 <? echo (mysql_result($extra,mysql_num_rows($history)-1,0)==0)?"SELECTED":""; ?>>Anyone</OPTION>
				<?
				$y=mysql_query("Select IF(LENGTH(p.nick)>0,concat(p.Nick,' ',p.Last),concat(p.First,' ',p.Last)) ,p.CampusID, concat(p.Last,p.First,p.Middle) AS LastFirst FROM Users as u, People as p WHERE u.CampusID = p.CampusID AND u.".mysql_result($history,mysql_num_rows($history)-1,4)."Staff='Y' AND u.FieldSupport='Y' ORDER BY LastFirst");
				for ($i=0;$i<mysql_num_rows($y);$i++) { ?>
					<OPTION VALUE="<? echo mysql_result($y,$i,1); ?>" <? echo (mysql_result($extra,mysql_num_rows($history)-1,0)==mysql_result($y,$i,1))?"SELECTED":""; ?>><? echo mysql_result($y,$i,0); ?></OPTION>
				<? } ?> 
				</SELECT>

			</TD></TR>
			<TR><TD colspan=3 align=center>
			<BR>
			Reason: <INPUT TYPE=TEXT NAME="Comment" VALUE="" SIZE=32>
			</TD></TR></TABLE><BR>


			<INPUT TYPE=HIDDEN NAME="PreComment" VALUE="Appointment">
			<INPUT TYPE=HIDDEN NAME="Appt" VALUE="1">
			<INPUT TYPE=HIDDEN NAME="NewState" VALUE="OPEN">
			<INPUT TYPE=HIDDEN NAME="Dept" VALUE="<?=mysql_result($history,mysql_num_rows($history)-1,"Dept")?>">
			<CENTER><INPUT TYPE=SUBMIT NAME="Action" VALUE="Make Appointment"></CENTER>

		</FORM>
		</TD></TR></TABLE>
		<BR><BR>



		<!-----Set Assignment----->
		<A NAME="Assign">
		<TABLE border=0 width=100% cellpadding=3 cellspacing=0>
		<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>
			Set Assignment
		<?=$cfont_title?></TD></TR><TR><TD bgcolor=<?=$color_table_dk_bg?>>

		Assignments are intented to be used when a specific RCC should work on a given problem. For example, that RCC is a specialist in something which another RCC was unable to solve.<BR><BR> 
		<FORM METHOD=POST>

			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?=$SID?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<?=$TID?>>

			<CENTER><TABLE border=0 cellspacing=0>
			<TR><TD>Field Support Tech:</TD><TD>Reason:</TD>
			<TR><TD align=center> <!--Select RCC-->
				<SELECT NAME="Dest">
				<OPTION VALUE=0 <? echo (mysql_result($extra,mysql_num_rows($history)-1,0)==0)?"SELECTED":""; ?>>Anyone</OPTION>
				<?
				$y=mysql_query("Select IF(LENGTH(p.nick)>0,concat(p.Nick,' ',p.Last),concat(p.First,' ',p.Last)) ,p.CampusID, concat(p.Last,p.First,p.Middle) AS LastFirst FROM Users as u, People as p WHERE u.CampusID = p.CampusID AND u.".mysql_result($history,mysql_num_rows($history)-1,4)."Staff='Y' AND u.FieldSupport='Y' ORDER BY LastFirst");
				for ($i=0;$i<mysql_num_rows($y);$i++) { ?>
					<OPTION VALUE="<? echo mysql_result($y,$i,1); ?>" <? echo (mysql_result($extra,mysql_num_rows($history)-1,0)==mysql_result($y,$i,1))?"SELECTED":""; ?>><? echo mysql_result($y,$i,0); ?></OPTION>
				<? } ?> 
				</SELECT>

			</TD><TD>
			<INPUT TYPE=TEXT NAME="Comment" VALUE="" SIZE=32>
			</TD></TR></TABLE><BR>


			<INPUT TYPE=HIDDEN NAME="PreComment" VALUE="Reassignment">
			<INPUT TYPE=HIDDEN NAME="Appt" VALUE="0">
			<INPUT TYPE=HIDDEN NAME="NewState" VALUE="OPEN">
			<INPUT TYPE=HIDDEN NAME="Dept" VALUE="<?=mysql_result($history,mysql_num_rows($history)-1,"Dept")?>">
			<CENTER><INPUT TYPE=SUBMIT NAME="Action" VALUE="Set Assignment"></CENTER>

		</FORM>
		</TD></TR></TABLE>
		<BR><BR>




		<!-----Set Department----->
		<A NAME="Trans">
		<TABLE border=0 width=100% cellpadding=3 cellspacing=0>
		<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>
			Transfer to Other Department
		<?=$cfont_title?></TD></TR><TR><TD bgcolor=<?=$color_table_dk_bg?>>

		If a problem cannot be solved by one department, perhaps it may be more appropriately handled by another. Before transfering jack issues to IT, the problem <B>MUST</B> be verified with a NetTool.
		<TABLE border=0 cellpadding=8 cellspacing=8 width=100%><TR><TD bgcolor=#FFFF00 align=center><font size=+1>Be sure to include the error as reported by the NetTool. Simply "verified problem with NetTool" is an inadequate explaination.</font></TD></TR></TABLE>
		<FORM METHOD=POST>

			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?=$SID?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<?=$TID?>>

			<CENTER><TABLE border=0 cellspacing=6>
			<TR><TD>Dept:</TD><TD>Reason:</TD>
			<TR><TD align=center> <!--Select Dept-->
				<SELECT NAME="Dept">
					<OPTION VALUE="LINC">LINC</OPTION>
					<OPTION VALUE="IT" <? echo ("LINC"==mysql_result($history,mysql_num_rows($history)-1,"Dept"))?"SELECTED":""; ?>>IT</OPTION>
				</SELECT>

			</TD><TD>
			<INPUT TYPE=TEXT NAME="Comment" VALUE="" SIZE=32>
			</TD></TR></TABLE><BR>


			<INPUT TYPE=HIDDEN NAME="PreComment" VALUE="Forwarded to other department">
			<INPUT TYPE=HIDDEN NAME="Appt" VALUE="0">
			<INPUT TYPE=HIDDEN NAME="NewState" VALUE="OPEN">
			<INPUT TYPE=HIDDEN NAME="Dest" VALUE="0">
			<CENTER><INPUT TYPE=SUBMIT NAME="Action" VALUE="Transfer Ticket"></CENTER>

		</FORM>
		</TD></TR></TABLE>
		<BR><BR>

	<? 
	}

	/*----------------------OPEN Ticket------------------*/
	if ("OPEN"==$state) { ?>

		<!-----Close----->
		<A NAME="Close">
		<TABLE border=0 width=100% cellpadding=3 cellspacing=0>
		<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>
			Premature Closure
		<?=$cfont_title?></TD></TR><TR><TD bgcolor=<?=$color_table_dk_bg?>>

		A ticket may be directly closed from its OPEN state under certain circumstances. Specifically a ticket should be closed if the client cannot be contacted, or the problem was resolved without involving a field support visit.<BR><BR> 
		<FORM METHOD=POST>

			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?=$SID?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<?=$TID?>>

			<CENTER>
			<SELECT NAME="PreComment">
				<OPTION></OPTION>
				<OPTION>Client could not be contacted - message was left with them to call back</OPTION>
				<OPTION>Client could not be contacted - contact information is incorrect</OPTION>
				<OPTION>Problem was unable to be resolved</OPTION>
				<OPTION>Client fixed the problem themselves</OPTION>
				<OPTION>Problem was resolved over the phone.</OPTION>
			</SELECT><BR>
			Further Explaination: <INPUT TYPE=TEXT NAME="Comment" VALUE="" SIZE=32><BR><BR>
			</CENTER>


			<INPUT TYPE=HIDDEN NAME="Appt" VALUE="0">
			<INPUT TYPE=HIDDEN NAME="Dest" VALUE="0">
			<INPUT TYPE=HIDDEN NAME="NewState" VALUE="CLOSED">
			<INPUT TYPE=HIDDEN NAME="Dept" VALUE="<?=mysql_result($history,mysql_num_rows($history)-1,"Dept")?>">
			<CENTER><INPUT TYPE=SUBMIT NAME="Action" VALUE="Close Ticket"></CENTER>

		</FORM>
		</TD></TR></TABLE>
		<BR><BR>




	<? /*----------------------ACTIVE Ticket------------------*/
	} else if ("ACTIVE"==$state) { ?>



	<? /*----------------------CLOSED Ticket------------------*/
	} else { ?>
		<!-----Re-Open----->
		<A NAME="Open">
		<TABLE border=0 width=100% cellpadding=3 cellspacing=0>
		<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>
			Re-open Ticket
		<?=$cfont_title?></TD></TR><TR><TD bgcolor=<?=$color_table_dk_bg?>>

		Tickets may be re-opened in the event a problem wasn't resolved or the same problem is happening again.<BR><BR> 
		<FORM METHOD=POST>

			<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?=$SID?>>
			<INPUT TYPE=HIDDEN NAME="TID" VALUE=<?=$TID?>>

			<CENTER>
			Standard Reason: <SELECT NAME="PreComment">
				<OPTION></OPTION>
				<OPTION>Client called back.</OPTION>
				<OPTION>Problem was never resolved.</OPTION>
				<OPTION>Same thing has happened again.</OPTION>
			</SELECT><BR>
			Nonstandard / Further Explaination: <INPUT TYPE=TEXT NAME="Comment" VALUE="" SIZE=32><BR><BR>
			</CENTER>


			<INPUT TYPE=HIDDEN NAME="Appt" VALUE="0">
			<INPUT TYPE=HIDDEN NAME="Dest" VALUE="0">
			<INPUT TYPE=HIDDEN NAME="NewState" VALUE="OPEN">
			<INPUT TYPE=HIDDEN NAME="Dept" VALUE="<?=mysql_result($history,mysql_num_rows($history)-1,"Dept")?>">
			<CENTER><INPUT TYPE=SUBMIT NAME="Action" VALUE="Open Ticket"></CENTER>

		</FORM>
		</TD></TR></TABLE>
		<BR><BR>

	<? } ?>


	<TABLE border=0 cellpadding=0 cellspacing=0 width=100%><TR><TD width=33%>
		<!-----Change Jack----->
		<A NAME="Jack">
		<TABLE border=0 width=100% cellpadding=3 cellspacing=0>
		<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>
			Change Jack Number
		<?=$cfont_title?></TD></TR><TR><TD bgcolor=<?=$color_table_dk_bg?> align=center valign=middle>
		<FORM METHOD=POST>

		<? echo "Jack:<BR><INPUT NAME=\x22JID\x22 VALUE=\x22".mysql_result($AboutTicket,0,"Jack")."\x22 SIZE=12><BR><INPUT TYPE=SUBMIT NAME=\x22Action\x22 VALUE=\x22Update Jack\x22><INPUT TYPE=HIDDEN NAME=SID VALUE=$SID><INPUT TYPE=HIDDEN NAME=TID VALUE=$TID>\n"; ?>

		</TD></TR></TABLE></FORM>

		</TD><TD>&nbsp;</TD><TD width=33%>


		<!-----Change Location----->
		<A NAME="Loc">
		<TABLE border=0 width=100% cellpadding=3 cellspacing=0>
		<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>
			Change Location
		<?=$cfont_title?></TD></TR><TR><TD bgcolor=<?=$color_table_dk_bg?> align=center valign=middle>
		<FORM METHOD=POST>

		<? echo "Location:<BR><INPUT NAME=\x22Loc\x22 VALUE=\x22".mysql_result($AboutTicket,0,"Loc")."\x22 SIZE=16><BR><INPUT TYPE=SUBMIT NAME=\x22Action\x22 VALUE=\x22Update Location\x22><INPUT TYPE=HIDDEN NAME=SID VALUE=$SID><INPUT TYPE=HIDDEN NAME=TID VALUE=$TID>\n"; ?>

		</TD></TR></TABLE></FORM>

		</TD><TD>&nbsp;</TD><TD width=33%>


		<!-----Change Computer----->
		<A NAME="Comp">
		<TABLE border=0 width=100% cellpadding=3 cellspacing=0>
		<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>
			Change Computer
		<?=$cfont_title?></TD></TR><TR><TD bgcolor=<?=$color_table_dk_bg?> align=center valign=middle>
		<FORM METHOD=POST>

		<?

		$ComputerList=mysql_query("SELECT ComputerID AS Hidden,Brand, Line, Model, OS, OSVer FROM Computer WHERE Computer.CampusID=".mysql_result($AboutTicket,0,"CID"));
		?>
			Computer:<BR>
			<SELECT NAME="Comp">
				<OPTION VALUE="NULL">n/a</OPTION><?
				for ($i=0;$i<mysql_num_rows($ComputerList);$i++) {
				?><OPTION VALUE=<?
					echo "\x22".mysql_result($ComputerList,$i,"Hidden")."\x22";
					if (mysql_result($AboutTicket,0,"Computer")==mysql_result($ComputerList,$i,"Hidden")) {
						echo " SELECTED";
					}
				?>><?
				echo mysql_result($ComputerList,$i,"Brand")." ".mysql_result($ComputerList,$i,"Line")." ".mysql_result($ComputerList,$i,"Model");
				?></OPTION><?
				} ?>
			</SELECT><BR>
			<INPUT TYPE=SUBMIT NAME="Action" VALUE="Update Computer">
		 	

		</TD></TR></TABLE></FORM>


		</TD></TR></TABLE>


		<? if ($userdata["IsAdmin"]=="Y") { //Allow Admins to change owner?>

		<BR>
		<TABLE border=0 width=100% cellpadding=3 cellspacing=0>
		<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>
			Change Client
		<?=$cfont_title?></TD></TR><TR><TD bgcolor=<?=$color_table_dk_bg?> align=center valign=middle>
		<FORM METHOD=POST>
		This option is only available to administrators. Make sure the CampusID number of the client is correct, or the database WILL have problems.<BR><BR>
			<? echo "Client: <INPUT NAME=\x22IdNum\x22 VALUE=\x22".mysql_result($AboutTicket,0,"CID")."\x22 SIZE=16><INPUT TYPE=SUBMIT NAME=\x22Action\x22 VALUE=\x22Update Client\x22><INPUT TYPE=HIDDEN NAME=SID VALUE=$SID><INPUT TYPE=HIDDEN NAME=TID VALUE=$TID>\n"; ?>
	 	

		</TD></TR></TABLE></FORM>
	<? } ?>


	</TABLE><?



	}
	} else { //if the user isn't allowed to edit the ticket, show the client's other tickets


	?><BR><BR><BR><BR><?
	$color=MakeTable(mysql_query("
		SELECT
			CallTicket.TicketID,
			PaperTrail.Comment AS Description,
			PaperTrail.Creation AS Created
		FROM
			CallTicket,PaperTrail
		WHERE
			CallTicket.CampusID=$CID AND
			PaperTrail.TicketID=CallTicket.TicketID AND
			PaperTrail.IsFirst='Y' AND
			PaperTrail.TicketID!=$TID
		ORDER BY
			TicketID
	"),1,1,1,3,"Your Other Tickets");

	}


} else { //what do do if we aren't allowed to view the ticket

include "header.php";


if (""!=$Action || $SID>0) {MustLogIn();}  //maybe we failed because we were unable to login?
?>
<H1>Wrong CampusID</H1>
The Campus ID Specified (<? echo $CID; ?>) does not match the database entry for ticket <? echo $TID; ?>. If you this this message is in error, and that is your correct Campus ID and ticket number, please contact 408-551-1705 (x1705 on Campus) to verify your ticket number.
<? //or maybe the CampusID # was entered incorrectly

}

include "footer.php";
db_logout($hdb);
?>
