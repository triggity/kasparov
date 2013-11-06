<?
/*
Title: Library COunt
Author: James Taguchi
Description: Page for reporting number of computers being used in the library after this policy went in on 2/2/2010
*/
include "config.php";
include "database.php";
include "functions.php";
include "login.php";
require "php-excel.class.php";

MustLogIn();

if ("Y"==$userdata["IsAdmin"]) {

$exportfilename;
$exportdata;

if(isset($_GET['data']) && $_GET['data'] == 'librarycount')
{

	if(isset($_GET['type']) && $_GET['type'] == 'excelxml') //for when we support other formats
	{
		$range = isset($_GET['range']) ? (int)$_GET['range'] : 1;
		if ($range < 0 || $range > 31)
			$range = 1;
		$exportfilename = 'librarycount';

		for ($daycounter = 0; $daycounter < $range; $daycounter++)
		{	
			$today = getdate((int)$_POST['starttime'] + ($daycounter * 24 * 60 * 60));
                        $tomorrow = getdate((int)$_POST['starttime'] + (($daycounter + 1) * 24 * 60 * 60));
			
			$todayfrom = date("Y-m-d H:i:s", mktime(7,0,0,$today['mon'],$today['mday'],$today['year']));
			$todayto = date("Y-m-d H:i:s", mktime(3,0,0,$tomorrow['mon'],$tomorrow['mday'],$tomorrow['year']));
			$curDate = date("l, F jS, Y", mktime(0,0,0,$today['mon'],$today['mday'],$today['year']));
			$query = mysql_query("SELECT Time, BasementPC, BasementMAC, FirstPC, FirstMAC, Second203MAC, Second205MAC, Second206PC, SecondMAC, DATE_FORMAT(Time, '%k:%i') as daytime FROM LibraryCounts WHERE Time > '$todayfrom' AND Time < '$todayto' AND Approved = '1' ORDER BY Time ASC");
			$entries = mysql_num_rows($query);
			$currentRow = 0;
			
			$exportdata[0][0] = '';
			$exportdata[0][1] = 'Sunday';
			$exportdata[0][2] = 'Monday';
			$exportdata[0][3] = 'Tuesday';
			$exportdata[0][4] = 'Wednesday';
			$exportdata[0][5] = 'Thursday';
			$exportdata[0][6] = 'Friday';
			$exportdata[0][7] = 'Saturday';
			
			
			for ($t = 1; $t < 18; $t++)
			{
			        $curtime = ($t + 6) % 24 . ':00';
			        $exportdata[$t][0] = (($t + 6) % 12 == 0 ? '12' : ($t + 6) % 12) . ':00';
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
			                for ($u = 1; $u < 8; $u++)
			                        $exportdata[$t][$u] = $row[$u];
			                $skipfetch = 0;
			        }
			        else {
			                for ($u = 1; $u < 8; $u++)
			                        $exportdata[$t][$u] = 'x';
			                $skipfetch = 1;
			        }
			}		
		}
	}

}

$xls = new Excel_XML('UTF-8', false);
$xls->addArray($exportdata);
$xls->generateXML("$exportfilename");

}

db_logout($hdb);

?>

