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

$title = "Library Count";
MustLogIn();

//form for on time submission
echo "<h2>Form for Current Hour Submission</h2>";
echo "Please enter how many people you have helped.<br>";
echo "If the current timestamp doesn't correspond correctly to your submission,
      please use the late submission form below.";  
$cur_hour = date("g A", strtotime("today"));
echo "<h1>";
echo "<font color='red'> Current Hour: $cur_hour </font color>"; 
echo "</h1>";
echo "<form action=\"mycode/process_form.php?SID=$SID\" method=\"post\">";
echo "<h1><font color='red'>Submission: <input type=\"text\" style=\"width: 40px\"  name=\"count\" />";
echo "<input type=\"submit\" value=\"Submit\" /></font color></h1>";
echo "<input type=\"hidden\" name=\"submitted\" value=\"1\">";
echo "</form>";

//form for late submission
echo "<h2>Form for Late Submission</h2>";
echo "<p>You may submit anywhere from an hour late up to seven days late</p>";
echo "<form action=\"mycode/process_form.php?SID=$SID\" method=\"post\">";
echo "<h1>Date: <select name=late_date size=\"1\">";
	$late_day = date("l M d, Y", strtotime("today"));
	echo "<option>$late_day</option>";
for ($i = 1; $i <=6; ++$i)
{
	$late_day = date("l M d, Y", strtotime("$i day ago"));
	echo "<option>$late_day</option>";
}
echo "</select>";
echo "  Time: <select name=late_time size=\"1\">";
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
echo "</select>";
echo "  <font color='red'>Late Submission: <input type=\"text\" style=\"width: 30px\"  name=\"late_count\" />";
echo "<input type=\"submit\" value=\"Submit\" />";
echo "</font color></h1>";
echo "<input type=\"hidden\" name=\"late_submitted\" value=\"1\">";
echo "</form>";

//RUN REPORT 
echo "<h2>";
echo "Run Report"; 
echo "</h2>";
echo "You may run report up to ten weeks prior<br>"; 
echo "Please choose the week's submissions you'd like to view 
      and then click Run Report<br><br>";

$k = 2; 
$j = 1;
$current_week_begin = date("M d", strtotime("last Sunday"));
$current_week_end = date("M d", strtotime("Saturday"));

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

//display current days submissions
echo "<h2>";
echo "Today's Count";
echo "</h2>";
$result = mysql_query("SELECT * FROM HelpCounts");
while ($row = mysql_fetch_row($result))
{
	$date_array[] =  array("date" => $row[1], "count" => $row[0]);
}
sort($date_array);
//echo "<pre>";
//print_r($date_array);
//echo "</pre>";
echo "<table border='1'>";
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
					echo "<td>0</td>";
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
						echo "<td>0</td>";
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
				echo "<td>0</td>";
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

include "footer.php";
db_logout($hdb);

?>

