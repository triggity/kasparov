<?
include "config.php";
include "database.php";
include "functions.php";

include "login.php";

//uncomment for testing only
//$day=-1;
//$Schedule=2;

MustLogIn();

include "schedule.php";

function GetDay($day) { //handy function to figure our the day of the week
	switch ($day) {
		case 0:
			return "Sunday";
		case 1:
			return "Monday";
		case 2:
			return "Tuesday";
		case 3:
			return "Wednesday";
		case 4:
			return "Thursday";
		case 5:
			return "Friday";
		case 6:
			return "Saturday";
		case 7:
			return "Holiday";
	}
}

$onthis=0; //So can we see this schedule or not?
for ($i=0;$i<mysql_num_rows($MySchedules);$i++) {
	if (mysql_result($MySchedules,$i,1)==$Schedule) {
		$onthis=1;
		if (mysql_result($MySchedules,$i,7)&4) $onthis++; //Are we an admin for this schedule? If so, $onthis=2
		$thisquantum=mysql_result($MySchedules,$i,2); // The smallest time inrecement for this schedule
		$thisstart=mysql_result($MySchedules,$i,5); //what time is the first item
		$thisend=mysql_result($MySchedules,$i,6); //what is the latest allowed time
		$thisname=mysql_result($MySchedules,$i,0);
		$thisholiday=(('Y'==mysql_result($MySchedules,$i,4))?1:0); //1 if we do holidays, 0 otherwise
	}
}


//Are we doing admin work, or just showing the schedule?

if ((2>$onthis && 'Y'!=$userdata["IsAdmin"]) || 1==$printversion) {$noedit=1;} else {$noedit=0;}

$title = $thisname." Schedule";

include "header.php"; //we had to wait till we had the title to start the header stuff


if (0==$onthis) { //What if the schedule doesn't exist, or they aren't allowed to see it?
?><H1>Unable to complete request for schedule</H1>
Perhaps you followed an out of date link.If you receive this error repeatedly and feel it is the result of a bug in this system, please contact an administrator.
<?
include "footer.php";
exit();
}


//set up the header row now, since we'll use it a bunch later
$HeaderRow="<TR>\n<TD valign=bottom>User&nbsp;Name</TD>\n";

	for ($i=((int)($thisstart/4)+5);$i<=((int)($thisend/4)+5);$i++) {
		$HeaderRow.='<TD colspan=4 valign=bottom align=center>';
		$t=$i%12; //show in 12 hour time, not 24hr
		if (0==$t) $t=12; //no weird looking "0:00" hours.
		if ($t<10) $HeaderRow.='&nbsp;'; //make the spacing nice
		$HeaderRow.=$t.":00</TD>\n";
	}

	if (0==$noedit) {  // only show for admins
		$HeaderRow.='<TD valign=bottom>Here</TD>';
		$HeaderRow.='<TD valign=bottom>Today</TD>';
		$HeaderRow.="<TD valign=bottom>Weekly</TD>\n";
	}

	$HeaderRow.="</TR>\n";




//figure out what we're doing in so far as what days to show
$oday=$day; //Backup the current Day
if (""==$day||$day<(-1)||$day>(6+$thisholiday)) //what if no date is specified or out of bounds?
	{$fd=date("w");$ld=date("w");} //then show info for today
else if ($day==(-1)) //what if the day is -1
	{$fd=0;$ld=(6+$thisholiday);} //then show the whole week
else {$fd=$day;$ld=$day;} //or otherwise just show the one day they requested



//set up some extra data for people editing the schedule
if (0==$noedit) {

	//Set up the color coding for the schedule
	$ltcolor[0]="E0E0E0";
	$ltcolor[(-1)]="E0E0E0";
	$dkcolor[0]="D0D0D0";
	$dkcolor[(-1)]="D0D0D0";
	$rmcolor="A0A0A0";
	for ($i=0;$i<mysql_num_rows($MySchedules);$i++) {
		$color=mysql_result($MySchedules,$i,4);
		if ($Schedule==mysql_result($MySchedules,$i,1)) $addcolor=$color; //color to use for newly added items
		$red=hexdec(substr($color,0,2));
		$green=hexdec(substr($color,2,2));
		$blue=hexdec(substr($color,4,2));
		$ltcolor[mysql_result($MySchedules,$i,1)]=dechex(ceil($red/2+127)).dechex(ceil($green/2+127)).dechex(ceil($blue/2+127));
		$dkcolor[mysql_result($MySchedules,$i,1)]=dechex(ceil($red/2+111)).dechex(ceil($green/2+111)).dechex(ceil($blue/2+111));
	}

}



for ($day=$fd; $day<=$ld; $day++) { //go through this loop for each day

	//clear the data
	for ($z=0; $z<(96/$thisquantum); $z++) {//clear the lists of people scheduled and people available
		$PeopleAvailable[$z]=0;
		$PeopleScheduled[$z]=0;
	}
	$SetDelta=""; //times added to schedule
	$ClearDelta=""; //times removed from the schedule
	$SetDeltaCount=0; //keeping track of how many...
	$ClearDeltaCount=0;
	$Updateing=($GLOBALS["UpdateDay".$day]=='Update')?1:0;
	$HoursHere=0;//hours worked by people at this location


	//print out the title for the schedule table
	?><FORM METHOD=POST><INPUT TYPE=HIDDEN NAME="day" VALUE=<? echo ($day); ?>><INPUT TYPE=HIDDEN NAME="SID" VALUE=<?echo $SID;?>>
<INPUT TYPE=HIDDEN NAME="Schedule" VALUE="<?=$Schedule?>">

	<TABLE border=0 width=100%><TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?><? echo $thisname; ?> Schedule for: <?=GetDay($day)?>
	<?=$cfont_title?></TD></TR><TR><TD>
	<TABLE border=1 width=100% cellspacing=0>
	<? 

	//Collect the list of all the users on this schedule
	$usernames=mysql_query("
		SELECT
			concat(p.Last, ',&nbsp;',p.First,'&nbsp;',p.Middle) AS Name,
			p.CampusID AS CampusID
		FROM
			People as p,
			Schedule_Permissions as s
		WHERE
			s.Schedule=$Schedule AND
			s.Flags & 1 AND
			p.CampusID=s.CampusID
		ORDER BY Name");


	$timedata=mysql_query("
		SELECT
			CampusID,
			Time,
			Schedule,
			Available
		FROM
			Schedule_Data
		WHERE
			Day=$day AND
			(Schedule=$Schedule
			".(($noedit==0)?"OR Available='Y'":"").")
		GROUP BY CampusID,Time
		"); //make sure this is in the same order as the list above
	//MakeTable($timedata,1,1,1,1,"x"); //debug only
	$timeptr=0;
	$timemax=mysql_num_rows($timedata);
	
	unset($TheSchedule);
	unset($AltSchedule);
	unset($Availability);
	for ($i=0;$i<mysql_num_rows($timedata);$i++) {
		//echo mysql_result($timedata,$i,0).":".mysql_result($timedata,$i,1)."<BR>";
		$cid=mysql_result($timedata,$i,0); //store the campus ID
		$AltSchedule[$cid][(int)(mysql_result($timedata,$i,1)/$thisquantum)]=mysql_result($timedata,$i,2);
		if (mysql_result($timedata,$i,2)==$Schedule) { //add only if they're working this schedule
			$TheSchedule[$cid][-1]++;
			$TheSchedule[$cid][floor(mysql_result($timedata,$i,1)/$thisquantum)]++;
			//if ($TheSchedule[$cid][floor(mysql_result($timedata,$i,1)/$thisquantum)]>2) echo "huh?floor(mysql_result($timedata,$i,1)/$thisquantum)-$i!!!<BR>";
/*
			//if we are editing the schedule, mark when people are working
			if ($noedit==0) {
				$PeopleScheduled[(int)(mysql_result($timedata,$i,1)/$thisquantum)]++;

			}
*/
		} else {
			if (mysql_result($timedata,$i,2)>0) { //the user is scheduled somewhere else
				$TheSchedule[$cid][-2]++;
			}

		}
		if (mysql_result($timedata,$i,3)=="Y") { //mark if we are available
			$Availability[$cid][(int)(mysql_result($timedata,$i,1)/$thisquantum)]++;
		}
	}
	

	//Loop though users to make the rows
	$u=0;
	for ($us=0;$us<mysql_num_rows($usernames);$us++) {
	$cid=mysql_result($usernames,$us,1); //sore the campus ID for this user
	    if ($TheSchedule[$cid][-1]>0 || $noedit==0) { //only if we have something to show...

		//Display a header every 6 rows
		if (($u % 6)==0) {echo $HeaderRow;}
		$u++;


		?><TR><TD><A HREF="<?
		echo ($userdata["IsAdmin"]=="N")?"clientinfo.php?SID=$SID&CID=":"myinfo.php?SID=$SID&IdNum=";
		echo mysql_result($usernames,$us,"CampusID")."\">";
		echo mysql_result($usernames,$us,"Name");
		?></A></TD><?

		$timesofar=0; //for how many hours they worked today
		$howfar=floor(((ceil($thisend/4))*4)/$thisquantum); // how far to go (make sure we are evenly on an hour's border)
		for ($i=(floor(((floor($thisstart/4))*4)/$thisquantum));$i<$howfar;$i++) {
			?><TD colspan=<?=$thisquantum?> BGCOLOR=<?
			if ($noedit==0) {
				if ($TheSchedule[$cid][$i]==$thisquantum && $Availability[$cid][$i]<$thisquantum) {
					$thiscolor="B00000";
				} else {
					if ($i%2) {
						$thiscolor=$ltcolor[(int)$AltSchedule[$cid][$i]];
					} else {
						$thiscolor=$dkcolor[(int)$AltSchedule[$cid][$i]];
					}
				}
				//echo "FFFFFF>".$TheSchedule[$cid][$i]."-".$Availability[$cid][$i]."-".$AltSchedule[$cid][$i];
				if ($TheSchedule[$cid][$i]==$thisquantum || ($Availability[$cid][$i]==$thisquantum && $AltSchedule[$cid][$i]<=0)) {

					$ischecked=(($TheSchedule[$cid][$i]==$thisquantum)?1:0); // is this box checked or not?
					//time for Magic
					if ($Updateing) {
						if (1==$ischecked && 1!=$GLOBALS['C'.$cid.'T'.$i]) {
							$ischecked=0; //time was removed
							for ($hr=0;$hr<$thisquantum;$hr++) {
								if (0<($ClearDeltaCount++)) $ClearDelta.=" OR ";
								$ClearDelta.= "Time=".($i*$thisquantum+$hr);
								$TheSchedule[$cid][-1]--;
							}
							$thiscolor=$rmcolor;//(($i%2)?$dkcolor[-1]:$dkcolor[-1]); //fix the color

						} else if (0==$ischecked && 1==$GLOBALS['C'.$cid.'T'.$i]) {
							$ischecked=1; //time was added
							for ($hr=0;$hr<$thisquantum;$hr++) {
								if (0<($SetDeltaCount++)) $SetDelta.=" OR ";
								$SetDelta.= "Time=".($i*$thisquantum+$hr);
								$TheSchedule[$cid][-1]++;
							}
							$thiscolor=$addcolor;//(($i%2)?$dkcolor[$Schedule]:$dkcolor[$Schedule]);//fix the color
						}
					}

					echo $thiscolor."><INPUT NAME=\x22C".$cid."T".$i."\x22 TYPE=CHECKBOX VALUE=1 ".(($ischecked==1)?"CHECKED":"").">";
					$PeopleAvailable[$i]++;
					$PeopleScheduled[$i]+=$ischecked; //if this box is checked, then someone is scheduled.
				} else { 
					echo $thiscolor.">&nbsp;&nbsp;&nbsp;";
				}
			} else {
				echo (($TheSchedule[$cid][$i]==$thisquantum)?"000000>X":"E0E0E0>&nbsp;&nbsp;");
			}
			?></TD><?

		}

		if ($noedit==0) {

		//Update the schedule
		if (0<$ClearDeltaCount) {
			mysql_query("UPDATE Schedule_Data SET Schedule=\x22-1\x22 WHERE Day=".$day." AND CampusID=$cid AND (".$ClearDelta.")");
			$ClearDeltaCount=0;
			$ClearDelta='';
		}
		if (0<$SetDeltaCount) {
			mysql_query("UPDATE Schedule_Data SET Schedule=".$Schedule." WHERE Day=".$day." AND CampusID=$cid AND (".$SetDelta.")");
			$SetDeltaCount=0;
			$SetDelta='';
		}


		//Compute Hours here worked today
		?><TD align=right><?
			printf("%.2f",($TheSchedule[$cid][-1])/4.0);
			$HoursHere+=($TheSchedule[$cid][-1])/4.0;
		?></TD><?
		


		//Compute Hours today for user
		?><TD align=right><?
			printf("%.2f",($TheSchedule[$cid][-1]+$TheSchedule[$cid][-2])/4.0);
		?></TD><?

		//Compute Hours total for user
		?><TD align=right><?
			$x=mysql_query("SELECT COUNT(*)/4 FROM Schedule_Data WHERE CampusID=$cid AND Schedule>0 GROUP BY CampusID");
			if (mysql_num_rows($x)>0) {
				echo mysql_result($x,0,0);
			} else {
				echo "0.00";
			}
		?></TD><?

		}
		?></TR><?
	    }
	}

	if ($noedit==0) {
	?>
	<!--Totals-->

	<TR><TD>Availability</TD><?
		$howfar=floor(((ceil($thisend/4))*4)/$thisquantum); // how far to go (make sure we are evenly on an hour's border)
		$boxes=0;//how many boxes are used for the schedule
		for ($i=(floor(((floor($thisstart/4))*4)/$thisquantum));$i<$howfar;$i++) {
			?><TD colspan=<?=$thisquantum?> BGCOLOR=#<?
			if (1<=$PeopleScheduled[$i]) {
				?>00FF00<?
			} else {
				if (1<=$PeopleAvailable[$i]) {
					?>FFFF00<?
				} else {
					?>FF0000<?
				}
			}
			$boxes++;
			?>>&nbsp;</TD><?
		}
		?><TD align=right><?
			printf("%.2f",$HoursHere);
		?></TD>
	</TR>

	
	<!--Update Buttons-->
	<TR><TD colspan=<?=(4+$thisquantum*$boxes)?>>
	<TABLE width=100% border=0><TR><TD width=50% align=left>
		<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?echo $SID;?>>
		<INPUT TYPE=SUBMIT NAME="UpdateDay<? echo ($day); ?>" VALUE="Update">
	</TD><TD width=50% align=right>
<INPUT TYPE=SUBMIT NAME="UpdateDay<? echo ($day); ?>" VALUE="Update">
	</TD></TR></TABLE>
	</TD></TR>

	<?
	}
 
	?>
	</TABLE></FORM></TD></TR></TABLE><BR><BR>
<? } ?>	
	<TABLE width=100% border=0><TR>

<? for ($j=0;$j<7;$j++) { ?>
	<TD width=12.5% align=center>
		<FORM>
		<INPUT TYPE=HIDDEN NAME="printversion" VALUE=<?echo $printversion;?>>
		<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?=$SID?>>
		<INPUT TYPE=HIDDEN NAME="notables" VALUE=<?=$notables?>>
		<INPUT TYPE=HIDDEN NAME="day" VALUE=<?=$j?>>
		<INPUT TYPE=HIDDEN NAME="Schedule" VALUE="<?=$Schedule?>">
		<INPUT TYPE=SUBMIT VALUE="<?=GetDay($j)?>"></FORM>
	</TD>
<? }  ?>

	<TD width=12.5% align=center>
		<FORM>
		<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?=$SID?>>
		<INPUT TYPE=HIDDEN NAME="printversion" VALUE=<?=$printversion?>>
		<INPUT TYPE=HIDDEN NAME="notables" VALUE="<?=$notables?>">
		<INPUT TYPE=HIDDEN NAME="day" VALUE="-1">
		<INPUT TYPE=HIDDEN NAME="Schedule" VALUE="<?=$Schedule?>">
		<INPUT TYPE=SUBMIT VALUE="  All  "></FORM>
	</TD>
	</TR></TABLE><BR>

	<TABLE border=0 width=100%><TR>
<?
	if ('Y'==$userdata["IsAdmin"] || 2<=$onthis) {
		?><TD width=50% align=center><?
		if (1==$noedit) {
			echo "<A HREF=\x22schedules.php?Schedule=$Schedule&day=$oday&SID=$SID&printversion=0&day=$oday&notables=$notables\x22>Edit Schedule</A>";
		} else {
			echo "<A HREF=\x22schedules.php?Schedule=$Schedule&day=$oday&SID=$SID&printversion=1&day=$oday&notables=$notables\x22>View Schedule Times Only</A>";
		}
		?></TD><TD width=50% align=center><?
	} else {
		?><TD swidth=100% align=center><?
	}
	if (1==$notables) {
		echo "<A HREF=\x22schedules.php?Schedule=$Schedule&day=$oday&SID=$SID&printversion=$printversion&notables=0\x22>View Schedule WITH Title and Left Menu Bar</A>";
	} else {
		echo "<A HREF=\x22schedules.php?Schedule=$Schedule&day=$oday&SID=$SID&printversion=$printversion&notables=1\x22>View Schedule WITHOUT Title or Left Menu Bar</A>";
	}
	?>
	</TD></TR></TABLE><?


include "footer.php";
db_logout($hdb);
?>
