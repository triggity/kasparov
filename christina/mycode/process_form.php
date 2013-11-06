<?php
include "../config.php";
include "../database.php";
include "../functions.php";
include "../login.php";
include "../header.php";

MustLogin();
$title = "Process Form";

echo "<h2>";
echo "Process Form";
echo "</h2>";

//error checking for empty, negative, non-numeric,  or >=99 submissions
if ((!is_numeric($count) || $count >= 99 || $count < 0
	           || $count != round($count)) && empty($late_count)
                   && $_POST['submitted'] == 1)
{
	$count = $_POST[count];
	echo "<font size=\"4\" color=\"red\">";
	echo "You did not enter a valid submission. Please reenter<br>";
	echo "</font>";
	//redisplay the form 
	echo "<br>";
	echo "<form action = 'mycode/process_form.php?SID=$SID' 
		    method = 'POST'>\n";
	echo "Submission:<input type=\"text\"style=\"width:30px\"name=\"count\"/>";
	echo "<input type=\"submit\"value=\"Submit\"/>";
	echo "<input type=\"hidden\"name=\"submitted\"value=\"1\">";
	echo "</form>";
	exit();
}

//same error checking for late submission
if ((!is_numeric($late_count) || $late_count < 0 ||
	  $late_count >= 99 || $late_count != round($late_count))&&
          empty($count) && $_POST['late_submitted'] == 1)
{
	$late_count = $_POST[late_count];
	echo "<font size=\"4\" color=\"red\">";
	echo "You did not enter a valid submission. Please reenter<br>";
	echo "</font>";
	echo "<br>";
	//redisplay the form 
	echo "<form action = 'process_form.php?SID=$SID' 
		    method = 'POST'>\n";
	echo "Date:<select name=late_date size=\"1\">";
		$late_day = date("l M d, Y", strtotime("today"));
		echo "<option>$late_day</option>";
	for ($i = 1; $i <=6; ++$i)
	{
		$late_day = date("l M d, Y", strtotime("$i day ago"));
		echo "<option>$late_day</option>";
	}
	echo "</select>";
	echo "  Time:<select name=late_time size=\"1\">";
	for ($j = 7; $j <=11; ++$j)
	{
		$late_time = date("h A" , strtotime("$j"));
		echo "<option>$late_time</option>";
	}
		$late_time = date("h A", strtotime("12pm"));
		echo "<option>$late_time</option>";
	for ($k = 1; $k <=11; ++$k)
	{
		$late_time = date("h A", strtotime("$k pm"));
		echo "<option>$late_time</option>";
	}
	echo "</select>";
	echo "Submission:<input type=\"text\"style=\"width:30px\"name=\"late_count\"/>";
	echo "<input type=\"submit\"value=\"Submit\"/>";
	echo "<input type=\"hidden\"name=\"late_submitted\"value=\"1\">";
	echo "</form>";
	exit();
}

//set up the timestamp for late submission
$today = date("Y/m/d/H");
$late_date = $_POST[late_date];
$late_time = $_POST[late_time];
$hour = date("H", strtotime($late_time));
$date = date("Y/m/d", strtotime($late_date));
$late_timestamp = date("Y/m/d/H", strtotime("$hour + $date", $late_timestamp));
$off_hour_end = date("Y/m/d/H", strtotime("7am"));

//insert row into test_table2
if (!empty($late_count))
{
	$query = "INSERT INTO HelpCounts(count, sub_date) 
		  VALUES ('$late_count', '$late_timestamp')";
	$result = mysql_query($query)
		  or die ("Couldn't execute query");
}
else if (!empty($count) && $today >= $off_hour_end)
{
	$query = "INSERT INTO HelpCounts(count, sub_date) 
		  VALUES ('$count', '$today')";
	$result = mysql_query($query)
		  or die ("Couldn't this execute query");
}
//delete entries in table from that are older than 15 weeks from present
$trash_date = date("Y/m/d/h", strtotime("15 weeks ago"));
mysql_query("DELETE FROM HelpCounts WHERE sub_date  <= '$trash_date'"); 

//query the database
$result = mysql_query("SELECT * FROM HelpCounts");

//display table of submissions
//leaving here for testing purposed
/*
echo "<table>";
echo "<tr><td>submission</td><td>timestamp</td></tr>";
while ($row = mysql_fetch_row($result))
{
	echo "<tr>";
	echo "<td>$row[0]</td>";
	echo "<td>$row[1]</td>";
}
*/

//redirect back to library count
echo "Your submission of ";
if (!empty($count))
{
	echo $count;
}
else if (!empty($late_count))
{
	echo $late_count;
}
echo " has been added.  Thank you!<br>"; 
?>
<a href="../librarycount.php?SID=<?=$SID?>">Return to Library Count</a>
<?

include "../footer.php";
db_logout($hdb);
?>

