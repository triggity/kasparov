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
?><TABLE border=0 width=100%><TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Library Count Viewer<?=$cfont_title?></TD></TR><TR><TD>
<?
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
for ($daycounter = 0; $daycounter < 7 ; $daycounter++)
{
$starttime = time() - (5 * 60 * 60) + (($offset - 6) * 24 * 60 * 60) + ($daycounter * 24 * 60 * 60);
$today = getdate(time() - (5 * 60 * 60) + (($offset - 6) * 24 * 60 * 60) + ($daycounter * 24 * 60 * 60)); //subtract 5 hours to get around date changing issue
$tomorrow = getdate(time() + (19 * 60 * 60) + (($offset - 6) * 24 * 60 * 60) + ($daycounter * 24 * 60 * 60));

        $todayfrom = date("Y-m-d H:i:s", mktime(7,0,0,$today['mon'],$today['mday'],$today['year']));
        $todayto = date("Y-m-d H:i:s", mktime(3,0,0,$tomorrow['mon'],$tomorrow['mday'],$tomorrow['year']));
	$curDate = date("l, F jS, Y", mktime(0,0,0,$today['mon'],$today['mday'],$today['year']));
$query = mysql_query("SELECT PC, Mac, Time, DATE_FORMAT(Time, '%k:%i') as daytime FROM LibraryCounts WHERE Time > '$todayfrom' AND Time < '$todayto' AND Approved = '1' ORDER BY Time ASC") or die(mysql_error());
$entries = mysql_num_rows($query);
$currentRow = 0;
?>
<h2>Record for <?=$curDate?></h2>
<form action="exportdata.php?SID=<?=$SID?>&data=librarycount&type=excelxml" method="POST">

	<input type="hidden" name="starttime" value="<?=$starttime?>" />
	<input type="submit" name="submit" value="Export to Excel" />
</form>

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
                echo '<font color="FF0000">X</font>';
                $skipfetch = 1;
        }

        echo '</td>';
}
?>
</tr>
<tr></tr><tr></tr>
<tr>
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
                echo '<font color="FF0000">X</font>';
                $skipfetch = 1;
        }

        echo '</td>';
}
?>
</tr>
</table>
<br />
<?
}
?>
<TABLE BORDER="0">
<TR>
	<TD width="25%"><a href="viewlibrarycount.php?SID=<?=$SID?>&offset=<?=$offset-7?>">Back 1 Week</a></TD>
	<TD width="25%"><a href="viewlibrarycount.php?SID=<?=$SID?>&offset=<?=$offset-1?>">Back 1 Day</a></TD>
	<TD width="25%"><a href="viewlibrarycount.php?SID=<?=$SID?>&offset=<?=$offset+1?>">Forward 1 Day</a></TD>
	<TD width="25%"><a href="viewlibrarycount.php?SID=<?=$SID?>&offset=<?=$offset+7?>">Forward 1 Week</a></TD>
</TR>
</TABLE>
<br />


</TD></TR></TABLE>
<TABLE border=0 width=100%><TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Late Submissions<?=$cfont_title?></TD></TR><TR><TD>
<?
$query = mysql_query("SELECT RecordID, Time, CreateTime, ".sql_nick("People.First","People.Nick","People.Last")." AS 'Name', PC, Mac FROM LibraryCounts, People WHERE LibraryCounts.CampusID = People.CampusID AND Approved = '0'");

?>
<table border="1">
	<tr>
		<th>Time</th>
		<th>Submitted at</th>
		<th>Submitted by</th>
		<th>PC</th>
		<th>Mac</th>
		<th>Options</th>
	</tr>
<?
while ($row = mysql_fetch_array($query))
{
?>
	<tr>
		<?
		for ($i = 1; $i < mysql_num_fields($query); $i++)
		{
			echo '<td>', $row[$i], '</td>';
		}
		?>
		<td><a href="viewlibrarycount.php?SID=<?=$SID?>&RID=<?=$row[0]?>&app=1">Approve</a> / <a href="viewlibrarycount.php?SID=<?=$SID?>&RID=<?=$row[0]?>&app=2">Delete</a></td>
	</tr>
<?	
}
?>
</table>
</TD></TR></TABLE>
<?
}
include "footer.php";
db_logout($hdb);
?>


