<?
include "config.php";
include "database.php";
include "functions.php";

$title = "Client Info for ".$CID;

include "login.php";

include "header.php";


MustLogIn();

$myclient=mysql_query("SELECT * FROM People WHERE CampusID=$CID");

if ("Add Person"==$Action && 0==mysql_num_rows($myclient) && 0<$CID && ""!=$Last) {
	$x=mysql_query("INSERT INTO People (CampusID,First,Last,Student,Faculty) VALUES ($CID,'$First','$Last','".((($Role & 1) == 1)?"Y":"N")."','".((($Role & 2) == 2)?"Y":"N")."')");
	$myclient=mysql_query("SELECT * FROM People WHERE CampusID=$CID");
}

if (0==mysql_num_rows($myclient)) {
	echo "<H1>Person with Campus ID $CID Not Found</H1>";
} else if (1==mysql_num_rows($myclient)) {


	//Is the entry for a user in out database?
	$UserResult=mysql_query("SELECT HelpDesk,FieldSupport FROM Users WHERE CampusID=$CID");
	$IsUser=mysql_num_rows($UserResult);
	If (1==$IsUser && ((mysql_result($UserResult,0,0)=="N" && mysql_result($UserResult,0,0)=="N") && ($userdata["IsHelpDesk"]=="Y" || $userdata["IsFieldSupport"]=="Y"))) {$IsUser=0;}
	If ("Y"==$userdata["IsAdmin"] || $CID==$CampusID) {$IsUser=0;}
	If ("1"==$OnlyInfo) {$IsUser=1;} //If OnlyInfo mode, don't give options

	//Fix some stuff
	if (""!=$Phone) { //Allow only number in phone number
		$Phone=ereg_replace("[^0-9]","",$Phone);
	}
	if (""!=$MAC) { //Allow only number in phone number
		$MAC=eregi_replace("[^0-F]","",$MAC);
	}
	if (""!=$Room) { //Allow only number in phone number
		$Room=eregi_replace("[^0-F]","",$Room);
	}

    //Do we need to perform any update?

    if (""!=$Action && (0==$IsUser || "Add Ticket"==$Action || "Add Computer"==$Action || "Add NIC"==$Action)) {

	if (ereg("^Remove",$Action) && (!ereg("Cancel",$Confirmation))) { //Removals
		if ($Action!=$Confirmation) { //Not yet appoved for removal
			?><FORM METHOD=POST><?
			echo "<BR><CENTER><P><H2><B>".stripslashes($Message)."</B></H2></P>\n";
			echo "<INPUT TYPE=HIDDEN NAME=\x22SID\x22 VALUE=\x22$SID\x22>\n";
			echo "<INPUT TYPE=HIDDEN NAME=\x22CID\x22 VALUE=\x22$CID\x22>\n";
			echo "<INPUT TYPE=HIDDEN NAME=\x22notables\x22 VALUE=\x22$notables\x22>\n";
			echo "<INPUT TYPE=HIDDEN NAME=\x22Action\x22 VALUE=\x22$Action\x22>\n";
			echo "<INPUT TYPE=HIDDEN NAME=\x22InfoID\x22 VALUE=\x22$InfoID\x22>\n";
			echo "<INPUT TYPE=HIDDEN NAME=\x22Building\x22 VALUE=\x22$Building\x22>\n";
			echo "<INPUT TYPE=HIDDEN NAME=\x22NID\x22 VALUE=\x22$NID\x22>\n";
			echo "<INPUT TYPE=HIDDEN NAME=\x22Room\x22 VALUE=\x22$Room\x22>\n";
			echo "<INPUT TYPE=HIDDEN NAME=\x22Phone\x22 VALUE=\x22$Phone\x22>\n";
			echo "<INPUT TYPE=SUBMIT NAME=\x22Confirmation\x22 VALUE=\x22$Action\x22>&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<INPUT TYPE=SUBMIT NAME=\x22Confirmation\x22 VALUE=\x22Cancel\x22>";
			
			?></CENTER></FORM><?
			include "footer.php";
			db_logout($hdb);
			exit; //quit till we get a response.
		} else if ($Action=="Remove Room") {
			$x=mysql_query("DELETE FROM Rooms WHERE CampusID=$CID AND RoomNumber=$Room AND Hall=$Building");
		} else if ($Action=="Remove Phone") {
			$x=mysql_query("DELETE FROM Phones WHERE CampusID=$CID AND PhNum=$Phone");
		} else if ($Action=="Remove NIC") {
			$x=mysql_query("DELETE FROM NICs WHERE CampusID=$CID AND NID=$NID");
		} else if ($Action=="Remove Info") {
			//Display old info when something is removed
			$InfoWhat=mysql_query("Select Description,Value FROM MiscInfo WHERE ID=$InfoID");
			$InfoOtherField=mysql_result($InfoWhat,0,0);
			$IText = mysql_result($InfoWhat,1,0);

			//Remove it
			$x=mysql_query("DELETE FROM Misc WHERE CampusID=$CID AND ID=$InfoID");
		}
	}

	if (("Correct Info"==$Action) && (0==$IsUser)) {
		$x=mysql_query("UPDATE People SET First='$First', Nick='$Nick', Middle='$Middle', Last='$Last' WHERE CampusID=$CID");
		$myclient=mysql_query("SELECT * FROM People WHERE CampusID=$CID");
	} else if (("Add Room"==$Action) && (""!=$Room) && (1>mysql_num_rows(mysql_query("SELECT CampusID FROM Rooms WHERE CampusID=$CID AND Hall='$Building' AND RoomNumber='$Room'")))) {
		$x=mysql_query("INSERT INTO Rooms (CampusID,Hall,RoomNumber) VALUES ($CID,$Building,$Room)");
	} else if (("Add Phone"==$Action) && (""!=$Phone) && (1>mysql_num_rows(mysql_query("SELECT CampusID FROM Phones WHERE CampusID=$CID AND PhNum='$Phone'")))) {
		if (0>$RoomBuild) {
			$PhHall=0;
			$PhRm="NULL";
		} else {
			$MyRooms=mysql_query("SELECT Buildings.Name AS 'Building', Buildings.Number AS Hidden, Rooms.RoomNumber AS 'Room #' FROM Buildings, Rooms WHERE Rooms.CampusID=$CID AND Buildings.Number=Rooms.Hall");
			$PhHall=mysql_result($MyRooms,$RoomBuild,1);
			$PhRm=mysql_result($MyRooms,$RoomBuild,2);

		}
		$x=mysql_query("INSERT INTO Phones (CampusID,PhNum,Extra,Hall,RoomNumber) VALUES ($CID,$Phone,'$PhInfo',$PhHall,$PhRm)");

	} else if ("Add Computer"==$Action) {

		If ("Other"!=$BrandId) {
			$Brand=$BrandId;
		}
		$x=mysql_query("INSERT INTO Computer (CampusID,Brand,Line,Model,OS,OSVer) VALUES ($CID,'$Brand','$Line','$Model','$OS','$OSVer')");


	} else if ("Add Info"==$Action) {

		If ("Other"!=$InfoBasicField) {
			$InfoOtherField=$InfoBasicField;
		}
		$x=mysql_query("INSERT INTO MiscInfo (CampusID,Description,Value) VALUES ($CID,'$InfoOtherField','$InfoText')");

	} else if ("Update This Computer"==$Action) {

		If ("Other"!=$BrandId) {
			$Brand=$BrandId;
		}
		$x=mysql_query("UPDATE Computer SET Brand='$Brand', OS='$OS', Line='$Line', Model='$Model', OSVer='$OSVer' WHERE ComputerID=$CompID");

	} else if ("Add Ticket"==$Action && ""!=$Description) {

		if ("Other (entered below)"==$LocOpt) {
			$LocOpt=($LocAlt);
		}
		if (""==$Jack) {$Jack="NULL";}
		//echo $TIDl
		if (""==$TID) {$TID="NULL";}
		if (""==$CreateDate) {$CreateDate="NOW()";} else {$CreateDate = "'".$CreateDate."'"; }
		//echo "INSERT INTO CallTicket (TicketID,CampusID,JackID,ComputerID,Location,Creation) VALUES ($TID,$CID,$Jack,$CompSel,'$LocOpt')";
		$x=mysql_query("INSERT INTO CallTicket (TicketID,CampusID,JackID,ComputerID,Location) VALUES ($TID,$CID,$Jack,$CompSel,'$LocOpt')");

		$InsertID= mysql_insert_id();
		if (0==$InsertID) {$InsertID=$TID;}
		$x=mysql_query("INSERT INTO PaperTrail (Creator_CampusID, Comment,Department, TicketID,IsFirst,IsLast,Creation) VALUES ($CampusID,'".$Description."','".(($userdata["IsLINC"]=="Y")?"LINC":"IT")."',$InsertID,'Y','Y',$CreateDate)");
		//echo "INSERT INTO PaperTrail (Creator_CampusID, Comment,Department, TicketID,Creation,IsFirst,IsLast,Creation) VALUES ($CampusID,'".$Description."','".(($userdata["IsLINC"]=="Y")?"LINC":"IT")."',LAST_INSERT_ID(),NOW(),'Y','Y',$CreateDate)";

	} else if ("Add NIC"==$Action && ($NetBrand!="" || "Other"!=$NetBrandId || $MAC!="")) {
		if (""!=$MAC && strlen($MAC)!=12) {
			$NErr="The MAC Address must be exactly 12 hexidecimal numbers (range 0-F), rather than ".strlen($MAC).".";
		} else if (ereg("^4445",$MAC)) {
			$NErr="Invalid MAC Address - this is either the item listed as \x22PPP Adapter\x22, or \x22AOL Adapter\x22.";
		} else {
			$allok=1;
			if (""!=$MAC) {
				$x=mysql_query("SELECT CampusID from NICs WHERE MediaAccess=conv('$MAC',16,10)");
				if (mysql_num_rows($x)>0) {
					$allok=0;
					$NErr="<A HREF=\x22clientinfo.php?CID=".mysql_result($x,0,0)."&SID=$SID\x22>Some else</A> has a card w/ that MAC address.";
				}
			}
			if (1==$allok) {
				if ("Other"!=$NetBrandId) {$NetBrand=$NetBrandId;};
				$x=mysql_query("INSERT INTO NICs (CampusID,Brand,Model,MediaAccess) VALUES ($CID,'$NetBrand','$NetModel',conv('$MAC',16,10))");
			}

		}
	}

    }








	//Display the page

	$about=mysql_fetch_assoc($myclient);

	?><TABLE border=0 cellspacing=0 cellpadding=3 width=100% BGCOLOR=#DFDFDF><TR><TD valign=top BGCOLOR=#DFDFDF><BR><H1><?
	echo "&nbsp;".$about["First"]." ".$about["Middle"]." ".$about["Last"];
	?></H1></TD><TD valign=top Align=right BGCOLOR=#DFDFDF><TABLE border=0><TR><TD bgcolor=#C8C8C8><BR><H1>&nbsp;<?
	echo $about["CampusID"];
	?>&nbsp;</H1></TD></TR></TABLE></TD></TR></TABLE>
	<BR><BR>

	<!-- Beginning of form elements-->
	<FORM METHOD=POST>
		<INPUT TYPE=HIDDEN NAME="SID" VALUE=<? echo $SID; ?>>
		<INPUT TYPE=HIDDEN NAME="CID" VALUE=<? echo $CID; ?>>

    <? if ($IsUser==0) { ?>

	<!--Correct User Information-->
	<TABLE width=100% border=0 cellspacing=1 cellpadding=3>
	<TR><TD BGCOLOR=#EE4510><TT>Corrections in Basic Client Information</TT></TD></TR>
	<TR><TD BGCOLOR=#DFDFDF valign=top align=center>
		<TABLE border=0>
		<TR><TD>First</TD><TD>Nick</TD><TD>Middle</TD><TD>Last</TD><TD></TD></TR>
		<TR>
			<TD><INPUT TYPE=TEXT SIZE=16 NAME=First VALUE="<? echo $about["First"]; ?>"></TD>
			<TD><INPUT TYPE=TEXT SIZE=10 NAME=Nick VALUE="<? echo $about["Nick"]; ?>"></TD>
			<TD><INPUT TYPE=TEXT SIZE=10 NAME=Middle VALUE="<? echo $about["Middle"]; ?>"></TD>
			<TD><INPUT TYPE=TEXT SIZE=20 NAME=Last VALUE="<? echo $about["Last"]; ?>"></TD>
			<TD><INPUT TYPE=SUBMIT NAME="Action" VALUE="Correct Info"></TD>
		<? if ($userdata["IsAdmin"]=="Y") {
			echo "<TD><A HREF=\x22myinfo.php?SID=$SID&IdNum=$CID\x22>Edit CampusID, Password, Schedule, etc...</A></TD>";
		} ?>
		</TR>
		</TABLE>
	</TD></TR>
	</TABLE>

	<BR><BR>

   <? } ?>

	<?
	$MyRooms=mysql_query("SELECT Buildings.Name AS 'Building', Buildings.Number AS Hidden, Rooms.RoomNumber AS 'Room #' ".(($IsUser==0)?",'delete room' AS 'Options', concat('clientinfo.php?SID=$SID&CID=$CID&notables=$notables&Action=Remove+Room&Building=',Rooms.Hall,'&Room=',Rooms.RoomNumber,'&Message=Are+you+sure+you+want+to+delete+',Buildings.Name,'+',Rooms.RoomNumber,'%3F') AS 'DelHyperlink'":"")." FROM Buildings, Rooms WHERE Rooms.CampusID=$CID AND Buildings.Number=Rooms.Hall");


	$color=MakeTable($MyRooms,1,1,0,2+$IsUser,"On Campus Room(s)");

	if (0==$IsUser) {
	?><TR><TD colspan=3 BGCOLOR=#<? echo ($color==1)?"C8C8C8":"DFDFDF" ?>>
	Building&nbsp;&nbsp;&nbsp;<SELECT NAME="Building">
	<?
	//$x=mysql_query("SELECT Number, Name FROM Buildings ORDER BY Name");
	$x=mysql_query("Select Number,Name FROM Buildings ".(("N"==$userdata["IsIT"])?"WHERE ResidenceHall='Y'":"")."ORDER BY Name");
	for ($i = 0;$i<mysql_num_rows($x);$i++) {
		?><OPTION VALUE="<?
		echo mysql_result($x,$i,0)."\x22";
		if (mysql_result($x,$i,0)==$Building) { ?> SELECTED<?}
		?>><?
		echo mysql_result($x,$i,1);
		?></OPTION><?
		echo "\n";
	}
	?>
	</SELECT>

	&nbsp;&nbsp;&nbsp;Room&nbsp;&nbsp;<INPUT TYPE=TEXT NAME="Room" SIZE=5 VALUE=<? echo $Room; ?>>&nbsp;&nbsp;&nbsp;

	<INPUT TYPE=SUBMIT NAME="Action" VALUE="Add Room">

	</TD></TR>

	<? } ?>

	</TABLE>

	<BR><BR><?

	$color=MakeTable(mysql_query("SELECT ".sql_phone("Phones.PhNum")." AS 'Number', IF(Phones.Hall=0,IF(Phones.Extra=NULL OR Phones.Extra='','',Phones.Extra),IF(Phones.Extra=NULL OR Phones.Extra='',concat(Buildings.Name,' ',Phones.RoomNumber),concat(Phones.Extra, ' - ',Buildings.Name,' ',Phones.RoomNumber))) AS 'Related Information' ".(($IsUser==0)?",'delete number' AS 'Options', concat('clientinfo.php?SID=$SID&notables=$notables&CID=$CID&Action=Remove+Phone&Phone=',Phones.PhNum,'&Message=Are+you+sure+you+want+to+delete+',Phones.PhNum,' (',Phones.Extra,')','%3F') AS 'DelHyperlink'":"")." FROM Phones, Buildings WHERE Phones.CampusID=$CID AND Buildings.Number=Phones.Hall"),1,1,0,2+$IsUser,"Phone Number(s)");

	if (0==$IsUser) {
	?><TR><TD colspan=3 BGCOLOR=#<? echo ($color==1)?"C8C8C8":"DFDFDF" ?>>

	Number:&nbsp;&nbsp;<INPUT TYPE=TEXT NAME="Phone" SIZE=11 VALUE=<? echo $Phone; ?>>&nbsp;&nbsp;&nbsp;
	Room:&nbsp;&nbsp;<SELECT NAME="RoomBuild">
	<OPTION VALUE="-1">n/a</OPTION>
	<?
	for ($i=0;$i<mysql_num_rows($MyRooms);$i++) {
		echo "<OPTION VALUE=$i";
		if ((mysql_result($MyRooms,$i,1)==$Building) && (mysql_result($MyRooms, $i, 2)==$Room)) { ?> SELECTED<? }
		echo ">";
		echo mysql_result($MyRooms,$i,0)."  ".mysql_result($MyRooms,$i,2);
		echo "</OPTION>\n";
	}
	?>
	</SELECT>

	&nbsp;&nbsp; Extra&nbsp;Information:&nbsp;&nbsp;<INPUT TYPE=TEXT NAME="PhInfo" SIZE=24 VALUE=<? echo stripslashes($PhInfo); ?>>&nbsp;&nbsp;&nbsp;

	<INPUT TYPE=SUBMIT NAME="Action" VALUE="Add Phone">

	</TD></TR>

	<? } ?>


	</TABLE>

	<BR><BR><?

	$color=MakeTable(mysql_query("SELECT Description, Value ".(($IsUser==0)?",'delete this' AS 'Options', concat('clientinfo.php?SID=$SID&notables=$notables&CID=$CID&Action=Remove+Info&InfoID=',ID,'&Message=Are+you+sure+you+want+to+delete+',Description,'+from+the+Additional+Info+table%3F') AS 'DelHyperlink'":"")." FROM MiscInfo WHERE MiscInfo.CampusID=$CID"),1,1,0,2+$IsUser,"Additional Information");

	if (0==$IsUser) {
	?><TR><TD colspan=3 BGCOLOR=#<? echo ($color==1)?"C8C8C8":"DFDFDF" ?>>

	<TABLE border=0><TR><TD valign=top>
	<BR>
	Field:&nbsp;&nbsp;<SELECT NAME="InfoBasicField">
	<OPTION>Other</OPTION>
	<OPTION>Address-Mailing</OPTION>
	<OPTION>Address-Residence</OPTION>
	<OPTION>Address-Home</OPTION>
	<OPTION>StaffInfo-Major</OPTION>
	<OPTION>StaffInfo-Favorite Movie</OPTION>
	<OPTION>StaffInfo-Favorite Music</OPTION>
	<OPTION>Client-Extra Info</OPTION>
	</SELECT><BR><BR>
	if Other:&nbsp;&nbsp;<INPUT TYPE=TEXT NAME="InfoOtherField" SIZE=16 VALUE="<? echo stripslashes($InfoOtherField); ?>">
	</TD><TD>&nbsp;&nbsp;&nbsp;&nbsp;<!--space out columns--></TD><TD valign="top">
	Extra&nbsp;Information:<BR><TEXTAREA NAME="InfoText" ROWS=3 COLS=28><? echo $IText; ?></TEXTAREA></TD><TD valign=middle>
	<BR>
	<INPUT TYPE=SUBMIT NAME="Action" VALUE="Add Info">
	</TD></TR></TABLE>
	</TD></TR>

	<? } ?>


	</TABLE>

	<BR><BR><?
	$ComputerList=mysql_query("SELECT Brand, ComputerID AS Hidden, Line, Model, OS, OSVer".((($IsUser==0 || $userdata["IsHelpDesk"]=="Y" || $userdata["IsFieldSupport"]=="Y") && 1!=$OnlyInfo )?",'update' AS 'Options', concat('clientinfo.php?SID=$SID&CID=$CID&CompID=',ComputerID,'&Brand=',REPLACE(Brand,' ','+'),'&Line=',REPLACE(Line,' ','+'),'&Model=',REPLACE(Model,' ','+'),'&OS=',REPLACE(REPLACE(OS,' ','+'),'/','%2F'),'&OSVer=',REPLACE(OSVer,' ','+'),'&UPGRADECOMP=YES&notables=$notables&sideless=$sideless') AS OptHyperlink":"")." FROM Computer WHERE Computer.CampusID=$CID");
	$color=MakeTable($ComputerList,1,1,0,2+((($IsUser==0 || $userdata["IsHelpDesk"]=="Y" || $userdata["IsFieldSupport"]=="Y")&& 1!=$OnlyInfo)?0:1),"Computer(s)");

if ((0==$IsUser || $userdata["IsHelpDesk"]=="Y" || $userdata["IsFieldSupport"]=="Y")&& 1!=$OnlyInfo) {

	?><TR><TD colspan=6 BGCOLOR=#<? echo ($color==1)?"C8C8C8":"DFDFDF" ?>>

	&nbsp;&nbsp;&nbsp;Brand:&nbsp;&nbsp;<SELECT NAME="BrandId">
		<OPTION>Other</OPTION>
		<OPTION>Dell</OPTION>
		<OPTION>Gateway</OPTION>
		<OPTION>Apple</OPTION>
		<OPTION>Micron</OPTION>
		<OPTION>Silicon Graphics</OPTION>
		<OPTION>Hewlett-Packard</OPTION>
		<OPTION>Sony</OPTION>
		<OPTION>Toshiba</OPTION>
		<OPTION>Acer</OPTOIN>
		<OPTION>Sun</OPTION>
		<OPTION>IBM</OPTION>
		<OPTION>NeXT</OPTION>
		<OPTION>Commodore</OPTION>
		<OPTION>eMachines</OPTION>
		<OPTION>Compaq</OPTION>
		<OPTION>Generic x86</OPTION>
	</SELECT>
	&nbsp;&nbsp;if&nbsp;"Other":&nbsp;<INPUT TYPE=TEXT NAME="Brand" VALUE="<? echo $Brand; ?>" SIZE=16>

	&nbsp;&nbsp;&nbsp;Line:&nbsp;<INPUT TYPE=TEXT NAME="Line"  VALUE="<? echo $Line; ?>" SIZE=16>

	&nbsp;&nbsp;&nbsp;Model:&nbsp;<INPUT TYPE=TEXT NAME="Model"  VALUE="<? echo $Model; ?>" SIZE=8>

	&nbsp;&nbsp;&nbsp;OS:&nbsp;<SELECT Name="OS">
		<OPTION <? if("Windows"==$OS) {echo "SELECTED";} ?>>Windows</OPTION>
		<OPTION <? if("Mac OS"==$OS) {echo "SELECTED";} ?>>Mac OS</OPTION>
		<OPTION <? if("Windows NT"==$OS) {echo "SELECTED";} ?>>Windows NT</OPTION>
		<OPTION <? if("Linux"==$OS) {echo "SELECTED";} ?>>Linux</OPTION>
		<OPTION <? if("BSD"==$OS) {echo "SELECTED";} ?>>BSD</OPTION>
		<OPTION <? if("Sun OS/Solaris"==$OS) {echo "SELECTED";} ?>>Sun OS/Solaris</OPTION>
		<OPTION <? if("HPUX"==$OS) {echo "SELECTED";} ?>>HPUX</OPTION>
		<OPTION <? if("NeXT"==$OS) {echo "SELECTED";} ?>>NeXT Step</OPTION>
		<OPTION <? if("AIX"==$OS) {echo "SELECTED";} ?>>AIX</OPTION>
		<OPTION <? if("IRIX"==$OS) {echo "SELECTED";} ?>>IRIX</OPTION>
		<OPTION <? if("UNIX"==$OS) {echo "SELECTED";} ?> VALUE="UNIX">Other UNIX</OPTION>
		<OPTION <? if("OS/2"==$OS) {echo "SELECTED";} ?>>OS/2</OPTION>
		<OPTION <? if("BeOS"==$OS) {echo "SELECTED";} ?>>BeOS</OPTION>
		<OPTION <? if("Amiga OS"==$OS) {echo "SELECTED";} ?>>Amiga OS</OPTION>
		<OPTION <? if("Palm OS"==$OS) {echo "SELECTED";} ?>>Palm OS</OPTION>
		<OPTION <? if("Other"==$OS) {echo "SELECTED";} ?>>Other</OPTION>
	</SELECT>


	&nbsp;&nbsp;&nbsp;Version:&nbsp;<INPUT TYPE=TEXT NAME="OSVer" VALUE="<? echo $OSVer; ?>" SIZE=8>

	<INPUT TYPE=HIDDEN NAME="CompID" VALUE="<? echo $CompID ?>">

	<?
		if ($UPGRADECOMP=="YES" && $CompID>0) {
		?>&nbsp;&nbsp;&nbsp;<INPUT TYPE=SUBMIT NAME="Action" VALUE="Update This Computer"><?
		}
	?>

	&nbsp;&nbsp;&nbsp;<INPUT TYPE=SUBMIT NAME="Action" VALUE="Add Computer"><?
	} //End Helpdesk/RCC only part
	?></TD></TR></TABLE>
	

	<BR><BR><?

	$color=MakeTable(mysql_query("SELECT Brand, Model, conv(MediaAccess,10,16) AS '<B>M</B>edia<B>AC</B>cess Number (MAC) - aka NIC Ethernet Address (NICEA)' ".((($IsUser==0 || $userdata["IsHelpDesk"]=="Y" || $userdata["IsFieldSupport"]=="Y") && 1!=$OnlyInfo)?",'delete NIC' AS 'Options',concat('clientinfo.php?SID=$SID&CID=$CID&notables=$notables&Action=Remove+NIC&NID=',NID,'&Message=Are+you+sure+you+want+to+delete+the+',REPLACE(IFNULL(Brand,''),' ','+'),'+',REPLACE(IFNULL(Model,''),' ','+'),IFNULL(concat('+(',conv(MediaAccess,10,16),')'),''),'%3F') AS 'DelHyperlink'":"")." FROM NICs WHERE CampusID=$CID"),1,1,0,2+((($IsUser==0 || $userdata["IsHelpDesk"]=="Y" || $userdata["IsFieldSupport"]=="Y")&& 1!=$OnlyInfo)?0:1),"Network Interface Card(s)");

	if ((0==$IsUser || $userdata["IsHelpDesk"]=="Y" || $userdata["IsFieldSupport"]=="Y")&& 1!=$OnlyInfo) {
	?><TR><TD colspan=4 BGCOLOR=#<? echo ($color==1)?"C8C8C8":"DFDFDF" ?>>
	<? if ($NErr!="") { echo "<B><font color=#FF0000>".$NErr."</font></B><BR>"; } ?>
	&nbsp;Brand:&nbsp;&nbsp;
	<SELECT NAME="NetBrandId">
		<OPTION>Other</OPTION>
		<OPTION NAME="LinkSys">LinkSys / Network Everywhere</OPTION>
		<OPTION>NETGEAR</OPTION>
		<OPTION>3Com</OPTION>
		<OPTION>Generic Realtek</OPTION>
		<OPTION>Generic Winbond</OPTION>
	</SELECT>
	&nbsp;&nbsp;if "Other": <INPUT TYPE=TEXT NAME="NetBrand" SIZE=16 VALUE="<? echo stripslashes($NetBrand);?>">

	&nbsp;&nbsp;&nbsp; Model:&nbsp;<INPUT TYPE=TEXT NAME="NetModel" SIZE=8 VALUE="<? echo stripslashes($NetModel);?>">



	&nbsp;&nbsp;&nbsp; Ethernet&nbsp;Address:&nbsp;<INPUT TYPE=TEXT NAME="MAC" SIZE=12 VALUE="<? echo $MAC;?>">

	&nbsp;&nbsp;&nbsp;<INPUT TYPE=SUBMIT NAME="Action" VALUE="Add NIC">


	</TD></TR><?
	} //End part not universally accessable.
	?></TABLE><?






/*--------------Call Ticket Stuff--------------*/



//Only Field Support & Help Desk have access to tickets!
if (($userdata["IsHelpDesk"]=="Y" || $userdata["IsFieldSupport"]=="Y" || $userdata["IsAdmin"]=="Y")& 1!=$OnlyInfo) {

	?><BR><BR><?
	$color=MakeTable(mysql_query("SELECT CallTicket.TicketID, concat('ticketstatus.php?TID=',CallTicket.TicketID) AS TicketIDPopup, PaperTrail.Comment AS Description, PaperTrail.Creation AS Created FROM CallTicket,PaperTrail WHERE CallTicket.CampusID=$CID AND PaperTrail.TicketID=CallTicket.TicketID AND PaperTrail.IsFirst='Y' "),1,1,0,2,"Tickets");
	?><TR><TD colspan=3 BGCOLOR=#<? echo ($color==1)?"C8C8C8":"DFDFDF" ?>>
	<TABLE border=0>
	<TR>
		<TD rowspan=2 align=right><H2>Add&nbsp;<BR>Ticket&nbsp;</H2></TD>
		<TD>Description:</TD>
		<TD>Computer (if Applicable):</TD>
		<TD>&nbsp;</TD>
		<TD>Jack Number (if Applicable):</TD>
	</TR>
	<TR>
		<TD><TEXTAREA NAME="Description" COLS=32 ROWS=5></TEXTAREA>&nbsp;</TD>
		<TD valign=top>
			<SELECT NAME="CompSel">
				<OPTION VALUE="NULL">n/a</OPTION><?
				for ($i=0;$i<mysql_num_rows($ComputerList);$i++) {
				?><OPTION VALUE="<?
				echo mysql_result($ComputerList,$i,"Hidden");
				?>"><?
				echo mysql_result($ComputerList,$i,"Brand")." ".mysql_result($ComputerList,$i,"Line")." ".mysql_result($ComputerList,$i,"Model");
				?></OPTION><?
				} ?>
			</SELECT><BR>
			Location:<BR>
			<SELECT NAME="LocOpt"><?
			for ($i=0;$i<mysql_num_rows($MyRooms);$i++) {
				echo "<OPTION";
				if ((mysql_result($MyRooms,$i,1)==$Building) && (mysql_result($MyRooms, $i, 2)==$Room)) { ?> SELECTED<? }
				echo ">";
				echo mysql_result($MyRooms,$i,0)."  ".mysql_result($MyRooms,$i,2);
				echo "</OPTION>\n";
			}
			?>
			<OPTION>Other (entered below)</OPTION>
			</SELECT><BR>
			<INPUT TYPE=TEXT NAME="LocAlt" SIZE=24>
		</TD>
		<TD>&nbsp;</TD>
		<TD valign=top>
			<INPUT TYPE=TEXT NAME="Jack" SIZE=16>
			<? if (0==$CampusID) {
				?><BR>TID: <INPUT TYPE=TEXT NAME="TID" SIZE=5><BR>Created: <INPUT TYPE=TEXT NAME="CreateDate" SIZE=20 VALUE="2000-10-31 14:30:00"><BR><BR><?
			} else {
				?><BR><BR><BR><?
			} ?>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE=SUBMIT NAME="Action" VALUE="Add Ticket">
		</TD>
	</TR>
	</TABLE>
	</TD></TR></TABLE><?
} //end part only available to RCCs and Help Desk
	?>

	</FORM>
	<!--End-->

	<?

} else {
	echo "Oh shit...something really bad happened.";
}

include "footer.php";
db_logout($hdb);
?>
