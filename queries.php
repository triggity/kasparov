<?
include "config.php";
include "database.php";
include "functions.php";

include "login.php";

include "header.php";

if ($QID=="" || $QID<1) {$QID=1;} 

//Are we allowed to run this query as a non admin? 
$test=mysql_query("SELECT QueryID FROM Queries WHERE QueryID=$QID AND NonAdminSafe='Y'");


if (mysql_num_rows($test)>0) {
	MustLogIn(0);
} else {
	MustLogIn(1);
}	

$query=mysql_query("SELECT Query,Description,IsPHPCode,FirstLimit,RestLimit,ProtectedVars FROM Queries WHERE QueryID=$QID");


if ("Y"==mysql_result($query,0,2)) {
	eval(mysql_result($query,0,0));
} else {
	$query_string=mysql_result($query,0,0);
}



$preserved="";
$protectedvars=explode(" ",mysql_result($query,0,5));
if (count($protectedvars)) {
	for ($i=0;$i<count($protectedvars);$i++) {
		if (strlen($GLOBALS[$protectedvars[$i]])>0) {
			$preserved.='&'.urlencode($protectedvars[$i]).'='.urlencode($GLOBALS[$protectedvars[$i]]);
		}
	}
};

$mylimit=0; //clear the variable
//enforce limits if limit is > 0
if (mysql_result($query,0,3)>0) {
	if ($startpoint>0) { //determine how many items to show
		$mylimit=mysql_result($query,0,4)+1;
	} else {
		$mylimit=mysql_result($query,0,3)+1;
		$startpoint=0;
	}
	$query_string.= " LIMIT $startpoint,$mylimit ";
}

$query_result=mysql_query($query_string);
//echo ($query_string);
MakeTable($query_result,1,1,1,3,mysql_result($query,0,1)." &nbsp;&nbsp;&nbsp;(".mysql_num_rows($query_result).")");

//Add prev/next buttons if applicable
?><TABLE border=0 WIDTH=100%><TR><TD align=left>
<?

if ($startpoint>0) { //we aren't at the first entry
	$next=$startpoint-mysql_result($query,0,4);
	if ($next<0) $next=0; //Can't let start start value be negative
	?><A HREF="queries.php?SID=<?=$SID?>&QID=<?=$QID?>&startpoint=<?
		echo ($next.$preserved);
	?>"><-- Previous Page</A><?
}

?></TD><TD align=right><?

if (mysql_num_rows($query_result)==$mylimit && $mylimit>0) { //we aren't at the first entry
	$startpoint+=mysql_result($query,0,4);
	if ($startpoint<0) $startpoint=0; //Can't let start start value be negative
	?><A HREF="queries.php?SID=<?=$SID?>&QID=<?=$QID?>&startpoint=<?=$startpoint.$preserved?>">Next Page--></A><?
}

?></TD></TR></TABLE><?
include "footer.php";
db_logout($hdb);
?>
