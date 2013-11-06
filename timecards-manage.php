<?php   include "config.php";
	include "database.php";
	include "login.php";
	include "functions.php";
	$title="Manage Time Cards";
	include "header.php";
	MustLogin(1);

if ("Manage"==$Mode) { //Managing a specific schedule
	$TCInfo=mysql_query("SELECT * FROM TimeCards_Info WHERE ID=$TimeCard");
	if (0==mysql_num_rows($TCInfo)) {
		?><H2>ERROR</H2>Schedule does not exist or you do not have permission to manage it.<?
		include "footer.php";
		exit;
	}

	//What does the user want us to do?
	switch ($Action) {
		case "<-- Add": //Add Location
			$x=mysql_query("INSERT INTO TimeCards_Locations (TimeCard,Location) VALUES ($TimeCard,$Off)");
			break;
		case "Remove -->": //Remove Location
			$x=mysql_query("DELETE FROM TimeCards_Locations WHERE Location=$On AND TimeCard=$TimeCard");
			break;
		case "Add": //Add a Pay Period
			$x=mysql_query("INSERT INTO TimeCards_Periods (TimeCard,Start,End,Due,RunID) VALUES ($TimeCard,'$Start','$End','$Due','$RunID')");
			break;
		case "EDIT": //Update a Pay Period
			$x=mysql_query("UPDATE TimeCards_Periods SET Start='$Start', End='$End', Due='$Due', RunID='$RunID' WHERE PeriodID=$ID AND TimeCard=$TimeCard");
			break;
		case "Update": //Update basic info about this timecard
			$x=mysql_query("UPDATE TimeCards_Info SET Name='$Name', File='$File' WHERE ID=$TimeCard")
;
			$TCInfo=mysql_query("SELECT * FROM TimeCards_Info WHERE ID=$TimeCard"); //get correct data
			break;
	}

	echo "<H1>Manage Time Card: ".mysql_result($TCInfo,0,"Name")."</H1>";

	//Basic name + file name stuff
	?><H2>Basics:</H2>
	<FORM METHOD=POST>
	<INPUT NAME=SID TYPE=HIDDEN VALUE="<?=$SID?>">
	<INPUT NAME=Mode TYPE=HIDDEN VALUE="Manage">
	<INPUT NAME=TimeCard TYPE=HIDDEN VALUE="<?=$TimeCard?>">
	<TABLE border=0>
	<TR>
		<TD align=right>Name:</TD>
		<TD><INPUT NAME="Name" TYPE=TEXT SIZE=32 VALUE="<?=mysql_result($TCInfo,0,"Name")?>"></TD>
	</TR><TR>
		<TD align=right>File:</TD>
		<TD><INPUT NAME="File" TYPE=TEXT SIZE=32 VALUE="<?=mysql_result($TCInfo,0,"File")?>"></TD>
	</TR>
	<TR><TD colspan=2 align=right>
		<INPUT NAME="Action" VALUE="Update" TYPE=SUBMIT>
	</TD></TR>
	</TABLE>
	</FORM><BR>

	<? /*Which locations do we claim membership? */ ?>
	<H2>Related Locations:</H2>
	<FORM METHOD=POST>
	<INPUT TYPE=HIDDEN NAME=SID VALUE=<?=$SID?>>
	<INPUT NAME=Mode TYPE=HIDDEN VALUE="Manage">
	<INPUT NAME=TimeCard TYPE=HIDDEN VALUE="<?=$TimeCard?>">
	<TABLE border=0>
	<TR><TD>
		<SELECT NAME="On" SIZE=6>
		<? //Items in this TimeCard's Location List
		$not_clause="1=1";//start with something we know is true
		$x=mysql_query("SELECT ID,Name FROM TimeCards_Locations,Locations WHERE Locations.ID=TimeCards_Locations.Location AND TimeCards_Locations.TimeCard=$TimeCard ORDER BY Name");
		for($i=0;$i<mysql_num_rows($x);$i++) {
			?><OPTION VALUE=<?=mysql_result($x,$i,0)?>>
			<?=mysql_result($x,$i,1)?>
			</OPTION><?
			$not_clause.=" AND ID!=".mysql_result($x,$i,0);
		}
		?>
		</SELECT>
	</TD><TD valign=middle align=center>
		<INPUT NAME="Action" VALUE="<-- Add" TYPE=SUBMIT><BR><BR>
		<INPUT NAME="Action" VALUE="Remove -->" TYPE=SUBMIT>
	</TD><TD>
		<SELECT NAME="Off" SIZE=6>
		<? //Items not in this locations Schedule List
		$x=mysql_query("SELECT ID,Name FROM Locations WHERE ".$not_clause." ORDER BY Name");
		for($i=0;$i<mysql_num_rows($x);$i++) {
			?><OPTION VALUE=<?=mysql_result($x,$i,0)?>>
			<?=mysql_result($x,$i,1)?>
			</OPTION><?
		}
		?>
		</SELECT>

	</TD></TR>
	</TABLE>
	</FORM><BR>


	<? /* Most Recent Pay Periods */ ?>
	<H2>Pay Periods:</H2>
	<A HREF="queries.php?SID=<?=$SID?>&QID=17&TimeCard=<?=$TimeCard?>">View all Pay Periods for this Time Card</A>
	<FORM METHOD=POST>
	<INPUT TYPE=HIDDEN NAME=SID VALUE=<?=$SID?>>
	<INPUT NAME=Mode TYPE=HIDDEN VALUE="Manage">
	<INPUT NAME=TimeCard TYPE=HIDDEN VALUE="<?=$TimeCard?>">
	<?

	$color=MakeTable(mysql_query("SELECT RunID,Start,End,Due,concat('<A HREF=\x22timecards-manage.php?SID=$SID&Mode=Manage&TimeCard=$TimeCard&Action=Edit&ID=',PeriodID,'\x22>Edit</A>') AS Options FROM TimeCards_Periods WHERE TimeCard=$TimeCard ORDER BY Due DESC LIMIT 5"),1,1,0,1,"",-1);

	if ("Edit"==$Action) {
		$x=mysql_query("SELECT RunID,Start,End,Due FROM TimeCards_Periods WHERE PeriodID=$ID AND $TimeCard=$TimeCard");
	} else {
		$x=mysql_query("SELECT '' AS RunID, CURDATE(), CURDATE(), CURDATE()");
	}

	?><TR>

	<TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
		<INPUT NAME="RunID" VALUE="<?=mysql_result($x,0,0)?>" SIZE=8 TYPE=TEXT>
	</TD>

	<TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
		<INPUT NAME="Start" VALUE="<?=mysql_result($x,0,1)?>" SIZE=10 TYPE=TEXT>
	</TD>

	<TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
		<INPUT NAME="End" VALUE="<?=mysql_result($x,0,2)?>" SIZE=10 TYPE=TEXT>
	</TD>

	<TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
		<INPUT NAME="Due" VALUE="<?=mysql_result($x,0,3)?>" SIZE=10 TYPE=TEXT>
	</TD>

	<TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
<?
		if ("Edit"==$Action) {
			?><INPUT NAME="ID" VALUE="<?=$ID?>" TYPE=HIDDEN>
			<INPUT NAME="Action" VALUE="EDIT" TYPE=SUBMIT><?
		} else {
			?><INPUT NAME="Action" VALUE="Add" TYPE=SUBMIT><?
		}
	?>
	</TD>


	</TR></TABLE>

	</FORM>


<?
} else { //Browsing All Schedules

	if ("Y"==$userdata["IsAdmin"]) {
		//echo $Action;
		switch ($Action) {
			case "Add":
				$x=mysql_query("INSERT INTO TimeCards_Info (Name,File) VALUES ('$Name','$FileName')");
				break;
			case "Delete":
				echo "ohno";
				break;
		}
	}

	$TCs=mysql_query("
		SELECT
			ID,
			Name,
			File,
			concat('<A HREF=\x22timecards-manage.php?SID=$SID&Mode=Manage&TimeCard=',ID,'\x22>Manage</A>'
			".(("Y"==$userdata["IsAdmin"])?"
			, '&nbsp;&nbsp;&nbsp;<A HREF=\x22timecards-manage.php?SID=$SID&Action=Delete&Mode=Browse&TimeCard=',ID,'\x22>Delete</A>'
			":"")."
			) AS Operations
		FROM
			TimeCards_Info
		ORDER BY Name
	");


	$color=MakeTable($TCs,1,1,0,2,"Time Cards");
	
	if ("Y"==$userdata["IsAdmin"]) {
	?><FORM METHOD=POST>
	<INPUT TYPE=HIDDEN NAME=SID VALUE=<?=$SID?>>
	<TR><TD colspan=2 BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
	<INPUT NAME="Name" TYPE=TEXT SIZE=24 MAXLENGTH=256>
	</TD><TD colspan=1 BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
	<INPUT NAME="FileName" TYPE=TEXT SIZE=32 MAXLENGTH=256>
	</TD><TD colspan=1 BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
	<INPUT TYPE="SUBMIT" NAME="Action" VALUE="Add">
	</TD></TR></TABLE>
	</FORM>
	<?
	} else {
		?></TABLE><?
	}
}

include "footer.php";
?>
