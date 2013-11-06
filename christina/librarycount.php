<?php
/*
* Program: mysqli_test.php
* Desc: Library count submission forms 
*/

include "config.php";
include "database.php";
include "functions.php";
include "login.php";
include "header.php";

mysql_select_db("test", $hdb);
$title = "Library Count";
MustLogIn();
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<div id="content">
<h1>Submission Page</h1>


<!--Display current hour-->
<?php
$cur_hour = date("g A", strtotime("today"));
$cur_day = date("l F d, Y", strtotime("today"));
echo "<h2><font color='dark orange'> Current Hour and Date: $cur_hour - $cur_day </font color></h2>";
?>



<div id="directions">Please enter how many people you have helped.<br />
If the day shown doesn't correspond correctly to your submission, please select the correct day from the form below</p>
 


<!--form for on time submission-->
<!--
<form action='librarycount.php?SID=<?=$SID?>' method='post'>
<h1><font color='red'>Submission: <input type='text' style='width: 40px' name='count'/>
<input type='submit' value='Submit'/></font color></h1>
<input type='hidden' name='submitted' value='1'>
</form>
-->

<!--error checking for on time submission-->
<?php
if ((!is_numeric($count) || $count >= 99 || $count < 0
	           || $count != round($count)) && empty($late_count)
                   && $_POST['submitted'] == 1)
{
	$count = $_POST[count];
	echo "<font size=\"4\" color=\"red\">";
	echo "You did not enter a valid submission. Please reenter<br>";
	echo "</font>";
	exit();
}

?>
<!--form for late submission-->


<h1>Hour Submission</h1>
<form action='librarycount.php?SID=<?=$SID?>' method='post'>
<h2>Select Day: <select name=late_date size='1'>
	<?php
	$late_day = date("l", strtotime("today"));
	echo "<option>$late_day</option>";
for ($i = 1; $i <=6; ++$i)
{
	$late_day = date("l", strtotime("$i day ago"));
	echo "<option>$late_day</option>";
}
	?>
</select>
<form><input type=submit name='Action' VALUE='Submit Day' style="align: center"> </INPUT></form>

<!--
Time: <select name=late_time size='1'>
<?php
for ($j = 7; $j <=11; ++$j)
{
	$late_time = date("g A", strtotime("$j"));
	echo "<option>$late_time</option>";
}
	$late_time = date("g A", strtotime("12pm"));
	echo "<option>$late_time</option>";
for ($k = 1; $k <= 11; ++$k)
{
	$late_time = date("g A", strtotime("$k pm"));
	echo "<option>$late_time</option>";
}
?>
</select>
<font color='red'>Late Submission: <input type='text' style='width: 30px'  name='late_count'/>
<input type='submit' value='Submit' />
</font color></h1>
<input type='hidden' name='late_submitted' value='1'>
-->
</form>

<!--same error checking for late submission-->
<!--
<?php
if ((!is_numeric($late_count) || $late_count < 0 ||
	  $late_count >= 99 || $late_count != round($late_count))&&
          empty($count) && $_POST['late_submitted'] == 1)
{
	$late_count = $_POST[late_count];
	echo "<font size=\"4\" color=\"red\">";
	echo "You did not enter a valid submission. Please reenter<br>";
	echo "</font>";
	echo "<br>";
	exit();
}
?>
-->

<!-- set up the timestamp for late submission-->
<!--
<?php
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
	$query = "INSERT INTO test_table2(count, sub_date) 
		  VALUES ('$late_count', '$late_timestamp')";
	$result = mysql_query($query)
		  or die ("Couldn't execute query");
}
else if (!empty($count) && $today >= $off_hour_end)
{
	$query = "INSERT INTO test_table2(count, sub_date) 
		  VALUES ('$count', '$today')";
	$result = mysql_query($query)
		  or die ("Couldn't this execute query");
}
//delete entries in table from that are older than 15 weeks from present
$trash_date = date("Y/m/d/h", strtotime("15 weeks ago"));
mysql_query("DELETE FROM test_table2 WHERE sub_date  <= '$trash_date'"); 

//query the database
$result = mysql_query("SELECT * FROM test_table2");

//display table of submissions
//leaving here for testing purposed

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
-->
</html>
<?php
//RUN REPORT 
/*
echo "<h2>";
echo "Run Report"; 
echo "</h2>";
echo "You may run report up to ten weeks prior<br>"; 
echo "Please choose the week's submissions you'd like to view 
      and then click Run Report<br><br>";
*/

$k = 2; 
$j = 1;
$current_week_begin = date("F jS", strtotime("last Sunday"));
$current_week_end = date("M d", strtotime("Saturday"));
$today = date("l F d", strtotime("today"));

echo "<br/><br/> Showing hourly submissions for [ $today ]"; 


/*
echo "<form action=\"mycode/process_report.php?SID=$SID\" method=\"post\">";
echo "<h2>Week: <select name=report_week size='1'></h2>";
for ($i = 0; $i <=10; ++$i)
{
	$prev_week_begin = date("M d", strtotime("Sunday $k weeks ago"));
	$prev_week_end = date("M d", strtotime("Saturday $j weeks ago"));
	if ($i == 0)
	{
		echo "<option>Current week: $current_week_begin - $current_week_end</option>";
	}
	else
	{
		echo "<option>$i week(s) ago: $prev_week_begin - $prev_week_end</option>";
		$k++; 
		$j++;
	}
}
echo "</select>";
echo "<input type='submit' value='Run Report' />";
echo "<input type='hidden' name='rune_report' value='1' />";
echo "</form>";
*/

//display current days submissions
echo "<h2>";
//echo "Today's Count";
echo "</h2>";
$result = mysql_query("SELECT * FROM test_table2");
while ($row = mysql_fetch_row($result))
{
	$date_array[] =  array("date" => $row[1], "count" => $row[0]);
}
sort($date_array);
//echo "<pre>";
//print_r($date_array);
//echo "</pre>";
echo "<table border='1' alt='Hourly Submission'>";
	echo "<th> Today </th>";
for ($k = 7; $k < 12; $k++)
{
		echo "<th>$k AM </th>";
}
echo "<th>12 PM </th>";
for ($k = 1; $k < 12; $k++)
{
		echo "<th>$k PM </th>";
}
echo "<tr>";
$today = date("l", strtotime("today"));
echo "<th>$today</th>";
$today = date("Y-m-d", strtotime("today"));

for($n =0; $n < 17; $n++){
echo "<td><input type=text size=3></td>";
}
?>


<?php 
$l = 7;
$temp_count = 0;
$size = sizeof($date_array);

for ($j = 0; $j < sizeof($date_array); $j++, $size--) 
{
	$date = date("Y-m-d", strtotime($date_array[$j]["date"]));
  	$hour = date("H", strtotime($date_array[$j]["date"])); 
  	$next_hour = date("H", strtotime($date_array[$j+1]["date"])); 
  	$prev_hour = date("H", strtotime($date_array[$j-1]["date"])); 
	$count = $date_array[$j]["count"];
	
	if ($date == $today) 
	{
		if ($hour == $l && $temp_count == 0) 
		{
			$temp_count += $count;
			if ($next_hour != $l)
			{
				echo "<td>$temp_count</td>";
				++$l;
				$temp_count = 0;
			}
		}
		else if ($temp_count != 0 && $prev_hour == $hour)
		{
			$temp_count += $count;
			if ($hour != $next_hour) 
			{
				$loop = $hour - $l;
				for ($p = $l; $loop != 0; $loop--)
				{
					echo "<td>n/a</td>";
					++$l;
				}
				echo "<td>$temp_count</td>";
				++$l; 
				$temp_count = 0; 
			}
		}
		else if ($hour != $l)
		{
			if ($temp_count != 0) 
			{
				if ($next_hour != $hour) 
				{
					$loop = $prev_hour - $l;
					for ($p = $l; $loop != 0; $loop--)
					{
						echo "<td>n/a</td>";
						++$l;
					}
					echo "<td>$temp_count</td>";
					++$l;
					$temp_count = $count;
				}
				else 
				{	
					$temp_count += $count;
				}
			}
			if ($hour < $l)
			{
				$l = $prev_hour + 1;
			}
			$loop = $hour - $l;
			for ($p = $l; $loop != 0; $loop--)
			{
				echo "<td>n/a</td>";
				++$l;
			}
			$temp_count = $count;
			if ($next_hour != $hour)
			{
				echo "<td>$temp_count</td>";
				$temp_count = 0; 
				++$l;
			}
		}
	}
}

?>
</table> 

<?php
//PROCESS FORM
//error checking for empty, negative, non-numeric,  or >=99 submissions
?>
<p>
<form><input type=submit name='Action' VALUE='Submit' style="align: center"> </INPUT></form>
</p>
<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/> Last edited: 3/13/13
<?
include "../footer.php";
db_logout($hdb);
?>

</div>
</body> </html>