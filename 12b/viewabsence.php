<?
/*
Title: View Absence
Author: James Taguchi
Description: Page for Viewing, claiming, and editing (admin only) absence records.
*/
include "config.php";
include "database.php";
include "functions.php";
include "absence_functions.php";
include "login.php";

$title = 'View Absence';
include "header.php";

MustLogIn();

?><TABLE border=0 width=100%><TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Absence Details<?=$cfont_title?></TD></TR><TR><TD><?

//If user clicked on Claim button, add claim record
if(isset($_POST['Action']) && $_POST['Action'] == 'Claim')
{
	$error = '';
//Double check to make sure no one has already claimed this absence while they were viewing the page
        $result = mysql_query("SELECT * FROM Absence_Cover WHERE RecordID = $RID");
        if(mysql_num_rows($result) > 0) {
                $error .= '<font color="FF0000">This absence has already been covered</font><br />';
        }

	//Check if absence is in the past
	$result = mysql_query("SELECT * FROM Absence_Records WHERE RecordID = $RID AND NOW() < EndTime");
	if(mysql_num_rows($result) < 1) {
		$error .= '<font color="FF0000">You cannot claim an absence from the past</font><br />';
	}

	//Check if the absence belongs to the user
	$result = mysql_query("SELECT * FROM Absence_Records WHERE RecordID = $RID AND CampusID = $CampusID");
	if(mysql_num_rows($result) > 0) {
		$error .= '<font color="FF0000">You cannot claim your own absence</font><br />'; 
	}

        if ($error == '') {
                mysql_query("INSERT INTO Absence_Cover (RecordID, CampusID, CreateTime) VALUES('$RID', '$CampusID', NOW())") or die(mysql_error());
        }
}

//If administrator clicks on delete button, delete record and all relevent data
if(isset($_POST['Action']) && $_POST['Action'] == 'Delete')
{
	mysql_query("DELETE FROM Absence_Data WHERE RecordID = $RID");
	mysql_query("DELETE FROM Absence_Records WHERE RecordID = $RID");
	mysql_query("DELETE FROM Absence_Cover WHERE RecordID = $RID");
}

//Find and display Absence Record
$result = mysql_query("
	SELECT
		".sql_nick("People.First","People.Nick","People.Last")." AS 'Name',
		DATE_FORMAT(StartTime, '%M %e, %Y %l:%i %p') AS 'StartTime',
		DATE_FORMAT(EndTime, '%M %e, %Y %l:%i %p') AS 'EndTime',
		DATE_FORMAT(CreateTime, '%M %e, %Y %l:%i %p') AS 'CreateTime',
		Notes,
		RecordID
	FROM
		Absence_Records,
		People
	WHERE
		Absence_Records.CampusID = People.CampusID AND
		RecordID = $RID
");

$info = mysql_fetch_array($result)
?>

<table border="0">
	<tr>
		<td><b>Name:</b></td>
		<td><?=$info['Name']?></td>
	</tr>
	<tr>
		<td><b>Starts at:</b></td>
		<td><?=$info['StartTime']?></td>
	</tr>
        <tr>
                <td><b>Ends at:</b></td>
                <td><?=$info['EndTime']?></td>
        </tr>
        <tr>
                <td><b>Submitted:</b></td>
                <td><?=$info['CreateTime']?></td>
        </tr>
        <tr>
                <td><b>Notes:</b></td>
                <td><?=$info['Notes']?></td>
        </tr>

	<tr><td colspan="2"><hr /></td></tr>

<?
//Search for claim record and display if found
$result = mysql_query("
	SELECT
		".sql_nick("People.First","People.Nick","People.Last")." AS 'Name',
		DATE_FORMAT(CreateTime, '%M %e, %Y %l:%i %p') AS 'CreateTime'
	FROM
		Absence_Cover,
		People
	WHERE
		Absence_Cover.CampusID = People.CampusID AND
		RecordID = $RID
");

if($info = mysql_fetch_array($result))
{
?>
        <tr>
		<td><b>Coverage:</b></td>
		<td><font color="00AA00">Claimed</font></td>
	</tr>
	<tr>
		<td><b>Covered by:</b></td>
		<td><?=$info['Name']?></td>
	</tr>
	<tr>
		<td><b>Claim Date:</b></td>
		<td><?=$info['CreateTime']?></td>
	</tr>
</table>	
<?
}
else
{
?>
	<tr>
		<td><b>Coverage:</b></td>
		<td><font color="FF0000">Unclaimed</font></td>
	</tr>
</table>
	<form method="POST"><input type="hidden" name="Action" value="Claim" /><input type="submit" value="Claim This Absence" /></form>
<? echo $error; ?>
	By clicking the above button, you agree to be at the helpdesk at all times specified above in the poster's absence.<br />
	Remember that helpdesk rules still apply. This does not grant you a shift of over 8 hours in a day, or over 20 hours a week during the school year. You will work the claimed shift as if they were assigned to you normally.<br />

<?
}

if('Y'==$userdata["IsAdmin"]) { ?>
	<br />Administrative: <form method="POST"><input type="hidden" name="Action" value="Delete" /><input type="submit" value="Delete Record" /></form>
<?
}
?>

</TD></TR></TABLE>

<?
include "footer.php";
db_logout($hdb);
?>
