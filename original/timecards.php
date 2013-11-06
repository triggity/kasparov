<?
include "config.php";
include "database.php";
include "functions.php";
include "login.php";


include "header.php";

if ($Automatic!=1) MustLogIn();
//Make sure non admins are editing themselves
if ($userdata["IsAdmin"]=="Y" && ""!=$IdNum) { $CampusID=$IdNum; }

//What time cards am I on?
$tcs=mysql_query("SELECT
		Name,
		File AS FHidden,
		ExtraInfo AS EHidden,
		PendingRegAdj AS RHidden,
		PendingOTAdj AS OHidden,
		Admin AS AHidden,
		ID AS IHidden,
		concat('<A HREF=\x22timecards.php?SID=$SID&IdNum=$CampusID&TC=',TimeCards_Info.ID,'\x22>Go</A>') AS What
	FROM
		TimeCards_Members,
		TimeCards_Info
	WHERE
		TimeCards_Members.CampusID=$CampusID
		AND TimeCards_Info.ID=TimeCards_Members.TimeCard
		".((strlen($TC)>0)?(" AND TimeCards_Info.ID=$TC"):(""))
	);

if (mysql_num_rows($tcs)==0) {//no time cards...
	?><H2>No TimeCard(s) Found.</H2><?
} else if (mysql_num_rows($tcs)>1) { //multiple time cards
	MakeTable($tcs,1,1,1,0,"Your Time Cards");
} else { //we're on one one time card

	//pending overtime and regular hours
	$PendingReg=mysql_result($tcs,0,'RHidden');
	$PendingOT=mysql_result($tcs,0,'OHidden');

	$TC=mysql_result($tcs,0,"IHidden"); //find the number of the time card we're working on.



    do {
	//Get the list of time cards
	$MyTCs=mysql_query("
		SELECT
			CardID AS ID,
			RunID AS Run,
			Stat1.Time AS Begin,
			Stat2.Time AS End,
			TimeCards_Data.Start AS StartHidden,
			TimeCards_Data.Stop AS StopHidden,
			(Reg+PendReg) AS Regular,
			(OT+PendOT) AS Overtime,
			(Reg+PendReg+OT+PendOT) AS Total,
			Status AS StatusHidden,
			concat('<A HREF=\x22tcdetails.php?SID=$SID&IdNum=$CampusID&CardID=',CardID,'\x22>Details</A> | <A HREF=\x22".mysql_result($tcs,0,'FHidden')."?SID=$SID&IdNum=$CampusID&CardID=',CardID,'\x22>PDF</A>') AS Options
		FROM	
			TimeCards_Data,
			Locations_Data AS Stat1,
			Locations_Data AS Stat2,
			TimeCards_Periods
		WHERE
			TimeCards_Data.CampusID=$CampusID AND
			Stat1.RecordID=TimeCards_Data.Start AND
			Stat2.RecordID=TimeCards_Data.Stop AND
			TimeCards_Periods.TimeCard=$TC AND
			TimeCards_Periods.PeriodID=TimeCards_Data.Period
		ORDER BY StopHidden DESC
		");



	$First=0; //first location data entry...

	if (mysql_num_rows($MyTCs)>0) {
		$RecentPeriod=mysql_result($MyTCs,0,'Begin').' to '.mysql_result($MyTCs,0,'End').' ('.mysql_result($MyTCs,0,'Run').')';

		//Add a time card
		if ("New TC"==$Action) {
			$FirstData=mysql_query("SELECT MIN(RecordID), MIN(Time), YEAR(MIN(Time))*54+WEEK(MIN(Time)) FROM Locations_Data, TimeCards_Locations WHERE Locations_Data.CampusID=$CampusID AND Locations_Data.LocationID=TimeCards_Locations.Location AND TimeCards_Locations.TimeCard=$TC AND Locations_Data.RecordID>".mysql_result($MyTCs,0,'StopHidden'));
			//echo "SELECT MIN(RecordID), MIN(Time), YEAR(MIN(Time))*54+WEEK(MIN(Time)) FROM Locations_Data, TimeCards_Locations WHERE Locations_Data.CampusID=$CampusID AND Locations_Data.LocationID=TimeCards_Locations.Location AND TimeCards_Locations.TimeCard=$TC AND Locations_Data.RecordID>".mysql_result($MyTCs,0,'StopHidden');
		} else if ("Update TC"==$Action) { //updating an existing time card
			//$FirstData=mysql_query("SELECT RecordID, Time, YEAR(Time)*54+WEEK(Time) FROM Locations_Data WHERE Locations_Data.RecordID=".mysql_result($MyTCs,0,'StartHidden'));

		}
	} else {
		$RecentPeriod="<font color=red>Never</font>";

		//Add the first time card...
		if ("New TC"==$Action) {
			$FirstData=mysql_query("SELECT MIN(RecordID), MIN(Time), YEAR(MIN(Time))*54+WEEK(MIN(Time)) FROM Locations_Data, TimeCards_Locations WHERE Locations_Data.CampusID=$CampusID AND Locations_Data.LocationID=TimeCards_Locations.Location AND TimeCards_Locations.TimeCard=$TC");
		}
		
	}

	if ("Loop"==$Action) $Action=""; //get out of the loop if need be...

	//Add time card if applicable
	if ((
	       'New TC'==$Action ||
	       (
	         'Update TC'==$Action &&
	         mysql_result($MyTCs,0,'StatusHidden')=='Pending'
	       ) &&
	       $NewDivider>mysql_result($MyTCs,0,'StopHidden')
	    ) &&
	    mysql_num_rows($FirstData)>0 &&
	    mysql_result($FirstData,0,0)>0) {

		if ('New TC'==$Action) { //last entry for a new time card
			$EndData=mysql_query("
				SELECT
					MAX(RecordID),
					MAX(Time),
					YEAR(MAX(Time))*54+WEEK(MAX(Time))
				FROM
					TimeCards_Periods,
					Locations_Data,
					TimeCards_Locations
				WHERE
					Locations_Data.CampusID=$CampusID AND
					TimeCards_Locations.TimeCard=$TC AND
					Locations_Data.LocationID=TimeCards_Locations.Location AND
					TimeCards_Periods.PeriodID=$PayP AND
					Locations_Data.Time<=TimeCards_Periods.End
				");
		} else {
			$EndData=mysql_query("
				SELECT
					MAX(RecordID),
					MAX(Time),
					YEAR(MAX(Time))*54+WEEK(MAX(Time))
				FROM
					Locations_Data
				WHERE
					CampusID=$CampusID AND
					(RecordID=$NewDivider OR RecordID=".mysql_result($MyTCs,0,'StopHidden').")
				");
		}
		//MakeTable($FirstData,1,1,1,1,"F");
		//MakeTable($EndData,1,1,1,1,"E");
		$OTWeeks=mysql_query("
			SELECT
				SUM(Locations.TimeQuantum*0.25),
				YEAR(Time)*54+WEEK(Time) AS WeekHash
			FROM
				Locations_Data,
				TimeCards_Locations,
				Locations
			WHERE
				Locations_Data.CampusID=$CampusID AND
				TimeCards_Locations.TimeCard=$TC AND
				Locations_Data.LocationID=TimeCards_Locations.Location AND
				Locations.ID=Locations_Data.LocationID AND
				YEAR(Time)*54+WEEK(Time) >= ".mysql_result($FirstData,0,2)." AND
				YEAR(Time)*54+WEEK(Time) <= ".mysql_result($EndData,0,2)."
			GROUP BY WeekHash
			ORDER BY WeekHash
			");
		$RegWeeks=mysql_query("
			SELECT
				SUM(Locations.TimeQuantum*0.25),
				YEAR(Time)*54+WEEK(Time) AS WeekHash
			FROM
				Locations_Data,
				TimeCards_Locations,
				Locations
			WHERE
				Locations_Data.CampusID=$CampusID AND
				TimeCards_Locations.TimeCard=$TC AND
				Locations_Data.LocationID=TimeCards_Locations.Location AND
				Locations.ID=Locations_Data.LocationID AND
				Time >= \x22".mysql_result($FirstData,0,1)."\x22 AND
				Time <= \x22".mysql_result($EndData,0,1)."\x22
			GROUP BY WeekHash
			ORDER BY WeekHash
			");

		$RegHours=0.0; //Start with no hours..
		$OTHours=0.0; //ditto

		for ($i=0;$i<mysql_num_rows($RegWeeks);$i++) {
			if (mysql_result($OTWeeks,$i,0)>40.0) { //overtime this week
				$OTTemp=mysql_result($OTWeeks,$i,0)-40; //OT Hours...
				if ($OTTemp>mysql_result($RegWeeks,$i,0)) {//OT carry over from last pay period
					$OTHours+=mysql_result($RegWeeks,$i,0);
				} else {
					$OTHours+=$OTTemp;
					$RegHours+=(mysql_result($RegWeeks,$i,0)-$OTTemp);
				}
			} else { //no over time
				$RegHours+=mysql_result($RegWeeks,$i,0);
			}
		}

		//echo "Reg: $RegHours  OT: $OTHours";

		if ("New TC"==$Action) {
			$x=mysql_query("INSERT INTO TimeCards_Data (CampusID,Start,Stop,Reg,OT,Period,Status,PendReg,PendOT) VALUES ($CampusID,".mysql_result($FirstData,0,0).",".mysql_result($EndData,0,0).",$RegHours,$OTHours,$PayP,'Pending',".mysql_result($tcs,0,'RHidden').",".mysql_result($tcs,0,'OHidden').")");
			//echo "INSERT INTO TimeCards_Data (CampusID,Start,Stop,Reg,OT,Period,Status,PendReg,PendOT) VALUES ($CampusID,".mysql_result($FirstData,0,0).",".mysql_result($EndData,0,0).",$RegHours,$OTHours,$PayP,'Pending',".mysql_result($tcs,0,'RHidden').",".mysql_result($tcs,0,'OHidden').")";

		} else { //Updating current time card
			$x=mysql_query("
				UPDATE
					TimeCards_Data
				SET
					Reg=$RegHours,
					OT=$OTHours,
					Stop=".mysql_result($EndData,0,0).",
					PendReg=PendReg + $PendingReg ,
					PendOT=PendOT + $PendingOT
				WHERE
					CardID=".mysql_result($MyTCs,0,'ID')."
				");
		}
		if (mysql_errno()==0) { //query was successful
			//reset pending values
			$PendingReg=0.0;
			$PendingOT=0.0;
			$x=mysql_query("UPDATE TimeCards_Members SET PendingRegAdj=0.0, PendingOTAdj=0.0 WHERE CampusID=$CampusID AND TimeCard=$TC");
		}


		//Debug to find if anyone has over time
		//MakeTable(mysql_query("SELECT COUNT(*) AS Tot, YEAR(Time)*54+WEEK(Time) AS WeekHash, CampusID, WEEK(Time)*CampusID AS X FROM Locations_Data GROUP BY X ORDER BY Tot"),1,1,1,1,"OT");

		$Action="Loop"; //we have to loop to get the relevant information again after we've added the time card.

	}

     } while("Loop"==$Action);

if ($Automatic!=1) {

	$NextTC=mysql_query("
		SELECT
			concat(Due,' (',RunID,')'),
			Due,
			RunID
		FROM
			TimeCards_Periods
		WHERE
			TimeCard=$TC AND
			SUBDATE(Start,INTERVAL 10 DAY)<NOW() AND
			SUBDATE(Due,INTERVAL 1 DAY)>NOW()
		ORDER BY
			Due

	");

	if (mysql_num_rows($NextTC)==0) {
		?><H1>No more pay periods - go bug <A HREF="mailto:<?=$admin_email?>"><?=$admin_name?></A></H1><?
	} else {
		$NextPeriod=mysql_result($NextTC,0,0);
	}

	$MoreHours=mysql_query("
		SELECT
			SUM(TimeQuantum)/4,
			MIN(Time),
			MAX(Time),
			MIN(RecordID),
			MAX(RecordID)
		FROM
			Locations_Data,
			Locations,
			TimeCards_Locations
		WHERE
			Locations.ID=Locations_Data.LocationID AND
			Locations_Data.CampusID=$CampusID AND
			Locations_Data.LocationID=TimeCards_Locations.Location AND
			TimeCards_Locations.TimeCard=$TC ".((mysql_num_rows($MyTCs)==0)?"":("AND Time>\x22".mysql_result($MyTCs,0,'End')."\x22"))."
		");


	?><H2><?=mysql_result($tcs,0,0)?> Time Cards</H2>
	<TABLE BORDER=0>
	<TR><TD><B>Last Time Card:</B></TD><TD><?=$RecentPeriod?></TR>
	<TR><TD><B>Next Time Card Due:&nbsp;</B></TD><TD><?=$NextPeriod?></TR>
	<TR><TD><B>Pending from Lab Counts:&nbsp;</B></TD><TD><?=mysql_result($MoreHours,0,0)?> Hrs (<A HREF="tcdetails.php?SID=<?=$SID."&magic=1&TC=$TC&IdNum=$IdNum&RID=".((mysql_num_rows($NextTC)==0)?"":mysql_result($NextTC,0,2))."&Beg=".urlencode(mysql_result($MoreHours,0,1))."&End=".urlencode(mysql_result($MoreHours,0,2))."&RBeg=".(mysql_result($MoreHours,0,3)+0)."&REnd=".(mysql_result($MoreHours,0,4)+0)?>">Click here for Details</A>)</TR>
	<TR><TD><B>Additional Pending:</B></TD><TD><?=$PendingReg?> Hrs</TR>
	<TR><TD><B>Additional Pending Over Time:&nbsp;</B></TD><TD><?=$PendingOT?> Hrs</TR>
	</TABLE>
	<BR><BR>


   <?$Periods=mysql_query("SELECT PeriodID,Due FROM TimeCards_Periods WHERE TimeCard=$TC and Due>=CURDATE() ORDER BY Due");
   if (mysql_num_rows($Periods)) {
	?>
	<TABLE border=0 cellspacing=4 cellpadding=2 width=100%>
	<FORM METHOD=POST>
	<INPUT NAME=TC TYPE=HIDDEN VALUE=<?=$TC?>>
	<INPUT NAME=IdNum TYPE=HIDDEN VALUE=<?=$CampusID?>>
	<INPUT NAME=SID TYPE=HIDDEN VALUE=<?=$SID?>>
	<TR><TD bgcolor=<?=$color_table_title?> colspan=2><?=$ofont_title?>Time Card Options<?=$cfont_title?></TD></TR>
	<TR>
	<TD BGCOLOR=<?=$color_table_lt_bg?> valign=top align=middle>
	<?
	$PayP=mysql_result($Periods,0,0);
	?>
	<INPUT NAME=PayP TYPE=HIDDEN VALUE=<?=$PayP?>>
	<INPUT NAME="Action" TYPE=SUBMIT VALUE="New TCs">
	Real simple now. No options - just wait till the day before time time cards are due
and they will magically appear. For the impatient, a button will appear here to let you
create the time card earlier, but that part has yet to be written.
	</TD>
	</TR>
	</FORM>
	</TABLE>
	<BR><BR>
<?  } 

	MakeTable($MyTCs,1,1,1,1,"Past Time Cards");
	?>
<?

}

}
include "footer.php";
if ($Automatic!=1) db_logout($hdb);


/* 45      2       *       *       *       lynx -useragent=cron -dump http://deepblue/helpdesk/cronjob.php | sendmail 
tedonline@aol.com]
*/
?>
