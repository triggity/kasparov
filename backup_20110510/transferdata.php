<?
include "config.php";
include "database.php";
include "functions.php";

include "login.php";

$title = "Edit My Info"; //Set Page Title

include "header.php";


MustLogIn(1);



/*

$x=mysql_query("SELECT CampusID, Time, Position+0 AS Pos FROM Schedule");

for ($i=0;$i<mysql_num_rows($x);$i++) {

	mysql_query("INSERT INTO Schedule_Data(CampusID,Time,Schedule,Day) VALUES (".mysql_result($x,$i,"CampusID").",".(((int)(mysql_result($x,$i,"Time")/7)>13)?((((int)(mysql_result($x,$i,"Time")/7))*2)-20):((((int)(mysql_result($x,$i,"Time")/7)+48)*2)-20)).",".((mysql_result($x,$i,"Pos")==1)?("-1"):((mysql_result($x,$i,"Pos")==2)?("4"):((mysql_result($x,$i,"Pos")==3)?("3"):((mysql_result($x,$i,"Pos")==4)?("2"):5)))).",".(mysql_result($x,$i,"Time")%7).")");
	mysql_query("INSERT INTO Schedule_Data(CampusID,Time,Schedule,Day) VALUES (".mysql_result($x,$i,"CampusID").",".(((int)(mysql_result($x,$i,"Time")/7)>13)?((((int)(mysql_result($x,$i,"Time")/7))*2)-19):((((int)(mysql_result($x,$i,"Time")/7)+48)*2)-19)).",".((mysql_result($x,$i,"Pos")==1)?("-1"):((mysql_result($x,$i,"Pos")==2)?("4"):((mysql_result($x,$i,"Pos")==3)?("3"):((mysql_result($x,$i,"Pos")==4)?("2"):5)))).",".(mysql_result($x,$i,"Time")%7).")");
	echo "\n<BR><BR>\n";

}


*/

/*

$x=mysql_query("SELECT * FROM Users");

for ($i=0;$i<mysql_num_rows($x);$i++) {


	?>User: <?=mysql_result($x,$i,"CampusID")?><BR><?
	if (mysql_result($x,$i,"TA")=="Y") { //LabTAs
		$y=mysql_query("INSERT INTO Schedule_Permissions(CampusID,Schedule,Flags) VALUES (".mysql_result($x,$i,"CampusID").",2,'Member,View')");
		$y=mysql_query("INSERT INTO Schedule_Permissions(CampusID,Schedule,Flags) VALUES (".mysql_result($x,$i,"CampusID").",3,'Member,View')");
	} else {
		$y=mysql_query("INSERT INTO Schedule_Permissions(CampusID,Schedule,Flags) VALUES (".mysql_result($x,$i,"CampusID").",2,'View')");
		$y=mysql_query("INSERT INTO Schedule_Permissions(CampusID,Schedule,Flags) VALUES (".mysql_result($x,$i,"CampusID").",3,'View')");
	}

	if (mysql_result($x,$i,"HelpDesk")=="Y" || mysql_result($x,$i,"FieldSupport")=="Y") { //Helpdesk
		$y=mysql_query("INSERT INTO Schedule_Permissions(CampusID,Schedule,Flags) VALUES (".mysql_result($x,$i,"CampusID").",4,'Member,View')");
	} else {
		$y=mysql_query("INSERT INTO Schedule_Permissions(CampusID,Schedule,Flags) VALUES (".mysql_result($x,$i,"CampusID").",4,'View')");
	}

	if (mysql_result($x,$i,"FieldSupport")=="Y") { //Helpdesk
		$y=mysql_query("INSERT INTO Schedule_Permissions(CampusID,Schedule,Flags) VALUES (".mysql_result($x,$i,"CampusID").",5,'Member,View')");
	} else {
		$y=mysql_query("INSERT INTO Schedule_Permissions(CampusID,Schedule,Flags) VALUES (".mysql_result($x,$i,"CampusID").",5,'View')");
	}

}
*/

/*
//Get time cards ready
$x=mysql_query("SELECT * FROM Users");

for ($i=0;$i<mysql_num_rows($x);$i++) {


	?>User: <?=mysql_result($x,$i,"CampusID")?><BR><?
	if (mysql_result($x,$i,"TA")=="Y") { //LabTAs
		$y=mysql_query("INSERT INTO TimeCards_Members(CampusID,TimeCard) VALUES (".mysql_result($x,$i,"CampusID").",1)");
	} else if (mysql_result($x,$i,"HelpDesk")=="Y" && mysql_result($x,$i,"FieldSupport")=="N"){
		$y=mysql_query("INSERT INTO TimeCards_Members(CampusID,TimeCard) VALUES (".mysql_result($x,$i,"CampusID").",1)");
	}
}
*/
include "footer.php";
db_logout($hdb);
?>
