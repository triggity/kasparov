<?php
/*
Title: Absence Functions
Author: James Taguchi
Description: Some functions for absence add-on to Student Tech Services scheduling system
*/

if (!defined("Absence_Functions_Included")) { //protect from double inclusion
define("Absence_Functions_Included",TRUE);

function calendar_view($date)
{
        // Get key day informations.
        // We need the first and last day of the month and the actual day
        $today    = getdate();

        if(isset($_GET['mon']) && isset($_GET['yr']) && $_GET['mon'] >= 0 && $_GET['mon'] <= 12 && (($_GET['yr'] >= 1901 && $_GET['yr'] <= 2038) || ($_GET['yr'] >= 0 && $_GET['yr'] <= 100))) {
		$date = getdate(mktime(0,0,0,$_GET['mon'],1,$_GET['yr']));
	}
	else {
                $date = $today;
	}
	
	$year = $date['year'];
	$nextyear = $date['year'];
	$lastyear = $date['year'];
        $month = $date['mon'];
	$nextmon = $date['mon'] + 1;
	if ($nextmon > 12) {
		$nextyear++;
		$nextmon = 1;
	}
	$lastmon = $date['mon'] - 1;
	if ($lastmon < 1) {
		$lastyear--;
		$lastmon = 12;
	}

        $firstDay = getdate(mktime(0,0,0,$date['mon'],1,$date['year']));
        $lastDay  = getdate(mktime(0,0,0,$date['mon']+1,0,$date['year']));

	//Check if we are looking at today's calendar
	$currentMonth = 0;
	if($date['mon'] == $today['mon'] && $date['year'] == $today['year']) {
		$currentMonth = 1;
	}

        // Create a table with the necessary header informations
	?>
        <table class="calendar">
        <tr>
		<th colspan="2"><?="<A HREF=\x22schedules.php?Schedule=".$_GET['Schedule'].'&day='.$_GET['day'].'&mon='.$lastmon.'&yr='.$lastyear.'&SID='.$_GET['SID'].'&printversion='.$_GET['printversion'].'&notables='.$_GET['notables']."\x22>";?>&lt;&lt;Previous Month</A></th>
		<th colspan="3"><?echo  $date['month'], ' - ', $date['year'];?></th>
		<th colspan="2"><?="<A HREF=\x22schedules.php?Schedule=".$_GET['Schedule'].'&day='.$_GET['day'].'&mon='.$nextmon.'&yr='.$nextyear.'&SID='.$_GET['SID'].'&printversion='.$_GET['printversion'].'&notables='.$_GET['notables']."\x22>";?>Next Month&gt;&gt;</th>
	</tr>
	
        <tr class="days">
	<? for ($i=0;$i<7;$i++)
		echo '<td><a href="schedules.php?printversion=', $_GET['printversion'], '&SID=', $_GET['SID'], '&notables=', $_GET['notables'], '&day=', $i, '&Schedule=', $_GET['Schedule'], '">', GetDay($i), '</a></td>';//Print days of the week


        // Display the first calendar row with correct positioning
        echo '<tr>';
        for($i=0;$i<$firstDay['wday'];$i++)
        {
                echo '<td class="emptyday1">&nbsp;</td>';
        }

        $actday = 0;
        for($i=$firstDay['wday'];$i<7;$i++)
        {
                $actday++;
                if ($currentMonth == 1 && $actday == $today['mday'])
                {
                        $class = ' class="actday"';
                }
                else
                {
                        $class = '';
                }

                echo "<td$class>$actday<br />";

                list_by_date($month, $actday, $year);

                echo '</td>';
        }
        echo '</tr>';

        //Get how many complete weeks are in the actual month
        $fullWeeks = floor(($lastDay['mday']-$actday)/7);

        for ($i=0;$i<$fullWeeks;$i++)
        {
                echo '<tr>';
                for ($j=0;$j<7;$j++)
                {
                        $actday++;
                        if ($currentMonth == 1 && $actday == $today['mday'])
                        {
                                $class = ' class="actday"';
                        }
                        else
                        {
                                $class = '';
                        }

                        echo "<td$class>$actday<br />";

                        list_by_date($month, $actday, $year);

                        echo '</td>';
                }
                echo '</tr>';
        }

        //Now display the rest of the month
        if ($actday < $lastDay['mday'])
        {
                echo '<tr>';

                for ($i=0; $i<7;$i++)
                {
                        $actday++;
                        if ($currentMonth == 1 && $actday == $today['mday'])
                        {
                                $class = ' class="actday"';
                        }
                        else
                        {
                                $class = '';
                        }

                        if ($actday <= $lastDay['mday'])
                        {
                                echo "<td$class>$actday<br />";
                                list_by_date($month, $actday, $year);
                                echo '</td>';
                        }
                        else
                        {
                                echo '<td class="emptyday2">&nbsp;</td>';
                        }
                }

                echo '</tr>';
        }
    echo '</table>';
}

function list_by_date($mon, $day, $year)
{
        $absent_date = mysql_query("
		SELECT
			RecordID,
			date_format(StartTime, '%h:%i%p') AS 'Start',
			date_format(EndTime, '%h:%i%p') AS 'End',
			Last,
			First
	 	FROM
			Absence_Records,
			People
		WHERE
			Absence_Records.CampusID = People.CampusID AND
			StartTime >= '$year-$mon-$day 07:30:00' AND
			StartTime <= date_add('$year-$mon-$day 02:00:00', INTERVAL 1 DAY)
		ORDER BY
			StartTime
	") or die(mysql_error());

        echo '<ul>';
        while($row = mysql_fetch_array($absent_date))
        {
		$result = mysql_query("
		        SELECT
               			".sql_nick("People.First","People.Nick","People.Last")." AS 'Name',
                		DATE_FORMAT(CreateTime, '%M %e, %Y %l:%i %p') AS 'CreateTime'
        		FROM
                		Absence_Cover,
                		People
        		WHERE
                		Absence_Cover.CampusID = People.CampusID AND
                		RecordID = ".$row['RecordID']."
		");

		if($info = mysql_fetch_array($result)) {
			echo "<li class=\x22claimed\x22><a class=\x22calen1\x22 href=\x22viewabsence.php?SID=".$_GET['SID']."&type=entry&RID=".$row['RecordID']."\x22>";
		}
		else {
                	echo "<li class=\x22unclaimed\x22><a class=\x22calen1\x22 href=\x22viewabsence.php?SID=".$_GET['SID']."&type=entry&RID=".$row['RecordID']."\x22>";
		}
                echo $row['Start'].' - '.$row['End'].'<br />';
                echo $row['Last'].', '.$row['First'];
                echo '</a></li>';
        }
        echo '</ul>';

        mysql_free_result($absent_date);
}
}

?>
