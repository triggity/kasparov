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
		$exportdata = array(0 => array ('Time', 'PC', 'Mac'));
		$exportfilename = 'librarycount';

		for ($daycounter = 0; $daycounter < $range; $daycounter++)
		{	
			$today = getdate((int)$_POST['starttime'] + ($daycounter * 24 * 60 * 60));
			$tomorrow = getdate((int)$_POST['starttime'] + (($daycounter + 1) * 24 * 60 * 60));
			
		        $todayfrom = date("Y-m-d H:i:s", mktime(7,0,0,$today['mon'],$today['mday'],$today['year']));
		        $todayto = date("Y-m-d H:i:s", mktime(3,0,0,$tomorrow['mon'],$tomorrow['mday'],$tomorrow['year']));
		        $curDate = date("l, F jS, Y", mktime(0,0,0,$today['mon'],$today['mday'],$today['year']));

			$query = mysql_query("SELECT PC, Mac, Time, DATE_FORMAT(Time, '%k:%i') as daytime FROM LibraryCounts WHERE Time > '$todayfrom' AND Time < '$todayto' AND Approved = '1' ORDER BY Time ASC") or die(mysql_error());
			$entries = mysql_num_rows($query);
			$currentRow = 0;
	
			$skipfetch = 0;
			for ($t = 1; $t < 39; $t++)
			{
				$exportdata[$t][0] = (((int)($t / 2) + 7) % 12 == 0 ? '12' : ((int)($t / 2) + 7) % 12) . ':' . ($t % 2 == 0 ? '00' : '30');
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
					$exportdata[$t][1] = $row['PC'];
	                                $exportdata[$t][2] = $row['Mac'];
					$skipfetch = 0;
				}
				else
				{
					$exportdata[$t][1] = 'x';
					$exportdata[$t][2] = 'x';
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

