<?
if($loginIncluded !="Y"){
function MustLogin ($MustBeAdmin = 0) {

	
	if ($GLOBALS["IsLoggedIn"] == 0) {
		?><H1>Login Invalid</H1><BR>To access this feature you must first be logged in with a valid username and password. Perhaps you mistyped your username or password. Please try again. If you think this message is in error, please contact <A HREF="mailto:<?
		echo $GLOBALS["admin_email"]."\">".$GLOBALS["admin_name"]."</A>";
		?>.<BR><BR> You may log in by going <A HREF="index.php">here</A>.<?
		include "footer.php";
		db_logout($GLOBALS["hdb"]);
		exit;
	} else if ($MustBeAdmin==1 && $GLOBALS["userdata"]["IsAdmin"]=='N') {
		?><H1>Insufficient Privledges</H1><BR>To access this feature you must first be logged in with a valid username and password that has administrator privledges. <?
		include "footer.php";
		db_logout($GLOBALS["hdb"]);
		exit;
		
	}

}


 function FriendlyName($First,$Nick,$Last) {
	if($Nick==""){
		$fulluname=$First." ".$Last;
	} else {
		$fulluname=$Nick." ".$Last;
	}
	return $fulluname;
}

function GetUserInfo($CampID) {
	//Get User's Information
	$x= mysql_query("SELECT People.First AS First, People.Nick AS Nick,People.Last AS Last, Users.ITStaff AS IsIT, Users.LINCStaff AS IsLINC, Users.FieldSupport AS IsFieldSupport, Users.HelpDesk AS IsHelpDesk, Users.Administrator AS IsAdmin, Users.TA AS IsTA, Users.UserName AS UserName, Users.BoardBits AS BoardBits, Users.lastlogin AS LastLogin, Users.thislogin AS LatestLogin, Users.BoardAdmin AS BoardAdmin FROM People, Users WHERE People.CampusID=$CampID AND Users.CampusID=People.CampusID");

	//also, find what Schedule's we're on
	$GLOBALS["MySchedules"]=mysql_query("SELECT Name, ID, TimeQuantum, Holiday, ColorCode,DayStart,DayEnd,Flags+0 as Flg FROM Schedule_Permissions, Schedule_Info WHERE CampusID='$CampID' AND Schedule=ID ORDER BY TimeQuantum DESC, ID");


	return mysql_fetch_assoc($x);
}

$IsLoggedIn=0;
$CampusID=-1; //default is to be no user

//Clear out expired sessions & lock from others clearing sessions...

$x=mysql_query("DELETE FROM Sessions WHERE IF((RemoteIP & (0xFFFFFC00))=0x81D2C400,timestamp < SUBDATE(NOW(), INTERVAL 90 MINUTE),timestamp < SUBDATE(NOW(), INTERVAL 20 MINUTE))") or die("Could not delete expired sessions"); 

//Verify username & password are valid, OR if SID!=0 then verify the session ID
$SID=($SID+1)-1; //force SID to be a number
if (0==$SID && ""!=$uname) {
//Validate uname & Pass, and setup SID
	$x=mysql_query($q = "SELECT CampusID FROM Users WHERE UserName='".$uname."' AND Password=PASSWORD('".$FORTYTWO."')");
//echo $q;
	//If username and password are valid create a new session, or revive an existing one
	if(mysql_num_rows($x)>0){

		$CampusID=mysql_result($x,0,0);
		$z=mysql_query("SELECT SessionID FROM Sessions WHERE CampusID=".$CampusID." AND RemoteIP='".iptoint($REMOTE_ADDR)."'");
		
		if (mysql_num_rows($z)>0) { //Are we already logged in?
			$SID=mysql_result($z,0,0);
			$y=mysql_query("UPDATE Sessions SET timestamp=NULL WHERE SessionID=".$SID); //Update Time Stamp
		} else { //If not create a session ID for us.
			$CampusID=mysql_result($x,0,0);
			$y=mysql_query("INSERT INTO Sessions(CampusID, RemoteIP) VALUES (".$CampusID.",'".iptoint($REMOTE_ADDR)."')");
			$SID=mysql_insert_id();
			$y=mysql_query("UPDATE Users SET lastlogin=thislogin WHERE CampusID=$CampusID");
			$y=mysql_query("UPDATE Users SET thislogin=NOW() WHERE CampusID=$CampusID");
		}
		$IsLoggedIn=1;
	} else if (""!=$FORTYTWO) { // login failed! - only care is password was sent
		$x=mysql_query("INSERT INTO LoginFailures (IP,Trying) VALUES ('".iptoint($REMOTE_ADDR)."','$uname')");
	}
		 
} else if ($SID > 0) {
//Validate SID
	$x=mysql_query("SELECT CampusID FROM Sessions WHERE SessionID=".$SID." AND RemoteIP='".iptoint($REMOTE_ADDR)."'");
	if(mysql_num_rows($x)>0){
		$IsLoggedIn=1;
		$CampusID=mysql_result($x,0,0);
		$y=mysql_query("UPDATE Sessions SET timestamp=NULL WHERE SessionID=".$SID); //Update Time Stamp
	}
}

//Check to see if logged in
if(1==$IsLoggedIn){
		
	//Get User's Information
	//$x=GetUserInfo($CampusID);
	
	//$userdata=mysql_fetch_assoc($x);
	$userdata=GetUserInfo($CampusID);


	$fulluname=FriendlyName($userdata["First"], $userdata["Nick"], $userdata["Last"]);

}

}
$loginIncluded="Y";



?>
