<?
/*
Title: Add Absence
Author: James Taguchi
Description: Page for adding a new absence record
*/
include "config.php";
include "database.php";
include "functions.php";
include "absence_functions.php";
include "login.php";

$title = 'Report Absence';
include "header.php";

MustLogIn();

?><TABLE border=0 width=100%><TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Report Absence<?=$cfont_title?></TD></TR><TR><TD><?

if(isset($_POST['Action']) && $_POST['Action']=='Add') //run if user submitted new entry
{
	$inputFromHr = $_POST['fromhr'];
        $inputFromMin = $_POST['frommin'];
        $inputToHr = $_POST['tohr'];
        $inputToMin = $_POST['tomin'];

	//Several checks for valid time range
	$errors = '';
	if(($inputFromHr == "02" && $inputFromMin == "00") || ($inputToHr == "21" && $inputToMin == "30")) {
		$errors .= 'Error: Time out of bounds. Must be between 7:30AM ~ 2:00AM.<br />';
	}

	if($inputFromHr > $inputToHr || ($inputFromHr == $inputToHr && $inputFromMin >= $inputToMin)) {
		$errors .= 'Error: Invalid time range.<br />';
	}

	//Check if we have a schedule selected (User needs to click on link in a schedule)
	if(!isset($_GET['Schedule'])) {
		$errors .= 'Error: Schedule not selected.<br />';
	}
	else {
		$inputSchedule = ($_GET['Schedule'] + 1) - 1;
	}		

	if($errors == '') //Did we pass the time checks?
	{
		if(isset($date)) {//Are we using the javascript version?
			$inputDate = $date;
		}
		else {//No javascript
			$inputDate = $year.'-'.$month.'-'.$day;
		}

		$adjustedFromTime = '';
	        if($inputFromHr + 5 > 23) {
        	        $adjustedFromTime = ($inputFromHr - 19); // Hour Adjustment: +5 -24 = -19
	        }
	        else {
	                $adjustedFromTime = $inputFromHr + 5;
	        }
	        $adjustedFromTime .= ':'.$inputFromMin;
	
	        $adjustedToTime = '';
	        if($inputToHr + 5 > 23) {
	                $adjustedToTime = ($inputToHr - 19); // Hour Adjustment: +5 -24 = -19
	        }
	        else {
	                $adjustedToTime = $inputToHr + 5;
	        }
	        $adjustedToTime .= ':'.$inputToMin;

		//Prepare variables for insert
                $inputnotes = mysql_real_escape_string($_POST['notes']);

		//Check the end time if it is past midnight. If so, date must be next day.
                if($inputFromHr < 19) {
                        $timefrom = " '$inputDate $adjustedFromTime:00'";
		}
		else {
			$timefrom = " DATE_ADD('$inputDate $adjustedFromTime:00', INTERVAL 1 DAY)";
		}

                if($inputToHr < 19) {
                        $timeto = " '$inputDate $adjustedToTime:00'";
		}
                else {
                        $timeto = " DATE_ADD('$inputDate $adjustedToTime:00', INTERVAL 1 DAY)";
		}

		// Calculate numerical value for time to be inserted into database
		$timeStartID = ($inputFromHr) * 4;
		if($inputFromMin == "30") {
			$timeStartID += 2;
		}
		$timeEndID = ($inputToHr) * 4;
		if($inputToMin == "30") {
			$timeEndID += 2;
		}

		//Check if user actually works at designated times
		$dayOfWeek = date('w', strtotime($inputDate));
		if(($timeEndID - $timeStartID) != mysql_num_rows(mysql_query("SELECT Time FROM Schedule_Data WHERE CampusID = $CampusID AND Schedule = $inputSchedule AND Day = $dayOfWeek AND Time >= $timeStartID AND Time < $timeEndID"))) {
			$errors .= 'Error: User not on schedule at some or all times selected.<br />';
		}
		//Check for absence record at same time
		if(0 != mysql_num_rows(mysql_query("SELECT Time FROM Absence_Data, Absence_Records WHERE Absence_Data.RecordID = Absence_Records.RecordID AND CampusID = $CampusID AND Date = '$inputDate' AND Time >= $timeStartID AND Time < $timeEndID"))) {
			$errors .= 'Error: User already has absence record for this time.<br />';
		}

		if($errors == '')
		{
			mysql_query("INSERT INTO Absence_Records (CampusID, StartTime, EndTime, CreateTime, Notes) VALUES('$CampusID', $timefrom, $timeto, NOW(), '$inputnotes')") or die(mysql_error());

			for($timeCount = $timeStartID; $timeCount < $timeEndID; $timeCount++) {
				mysql_query("INSERT INTO Absence_Data (Time, RecordID, Schedule, Date) VALUES('$timeCount', LAST_INSERT_ID(), '$inputSchedule', '$inputDate')") or die(mysql_error());
			}
		
		?>Submission Complete. Please check the absence calendar to make sure your entry is correct. If it is incorrect, or the submission did not successfully appear on the schedule, it is your responsibility to correct it.<br /><br />
<font color="FF0000">IMPORTANT:</font> Remember that reporting an absence here does not automatically exempt you from not showing up.<br />
		You must do all of the following for your absence to be valid:<br />
		<b>1)</b> You must <b><u>send an email to everyone on the staff</u></b> with the date, time, and reason(s) of your absence.<br />
		<b>2)</b> You must <b><u>find a replacement for your shift</u></b>. You must cover your shift if you cannot find a replacement.<br />
		<b>3)</b> If you are reporting your absence within 24 hours of the beginning of the absence, you must <b><u>call the student lead</u></b> and let them know of the absence.

</TD></TR></TABLE>
		<?
		}
                else
                {
                        echo $errors;
                }

	}
	else
	{
		echo $errors;
	}
	?>
	<br />
	<a href="schedules.php?printversion=1&Schedule=<?=$inputSchedule?>&SID=<?=$SID?>">Return to Schedule</a>
	<?
}
else
{
	?>
	I will be absent on...
	<form method="POST">
	
	<script>DateInput('date', true, 'YYYY-MM-DD')</script>
	
	<noscript>
	Date:
	<select style="letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;" name="month">
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
	
	<select style="letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;" name="day">
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
	<input type="text" style="letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;" name="year" value="<?=date("Y");?>" size="4" maxlength="4" />
	<br />
	</noscript>
	
	From:
	<select name="fromhr" style="letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;">
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
	</select>

	: <select name="frommin" style="letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;">
	<option value="00">00</option>
	<option value="30">30</option>
	</select>&nbsp;&nbsp;&nbsp;&nbsp;

	To:
	<select name="tohr" style="letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;">
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

	: <select name="tomin" style="letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;">
	<option value="00">00</option>
	<option value="30">30</option>
	</select>

	<br />
	(Note: When entering a time between midnight and 2:00AM, there is no need to change the date. The day continues until the desk closes, just like how the schedule is displayed.)<br />
	
	Reason:<br />
	<textarea name="notes" cols="40" rows="5">You must enter your reason for your absence.</textarea>
	<br />
	<input type="hidden" name="Action" value="Add">
	<input type="submit" value="Submit">

	</form>
	<?
}
?>

</TD></TR></TABLE>

<?
include 'footer.php';
db_logout($hdb);

?>
