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

define("MAX_PC", 54);
define("MAX_MAC", 8);

//generate times
$currentHr = date("G");
$currentMin = date("i");

if ($currentMin >= 15 && $currentMin < 45) {
        $currentMin = '30';
}
else if ($currentMin >= 45 && $currentMin < 60) {
        $currentMin = '00';
        $currentHr = $currentHr + 1;
}
else {
        $currentMin = '00';
}

if ($currentHr == 24) {
        $currentHr = 0;
}

$currentDate = date("Y-m-d H:i:s", mktime($currentHr,$currentMin,0,date("m"),date("d"),date("Y")));
$prevData = NULL; //will be used when someone overwrites previous entry

$num_pc = (int)$_POST['pccount'];
$num_mac = (int)$_POST['maccount'];


if(isset($_POST['Action']) && $_POST['Action']=='ReportCurrent') //run if user submitted new entry
{
	if ($currentHr != $_POST['lastHr'] || $currentMin != $_POST['lastMin'])
	{
	?>
		<script language="javascript">
		        alert('The submission timeframe has changed from when the last page was loaded. Please double check the time. Use the lower form if this submission was for the last timeframe.');
		</script>
	<?	
	}
	else
	{
		$cur_errors = '';

		//Check for negative values and set (max - input)
		if ($num_pc < 0 && $num_pc >= -(MAX_PC))
			$num_pc = MAX_PC + $num_pc;
		if ($num_mac < 0 && $num_mac >= -(MAX_MAC))
			$num_mac = MAX_MAC + $num_mac;
		if (!($num_pc >= 0 && $num_pc <= MAX_PC && $num_mac >= 0 && $num_mac <= MAX_MAC)) {
			$cur_errors .= '<br />Out of range';
		}
		
		if ($cur_errors == '') {
			$query = mysql_query("SELECT ".sql_nick("People.First","People.Nick","People.Last")." AS 'Name', Time, PC, Mac FROM LibraryCounts, People WHERE LibraryCounts.CampusID = People.CampusID AND Time = '$currentDate'");
			if (mysql_num_rows($query) > 0) {
				$prevData = $query;
				mysql_query("UPDATE LibraryCounts SET CampusID = '$CampusID', PC = '$num_pc', Mac = '$num_mac', CreateTime = NOW(), Approved = '1' WHERE Time = '$currentDate'") or die(mysql_error());
			}
			else {
				mysql_query("INSERT INTO LibraryCounts (CampusID, Time, PC, Mac, CreateTime, Approved) VALUES('$CampusID', '$currentDate', '$num_pc', '$num_mac', NOW(), '1')") or die(mysql_error());
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

                $adjustedTime = '';
                if($inputHr + 5 > 23) {
                        $adjustedTime = ($inputHr - 19); // Hour Adjustment: +5 -24 = -19
                }
                else {
                        $adjustedTime = $inputHr + 5;
                }
                $adjustedTime .= ':'.$inputMin;

                $inputTime = "'$inputDate $adjustedTime:00'";
		if ((strtotime("$inputDate $adjustedTime") + (15*60)) >= time()) {
			$late_errors = "idate: $inputDate, ".strtotime("$inputDate $adjustedTime").' '.time().'TF:'.($checkTime+1).(strtotime("$inputDate $adjustedTime") + (15*60) < time())."You can't predict the future!";
		}

	//Check for negative values and set (max - input)
        if ($num_pc < 0 && $num_pc >= -(MAX_PC))
                $num_pc = MAX_PC + $num_pc;
        if ($num_mac < 0 && $num_mac >= -(MAX_MAC))
                $num_mac = MAX_MAC + $num_mac;
        if (!($num_pc >= 0 && $num_pc <= MAX_PC && $num_mac >= 0 && $num_mac <= MAX_MAC)) {
                $late_errors .= 'Out of range';
        }

	if ($late_errors == '')
	{
		$query = mysql_query("SELECT * FROM LibraryCounts WHERE Time = $inputTime");
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
			mysql_query("INSERT INTO LibraryCounts (CampusID, Time, PC, Mac, CreateTime, Approved) VALUES('$CampusID', $inputTime, '$num_pc', '$num_mac', NOW(), '0')") or die(mysql_error());
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
		The previous entry for <?=$prevRow['Time']?> submitted by <?=$prevRow['Name']?> with the following data:<br />
		PC: <?=$prevRow['PC']?><br />
		Mac: <?=$prevRow['Mac']?><br />
		<br />
		Has been replaced with your submission:<br />
		PC: <?=$num_pc?><br />
		Mac: <?=$num_mac?><br />
		<br />
		<?
	}
	else
	{
		?>
		PC: <?=$num_pc?><br />
	        Mac: <?=$num_mac?><br />
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
$query = mysql_query("SELECT PC, Mac, Time, DATE_FORMAT(Time, '%k:%i') as daytime FROM LibraryCounts WHERE Time > '$todayfrom' AND Time < '$todayto' AND Approved = '1' ORDER BY Time ASC");
$entries = mysql_num_rows($query);
$currentRow = 0;
?>
<table border="1">
<tr>
<?
for ($t = 1; $t < 20; $t++)
{
	echo '<td align="right" width="50"><b>';
	echo ((int)($t / 2) + 7) % 12 == 0 ? '12' : ((int)($t / 2) + 7) % 12, ':', $t % 2 == 0 ? '00' : '30';
	echo '</b></td>';
}
?>
</tr>
<tr>
<?
$skipfetch = 0;
for ($t = 1; $t < 20; $t++)
{
	echo '<td align="center" width="50">';
	$curtime = ((int)($t / 2) + 7) % 24 . ':' . ($t % 2 == 0 ? '00' : '30');
	if ($currentRow > $entries)
	{
		$skipfetch = 1;
	}
	
	if ($skipfetch != 1)
	{
		$row = mysql_fetch_array($query, MYSQL_ASSOC);
		$currentRow++;
	}
	
	if ($row['daytime'] == $curtime)
	{
		echo $row['PC'], ' / ', $row['Mac'];
		$skipfetch = 0;
	}
	else {
		echo 'x';
		$skipfetch = 1;
	}

	echo '</td>';
}
?>
</tr>
<tr></tr><tr></tr>
<tr>
<?
for ($t = 20; $t < 39; $t++)
{
        echo '<td align="right" width="50"><b>';
        echo ((int)($t / 2) + 7) % 12 == 0 ? '12' : ((int)($t / 2) + 7) % 12, ':', $t % 2 == 0 ? '00' : '30';
	echo '</b></td>';
}

?>
</tr>
<tr>
<?
for ($t = 20; $t < 39; $t++)
{
        echo '<td align="center" width="50">';
        $curtime = ((int)($t / 2) + 7) % 24 . ':' . ($t % 2 == 0 ? '00' : '30');
        if ($currentRow > $entries)
        {
                $skipfetch = 1;
        }

        if ($skipfetch != 1)
        {
                $row = mysql_fetch_array($query, MYSQL_ASSOC);
                $currentRow++;
        }

        if ($row['daytime'] == $curtime)
        {
                echo $row['PC'], ' / ', $row['Mac'];
                $skipfetch = 0;
        }
        else {
                echo 'x';
                $skipfetch = 1;
        }

        echo '</td>';
}
?>
</tr>
</table>

<br />
<table border="0">
<tr>
<td width="50%" valign="top">
<TABLE border=0 width=100%><TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Submission Form for Current Period<?=$cfont_title?></TD></TR><TR><TD>
Please enter the number of computers currently being used. (Not the number of people using computers!)<br />
<u>Include:</u><br />
&nbsp;&nbsp;&nbsp;+ Upper Info Commons main PC area<br />
&nbsp;&nbsp;&nbsp;+ Upper Info Commons public Mac area<br />
&nbsp;&nbsp;&nbsp;+ Lower Info Commons PC and Mac area<br />
<u>Do not include:</u><br />
&nbsp;&nbsp;&nbsp;+ Walkup stations<br />
&nbsp;&nbsp;&nbsp;+ Upper Info Commons south PC area<br />
&nbsp;&nbsp;&nbsp;+ 2nd Floor<br />

<br />
<b>Current submission time: <?=$currentHr.':'.$currentMin;?></b>
<?
$query = mysql_query("SELECT * FROM LibraryCounts WHERE Time = '$currentDate'");
$pcvalue = '';
$macvalue = '';
if (mysql_num_rows($query) > 0)
{
	$row = mysql_fetch_array($query, MYSQL_ASSOC);
	$pcvalue = $row['PC'];
	$macvalue = $row['Mac'];
?>
	<br />
	<font color="FF0000">Warning: There is already a record for this time!<br />Using this form will overwrite the previous entry. Please resubmit only to correct errors.</font><br />
<?
}
if (isset($cur_errors) && $cur_errors != '')
        echo $cur_errors;
?>
<br />
<form method="POST">
	<table border="0">
	<tr>
		<td align="right">PC:</td>
		<td><input type="text" name="pccount" size="3" maxlength="3" align="right" value="<?=$pcvalue?>"/> (0 - <?=MAX_PC?>)</td>
	</tr>
	<tr>
		<td align="right">Mac:</td>
		<td><input type="text" name="maccount" size="3" maxlength="2" align="right" value="<?=$macvalue?>"/> (0 - <?=MAX_MAC?>)</td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="submit" value="Submit" /></td>
	</tr>
	</table>
<input type="hidden" name="Action" value="ReportCurrent" />
<input type="hidden" name="lastHr" value="<?=$currentHr?>" />
<input type="hidden" name="lastMin" value="<?=$currentMin?>" />
</form>

</TD></TR></TABLE>
</td>

<td width="50%" valign="top">
<TABLE border=0 width=100%><TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Statistics<?=$cfont_title?></TD></TR><TR><TD>

<table border="1" style="margin-left:auto; margin-right:auto" width="50%">
<tr>
<td colspan="2" align="center">
<font size="4">Top 3 Counters</font>
</td>
</tr>
<tr>
<th>Name</th>
<th>Submissions</th>
</tr>
<?
$query = mysql_query("SELECT ".sql_nick("People.First","People.Nick","People.Last")." AS 'Name', Count(RecordID) AS 'Submissions' FROM LibraryCounts, People WHERE LibraryCounts.CampusID = People.CampusID GROUP BY LibraryCounts.CampusID ORDER BY Submissions DESC LIMIT 3");
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
<table border="1" style="margin-left:auto; margin-right:auto" width="50%">
<tr>
<td colspan="2" align="center">
<font size="4">Worst 5 Counters</font>
</td>
</tr>
<tr>
<th>Name</th>
<th>Submissions</th>
</tr>
<?
$query = mysql_query("SELECT ".sql_nick("People.First","People.Nick","People.Last")." AS 'Name', Count(RecordID) AS 'Submissions' FROM LibraryCounts, People WHERE LibraryCounts.CampusID = People.CampusID GROUP BY LibraryCounts.CampusID ORDER BY Submissions ASC LIMIT 3");
while ($row = mysql_fetch_array($query, MYSQL_ASSOC))
{
        echo '<tr>';
        echo '<td align="left">', $row['Name'], '</td>';
        echo '<td align="right">', $row['Submissions'], '</td>';
        echo '</tr>';
}
?>
</table>



</TD></TR></TABLE>
</td>
</tr>
</table>

</TD></TR></TABLE>
<TABLE border=0 width=100%><TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Late Submission Form<?=$cfont_title?></TD></TR><TR><TD>
Better late than never! If you missed a time, use this form to submit numbers for earlier times. Any submissions using this form will be flagged as late for review by an administrator before being finalized in the database.
<br />
<?if (isset($late_errors)) echo '<font color="FF0000">', $late_errors, '</font>';?>
<br />
<form method="POST">
        <table border="0">
        <tr>
                <td align="right">PC:</td>
                <td><input type="text" name="pccount" size="3" maxlength="3" align="right"/> (0 - <?=MAX_PC?>)</td>
        </tr>
        <tr>
                <td align="right">Mac:</td>
                <td><input type="text" name="maccount" size="3" maxlength="2" align="right"/> (0 - <?=MAX_MAC?>)</td>
        </tr>
	</table>
        <table border="0">

	<tr>
	<td colspan="2">
        <script>DateInput('date', true, 'YYYY-MM-DD')</script>

        <noscript>
        Date:
        <select style="letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;" name="inputmonth">
        <option value="01">Jan</option>
        <option value="02">Feb</option>
        <option value="03">Mar</option>
        <option value="04">Apr</option>
        <option value="05">May</option>
        <option value="06">Jun</option>
        <option value="07">Jul</option>
        <option value="08">Aug</option>
        <option value="09">Sep</option>
        <option value="10">Oct</option>
        <option value="11">Nov</option>
        <option value="12">Dec</option>
        </select>

        <select style="letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;" name="inputday">
        <option value="01">01</option>
        <option value="02">02</option>
        <option value="03">03</option>
        <option value="04">04</option>
        <option value="05">05</option>
        <option value="06">06</option>
        <option value="07">07</option>
        <option value="08">08</option>
        <option value="09">09</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        <option value="20">20</option>
        <option value="21">21</option>
        <option value="22">22</option>
        <option value="23">23</option>
        <option value="24">24</option>
        <option value="25">25</option>
        <option value="26">26</option>
        <option value="27">27</option>
        <option value="28">28</option>
        <option value="29">29</option>
        <option value="30">30</option>
        <option value="31">31</option>
        </select>
        <input type="text" style="letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;" name="inputyear" value="<?=date("Y");?>" size="4" maxlength="4" />
        </noscript>
	</td>
	</tr>

	<tr><td>
	Time:
        <select name="inputhr" style="letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;">
        <option value="02">07</option>
        <option value="03">08</option>
        <option value="04">09</option>
        <option value="05">10</option>
        <option value="06">11</option>
        <option value="07">12</option>
        <option value="08">13</option>
        <option value="09">14</option>
        <option value="10">15</option>
        <option value="11">16</option>
        <option value="12">17</option>
        <option value="13">18</option>
        <option value="14">19</option>
        <option value="15">20</option>
        <option value="16">21</option>
        <option value="17">22</option>
        <option value="18">23</option>
        <option value="19">00</option>
        <option value="20">01</option>
	<option value="21">02</option>
        </select>

        : <select name="inputmin" style="letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;">
        <option value="00">00</option>
        <option value="30">30</option>
        </select>
	</td>
	</tr>
	</table>

        <input type="submit" name="submit" value="Submit" />
	<input type="hidden" name="Action" value="ReportLate" />
</form>

</TD></TR></TABLE>
<?
}

include "footer.php";
db_logout($hdb);
?>

