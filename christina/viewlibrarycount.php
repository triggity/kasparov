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

$title = 'View Library Count';
include "header.php";

MustLogIn();

if ("Y"==$userdata["IsAdmin"]) {

if (isset($_GET['RID']) && isset($_GET['app']))
{
	$RID = (int)$_GET['RID'];
	$app = (int)$_GET['app'];

	$query = mysql_query("SELECT * FROM LibraryCounts WHERE RecordID = '$RID' AND Approved = '0'");
	if (mysql_num_rows($query) > 0)
	{
		if ($app == 1)
			mysql_query("UPDATE LibraryCounts SET Approved = '1' WHERE RecordID = '$RID'") or die(mysql_error());
		else if ($app == 2)
			mysql_query("DELETE FROM LibraryCounts WHERE RecordID = '$RID'") or die(mysql_error());
	}
}
?><TABLE border=0 width=100% alt='Weekly Overview'><TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Weekly Overview<?=$cfont_title?></TD></TR><TR><TD>
<?
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$starttime = time() - (5 * 60 * 60) + (($offset) * 24 * 60 * 60);
$today = getdate(time() - (5 * 60 * 60) + (($offset) * 24 * 60 * 60)); //subtract 5 hours to get around date changing issue
$tomorrow = getdate(time() + (19 * 60 * 60) + (($offset) * 24 * 60 * 60));

        $todayfrom = date("Y-m-d H:i:s", mktime(7,0,0,$today['mon'],$today['mday'],$today['year']));
        $todayto = date("Y-m-d H:i:s", mktime(3,0,0,$tomorrow['mon'],$tomorrow['mday'],$tomorrow['year']));
	$curDate = date("l, F jS, Y", mktime(0,0,0,$today['mon'],$today['mday'],$today['year']));
	$current_week_begin = date("F jS", strtotime("last Sunday"));
	$current_week_end = date("F jS", strtotime("Saturday"));
$query = mysql_query("SELECT Time, BasementPC, BasementMAC, FirstPC, FirstMAC, Second203MAC, Second205MAC, Second206PC, SecondMAC, DATE_FORMAT(Time, '%k:%i') as daytime FROM LibraryCounts WHERE Time > '$todayfrom' AND Time < '$todayto' AND Approved = '1' ORDER BY Time ASC");
$entries = mysql_num_rows($query);
$currentRow = 0;

$data[0][0] = '';
$data[1][0] = 'Sunday';
$data[2][0] = 'Monday';
$data[3][0] = 'Tuesday';
$data[4][0] = 'Wednesday';
$data[5][0] = 'Thursday';
$data[6][0] = 'Friday';
$data[7][0] = 'Saturday';


for ($t = 1; $t < 18; $t++)
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
                for ($u = 1; $u < 8; $u++)
                        $data[$u][$t] = $row[$u];
                $skipfetch = 0;
        }
        else {
                for ($u = 1; $u < 8; $u++)
                        $data[$u][$t] = 'x';
                $skipfetch = 1;
        }
}
?>

<h2>Record for week of <?=$curDate?>  </h2>

<table border="1" width="100%">
<?

for ($i = 0; $i < 8; $i++)
{
?>
        <tr>
        <?
        for ($j = 0; $j < 18; $j++)
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
<?

?>
<br />
<center>
<TABLE BORDER="0">
<TR>
	<TD><a href="viewlibrarycount.php?SID=<?=$SID?>&offset=<?=$offset-7?>"><< Previous Week</a></TD>
	<TD><a href="viewlibrarycount.php?SID=<?=$SID?>&offset=<?=$offset+7?>">Next Week >></a></TD>
</TR>
</TABLE>
</center>
<br />

<form style="float:right" action="exportdata.php?SID=<?=$SID?>&data=librarycount&type=excelxml" method="POST">

        <input type="hidden" name="starttime" value="<?=$starttime?>" />
        <input type="submit" name="submit" value="Export to Excel" />
</form>


<?
}
include "footer.php";
db_logout($hdb);
?>


