<?php   include "config.php";
	include "database.php";
	include "login.php";
	include "functions.php";
	$title="Manage Locations";
	include "header.php";
	MustLogin(1);

if ("Manage"==$Mode) { //Managing a specific schedule
	$LocInfo=mysql_query("SELECT * FROM Locations WHERE ID=$Location");
	if (0==mysql_num_rows($LocInfo)) {
		?><H2>ERROR</H2>Location does not exist or you do not have permission to manage it.<?
		include "footer.php";
		exit;
	}

	switch ($Action) {
		case "Add IP": //Add IP Address
			$IPMask=iptoint($IPMask); //Gott send IP and mask as signed ints
			$IP=((int)$IPMask & (int)iptoint($IPNum));
			$x=mysql_query("INSERT INTO Locations_IPs (LocationID, IP, IPMask) VALUES ($Location,'$IP','$IPMask')");
			break;
		case "Remove": //Remove IP Address
			$x=mysql_query("DELETE FROM Locations_IPs WHERE LocationID=$Location AND IP=".$IPs);
			break;
		case "<-- Add": //Add Schedule
			$x=mysql_query("INSERT INTO Locations_Schedules (LocationID,ScheduleID) VALUES ($Location,$Off)");
			break;
		case "Remove -->": //Remove Schedule
			$x=mysql_query("DELETE FROM Locations_Schedules WHERE LocationID=$Location AND ScheduleID=$On");
			break;
		case "Add": //Add Statistic
			$x=mysql_query("INSERT INTO Locations_Stats (Name,Location,Minimum,Maximum,Scale) VALUES ('$Name',$Location,$Minimum,$Maximum,$Scale)");
			break;
		case "EDIT": //Edit a statistic
			$x=mysql_query("UPDATE Locations_Stats SET Name='$Name',Minimum=$Minimum,Maximum=$Maximum,Scale=$Scale WHERE ID=$ID AND Location=$Location");
			break;
	}

	echo "<H1>Manage Location: ".mysql_result($LocInfo,0,"Name")."</H1>";
	?><H2>Basics:</H2>
	<FORM METHOD=POST>
	<INPUT TYPE=HIDDEN NAME=SID VALUE=<?=$SID?>>
	<TABLE border=0>
	<TR>
	<TD align=right>Name:</TD>
	<TD><INPUT TYPE=TEXT NAME="Name" SIZE=24 VALUE="<?=mysql_result($LocInfo,0,"Name")?>"></TD>
	</TR><TR>
	<TD align=right>Block Size:</TD>
	<TD><INPUT TYPE=TEXT NAME="Block" SIZE=6 VALUE="<?=mysql_result($LocInfo,0,"TimeQuantum")?>"> (increments of 15 minutes)</TD>
	</TR><TR>
	<TD align=right>Action:</TD>
	<TD>
	<? $TestVal=mysql_result($LocInfo,0,"Action"); ?>
	<SELECT NAME="Act">
		<OPTION VALUE="ClockIn">ClockIn</OPTION>
		<OPTION VALUE="GetStats" <?=(("GetStata"==$TestVal)?"SELECTED":"")?>>GetStats</OPTION>
		<OPTION VALUE="Both" <?=(("Both"==$TestVal)?"SELECTED":"")?>>Both</OPTION>
	</SELECT>
	</TD>
	</TR><TR>
	<TD align=right>Clock Mode:</TD>
	<TD>
	<? $TestVal=mysql_result($LocInfo,0,"ClockMode"); ?>
	<SELECT NAME="Type">
		<OPTION VALUE="Point">Point</OPTION>
		<OPTION VALUE="InOut" <?=(("InOut"==$TestVal)?"SELECTED":"")?>>InOut</OPTION>
	</SELECT>
	</TD>
	</TR><TR>
	<TD align=right>Offset:</TD>
	<TD><INPUT TYPE=TEXT NAME="Offset" SIZE=6 VALUE="<?=mysql_result($LocInfo,0,"Offset")?>"> (minutes)</TD>
	</TR>
	<TR><TD colspan=2 align=right>
	<INPUT NAME="Action" VALUE="Update" TYPE=SUBMIT>
	</TD></TR>
	</TABLE>
	</FORM>

	<BR>

	<H2>IP Addresses:</H2>
	<FORM METHOD=POST>
	<INPUT TYPE=HIDDEN NAME=SID VALUE=<?=$SID?>>
	<TABLE border=0>
	<TR><TD><B>Current:</B></TD><TD><B>Add:</B></TD></TR>
	<TD align=center VALIGN=TOP>
		<SELECT NAME="IPs" SIZE=6>
		<?
		$x=mysql_query("SELECT IP, IPMask FROM Locations_IPs WHERE LocationID=$Location");
		for ($i=0;$i<mysql_num_rows($x);$i++) {
			?><OPTION VALUE="<?=mysql_result($x,$i,0)?>">
				<?=(inttoip(mysql_result($x,$i,0))."  /  ".inttoip(mysql_result($x,$i,1)));?>
			</OPTION>
			<?
		}
		?>
		</SELECT><BR>
	</TD><TD valign=top align=left>
		IP Address:<BR>
		<INPUT NAME="IPNum" TYPE=TEXT SIZE=15 MAXLENGTH=15><BR>
		Mask:<BR>
		<INPUT NAME="IPMask" TYPE=TEXT SIZE=15 MAXLENGTH=15><BR>
	</TD></TR>
	<TR><TD align=left>
		<INPUT NAME="Action" VALUE="Remove" TYPE=SUBMIT>

	</TD><TD align=right>
		<INPUT NAME="Action" VALUE="Add IP" TYPE=SUBMIT>
	</TD></TR>
	</TABLE>
	</FORM>

	<BR>

	<H2>Required Schedules:</H2>
	<FORM METHOD=POST>
	<INPUT TYPE=HIDDEN NAME=SID VALUE=<?=$SID?>>
	<TABLE border=0>
	<TR><TD>
		<SELECT NAME="On" SIZE=6>
		<? //Items in this Location's Schedule List
		$not_clause="1=1";//start with something we know is true
		$x=mysql_query("SELECT ScheduleID,Name FROM Locations_Schedules,Schedule_Info WHERE LocationID=$Location AND ID=ScheduleID ORDER BY Name");
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
		$x=mysql_query("SELECT ID,Name FROM Schedule_Info WHERE ".$not_clause." ORDER BY Name");
		for($i=0;$i<mysql_num_rows($x);$i++) {
			?><OPTION VALUE=<?=mysql_result($x,$i,0)?>>
			<?=mysql_result($x,$i,1)?>
			</OPTION><?
		}
		?>
		</SELECT>

	</TD></TR>
	</TABLE>
	</FORM>

	<BR>

	<H2>Statistics</H2>
	<FORM METHOD=POST>
	<INPUT TYPE=HIDDEN NAME=SID VALUE=<?=$SID?>>
	<? $color=MakeTable(mysql_query("SELECT Name,Minimum,Maximum,Scale,concat('<A HREF=\x22locations-manage.php?SID=$SID&Mode=Manage&Location=$Location&Action=Edit&ID=',ID,'\x22>Edit</A>') AS Options FROM Locations_Stats WHERE Location=$Location ORDER BY Name"),1,1,0,1,"");

	if ("Edit"==$Action) {
		$x=mysql_query("SELECT Name,Minimum,Maximum,Scale FROM Locations_Stats WHERE ID=$ID AND Location=$Location");
	} else {
		$x=mysql_query("SELECT '' AS Name, 0 AS Minimum, 32767 AS Maximum, 1.00 AS Scale");
	}

	?><TR><TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
		<INPUT NAME="Name" VALUE="<?=mysql_result($x,0,0)?>" SIZE=20 TYPE=TEXT>
	</TD><TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
		<INPUT NAME="Minimum" VALUE="<?=mysql_result($x,0,1)?>" SIZE=6 TYPE=TEXT>
	</TD><TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
		<INPUT NAME="Maximum" VALUE="<?=mysql_result($x,0,2)?>" SIZE=6 TYPE=TEXT>
	</TD><TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
		<INPUT NAME="Scale" VALUE="<?=mysql_result($x,0,3)?>" SIZE=6 TYPE=TEXT>
	</TD><TD BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>><?
		if ("Edit"==$Action) {
			?><INPUT NAME="ID" VALUE="<?=$ID?>" TYPE=HIDDEN>
			<INPUT NAME="Action" VALUE="EDIT" TYPE=SUBMIT><?
		} else {
			?><INPUT NAME="Action" VALUE="Add" TYPE=SUBMIT><?
		}
	?></TD></TR>
	</TABLE>	
	</FORM>

	
	<?
} else { //Browsing All Schedules

	if ("Y"==$userdata["IsAdmin"]) {
		//echo $Action;
		switch ($Action) {
			case "Add":
				$x=mysql_query("INSERT INTO Locations (Name,TimeQuantum,Action,ClockMode,Offset) VALUES ('$Name',$Block,'$Act','$Type','$Offset')");
				break;
			case "Delete":
				echo "ohno";
				break;
		}
	}

	$Locations=mysql_query("
		SELECT
			Name,
			ID,
			concat((TimeQuantum)/4,' hr(s)') AS 'Block Size',
			Action,
			ClockMode,
			Offset,
			concat('<A HREF=\x22locations-manage.php?SID=$SID&Mode=Manage&Location=',ID,'\x22>Manage</A>'
			".(("Y"==$userdata["IsAdmin"])?"
			, '&nbsp;&nbsp;&nbsp;<A HREF=\x22locations-manage.php?SID=$SID&Action=Delete&Mode=Browse&Location=',ID,'\x22>Delete</A>'
			":"")."
			) AS Operations
		FROM
			Locations
		ORDER BY Name
	");
	//$Schedules=mysql_query("SELECT ID, Name, TimeQuantum AS 'Block Size', Holiday, ColorCode, concat('AS Operations FROM Schedule_Info");

	$color=MakeTable($Locations,1,1,0,1,"Locations");
	
	if ("Y"==$userdata["IsAdmin"]) {
	?><FORM METHOD=POST>
	<INPUT TYPE=HIDDEN NAME=SID VALUE=<?=$SID?>>
	<TR><TD colspan=2 BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
	<INPUT NAME="Name" TYPE=TEXT SIZE=20>
	</TD><TD colspan=1 BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
	<SELECT NAME="Block">
		<OPTION VALUE=1>15 Min</OPTION>
		<OPTION VALUE=2 SELECTED>30 Min</OPTION>
		<OPTION VALUE=4>1 Hour</OPTION>
		<OPTION VALUE=8>2 Hrs</OPTION>
		<OPTION VALUE=12>3 Hrs</OPTION>
	</SELECT>
	</TD><TD colspan=1 BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
	<SELECT NAME="Act">
		<OPTION VALUE="ClockIn">ClockIn</OPTION>
		<OPTION VALUE="GetStats">GetStats</OPTION>
		<OPTION VALUE="Both" SELECTED>Both</OPTION>
	</SELECT>
	</TD><TD colspan=1 BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
	<SELECT NAME="Type">
		<OPTION VALUE="Point">Point</OPTION>
		<OPTION VALUE="InOut">InOut</OPTION>
	</SELECT>
	</TD><TD colspan=1 BGCOLOR=#<? echo ($color==1)?$color_table_dk_bg:$color_table_lt_bg ?>>
	<INPUT NAME="Offset" TYPE=TEXT SIZE=6 MAXLENGTH=6>
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