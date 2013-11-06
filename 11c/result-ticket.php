<?
//this is called from queries.php

//Adds "\" for "%" and "_" characters so they don't confuse a "LIKE" SQL search
function LikeSlashes($text) {

	return ereg_replace("_","\_",ereg_replace("%","\%",$text));

}

//Add a text field that we're searching to the Query, keeping in mind 
function QueryText($field, $SQL) {

	switch ($GLOBALS["$field-Opts"]) {
		case "I":
			return " AND $SQL='".$GLOBALS[$field]."' ";
		case "A":
			return " AND $SQL LIKE '%".LikeSlashes($GLOBALS[$field])."%' ";
		case "B":
			return " AND $SQL LIKE '".LikeSlashes($GLOBALS[$field])."%' ";
		case "E":
			return " AND $SQL LIKE '%".LikeSlashes($GLOBALS[$field])."' ";
		case "L":
			return " AND $SQL LIKE '".$GLOBALS[$field]."' ";
		case "R":
			return " AND $SQL REGEXP '".stripslashes($GLOBALS[$field])."' ";
	}
	return "";
}

function QueryList($field, $SQL) {
	if ($GLOBALS[$field]=="Don\\'t Care" || $GLOBALS[$field]=="") {
		return "";
	} else {
		return " AND $SQL='".$GLOBALS[$field]."' ";
	}
}

function QueryDate($field, $SQL, $Seconds="00", $Compare="=") {
//	echo $GLOBALS[$field."-Opts"]."--I<BR>";
	if ($GLOBALS[$field."-Opts"]=="I") {
		return " AND ".$SQL.$Compare."'".$GLOBALS[$field."-Year"]."-".$GLOBALS[$field."-Month"]."-".$GLOBALS[$field."-Day"]." ".(($GLOBALS[$field."-HalfDay"]=="AM")?$GLOBALS[$field."-Hour"]:($GLOBALS[$field."-Hour"]+12)).":".$GLOBALS[$field."-Minute"].":".$Seconds."' ";
	} else {
		return "";
	}
}

$FROM="PaperTrail AS t1, PaperTrail AS t2";

$WHERE="t1.TicketID=t2.TicketID AND t1.IsFirst='Y' AND t2.IsLast='Y'";

$IncPerson=0;
$IncTicket=0;
$IncComp=0;
$IncNIC=0;
$IncPaper=0;

//Client
if ($WHERE!=($WHERE.=QueryText("First","p.First"))) { $IncPerson=1; }

if ($WHERE!=($WHERE.=QueryText("Last","p.Last"))) { $IncPerson=1; }

if ($WHERE!=($WHERE.=QueryText("Nick","p.Nick"))) { $IncPerson=1; }

if ($WHERE!=($WHERE.=QueryText("Middle","p.Middle"))) { $IncPerson=1; }

if ("I"==$GLOBALS["CID-Opts"]) {$WHERE.=" AND p.CampusID='$CID' "; $IncPerson=1; }

if ($WHERE!=($WHERE.=QueryList("Student","p.Student"))) { $IncPerson=1; }

if ($WHERE!=($WHERE.=QueryList("Faculty","p.Faculty"))) { $IncPerson=1; }


//Location
if ($WHERE!=($WHERE.=QueryText("Location","ct.Location"))) { $IncTicket=1; }


//Jack
if ($WHERE!=($WHERE.=QueryText("Jack","ct.JackID"))) { $IncTicket=1; }


//Computer
if ($WHERE!=($WHERE.=QueryText("CBrand","c.Brand"))) { $IncComp=1; }

if ($WHERE!=($WHERE.=QueryText("CLine","c.Line"))) { $IncComp=1; }

if ($WHERE!=($WHERE.=QueryText("CModel","c.Model"))) { $IncComp=1; }

if ($WHERE!=($WHERE.=QueryList("OS","c.OS"))) { $IncComp=1; }

if ($WHERE!=($WHERE.=QueryText("Version","c.OSVer"))) { $IncComp=1; }


//NIC
if ($WHERE!=($WHERE.=QueryText("NBrand","n.Brand"))) { $IncNIC=1; }

if ($WHERE!=($WHERE.=QueryText("NLine","n.Line"))) { $IncNIC=1; }

if ($WHERE!=($WHERE.=QueryText("NModel","n.Model"))) { $IncNIC=1; }


//First Entry In PaperTrail
$WHERE.=QueryText("Desc1","t1.Comment");
$WHERE.=QueryList("Dept1","t1.Department");
$WHERE.=QueryDate("From1","t1.Creation","00",">");
$WHERE.=QueryDate("Till1","t1.Creation","59","<");
if ("I"==$GLOBALS["CCID1-Opts"]) {$WHERE.=" AND t1.Creator_CampusID='$CCID1' ";}



//Last Entry In PaperTrail
$WHERE.=QueryText("Desc2","t2.Comment");
$WHERE.=QueryList("Dept2","t2.Department");
$WHERE.=QueryList("State2","t2.State");
$WHERE.=QueryDate("From2","t2.Creation","00",">");
$WHERE.=QueryDate("Till2","t2.Creation","59","<");
if ("I"==$GLOBALS["CCID2-Opts"]) {$WHERE.=" AND t2.Creator_CampusID='$CCID2' ";}


//Any Entry In PaperTrail
if ($WHERE!=($WHERE=$WHERE.QueryText("Desc3","t3.Comment").QueryList("Dept3","t3.Department").QueryDate("From3","t3.Creation","00",">").QueryDate("Till3","t3.Creation","59","<").(("I"==$GLOBALS["CCID3-Opts"])?" AND t3.Creator_CampusID='$CCID3' ":""))) {$IncPaper=1;}



if (1==$IncNIC) {
	$FROM.=", NICs as n ";
	$WHERE.=" AND n.CampusID=p.CampusID ";
	$IncPerson=1;
}

if (1==$IncPerson) {

	$FROM.=", People as p ";
	$WHERE.=" AND ct.CampusID=p.CampusID ";
	$IncTicket=1;

}

if (1==$IncComp) {

	$FROM.=", Computer as c ";
	$WHERE.=" AND ct.ComputerID=c.ComputerID ";
	$IncTicket=1;

}

if (1==$IncTicket) {

	$FROM.=", CallTicket as ct ";
	$WHERE.=" AND ct.TicketID=t1.TicketID ";

}

if (1==$IncPaper) {

	$FROM.=", PaperTrail as t3 ";
	$WHERE.=" AND t3.TicketID=t1.TicketID GROUP BY TicketID";

}


//Assemble SQL Query
$query_string="SELECT t1.TicketID AS TicketID, concat('ticketstatus.php?TID=',t1.TicketID) as TIDPopup, t1.Comment AS Description FROM ".$FROM." WHERE ".$WHERE;

//for testing:
//echo $query_string;
?>