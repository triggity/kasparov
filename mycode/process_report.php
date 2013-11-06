<?php

include "../config.php";
include "../database.php";
include "../functions.php";
include "../login.php";
include "../header.php";
$title = "Process Report";

MustLogin();

echo "<br><br>The current report displays submissions for <h4> $_POST[report_week]</h4>";
?>
<a href="../librarycount.php?SID=<?=$SID?>">Return to Library Count</a>
<?
$week = $_POST[report_week];
$tok = strtok($week, "-:");
$count = 0; 
while ($tok !== false)
{
	if ($count == 1) 
	{
		$store_week = date("Y-m-d", strtotime($tok));
	}
	else if ($count == 2)
	{
		$store_week_end = date("Y-m-d", strtotime($tok));
	}
	$tok = strtok("-:");
	++$count;
}

//fetch and sort requested week's submissions
echo "<h4>";
echo "Display Report";
echo "</h4>";

$result = mysql_query("SELECT * FROM HelpCounts");

while ($row = mysql_fetch_row($result))
{
	$date_array[] =  array("date" => $row[1], "count" => $row[0]);
}
for ($j = 0; $j < sizeof($date_array); $j++)
{
	$temp[$j]["date"] = date("Y-m-d", strtotime($date_array[$j]["date"]));
}
for ($i = 0; $i < sizeof($date_array); $i++)
{
	if ($temp[$i]["date"] >= $store_week && $temp[$i]["date"] <= $store_week_end) 
	{
		//echo $date_array[$i]["date"];
		//echo "<br>";
		$temp2[] = array("date" => $date_array[$i]["date"], "count" => $date_array[$i]["count"]);
	}
}
$date_array = $temp2;
sort($date_array);

//here for testing purposes
/*
echo "<pre>";
print_r($temp);
echo "</pre>";
*/
echo "<table border='1'>";
	echo "<th> Day </th>";
for ($k = 7; $k < 24; $k++)
{
	echo "<th>$k:00 </th>";
}

//here for testing purposes
/*
for ($m = 0; $m < sizeof($date_array); $m++)
{
	print_r($date_array[$m]); 
	echo "<br>";
}
*/

$l = 7; //$l starts at 7am and will cycle through till 11pm 
$temp_count = 0; //keeps track of submissions
$size = sizeof($date_array);
$day = 0; //start day on Sunday and cycle through till Saturday
$track = 0; 

echo "<tr>";
echo "<th>Sunday</th>";

for ($j = 0; $j < sizeof($date_array); $j++, --$size)
{
	$date = date("w", strtotime($date_array[$j]["date"]));
	$prev_date = date("w", strtotime($date_array[$j-1]["date"]));
	$next_date = date("w", strtotime($date_array[$j+1]["date"]));
  	$hour = date("H", strtotime($date_array[$j]["date"])); 
  	$next_hour = date("H", strtotime($date_array[$j+1]["date"])); 
  	$prev_hour = date("H", strtotime($date_array[$j-1]["date"])); 
	$count = $date_array[$j]["count"];
	$next_count = $date_array[$j+1]["count"];

	if ($hour >= 1 && $hour < 7) 
	{
		++$track;
		$hour = $next_hour;
	}
	if ($date == $day && $day < 7) 
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
			if ($hour != $next_hour || $size == 1) 
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
			if ($next_date != $day)
			{
 				while ($l <= 23) 
				{
					echo "<td>0</td>";
					++$l;
				}
			}
		}
		else if ($hour != $l)
		{
			if ($temp_count != 0) 
			{
				if ($next_hour != $hour) 
				{
					if ($hour < $l) 
					{
						$hour = $next_hour;
					}
					$loop = $hour - $l;
					for ($p = $l; $loop != 0; $loop--)
					{
						echo "<td>0</td>";
						++$l;
					}
					$temp_count = $count;
				}
				else if ($prev_hour == $hour - 1)
				{
					echo "<td>$temp_count</td>";
					++$l;
					$temp_count = 0;
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
			if ($next_hour != $hour ||
				 $next_date != $day)
			{
				echo "<td>$temp_count</td>";
				$temp_count = 0; 
				++$l;
				if ($next_date != $day && $day < 6)
				{
					while ($l <= 23)
					{
						echo "<td>0</td>";
						++$l;
					}
				}
			}
		}
		while ($l == 24 && $day < 6) 
		{
			++$day;
			echo "</tr>";
			echo "<tr>";
			$new_day = date("l", strtotime("$store_week + $day days"));
			echo "<th>$new_day</th>";
			$l = 7;
		}
	}
	else if($date != $day && $day < 6) 
	{
		$temp = 0;
		$temp_count = $count;
		while ($day != $date)
		{
			++$temp;
			while ($l <= 23)
			{
				echo "<td>0</td>";
				++$l;
			}
			echo "</tr>";
			echo "<tr>";
			++$day;
			$new_day = date("l", strtotime("$store_week + $day days"));
			echo "<th>$new_day</th>";
			$l = 7;
		}
		if ($temp_count != 0 && $temp >= 1 ||($date == $day + 1 && $next_date == $day + 2)) 
		{
			$loop = $hour - $l;
			for ($p = $l; $loop != 0; $loop--)
			{
				echo "<td>0</td>";
				++$l;
			}
			if ($size == 1 || $next_date == $day + 1)
			{	
				echo "<td>$count</td>";
				while ($l < 23)
				{
					echo "<td>0</td>";
					++$l;
				}
				$temp_count = 0;
 				$l = 24;
				$temp = 0;
			}
			if ($temp_count != 0 && $next_hour != $hour)	
			{	 
				echo "<td>$temp_count</td>";
				$temp = 0;
				$temp_count = $next_count; 
				++$l;
			}
			if ($track > 0)
			{
				echo "<td>$temp_count</td>";
				$track = 0;
				$l++;
			}
		}
	}	
	if ($size == 1) 
	{
		$loop = $hour - $l;
		if ($temp_count != 0) 
		{
			for ($p = $l; $loop != 0; $loop--)
			{
				echo "<td>0</td>";
				++$l;
			}
			echo "<td>$count</td>";
			while ($l < 23)
			{
				echo "<td>0</td>";
				++$l;
			}
			$temp_count = 0;
			$l = 7;
		}
		else
		 {
			while ($l <= 23)
			{
			echo "<td>0</td>";
			++$l;
			}
		}
		while ($day < 6) 
		{
			echo "</tr>";
			echo "<tr>";
			++$day;
			$new_day = date("l", strtotime("$store_week + $day days"));
			echo "<th>$new_day</th>";
			$l = 7;
			while ($l <= 23)
			{
				echo "<td>0</td>";
				++$l;
			}
		}	
		$l = 7;
	}
}

include "../footer.php";
db_logout($hdb);
?>
