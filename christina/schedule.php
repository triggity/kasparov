<?

function usertimes($IdNum) {

?>
<FORM METHOD=GET>
<INPUT TYPE=HIDDEN NAME="IsLoggedIn" VALUE="1">
<INPUT TYPE=HIDDEN NAME="IsAdmin" VALUE="1">
<INPUT TYPE=HIDDEN NAME="CampusID" VALUE=<? echo $IdNum; ?>>
<INPUT TYPE=HIDDEN NAME="IsLoggedIn" VALUE="1">
<TABLE border=1><TR><TD>Time</TD><TD>Su</TD><TD>M</TD><TD>Tu</TD><TD>W</TD><TD>Th</TD><TD>F</TD><TD>Sa</TD></TR>
<?

$x = mysql_query("SELECT * FROM Schedule where CampusID = \"".$IdNum."\" ORDER BY Time");

for ($i=0; $i<mysql_num_rows($x) ; $i++) {

$times[mysql_result($x,$i,"Time")]=1;	

}

for ($hour=0; $hour<48; $hour++) {
	?>
	<TR>
	<?
	$t=$hour/2;
	echo "<TD>".floor($t);
	if (((double)$t)==((double)floor($t)))
		{ echo ":00</TD>"; }
		else
		{ echo ":30</TD>"; }
	for ($day=0; $day<7; $day++) {
		?><TD><INPUT TYPE=CHECKBOX NAME="TIME<?
		echo ($day + ($hour*7));
		?>" VALUE="1" <?
		if (1==$times[$day + $hour * 7]) { echo "CHECKED"; }
		?>></TD><?
	}
	?>
	</TR>
	<?
}

?></TABLE>

<P ALIGN=right><INPUT TYPE=SUBMIT NAME="Action" VALUE="Update Schedule"></P>
</FORM>
<?

}


function DoUpdate($IdNum) {

	for ($hour=0; $hour<48; $hour++) {
		for ($day=0; $day<7; $day++) {
			$t=$day+$hour*7;
			if (0==$GLOBALS["TIME".$t]) {
				$x=mysql_query("DELETE FROM Schedule WHERE CampusID=".$IdNum." AND Time=".$t);
			} else {
				if (0==mysql_num_rows(mysql_query("SELECT Time FROM Schedule WHERE CampusID=".$IdNum." AND Time=".$t))) {
					$x=mysql_query("INSERT INTO Schedule (CampusID,Time) VALUES(".$IdNum.",".$t.")");
				}
			}
		}
	}

}

?>
