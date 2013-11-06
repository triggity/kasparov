<?
//For debugging
/*
include "config.php";
include "database.php";
*/

if($functionsIncluded!='Y'){

//Help Desk System Useful functions

//Make up a table featuring the results of a MySQL Query
//$query_result= the result of the query to use as the basis of the table
//$even        = are we starting on an even row ($even=1) or
//               an odd row ($even=0) (i.e. what color to make the row)
//$tabletop    = 1=create table heading info, 0=assume the table has been
//		 already ser up.
//$tablebottom = 1=close table, 0=no </TABLE>, let the user to that
//$header      = 0=no header, 1=show column names, 2=show column names
//		 only if there is data in the table, 3= show column names,
//		 or "(none)" if the table is empty.
//$title       = Title for table
//$direction   = The direction to display the table, 1=ascending, -1=descending (reversed)
//Any columns with these keywords anywhere in their title are treated  specially:
//    hidden     - the column is not shown in the table - this must
//                 never be the last column or bad things will happen
//    hyperlink  - the value of this column is treated as a hyperlink for
//                 the previous column.
//    popup      - this is like a hyperlink, however the link is loaded in
//                 a new window.
function MakeTable($query_result, $even,$tabletop,$tablebottom,$header,$title,$direction=1) {
	//are in initiating the table outselves 1 or was it done already 0
	if (1==$tabletop) {
	?>
		<TABLE border=0 cellspacing=2 cellpadding=2 width=100%>
	<?
	}

	$cols=0;
	$headrow="";
	//Set Up header
	while ($field<mysql_num_fields($query_result)) {
		//don't add the field if it is a popup, hyperlink, or hidden
		if (!(eregi("popup",mysql_field_name($query_result,$field)) || eregi("hyperlink",mysql_field_name($query_result,$field)) || eregi("hidden",mysql_field_name($query_result,$field)))) {
			$headrow.="<TD>".mysql_field_name($query_result,$field)."</TD>";
			$cols++;
		}
		$field++;
	}

	//display header if applicable
	if (""!=$title) { echo "<TR><TD COLSPAN=$cols BGCOLOR=".$GLOBALS["color_table_title"].">".$GLOBALS["ofont_title"]." $title ".$GLOBALS["cfont_title"]."</TD></TR>"; }

	//Print Header
	if (1==$header || ((2==$header || 3==$header) && 0<mysql_num_rows($query_result))) { echo "<TR>".$headrow."</TR>"; } else if (3==$header) {echo "<TR><TD>(none)</TD></TR>"; }

	//loop through all the rows of the result
	if (0==$direction) $direction=1; //can have a zero direction
	if (0>$direction) {
		$firstrow=mysql_num_rows($query_result)-1;
		$lastrow=-1;
	} else {
		$lastrow=mysql_num_rows($query_result);
		$firstrow=0;
	}
	for ($row=$firstrow;$row!=$lastrow;$row+=$direction) {
		?><TR><?
		//loop through the fields
		$field=0;
		while ($field<mysql_num_fields($query_result)) {

			?>
			<TD valign=top BGCOLOR=#<?
				if (0==$even) {
					echo $GLOBALS["color_table_lt_bg"];
				} else {
					echo $GLOBALS["color_table_dk_bg"];
				}
			?>><?

			$hyperlinked=0; //flag if this is a hyperlink

			//Do we have a hidden field?
			while (eregi("hidden",mysql_field_name($query_result,$field))) {
				$field++;
			}
			if (($field+1)<mysql_num_fields($query_result)) {


				//Are we inserting a hyperlink?
				if (eregi("hyperlink",mysql_field_name($query_result,$field+1))) {
					?><A HREF="<?
					echo mysql_result($query_result,$row,$field+1)."&SID=".$GLOBALS["SID"];
					?>"><?
					$hyperlinked=1;
				} else if (eregi("popup",mysql_field_name($query_result,$field+1))) {
					?><A HREF="<?
					echo mysql_result($query_result,$row,$field+1)."&SID=".$GLOBALS["SID"];
					?>" onclick="window.open('<?
					echo mysql_result($query_result,$row,$field+1)."&SID=".$GLOBALS["SID"];

					?>&notables=1','Popup','width=640,height=440,resizable=yes,menubar=no,scrollbars=yes,status=no');return false;"><?
					$hyperlinked=1;
				}
			}

			if ($field < mysql_num_fields($query_result)) {
			//Print out value
			$out=mysql_result($query_result,$row,$field);
			if (""==$out) {
				echo "&nbsp;";
			} else {
				$out=ereg_replace("\n","<BR>",$out);
				echo $out;
			}
			}

			$field++;

			//Close Hyperlink if need be
			if (1==$hyperlinked) { $field++; ?></A><? }

			?></TD><?
		}
		?></TR><?
		$even=($even+1)%2;
	}

	//if the user wants us to close out the table.
	if (1==$tablebottom) {
	?>
		</TABLE>
	<?
	}

	//return whether we are on an even or odd row (useful if this is
	//one segment of a bigger table.
	return $even;
}

//Make up a table featuring the results of a MySQL Query
//$query_result= the result of the query to use as the basis of the table
//$title       = Title for table
function MakeTableSideways($query_result,$title) {
	?>
		<TABLE border=0 cellspacing=2 cellpadding=3>
	<?

	$cols=1+mysql_num_rows($query_result);
	if (""!=$title) { echo "<TR><TD COLSPAN=$cols BGCOLOR=".$GLOBALS["color_table_title"].">".$GLOBALS["ofont_title"]." $title ".$GLOBALS["cfont_title"]."</TD></TR>"; }

	for ($row=0;$row<mysql_num_fields($query_result);$row++) {
		?><TR><?
			?><TD align=right><?
			echo mysql_field_name($query_result,$row)."&nbsp;";
			?></TD><?
		for ($col=0;$col<mysql_num_rows($query_result);$col++) {

			?><TD BGCOLOR=#<?
				if (0==$even) {
					echo $GLOBALS["color_table_dk_bg"];
				} else {
					echo $GLOBALS["color_table_lt_bg"];
				}
			?>><?

			//Print out value
			$out=mysql_result($query_result,$col,$row);
			if (""==$out) {
				echo "&nbsp;";
			} else {
				$out=ereg_replace("\n","<BR>",$out);
				echo $out;
			}

			?></TD><?
		}
		?></TR><?
		$even=($even+1)%2;
	}

	?>
		</TABLE><?
}

//Make a drop down combo box using result from a SQLquery
 function droplist($query_result,$name,$valuefield,$displayfield,$selected) {
   ?><SELECT NAME="<?=$name?>"><?
    for ($i=0;$i<mysql_num_rows($query_result);$i++) {
      $thisvalue=mysql_result($query_result,$i,$valuefield);
      echo "<OPTION VALUE=\x22".$thisvalue."\x22 ";
      if ($thisvalue==$selected) echo "SELECTED";
      echo ">".mysql_result($query_result,$i,$displayfield)."</OPTION>";
    }
   ?></SELECT><?
 }

function sql_phone($dbtable) {
return "IF(".$dbtable."<10000 OR ((".$dbtable."<10000000 AND MOD(FLOOR(".$dbtable."/10000),1000)=551) OR (".$dbtable.">=10000000 AND MOD(FLOOR(".$dbtable."/10000),1000000)=408551)) ,concat('x',MOD(".$dbtable.",10000)),concat(IF((".$dbtable."<10000000),'1-408-',concat('1-',MOD(FLOOR(".$dbtable."/10000000),1000),'-')),MOD(FLOOR(".$dbtable."/10000),1000),'-',IF(MOD(".$dbtable.",10000)<1000,'0',''),IF(MOD(".$dbtable.",10000)<100,'0',''),IF(MOD(".$dbtable.",10000)<10,'0',''),MOD(".$dbtable.",10000)))";
}

function sql_nick($dbfirst,$dbnick,$dblast) {
return "IF(LENGTH($dbnick)>0,concat($dbnick,' ',$dblast),concat($dbfirst,' ',$dblast))";
}

function sql_daytime($field) {
return "DATE_FORMAT($field,'%e %b \'%y - %l:%i%p')";
}

function OSes($DC=0) {
?><SELECT Name="OS">
		<? if ($DC==1) {?>
		<OPTION>Don't Care</OPTION>
		<? } ?>
		<OPTION <? if("Windows"==$OS) {echo "SELECTED";} ?>>Windows</OPTION>
		<OPTION <? if("Mac OS"==$OS) {echo "SELECTED";} ?>>Mac OS</OPTION>
		<OPTION <? if("Windows NT"==$OS) {echo "SELECTED";} ?>>Windows NT</OPTION>
		<OPTION <? if("Linux"==$OS) {echo "SELECTED";} ?>>Linux</OPTION>
		<OPTION <? if("BSD"==$OS) {echo "SELECTED";} ?>>BSD</OPTION>
		<OPTION <? if("Sun OS/Solaris"==$OS) {echo "SELECTED";} ?>>Sun OS/Solaris</OPTION>
		<OPTION <? if("HPUX"==$OS) {echo "SELECTED";} ?>>HPUX</OPTION>
		<OPTION <? if("NeXT"==$OS) {echo "SELECTED";} ?>>NeXT Step</OPTION>
		<OPTION <? if("AIX"==$OS) {echo "SELECTED";} ?>>AIX</OPTION>
		<OPTION <? if("IRIX"==$OS) {echo "SELECTED";} ?>>IRIX</OPTION>
		<OPTION <? if("UNIX"==$OS) {echo "SELECTED";} ?> VALUE="UNIX">Other UNIX</OPTION>
		<OPTION <? if("OS/2"==$OS) {echo "SELECTED";} ?>>OS/2</OPTION>
		<OPTION <? if("BeOS"==$OS) {echo "SELECTED";} ?>>BeOS</OPTION>
		<OPTION <? if("Amiga OS"==$OS) {echo "SELECTED";} ?>>Amiga OS</OPTION>
		<OPTION <? if("Palm OS"==$OS) {echo "SELECTED";} ?>>Palm OS</OPTION>
		<OPTION <? if("Other"==$OS) {echo "SELECTED";} ?>>Other</OPTION>
	</SELECT><?
}

function DateOpt($field = "", $theday=-1) {
			if (-1==$theday || 0==$theday) {$theday=time();}
?>
			<SELECT NAME="<?=$field?>Month">
			<? for ($i=2;$i<14;$i++) { ?>
			<OPTION VALUE=<?
			echo $i-1;
			echo (date("n",$theday)==(string)($i-1))?" SELECTED":" ";
			echo ">";
			echo date("M",gmmktime(0,0,0,$i,0,0));
			?></OPTION>
			<? } ?>
			</SELECT>
			<INPUT TYPE=TEXT SIZE=2 NAME="<?=$field?>Day" VALUE=<? echo date("d",$theday); ?>>
			<INPUT TYPE=TEXT SIZE=4 NAME="<?=$field?>Year" VALUE=<? echo date("Y",$theday); ?>><?
}


function TimeOpt($field = "", $theday=-1) {
			if (-1==$theday || 0==$theday) {$theday=time();}
?>
			<INPUT TYPE=TEXT SIZE=2 NAME="<?=$field?>Hour" align=right VALUE=<? echo date("g",$theday); ?>>
			:
			<INPUT TYPE=TEXT SIZE=2 NAME="<?=$field?>Minute" VALUE=<? echo date("i",$theday); ?>>
			&nbsp;<SELECT NAME="<?=$field?>HalfDay">
			<OPTION VALUE="AM" <? echo ("AM"==date("A",$theday))?"SELECTED":""; ?>>AM</OPTION>
			<OPTION VALUE="PM" <? echo ("PM"==date("A",$theday))?"SELECTED":""; ?>>PM</OPTION>
			</SELECT>
<?
}

//For debugging use
/*
MakeTable(mysql_query("SELECT First,'index.php' as MyPopUP,CampusID FROM People"),0,1,1,1,"Hello World");

db_logout($hdb);
*/
}
$functionsIncluded='Y';
?>
