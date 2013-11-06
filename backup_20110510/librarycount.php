<?
/*
Title: Library Count
Author: James Taguchi
Description: Page for reporting number of computers being used in the library after this policy went in on 2/2/2010
Notes: After the new policy, this system had to be made as soon as possible. Most of it was written over a weekend. As a result, this code is a mess. Variables don't match up because difference pieces of code were taken from different sources, the flow is confusing and inefficient. Sorry to whoever had to modify this code later.
*/
include "config.php";
include "database.php";
include "functions.php";
include "login.php";

$title = 'Library Count';
include "header.php";

MustLogIn();

define("MAX_BasementPC", 24);
define("MAX_BasementMAC", 4);
define("MAX_FirstPC", 30);
define("MAX_FirstMAC", 4);
define("MAX_Second203MAC", 30);
define("MAX_Second205MAC", 45);
define("MAX_Second206PC", 14);
define("MAX_SecondMAC", 4);

//generate times
$currentHr = date("G");
$currentMin = date("i");

if ($currentMin >= 30) {
        $currentHr++;
}
if ($currentHr == 24) {
        $currentHr = 0;
}
$currentMin = '00';

$currentDate = date("Y-m-d H:i:s", mktime($currentHr,0,0,date("m"),date("d"),date("Y")));
$prevData = NULL; //will be used when someone overwrites previous entry

$FirstPCInput = (int)$_POST['FirstPCInput'];
$FirstMACInput = (int)$_POST['FirstMACInput'];
$BasementPCInput = (int)$_POST['BasementPCInput'];
$BasementMACInput = (int)$_POST['BasementMACInput'];
$Second203MACInput = (int)$_POST['Second203MACInput'];
$Second205MACInput = (int)$_POST['Second205MACInput'];
$Second206PCInput = (int)$_POST['Second206PCInput'];
$SecondMACInput = (int)$_POST['SecondMACInput'];

if(isset($_POST['Action']) && $_POST['Action']=='ReportCurrent') //run if user submitted new entry
{
	if ($currentHr != $_POST['lastHr'])
	{
	?>
		<script language="javascript">
		        alert('The submission timexframe has changed from when the last page was loaded. Please double check the time. Use the lower form if this submission was for the last timeframe.');
		</script>
	<?	
	}
	else
	{
		$cur_errors = '';

		//Check for negative values and set (max - input)
		if ($FirstPCInput < 0 && $FirstPCInput >= -(MAX_FirstPC))
			$FirstPCInput = MAX_FirstPC + $FirstPCInput;
                if ($FirstMACInput < 0 && $FirstMACInput >= -(MAX_FirstMAC))
                        $FirstMACInput = MAX_FirstMAC + $FirstMACInput;
                if ($BasementPCInput < 0 && $BasementPCInput >= -(MAX_BasementPC))
                        $BasementPCInput = MAX_BasementPC + $BasementPCInput;
                if ($BasementMACInput < 0 && $BasementMACInput >= -(MAX_BasementMAC))
                        $BasementMACInput = MAX_BasementMAC + $BasementMACInput;
                if ($Second203MACInput < 0 && $Second203MACInput >= -(MAX_Second203MAC))
                        $Second203MACInput = MAX_Second203MAC + $Second203MACInput;
                if ($Second205MACInput < 0 && $Second205MACInput >= -(MAX_Second205MAC))
                        $Second205MACInput = MAX_Second205MAC + $Second205MACInput;
                if ($Second206PCInput < 0 && $Second206PCInput >= -(MAX_Second206PC))
                        $Second206PCInput = MAX_Second206PC + $Second206PCInput;
                if ($SecondMACInput < 0 && $SecondMACInput >= -(MAX_SecondMAC))
                        $SecondMACInput = MAX_SecondMAC + $SecondMACInput;

		if (!($FirstPCInput >= 0 && $FirstPCInput <= MAX_FirstPC) ||
		    !($FirstMACInput >= 0 && $FirstMACInput <= MAX_FirstMAC) ||
		    !($BasementPCInput >= 0 && $BasementPCInput <= MAX_BasementPC) ||
		    !($BasementMACInput >= 0 && $BasementMACInput <= MAX_BasementMAC) ||
		    !($Second203MACInput >= 0 && $Second203MACInput <= MAX_Second203MAC) ||
		    !($Second205MACInput >= 0 && $Second205MACInput <= MAX_Second205MAC) ||
		    !($Second206PCInput >= 0 && $Second206PCInput <= MAX_Second206PC) ||
		    !($SecondMACInput >= 0 && $SecondMACInput <= MAX_SecondMAC)) {
			$cur_errors .= '<br />Out of range';
		}
		
		if ($cur_errors == '') {
			$query = mysql_query("SELECT ".sql_nick("People.First","People.Nick","People.Last")." AS 'Name', Time, BasementPC, BasementMAC, FirstPC, FirstMAC, Second203MAC, Second205MAC, Second206PC, SecondMAC FROM LibraryCounts, People WHERE LibraryCounts.CampusID = People.CampusID AND Time = '$currentDate'");
			if (mysql_num_rows($query) > 0) {
				$prevData = $query;

				mysql_query("UPDATE LibraryCounts SET CampusID = '$CampusID', FirstPC = '$FirstPCInput', FirstMAC = '$FirstMACInput', BasementPC = '$BasementPCInput', BasementMAC = '$BasementMACInput', Second203MAC = '$Second203MACInput', Second205MAC = '$Second205MACInput', Second206PC = '$Second206PCInput', SecondMAC = '$SecondMACInput', CreateTime = NOW(), Approved = '1' WHERE Time = '$currentDate'") or die(mysql_error());
			}
			else {
				mysql_query("INSERT INTO LibraryCounts (CampusID, Time, FirstPC, FirstMAC, BasementPC, BasementMAC, Second203MAC, Second205MAC, Second206PC, SecondMAC, CreateTime, Approved) VALUES('$CampusID', '$currentDate', '$FirstPCInput', '$FirstMACInput', '$BasementPCInput', '$BasementMACInput', '$Second203MACInput', '$Second205MACInput', '$Second206PCInput', '$SecondMACInput', NOW(), '1')") or die(mysql_error());
			}
		}
	}
}

if(isset($_POST['Action']) && $_POST['Action']=='ReportLate') //run if user submitted late
{

	$inputHr = $_POST['inputhr'];
        $inputMin = $_POST['inputmin'];
	$late_errors = '';
		
	        if(isset($date)) {//Are we using the javascript version?
                        $inputDate = $date;
                }
                else {//No javascript
			if($inputmonth >= 1 && $inputmonth <= 12 && $inputday >= 1 && $inputday <= 31)
	                        $inputDate = $inputyear.'-'.$inputmonth.'-'.$inputday;
			else
				$late_errors .= '<br />Invalid date';
                }

                $inputTime = "$inputDate $inputHr:$inputMin:00";
		if ((strtotime("$inputDate $inputHr:$inputMin:00") + (30*60)) >= time()) {
			$late_errors = "You can't predict the future!";
		}

                //Check for negative values and set (max - input)
                if ($FirstPCInput < 0 && $FirstPCInput >= -(MAX_FirstPC))
                        $FirstPCInput = MAX_FirstPC + $FirstPCInput;
                if ($FirstMACInput < 0 && $FirstMACInput >= -(MAX_FirstMAC))
                        $FirstMACInput = MAX_FirstMAC + $FirstMACInput;
                if ($BasementPCInput < 0 && $BasementPCInput >= -(MAX_BasementPC))
                        $BasementPCInput = MAX_BasementPC + $BasementPCInput;
                if ($BasementMACInput < 0 && $BasementMACInput >= -(MAX_BasementMAC))
                        $BasementMACInput = MAX_BasementMAC + $BasementMACInput;
                if ($Second203MACInput < 0 && $Second203MACInput >= -(MAX_Second203MAC))
                        $Second203MACInput = MAX_Second203MAC + $Second203MACInput;
                if ($Second205MACInput < 0 && $Second205MACInput >= -(MAX_Second205MAC))
                        $Second205MACInput = MAX_Second205MAC + $Second205MACInput;
                if ($Second206PCInput < 0 && $Second206PCInput >= -(MAX_Second206PC))
                        $Second206PCInput = MAX_Second206PC + $Second206PCInput;
                if ($SecondMACInput < 0 && $SecondMACInput >= -(MAX_SecondMAC))
                        $SecondMACInput = MAX_SecondMAC + $SecondMACInput;

                if (!($FirstPCInput >= 0 && $FirstPCInput <= MAX_FirstPC) ||
                    !($FirstMACInput >= 0 && $FirstMACInput <= MAX_FirstMAC) ||
                    !($BasementPCInput >= 0 && $BasementPCInput <= MAX_BasementPC) ||
                    !($BasementMACInput >= 0 && $BasementMACInput <= MAX_BasementMAC) ||
                    !($Second203MACInput >= 0 && $Second203MACInput <= MAX_Second203MAC) ||
                    !($Second205MACInput >= 0 && $Second205MACInput <= MAX_Second205MAC) ||
                    !($Second206PCInput >= 0 && $Second206PCInput <= MAX_Second206PC) ||
                    !($SecondMACInput >= 0 && $SecondMACInput <= MAX_SecondMAC)) {
                        $cur_errors .= '<br />Out of range';
                }

	if ($late_errors == '')
	{
		$query = mysql_query("SELECT Approved FROM LibraryCounts WHERE Time = '$inputTime'");
		if (mysql_num_rows($query) > 0)
		{
			$row = mysql_fetch_array($query, MYSQL_ASSOC);
			if ($row['Approved'] == 1) {
				$late_errors .= 'There is already a submission for this time';
			}
			else {
				$late_errors .= 'There is a submission pending administrator approval for this time. The administrator must reject the old submission for a new one to be submitted';
			}
		}
		else
		{
			 mysql_query("INSERT INTO LibraryCounts (CampusID, Time, FirstPC, FirstMAC, BasementPC, BasementMAC, Second203MAC, Second205MAC, Second206PC, SecondMAC, CreateTime, Approved) VALUES('$CampusID', '$inputTime', '$FirstPCInput', '$FirstMACInput', '$BasementPCInput', '$BasementMACInput', '$Second203MACInput', '$Second205MACInput', '$Second206PCInput', '$SecondMACInput', NOW(), '0')") or die(mysql_error());
			$late_errors .= 'Submission Successful';
		}
	}
}

if ((isset($cur_errors) && $cur_errors == '') || (isset($late_errors) && $late_errors == ''))
{
	?>
	<TABLE border=0 width=100%><TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Library Count : Submission Confirmation<?=$cfont_title?></TD></TR><TR><TD>
	Submission Successful<br />
	<br />
	<?
	if ($prevData != NULL)
	{
		$prevRow = mysql_fetch_array($prevData, MYSQL_ASSOC);
		?>
		The previous entry for <?=$prevRow['Time']?> submitted by <?=$prevRow['Name']?> has been replaced with your submission:<br />
                1st Floor PC: <?=$FirstPCInput?><br />
                1st Floor Mac: <?=$FirstMACInput?><br />
                Basement PC: <?=$BasementPCInput?><br />
                Basement Mac: <?=$BasementMACInput?><br />
                2nd Floor 203: <?=$Second203MACInput?><br />
                2nd Floor 205: <?=$Second205MACInput?><br />
                2nd Floor 206: <?=$Second206PCInput?><br />
                2nd Floor Mac: <?=$SecondMACInput?><br />		
		<br />
		<?
	}
	else
	{
		?>
	        1st Floor PC: <?=$FirstPCInput?><br />
		1st Floor Mac: <?=$FirstMACInput?><br />
		Basement PC: <?=$BasementPCInput?><br />
		Basement Mac: <?=$BasementMACInput?><br />
		2nd Floor 203: <?=$Second203MACInput?><br />
		2nd Floor 205: <?=$Second205MACInput?><br />
		2nd Floor 206: <?=$Second206PCInput?><br />
		2nd Floor Mac: <?=$SecondMACInput?><br />
		<br />
		<?
	}
?>
<a href="librarycount.php?SID=<?=$SID?>">Return to Library Count >></a>
<?
}

else
{
?>
<TABLE border=0 width=100%><TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Library Count<?=$cfont_title?></TD></TR><TR><TD>
<?

$today = getdate(time() - (5 * 60 * 60)); //subtract 5 hours to get around date changing issue
$tomorrow = getdate(time() + (19 * 60 * 60));

	$todayfrom = date("Y-m-d H:i:s", mktime(7,0,0,$today['mon'],$today['mday'],$today['year']));
	$todayto = date("Y-m-d H:i:s", mktime(3,0,0,$tomorrow['mon'],$tomorrow['mday'],$tomorrow['year']));
$query = mysql_query("SELECT Time, BasementPC, BasementMAC, FirstPC, FirstMAC, Second203MAC, Second205MAC, Second206PC, SecondMAC, DATE_FORMAT(Time, '%k:%i') as daytime FROM LibraryCounts WHERE Time > '$todayfrom' AND Time < '$todayto' AND Approved = '1' ORDER BY Time ASC");
$entries = mysql_num_rows($query);
$currentRow = 0;

$data[0][0] = '';
$data[1][0] = 'Helped';

for ($t = 1; $t < 20; $t++)
{
	$curtime = ($t + 7) % 24 . ':00';
	$data[0][$t] = (($t + 7) % 12 == 0 ? '12' : ($t + 7) % 12) . ':00';
        if ($currentRow > $entries)
        {
                $skipfetch = 1;
        }

        if ($skipfetch != 1)
        {
                $row = mysql_fetch_array($query, MYSQL_BOTH);
                $currentRow++;
        }

        if ($row['daytime'] == $curtime)
        {
		for ($u = 1; $u < 9; $u++)
			$data[$u][$t] = $row[$u];
                $skipfetch = 0;
        }
        else {
                for ($u = 1; $u < 9; $u++)
                        $data[$u][$t] = 'x';
                $skipfetch = 1;
        }
}
?>

<table border="1" width="100%">
<?
for ($i = 0; $i < 2; $i++)
{
?>
	<tr>
	<?
	for ($j = 0; $j < 20; $j++)
	{
	?>
		<td align="right"><?=$data[$i][$j]?></td>
	<?
	}
	?>
	</tr>
<?
}
?>
</table>

<br />
<table border="0">
<tr>
<td width="50%" valign="top">
<TABLE border=0 width=100%><TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Submission Form for Current Period<?=$cfont_title?></TD></TR><TR><TD>
Please enter the number of computers currently being used. (Not the number of people using computers!)<br />

<br />
<b>Current submission time: <?=$currentHr.':'.$currentMin;?></b>
<?
$query = mysql_query("SELECT * FROM LibraryCounts WHERE Time = '$currentDate'");
if (mysql_num_rows($query) > 0)
{
	$row = mysql_fetch_array($query, MYSQL_ASSOC);
	$FirstPC = $row['FirstPC'];
	
?>
	<br />
	<font color="FF0000">Warning: There is already a record for this time!<br />Using this form will overwrite the previous entry. Please resubmit only to correct errors.</font><br />
<?
}
if (isset($cur_errors) && $cur_errors != '')
        echo $cur_errors;
?>
<br /><br />
<form method="POST">
        <table border="0" style="margin-left:auto; margin-right:auto" width="80%">
        <tr>
                <th colspan="2">How many people you have helped></th>
                
        </tr>
        <tr>
                <td align="right">number:</td>
                <td><input type="text" name="FirstPCInput" size="3" maxlength="3" align="right" value="<?=$FirstPC?>"/> (0 - <?=MAX_FirstPC?>)</td>

                       </tr>
        <tr>
        <tr>
                <td colspan="4" align="center"><input type="submit" name="submit" value="Submit" /></td>
        </tr>
        </table>
<input type="hidden" name="Action" value="ReportCurrent" />
<input type="hidden" name="lastHr" value="<?=$currentHr?>" />
<input type="hidden" name="lastMin" value="<?=$currentMin?>" />
</form>

</TD></TR></TABLE>


        <input type="hidden" name="Action" value="ReportLate" />
</form>

</TD></TR></TABLE>

</td>

<td width="50%" valign="top">
<TABLE border=0 width=100%><TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Monthly Statistics<?=$cfont_title?></TD></TR><TR><TD>

<?
$date = getdate();

$datefrom = date("Y-m-d H:i:s", mktime(0,0,0,$date['mon'],1,$date['year']));
$dateto = date("Y-m-d H:i:s", mktime(0,0,0,$date['mon']+1,0,$date['year']));
?>

<table border="1" style="margin-left:auto; margin-right:auto" width="80%">
<tr>
<td colspan="2" align="center">
<font size="4">O' Great Ones</font><br />
<font size="3">(Most Submissions)</font>
</td>
</tr>
<tr>
<th>Name</th>
<th>Submissions</th>
</tr>
<?
	echo '<tr><td align="left">Allison Rodriguez</td><td align="right">733T</td></tr>';
$query = mysql_query("SELECT ".sql_nick("People.First","People.Nick","People.Last")." AS 'Name', Count(RecordID) AS 'Submissions' FROM LibraryCounts, People WHERE LibraryCounts.CampusID = People.CampusID AND Approved = '1' AND Time >= '$datefrom' AND Time <= '$dateto' AND People.CampusID != 586927 GROUP BY LibraryCounts.CampusID ORDER BY Submissions DESC LIMIT 2");
while ($row = mysql_fetch_array($query, MYSQL_ASSOC))
{
	echo '<tr>';
	echo '<td align="left">', $row['Name'], '</td>';
	echo '<td align="right">', $row['Submissions'], '</td>';
	echo '</tr>';
}
?>
</table>
<br />
<table border="1" style="margin-left:auto; margin-right:auto" width="80%">
<tr>
<td colspan="2" align="center">
<font size="4">O' Slackers</font><br />
<font size="3">(Least Submissions)</font>
</td>
</tr>
<tr>
<th>Name</th>
<th>Submissions</th>
</tr>
<?
$query = mysql_query("SELECT ".sql_nick("People.First","People.Nick","People.Last")." AS 'Name', Count(RecordID) AS 'Submissions' FROM LibraryCounts, People WHERE LibraryCounts.CampusID = People.CampusID AND Approved = '1' AND Time >= '$datefrom' AND Time <= '$dateto' GROUP BY LibraryCounts.CampusID ORDER BY Submissions ASC LIMIT 3");
while ($row = mysql_fetch_array($query, MYSQL_ASSOC))
{
        echo '<tr>';
        echo '<td align="left">', $row['Name'], '</td>';
        echo '<td align="right">', $row['Submissions'], '</td>';
        echo '</tr>';
}
?>
</table>
<br />
<table border="1" style="margin-left:auto; margin-right:auto" width="80%">
<tr>
<td colspan="3" align="center">
<font size="4">Right On Time</font><br />
<font size="3">(Closest to the Half Hour)</font>
</td>
</tr>
<tr>
<th>Name</th>
<th>Submitted</th>
<th>Difference</th>
</tr>
<?
$query = mysql_query("SELECT ".sql_nick("People.First","People.Nick","People.Last")." AS 'Name', TIME_TO_SEC(TIMEDIFF(CreateTime,Time)) AS 'TimeDiff', ABS(TIME_TO_SEC(TIMEDIFF(CreateTime,Time))) AS 'AbsTimeDiff', CreateTime FROM LibraryCounts, People WHERE LibraryCounts.CampusID = People.CampusID AND Approved = '1' AND Time >= '$datefrom' AND Time <= '$dateto' ORDER BY AbsTimeDiff, Time ASC LIMIT 3");
while ($row = mysql_fetch_array($query, MYSQL_ASSOC))
{
        echo '<tr>';
        echo '<td align="left">', $row['Name'], '</td>';
        echo '<td>', $row['CreateTime'], '</td>';
	echo '<td align="right">', $row['TimeDiff'], ' seconds</td>';
        echo '</tr>';
}
?>
</table>

</TD></TR></TABLE>
</td>
</tr>
</table>

</TD></TR></TABLE>
<?
}

include "footer.php";
db_logout($hdb);
?>

