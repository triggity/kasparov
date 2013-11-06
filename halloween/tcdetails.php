<?
include "config.php";
include "database.php";
include "functions.php";
include "login.php";


include "header.php";

MustLogIn();

//Make sure non admins are editing themselves
if ($userdata["IsAdmin"]=="N" || ""==$IdNum) { $IdNum=$CampusID; }

if ($magic==1) {
	$ThisCard=mysql_query("
		SELECT
			'$RID' AS Run,
			'$Beg' AS Begin,
			'$End' AS End,
			'$RBeg' AS StartHidden,
			'$REnd' AS StopHidden,
			-1 AS Reg,
			-1 AS PendReg,
			-1 AS OT,
			-1 AS PendOT,
			-4 AS Total,
			'Ethereal' AS Status,
			'$TC' AS TimeCard
		");
} else {
	$ThisCard=mysql_query("
		SELECT
			RunID AS Run,
			Stat1.Time AS Begin,
			Stat2.Time AS End,
			TimeCards_Data.Start AS StartHidden,
			TimeCards_Data.Stop AS StopHidden,
			Reg,
			PendReg,
			OT,
			PendOT,
			(Reg+PendReg+OT+PendOT) AS Total,
			'Valid' AS Status,
			TimeCard
		FROM	
			TimeCards_Data,
			Locations_Data AS Stat1,
			Locations_Data AS Stat2,
			TimeCards_Periods
		WHERE
			TimeCards_Data.CardID=$CardID AND
			TimeCards_Data.CampusID=$IdNum AND
			Stat1.RecordID=TimeCards_Data.Start AND
			Stat2.RecordID=TimeCards_Data.Stop AND
			TimeCards_Periods.PeriodID=TimeCards_Data.Period
		ORDER BY StartHidden DESC
		");
}
if (mysql_num_rows($ThisCard)!=1) { //problem!

	echo "<H2>Unable to load time card: $CardID</H2>";

} else { // everything is fine

	?>
	<H2>Time Card <?=$CardID?></H2>
	<BR>
	<TABLE border=0 BGCOLOR=<?=$color_table_lt_bg?>><TR><TD>
	<Table border=0 cellspacing=3>
	<TR><TD colspan=2 align=center><B>Details</B></TD><TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD colspan=4 align=center><B>Hours</B></TD></TR>
	<TR><TD><B>Begin:</B></TD><TD><?=mysql_result($ThisCard,0,'Begin')?></TD><TD colspan=2></TD><TD><B>&nbsp;&nbsp;&nbsp;&nbsp;Calcuated</B></TD><TD><B>&nbsp;&nbsp;&nbsp;&nbsp;Appended</B></TD><TD><B>&nbsp;&nbsp;&nbsp;&nbsp;Total</B></TD></TR>
	<TR><TD><B>End:</B></TD><TD><?=mysql_result($ThisCard,0,'End')?></TD><TD></TD><TD><B>Regular</B></TD><TD align=right><?=mysql_result($ThisCard,0,'Reg')+0.0?></TD><TD align=right><?=mysql_result($ThisCard,0,'PendReg')+0.0?></TD><TD align=right><?=mysql_result($ThisCard,0,'Reg')+mysql_result($ThisCard,0,'PendReg')?></TD></TR>
	<TR><TD><B>Status:</B></TD><TD><?=mysql_result($ThisCard,0,'Status')?></TD><TD></TD><TD><B>Overtime</B></TD><TD align=right><?=mysql_result($ThisCard,0,'OT')+0.0?></TD><TD align=right><?=mysql_result($ThisCard,0,'PendOT')+0.0?></TD><TD align=right><?=mysql_result($ThisCard,0,'OT')+mysql_result($ThisCard,0,'PendOT')?></TD></TR>
	<TR><TD><B>Run ID:</B></TD><TD><?=mysql_result($ThisCard,0,'Run')?></TD><TD></TD><TD><B>Total</B></TD><TD align=right><?=mysql_result($ThisCard,0,'OT')+mysql_result($ThisCard,0,'Reg')?></TD><TD align=right><?=mysql_result($ThisCard,0,'PendOT')+mysql_result($ThisCard,0,'PendReg')?></TD><TD align=right><?=mysql_result($ThisCard,0,'Total')+0.0?></TD></TR>
	</TABLE>
	</TD></TR></TABLE>
	<BR><BR>
	<?

	//All the times we submitted data
	$PinPointsQ="
		SELECT
			RecordID,
			DATE_FORMAT(Time,'%r'),
			DATE_FORMAT(Time,'%W, %e %b %Y'),
			IP,
			LocationID,
			ScheduleID,
			Locations.Name,
			TimeQuantum,
			MOD(HOUR(Time)+19,24)*4+FLOOR(MINUTE(Time)/15) AS ShTime,
			MOD(WEEKDAY(DATE_SUB(Time,INTERVAL '5' HOUR))+1,7) AS ShDay,
			Time
			
		FROM
			Locations_Data,
			TimeCards_Locations,
			Locations
		WHERE
			Locations_Data.CampusID=$IdNum AND
			Locations_Data.RecordID>=".mysql_result($ThisCard,0,'StartHidden')." AND
			Locations_Data.RecordID<=".mysql_result($ThisCard,0,'StopHidden')." AND
			TimeCards_Locations.TimeCard=".mysql_result($ThisCard,0,'TimeCard')." AND
			Locations_Data.LocationID=TimeCards_Locations.Location AND
			Locations.ID=Locations_Data.LocationID
		ORDER BY Time
		";
	//echo $PinPointsQ."<BR>";
	$PinPoints=mysql_query($PinPointsQ);
	//MakeTable($PinPoints,1,1,1,1,"");

	//set up schedule info
	//$OnSchedule="1=0";
	for ($i=0;$i<mysql_num_rows($MySchedules);$i++) {
		$ScheduleInfo[mysql_result($MySchedules,$i,1)+0]=mysql_result($MySchedules,$i,0);
		//if (mysql_result($MySchedules,$i,7)&1)	$OnSchedule.=' OR ScheduleID='.mysql_result($MySchedules,$i,1);
	}
	//echo $OnSchedule;
	$ScheduleInfo[-1]="Unknown";
	$ScheduleInfo[0]="None";


	?><TABLE border=0 cellpadding=2><?
	$TotalTime=0;
	for ($i=0;$i<mysql_num_rows($PinPoints);$i++) {

		//begin a new day if need be
		if (mysql_result($PinPoints,$i,2)!=$LastDay) {
			$LastDay=mysql_result($PinPoints,$i,2);
			?>
			<TR><TD>&nbsp;</TD></TR>
			<TR><TD colspan=6 BGCOLOR=<?=$color_table_title.">".$ofont_title?><?=$LastDay.$cfont_title?></TD></TR>
			<TR><TD>Record</TD><TD>Time</TD><TD>Location</TD><TD>Schedule</TD><TD>IP Address</TD><TD>Total Hrs</TD></TR>
			<?
		}

		//figure out colors
		$rowcolor=(($rowcolor==$color_table_dk_bg)?$color_table_lt_bg:$color_table_dk_bg);
		$nonecolor=(($nonecolor=='EEEE88')?'FFFFAA':'EEEE88');
		$suspectcolor=(($suspectcolor=='E07070')?'FFA0A0':'E07070');
		$thiscolor=$rowcolor;
		if (mysql_result($PinPoints,$i,5)==0) { //Not working on scheduled time!!
			$thiscolor=$suspectcolor;
			//Who was supposed to be working then
			$people=mysql_query(" 
				SELECT CampusID,Schedule
				FROM Schedule_Data,Locations_Schedules
				WHERE
					Locations_Schedules.LocationID=".mysql_result($PinPoints,$i,4)." AND
					Time=".mysql_result($PinPoints,$i,'ShTime')." AND
					Day=".mysql_result($PinPoints,$i,'ShDay')." AND
					Schedule=Locations_Schedules.ScheduleID
				");
			//complicated version

			$options=mysql_query("
				SELECT CampusID,
					FLOOR(UNIX_TIMESTAMP(Time)/TimeQuantum*900),
					FLOOR(UNIX_TIMESTAMP('".mysql_result($PinPoints,$i,'Time')."')/TimeQuantum*900)
				FROM
					Locations_Data,
					Locations,
					Locations_Schedules as LocS1, 
					Locations_Schedules as LocS2
				WHERE
					LocS1.LocationID=".mysql_result($PinPoints,$i,4)." AND
					LocS2.ScheduleID=LocS1.ScheduleID AND
					Locations_Data.LocationID=LocS2.LocationID AND
					Locations.ID=Locations_Data.LocationID AND
 					FLOOR(UNIX_TIMESTAMP(Time)/(TimeQuantum*900))=FLOOR(UNIX_TIMESTAMP('".mysql_result($PinPoints,$i,'Time')."')/(TimeQuantum*900))

				");

/*
			$options=mysql_query("
				SELECT CampusID,
					FLOOR(UNIX_TIMESTAMP(Time)/TimeQuantum*900),
					FLOOR(UNIX_TIMESTAMP('".mysql_result($PinPoints,$i,'Time')."')/TimeQuantum*900)
				FROM
					Locations_Data,
					Locations
				WHERE
					Locations.ID=Locations_Data.LocationID AND
 					FLOOR(UNIX_TIMESTAMP(Time)/(TimeQuantum*900))=FLOOR(UNIX_TIMESTAMP('".mysql_result($PinPoints,$i,'Time')."')/(TimeQuantum*900))

				");
*/

			//look through our options
			$Alts=""; //alternate options
			
			for ($j=0;$j<mysql_num_rows($people);$j++) {
				if (mysql_result($people,$j,0)==$IdNum) {//oops, it didn't get registered right
					$thiscolor=$rowcolor;
					break; //ensure loop exit
				} else {
					for ($k=0;$k<mysql_num_rows($options);$k++) {
						if (mysql_result($people,$j,0)==mysql_result($options,$k,0)) { //not this person
							break;
						}
					}
					if ($k==mysql_num_rows($options)) {//no one was found (we went thhrough the whole for loop)
						if (strlen($Alts)>0) $Alts.=", or ";
						$Alts.=mysql_result(mysql_query("SELECT concat(First,' ',Last) FROM People WHERE CampusID=".mysql_result($people,$j,0)),0,0).' at '.$ScheduleInfo[mysql_result($people,$j,1)];
						$thiscolor=$nonecolor;
					}
							
				}
			}
		}

		//spit out the data
		?><TR>
		<TD bgcolor=<?=$thiscolor?>><?=mysql_result($PinPoints,$i,0)?></TD>
		<TD bgcolor=<?=$thiscolor?>><?=mysql_result($PinPoints,$i,1)?></TD>
		<TD bgcolor=<?=$thiscolor?>><?=mysql_result($PinPoints,$i,6)?></TD>
		<TD bgcolor=<?=$thiscolor?>><?=(($nonecolor==$thiscolor)?$Alts:$ScheduleInfo[mysql_result($PinPoints,$i,5)+0])?></TD>
		<TD bgcolor=<?=$thiscolor?>><?=inttoip(mysql_result($PinPoints,$i,3))?></TD>
		<TD align=right bgcolor=<?=$thiscolor?>><?printf("%.2f",($Hours+=mysql_result($PinPoints,$i,7)*0.25))?></TD>

		</TR><?
	}
	?></TABLE><?
}


include "footer.php";
db_logout($hdb);
?>
