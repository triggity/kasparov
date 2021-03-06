<?php

include "config.php";
include "database.php";
include "functions.php";
include "login.php";
$title="Lab Counts";
if (!defined("Header_Included")) $IamOnDuty=0; //set the inital value only if the header.php has not yet been included
include "header.php";
//echo $IamOnDuty;
MustLogIn();

$extra="";
if ($Action=="Go" || $Action=="Submit") {$extra="AND Locations.ID=\x22$Loc\x22";}

$x=mysql_query($q = "
	SELECT
		Locations.Name,
		Locations.ID,
		min(abs(".iptoint($REMOTE_ADDR)."-(Locations_IPs.IP))) AS
Distance,
		TimeQuantum,
		Offset
	FROM
		Locations,
		Locations_IPs,
		Locations_Schedules,
		Schedule_Permissions
	WHERE
		Schedule_Permissions.CampusID=$CampusID AND
		Schedule_Permissions.Flags & 5 AND
		Locations.ID=Locations_IPs.LocationID AND
		Locations_IPs.IP = cast((".iptoint($REMOTE_ADDR)." & Locations_IPs.IPMask) as signed) AND
		Locations_Schedules.LocationID=Locations.ID AND
		Locations_Schedules.ScheduleID=Schedule_Permissions.Schedule
		".$extra."
	GROUP BY ID
	ORDER BY Distance,ID");
//echo $q;
if (mysql_num_rows($x)==0) {
	print $REMOTE_ADDR;
	?><H1>Resource not available from current location.</H1><?
} else if (mysql_num_rows($x)==1) {

			?><H1><?=mysql_result($x,0,0)?></H1><?
	$Loc=mysql_result($x,0,1);
	$TimeQuantum=mysql_result($x,0,3);
	$SubmissionTime=time(); //Capture time NOW
	$LocationTime=((int)($SubmissionTime/(900*$TimeQuantum)))*900*$TimeQuantum+(450*$TimeQuantum); //Which submission this counts for
	/*	echo "SU:::$SubmissionTime<br />";
	echo "ST:::".mysql_result(mysql_query("select FROM_UNIXTIME($SubmissionTime)"),0,0)."<br />";
	echo "MU:::".mysql_result(mysql_query("select NOW()"),0,0)."<br />";
	echo "MT:::".mysql_result(mysql_query("select FROM_UNIXTIME(UNIX_TIMESTAMP(NOW())+0)"),0,0)."<br />";
	echo "PMT:::".date("g:i:sa j F Y",mysql_result(mysql_query("select UNIX_TIMESTAMP(NOW())"),0,0));*/

	if ("Submit"==$Action) {
		if (abs($SubmissionTime-$LocationTime)>($TimeQuantum*450-240)) { //Check if attempted submission time is within the window
			?><H2>Current Time (<?=date("g:ia",$SubmissionTime)?>)
			is not within the +/- <?=(int)($TimeQuantum*7.5-5)?> minute
			window for <?=date("g:ia",$LocationTime)?>. No data recorded.</H2><?

	if ($popper==2) {
		?><INPUT TYPE=SUBMIT VALUE="Wait for next count" onClick='window.location.href="popper.php?notables=1&SID=<?=$SID?>'><?
	} else {
		?><INPUT TYPE=SUBMIT VALUE="<<BACK" onClick="self.back();"><?
	}

		} else {
			$y=mysql_query("SELECT UNIX_TIMESTAMP(Time),IP,RecordID FROM Locations_Data WHERE CampusID=$CampusID AND FLOOR(UNIX_TIMESTAMP(Time)/".($TimeQuantum*900).") = ".((int)($SubmissionTime/(900*$TimeQuantum)))); //Look if we already submitted this data
			if (mysql_num_rows($y)>0) { //Don't let us proceed if we already entered the lab count
				?><H2>You already submitted data for this time block!</H2><?
				?>Time: <?=date("g:i:sa j F Y",mysql_result($y,0,0))?><BR>
				From: <?=inttoip(mysql_result($y,0,1))?><BR>
				RecordID: <?=mysql_result($y,0,2)?><BR><BR>
				If you are sure you did not already submit data for this time block, please take note of the information above, and contact one of this system's administrators.
				<?
			} else {

				$myfields=mysql_query("SELECT * FROM Locations_Stats WHERE Location=$Loc");
				$allok=1; //stuff is fine for now				

				for ($i=0;$i<mysql_num_rows($myfields);$i++) {
					$GLOBALS["D".mysql_result($myfields,$i,"ID")]*=mysql_result($myfields,$i,"Scale");
					$GLOBALS["D".mysql_result($myfields,$i,"ID")]=(int)($GLOBALS["D".mysql_result($myfields,$i,"ID")]);
					if ($GLOBALS["D".mysql_result($myfields,$i,"ID")] < mysql_result($myfields,$i,"Minimum")  || $GLOBALS["D".mysql_result($myfields,$i,"ID")] > mysql_result($myfields,$i,"Maximum")) {
						echo "<B>Value for ".mysql_result($myfields,$i,"Name")." is out of range!</B><BR>\n";
						$allok=0;
					}
				}

				if (0==$allok) { //something bad happened
					?><H2>Invalid data submitted. Please go back and check values</H2><INPUT TYPE=SUBMIT VALUE="<<BACK" onClick="self.back();"><?
				} else {
					$y=mysql_query("INSERT INTO Locations_Data(CampusID,LocationID,Time,IP,ScheduleID) VALUES($CampusID,$Loc,FROM_UNIXTIME($SubmissionTime),".iptoint($REMOTE_ADDR).",$IamOnDuty)");
					$RecordID=mysql_insert_id();
					?><H3>Data Submitted into Record #<?=$RecordID?> for, <?=date("g:ia",$LocationTime)?> </H3><TABLE BORDER=0><?
					for ($i=0;$i<mysql_num_rows($myfields);$i++) {
						echo "<TR><TD>".mysql_result($myfields,$i,"Name").":</TD><TD>".$GLOBALS["D".mysql_result($myfields,$i,"ID")]."</TD></TR>";
						$y=mysql_query("INSERT INTO Locations_Stats_Data(DataRef,StatID,Value) VALUES ($RecordID,".mysql_result($myfields,$i,"ID").",".$GLOBALS["D".mysql_result($myfields,$i,"ID")].")");
					}
					?></TABLE><?

					if ($popper==2) {
					?><BR>Page will reload and resume popup operation in 30 seconds. <B>DO NOT</B> close this window if you wish automatic lab count pop-ups to continue.
					<meta HTTP-EQUIV=Refresh CONTENT='30, "popper.php?notables=1&SID=<?=$SID?>'><?
					}
				}
			}
		}
		
	} else {
		?><FORM METHOD=POST>
		<INPUT NAME="SID" VALUE="<?=$SID?>" TYPE=HIDDEN>
		<INPUT NAME="popper" VALUE="<?=$popper?>" TYPE=HIDDEN>
		<INPUT NAME="Loc" VALUE="<?=$Loc?>" TYPE=HIDDEN>
		<?
		?>Data submission at this location is every <?=mysql_result($x,0,3)*15?> (<SUP>+</SUP>/<font size=-1>-</font> <?=mysql_result($x,0,3)*5?>) minutes. Based on system time when this page was loaded (<?=date("g:ia")?>), this submission will count for <?=date("g:ia j F Y",$LocationTime)?>.
		<BR><BR><?

		$DataCollection=mysql_query("SELECT Name AS Field, ID AS IDHidden, concat('<INPUT TYPE=TEXT NAME=D',ID,' SIZE=5 MAXLENGTH=7>') AS Value FROM Locations_Stats WHERE Location=$Loc ORDER BY ID");
		if (mysql_num_rows($DataCollection)>0) {
			MakeTable($DataCollection,1,1,1,0,"Statistics");
		}

		?><BR><BR><INPUT NAME="Action" VALUE="Submit" TYPE=SUBMIT><?

	}

} else if (mysql_num_rows($x)>1) {
	?><H2>Select your Location:</H2>
	<FORM METHOD=POST>
	<INPUT NAME="SID" VALUE="<?=$SID?>" TYPE=HIDDEN>
	<INPUT NAME="popper" VALUE="<?=$popper?>" TYPE=HIDDEN>
	<SELECT NAME="Loc">
	<?
	for ($i=0;$i<mysql_num_rows($x);$i++) {
		?><OPTION VALUE=<?=mysql_result($x,$i,1)?>><?=mysql_result($x,$i,0)?></OPTION><?
	}
	?>
	</SELECT>
	<INPUT NAME="Action" VALUE="Go" TYPE=SUBMIT>
	</FORM>
	<?
}

include "footer.php";
db_logout($hdb);

?>
