<?
include "config.php";
include "database.php";
include "functions.php";

include "login.php";

//Are we doing admin work, or just showing the schedule
if ('N'==$userdata["IsAdmin"] || 1==$printversion) {$noedit=1;} else {$noedit=0;}

$title = $Position." Schedule";

include "header.php";


MustLogIn();

include "schedule.php";

function HeaderRow() {
	?>
	<TR>
	<TD valign=bottom>User&nbsp;Name</TD>
	<TD colspan=2 valign=bottom align=center>&nbsp;7:00</TD>
	<TD colspan=2 valign=bottom align=center>&nbsp;8:00</TD>
	<TD colspan=2 valign=bottom align=center>&nbsp;9:00</TD>
	<TD colspan=2 valign=bottom align=center>10:00</TD>
	<TD colspan=2 valign=bottom align=center>11:00</TD>
	<TD colspan=2 valign=bottom align=center>12:00</TD>
	<TD colspan=2 valign=bottom align=center>&nbsp;1:00</TD>
	<TD colspan=2 valign=bottom align=center>&nbsp;2:00</TD>
	<TD colspan=2 valign=bottom align=center>&nbsp;3:00</TD>
	<TD colspan=2 valign=bottom align=center>&nbsp;4:00</TD>
	<TD colspan=2 valign=bottom align=center>&nbsp;5:00</TD>
	<TD colspan=2 valign=bottom align=center>&nbsp;6:00</TD>
	<TD colspan=2 valign=bottom align=center>&nbsp;7:00</TD>
	<TD colspan=2 valign=bottom align=center>&nbsp;8:00</TD>
	<TD colspan=2 valign=bottom align=center>&nbsp;9:00</TD>
	<TD colspan=2 valign=bottom align=center>10:00</TD>
	<TD colspan=2 valign=bottom align=center>11:00</TD>
	<TD colspan=2 valign=bottom align=center>&nbsp;12:00</TD>
	<TD colspan=2 valign=bottom align=center>&nbsp;1:00</TD>
	<? if (0==$GLOBALS ["noedit"]) { ?>
	<TD valign=bottom>Today</TD>
	<TD valign=bottom>Weekly</TD>
	<?
	 }
}



switch ($Position) {
	case "HELPDESK":
		$Pos="HelpDesk";
		break;
	case "RCC":
		$Pos="FieldSupport";
		break;
	case "KENNA":
		$Pos="TA";
		break;
	case "ORRADRE":
		$Pos="TA";
		break;
}

$oday=$day; //Backup the current Day
if (""==$day||$day<(-1)||$day>6)
	{$fd=date("w");$ld=date("w");}
else if ($day==(-1))
	{$fd=0;$ld=6;}
else {$fd=$day;$ld=$day;}

for ($day=$fd; $day<=$ld; $day++) {

	for ($z=0; $z<48; $z++) {
		$PeopleAvailable[$z]=0;
		$PeopleScheduled[$z]=0;
	}

	?><FORM><INPUT TYPE=HIDDEN NAME="day" VALUE=<? echo ($day); ?>><INPUT TYPE=HIDDEN NAME="SID" VALUE=<?echo $SID;?>>
<INPUT TYPE=HIDDEN NAME="Position" VALUE="<? echo $Position."\">";

	?><TABLE border=0 width=100%><TR><TD bgcolor=#EE4510><TT><B><? echo $Position; ?> Schedule for: <?
	switch ($day) {
		case 0:
			?>Sunday<?
			break;
		case 1:
			?>Monday<?
			break;
		case 2:
			?>Tuesday<?
			break;
		case 3:
			?>Wednesday<?
			break;
		case 4:
			?>Thursday<?
			break;
		case 5:
			?>Friday<?
			break;
		case 6:
			?>Saturday<?
			break;
	}
	?>
	</B></TT></TD></TR></TABLE>
	<TABLE border=1 width=100% cellspacing=0>
	<?


	$usernames=mysql_query("SELECT concat(p.Last, ',&nbsp;',p.First,'&nbsp;',p.Middle) AS Name, p.CampusID AS CampusID FROM People as p, Users as u WHERE u.".$Pos."='Y' AND p.CampusID=u.CampusID ORDER BY Name");


	//Loop though users
	$u=0;
	for ($us=0;$us<mysql_num_rows($usernames);$us++) {

	    $x=mysql_query("SELECT Time, Position FROM Schedule WHERE CampusID=".mysql_result($usernames,$us,"CampusID")." AND MOD(Time,7)=".$day." AND (".(($noedit==1)?" ":"Position='UNASSIGNED' OR ")."Position='".$Position."') ORDER BY Time");

//	    $x=mysql_query("SELECT Time, Position FROM Schedule WHERE CampusID=".mysql_result($usernames,$us,"CampusID")." AND MOD(Time,7)=".$day." (AND Position='UNASSIGNED' OR Position='".$Position."') ORDER BY Time");

	    if (mysql_num_rows($x)>0 || $noedit==0) {

		if ((double)($u/6)==(double)floor($u/6)) {HeaderRow();}
		$u++;


		?><TR><TD><A HREF="<?
		echo ($userdata["IsAdmin"]=="N")?"clientinfo.php?SID=$SID&CID=":"myinfo.php?SID=$SID&IdNum=";
		echo mysql_result($usernames,$us,"CampusID")."\">";
		echo mysql_result($usernames,$us,"Name");
		?></A></TD><?

		$timecode=($day+14*7);
		$xpos=0;


		while ($xpos < mysql_num_rows($x) && mysql_result($x,$xpos,0)<$timecode) {$xpos++;}
		if (mysql_num_rows($x)>0) {$xpos=$xpos % mysql_num_rows($x);}
		if (mysql_num_rows($x)>0 && mysql_result($x,$xpos,0)<$timecode) {$xpos=0;}
		for($h=0;$h<38;$h++) {
			$hour=($h+14) % 48;
			$timecode=($day+$hour*7);

			if ($noedit == 1) {

				if ((mysql_num_rows($x)>0) && mysql_result($x,$xpos,0)==$timecode) {
				if (mysql_result($x,$xpos,1)!="UNASSIGNED") {
					?><TD BGCOLOR=#000000>X<?
				} else {
					?><TD BGCOLOR=#E0E0E0>&nbsp;&nbsp;<?
				}
					$xpos=($xpos+1)%mysql_num_rows($x);//Increase pointer in SQL result
				} else {
					?><TD BGCOLOR=#E0E0E0>&nbsp;&nbsp;<?
				}

			} else {

				if ((double)($hour/2)==(double)floor($hour/2))
					{ ?><TD bgcolor=#2266AA valign=top><? }
				else
					{ ?><TD bgcolor=#CC6622 valign=top><? }

			}

			if ((mysql_num_rows($x)==0) || (mysql_result($x,$xpos,0)!=$timecode) || $noedit==1) {
				if ($noedit==0) {?>&nbsp;<? }
			} else {
				$PeopleAvailable[$h]++;
				//Is this time period assigned yet?
				if (mysql_result($x,$xpos,1)!="UNASSIGNED") {
					$ass=1;
				} else {
					$ass=0;
				}
				if ("Update"==$GLOBALS["UpdateDay".$day]) {
					if ($ass!=$GLOBALS["U".mysql_result($usernames,$us,"CampusID")."T".$timecode]) {
						if (1==$ass) {
							$ass=0;
							$y=mysql_query("UPDATE Schedule SET Position='UNASSIGNED' WHERE CampusID=".mysql_result($usernames,$us,"CampusID")." AND Time=".$timecode);
						} else {
							$ass=1;
							$y=mysql_query("UPDATE Schedule SET Position='".$Position."' WHERE CampusID=".mysql_result($usernames,$us,"CampusID")." AND Time=".$timecode);
						}
					}
				}

				?><INPUT TYPE=CHECKBOX NAME="<?
				echo "U".mysql_result($usernames,$us,"CampusID")."T".($day+$hour*7);
				?>" VALUE=1 <?
				if (1==$ass) { $PeopleScheduled[$h]++; echo "CHECKED"; }
				?>><?
				$xpos=($xpos+1)%mysql_num_rows($x);//Increase pointer in SQL result
			}
			?></TD><?
		}
		mysql_free_result($x);

		if ($noedit==0) {

		//Compute Hours worked today
		?><TD align=right><?
			$x=mysql_query("SELECT COUNT(*)/2 FROM Schedule WHERE CampusID=".mysql_result($usernames,$us,"CampusID")." AND MOD(Time,7)=".$day." AND Position!='UNASSIGNED' GROUP BY CampusID");
			if (mysql_num_rows($x)>0) {
				echo mysql_result($x,0,0);
			} else {
				echo "0.00";
			}
		?></TD><?
		


		//Compute Hors total for user
		?><TD align=right><?
			$x=mysql_query("SELECT COUNT(*)/2 FROM Schedule WHERE CampusID=".mysql_result($usernames,$us,"CampusID")." AND Position!='UNASSIGNED' GROUP BY CampusID");
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
		for ($h=0;$h<38;$h++) {
			?><TD BGCOLOR=#<?
			if (0==$PeopleAvailable[$h]) {
				?>FF0000<?
			} else {
				if (0==$PeopleScheduled[$h]) {
					?>FFFF00<?
				} else {
					?>00FF00<?
				}
			}
			?>>&nbsp;</TD><?
		}
	?></TR>

	
	<!--Update Buttons-->
	<TR><TD colspan=41>
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
	</TABLE></FORM><BR><BR>
<? } ?>
	<TABLE width=100% border=0><TR>

<? for ($j=0;$j<7;$j++) { ?>
	<TD width=12.5% align=center>
		<FORM>
		<INPUT TYPE=HIDDEN NAME="printversion" VALUE=<?echo $printversion;?>>
		<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?echo $SID;?>>
		<INPUT TYPE=HIDDEN NAME="notables" VALUE=<?echo $notables;?>>
		<INPUT TYPE=HIDDEN NAME="day" VALUE=<? echo $j; ?>>
		<INPUT TYPE=HIDDEN NAME="Position" VALUE="<? echo $Position; ?>">
		<INPUT TYPE=SUBMIT VALUE="<?
		switch ($j) {
			case 0:
				?>Sunday<?
				break;
			case 1:
				?>Monday<?
				break;
			case 2:
				?>Tuesday<?
				break;
			case 3:
				?>Wednesday<?
				break;
			case 4:
				?>Thursday<?
				break;
			case 5:
				?>Friday<?
				break;
			case 6:
				?>Saturday<?
				break;
		}
		?>"></FORM>
	</TD>
<? }  ?>

	<TD width=12.5% align=center>
		<FORM>
		<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?echo $SID;?>>
		<INPUT TYPE=HIDDEN NAME="printversion" VALUE=<?echo $printversion;?>>
		<INPUT TYPE=HIDDEN NAME="notables" VALUE="<?echo $notables;?>">
		<INPUT TYPE=HIDDEN NAME="day" VALUE="-1">
		<INPUT TYPE=HIDDEN NAME="Position" VALUE="<? echo $Position; ?>">
		<INPUT TYPE=SUBMIT VALUE="  All  "></FORM>
	</TD>
	</TR></TABLE><BR>

	<TABLE border=0 width=100%><TR>
<?
	if ('Y'==$userdata["IsAdmin"]) {
		?><TD width=50% align=center><?
		if (1==$noedit) {
			echo "<A HREF=\x22schedules.php?Position=$Position&day=$oday&SID=$SID&printversion=0&day=$day&notables=$notables\x22>Edit Schedule</A>";
		} else {
			echo "<A HREF=\x22schedules.php?Position=$Position&day=$oday&SID=$SID&printversion=1&day=$day&notables=$notables\x22>View Schedule Times Only</A>";
		}
		?></TD><TD width=50% align=center><?
	} else {
		?><TD swidth=100% align=center><?
	}
	if (1==$notables) {
		echo "<A HREF=\x22schedules.php?Position=$Position&day=$oday&SID=$SID&printversion=$printversion&notables=0\x22>View Schedule WITH Title and Left Menu Bar</A>";
	} else {
		echo "<A HREF=\x22schedules.php?Position=$Position&day=$oday&SID=$SID&printversion=$printversion&notables=1\x22>View Schedule WITHOUT Title or Left Menu Bar</A>";
	}
	?>
	</TD></TR></TABLE><?

include "footer.php";
db_logout($hdb);
?>
