<?
//For debugging
/*
include "config.php";
include "database.php";
*/


//Help Desk System Useful functions

//Make up a table featuring the results of a MySQL Query
//$query       = the result of the query to use as the basis of the table
//$even        = are we starting on an even row ($even=1) or
//               an odd row ($even=0)
//$tabletop    = create table heading info
//$tablebottom = close table
//$header      = show column names
//$title       = Title for table
function MakeTable($query_result, $even,$tabletop,$tablebottom,$header,$title) {
	if (1==$tabletop) {
	?>
		<TABLE border=0 cellspacing=2 cellpadding=2 width=100%>
	<?
	}

	$cols=0;
	$headrow="";
	//Set Up header
	while ($field<mysql_num_fields($query_result)) {

		if (eregi("popup",mysql_field_name($query_result,$field)) || eregi("hyperlink",mysql_field_name($query_result,$field))) {
			$field++;
		}
			//Print out value
		$headrow.="<TD>".mysql_field_name($query_result,$field)."</TD>";
		$cols++;
		$field++;
	}

	if (""!=$title) { echo "<TR><TD COLSPAN=$cols BGCOLOR=#EE4510><B> $title </B></TD></TR>"; }

	if (1==$header) { echo "<TR>".$headrow."</TR>"; } //Print header

	for ($row=0;$row<mysql_num_rows($query_result);$row++) {
		?><TR><?
		$field=0;
		while ($field<mysql_num_fields($query_result)) {

			?><TD BGCOLOR=#<?
				if (0==$even) {
					echo "DDDDDD";
				} else {
					echo "C0C0C0";
				}
			?>><?

			$hyperlinked=0;

			if (($field+1)<mysql_num_fields($query_result)) {
				//Are we inserting a hyperlink?
				if (eregi("hyperlink",mysql_field_name($query_result,$field+1))) {
					?><A HREF="<?
					echo mysql_result($query_result,$row,$field+1);
					?>"><?
					$hyperlinked=1;
				} else if (eregi("popup",mysql_field_name($query_result,$field+1))) {
					?><A HREF="<?
					echo mysql_result($query_result,$row,$field+1);
					?>" onclick="x=window.open('<?
					echo mysql_result($query_result,$row,$field+1);

					?>','Popup','width=600,height=400,resizable=yes,menubar=no,scrollbars=yes,status=no');return false;"><?
					$hyperlinked=1;
				}
			}

			//Print out value
			echo mysql_result($query_result,$row,$field);

			$field++;

			//Close Hyperlink if need be
			if (1==$hyperlinked) { $field++; ?></A><? }

			?></TD><?
		}
		?></TR><?
		$even=($even+1)%2;
	}

	if (1==$tabletop) {
	?>
		</TABLE>
	<?
	}

}

//For debugging use
/*
MakeTable(mysql_query("SELECT First,'index.php' as MyPopUP,CampusID FROM People"),0,1,1,1,"Hello World");

db_logout($hdb);
*/
?>
