<?
//Cron Job file - this is automatically run nightly (around 4am suggested)
include "config.php";
include "database.php";
include "functions.php";
$loginIncluded="Y"; //trick login requirement
$userdata["IsAdmin"]="Y";

//Are there timecards to be generated?
$TCsDue=mysql_query("
	SELECT
		TimeCard,PeriodID
	FROM
		TimeCards_Periods
	WHERE
		DATE_ADD(CURDATE(),INTERVAL 1 DAY) = Due
");
//		DATE_ADD(CURDATE(),INTERVAL 1 DAY) = Due
MakeTable($TCsDue,1,1,1,1,"");
for ($ThisTC=0;mysql_num_rows($TCsDue)>$ThisTC;$ThisTC++) { //there are timecards due...
	//Who do we need to make time cards for?
	$Members=mysql_query("
		SELECT
			CampusID
		FROM
			TimeCards_Members
		WHERE
			TimeCard=".mysql_result($TCsDue,$ThisTC,0)
	);
	for ($Who=0;mysql_num_rows($Members)>$Who;$Who++) { //make timecards
		$IdNum=mysql_result($Members,$Who,0);
		$TC=mysql_result($TCsDue,$ThisTC,0);
		$PayP=mysql_result($TCsDue,$ThisTC,1);
		$Automatic=1;
		$Action="New TC";
		echo "for $IdNum...";
		include("timecards.php");
	}
}
?>
