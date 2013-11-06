<?php

//Written by TK Schundler (sometime in 2001, I think?)
//Modified 6/4/2004 by R. Dykes.
	//All the assorted files we draw upon to make this possable
	include "makegraph.php";
	include "config.php";
	include "database.php";
	include "login.php";
	include "functions.php";
	$title="UNIX Usage Statistics";
	include "header.php";
	MustLogin();


function GraphDay($dbData,$day,$title) { //function to plot one day's data
	for ($i=0;$i<20;$i++) {
		$names[$i]=(((($i+7)%24)<10)?"&nbsp;":"")."&nbsp;".(($i+7)%24).":30&nbsp;";
		//$Times[(($i<3 || $i>17)?"0":"").(($i==17)?"0":"").(($i+7)%24).":30:00"]=$i;
	}

	for ($i=0;$i<20;$i++) {
		$values[$i]=0;
		$dvalues[$i]=0;
		$evalues[$i]=0;
	}

	for ($i=0;$i<mysql_num_rows($dbData);$i++) {
	  if ($day<0 || mysql_result($dbData,$i,3)==$day) {//make sure this is the day of the week that we're interested in
		if ($title=="+") { //we need to figure out the title ourselves
			$title=mysql_result($dbData,$i,"Date");
		}
		if (mysql_result($dbData,$i,1)=="PC") {
			$values[(mysql_result($dbData,$i,0)+22)%24]=(INT)mysql_result($dbData,$i,2);
			//echo mysql_result($dbData,$i,0)."->".((mysql_result($dbData,$i,0)+22)%24)."<BR>";
			
		} else if (mysql_result($dbData,$i,1)=="Mac") {
			$evalues[(mysql_result($dbData,$i,0)+22)%24]=(INT)mysql_result($dbData,$i,2);
		} else {
			$dvalues[(mysql_result($dbData,$i,0)+22)%24]=(INT)mysql_result($dbData,$i,2);
		}
//		$dvalues[$Times[mysql_result($result,$i,"Time")]]=mysql_result($result,$i,"UNIX");
//		$evalues[$Times[mysql_result($result,$i,"Time")]]=mysql_result($result,$i,"MAC");
	  }
	}
   

    $largest =  max($values);

    // You cannot use color codes in the vertical charts. For this 
    // reason we use only graphics in the bars and dbars array.
    $bars  = array();
    for( $i=0;$i<20;$i++ )
       {
        $bars[$i] = "hbar_blue.gif";
       }
    $dbars  = array();
    for( $i=0;$i<20;$i++ )
       {
        $dbars[$i] = "hbar_green.gif";
       }
	$ebars  = array();
    for( $i=0;$i<20;$i++ )
       {
        $ebars[$i] = "hbar_aqua.gif";
       }

	

   	$graph_vals = array("vlabel"=>"N<BR>u<BR>m<BR>b<BR>e<BR>r",
                        "hlabel"=>$title,
                        "type"=>4,
                        "cellpadding"=>"1",
                        "cellspacing"=>"1",
                        "border"=>"",
                        "width"=>"",
                        "vfcolor"=>"#FFFFFF",
                        "hfcolor"=>"#FFFFFF",
                        "vbgcolor"=>"#".$GLOBALS["color_table_title"],
                        "hbgcolor"=>"#".$GLOBALS["color_table_title"],
                        "vfstyle"=>"Verdana, Arial, Helvetica",
                        "hfstyle"=>"Verdana, Arial, Helvetica",
                        "scale"=>200/(($largest>0)?($largest):(1)),
                        "namebgcolor"=>"#C8C8C8",
                        "namefcolor"=>"",
                        "valuefcolor"=>"#000000",
                        "namefstyle"=>"Verdana, Arial, Helvetica",
                        "valuefstyle"=>"",
                        "doublefcolor"=>"#000000"); 

    html_graph($names, $values, $bars, $graph_vals, $dvalues, $dbars,$evalues,$ebars);
}

function GraphMonth($dbData,$title) { //function to plot one day's data
	for ($i=0;$i<31;$i++) {
		$names[$i]="&nbsp;&nbsp;".($i+1)."&nbsp;&nbsp;";
		//$Times[(($i<3 || $i>17)?"0":"").(($i==17)?"0":"").(($i+7)%24).":30:00"]=$i;
	}

	for ($i=0;$i<31;$i++) {
		$values[$i]=0;
		$dvalues[$i]=0;
		$evalues[$i]=0;
	}

	for ($i=0;$i<mysql_num_rows($dbData);$i++) {
//		if (mysql_result($dbData,$i,0)=="PC") {
		$values[mysql_result($dbData,$i,2)-1]+=(INT)mysql_result($dbData,$i,1);
//		} else if (mysql_result($dbData,$i,0)=="Mac") {
	//		$evalues[mysql_result($dbData,$i,2)-1]=(INT)mysql_result($dbData,$i,1);
		//} else {
			//$dvalues[mysql_result($dbData,$i,2)-1]=(INT)mysql_result($dbData,$i,1);
//		}
//		$dvalues[$Times[mysql_result($result,$i,"Time")]]=mysql_result($result,$i,"UNIX");
//		$evalues[$Times[mysql_result($result,$i,"Time")]]=mysql_result($result,$i,"MAC");
	}
   

    $largest =  max($values);

    // You cannot use color codes in the vertical charts. For this 
    // reason we use only graphics in the bars and dbars array.
    $bars  = array();
    for( $i=0;$i<31;$i++ )
       {
        $bars[$i] = "hbar_blue.gif";
       }
    $dbars  = array();
    for( $i=0;$i<31;$i++ )
       {
        $dbars[$i] = "hbar_green.gif";
       }
	$ebars  = array();
    for( $i=0;$i<31;$i++ )
       {
        $ebars[$i] = "hbar_aqua.gif";
       }

	

   	$graph_vals = array("vlabel"=>"N<BR>u<BR>m<BR>b<BR>e<BR>r",
                        "hlabel"=>$title,
                        "type"=>1,
                        "cellpadding"=>"1",
                        "cellspacing"=>"1",
                        "border"=>"",
                        "width"=>"",
                        "vfcolor"=>"#FFFFFF",
                        "hfcolor"=>"#FFFFFF",
                        "vbgcolor"=>"#".$GLOBALS["color_table_title"],
                        "hbgcolor"=>"#".$GLOBALS["color_table_title"],
                        "vfstyle"=>"Verdana, Arial, Helvetica",
                        "hfstyle"=>"Verdana, Arial, Helvetica",
                        "scale"=>200/(($largest>0)?($largest):(1)),
                        "namebgcolor"=>"#C8C8C8",
                        "namefcolor"=>"",
                        "valuefcolor"=>"#000000",
                        "namefstyle"=>"Verdana, Arial, Helvetica",
                        "valuefstyle"=>"",
                        "doublefcolor"=>"#000000"); 

    html_graph($names, $values, $bars, $graph_vals, $dvalues, $dbars,$evalues,$ebars);
}


if ($notables==0) { //form generation options
?>
	<FORM>
	<TABLE width=100% border=0 cellspacing=1 cellpadding=3>
	<TR><TD BGCOLOR=<?=$color_table_title?>>
		<?=$ofont_title?>Report Options:<?=$cfont_title?>
	</TD></TR>
	<TR><TD BGCOLOR=<?=$color_table_lt_bg?> valign=top align=center>
		<TABLE border=0><TR>
			<TD>Report:</TD>
			<TD>Start:</TD>
			<TD>Periods*:</TD>
			<TD>Location:</TD>
		</TR><TR>
			<TD valign=top>
			<SELECT NAME="Report">
				<OPTION VALUE=01>Hours/Day</OPTION>
				<OPTION VALUE=02>Hours/Days/Week</OPTION>
				<OPTION VALUE=03>Days/Months</OPTION>
			</SELECT>
			</TD>
			<TD vlaign=top>

			<SELECT NAME="Month">
				<option value="01" <?=(($Month=="01")?"SELECTED":"")?>>Jan</option>
				<option value="02" <?=(($Month=="02")?"SELECTED":"")?>>Feb</option>
				<option value="03" <?=(($Month=="03")?"SELECTED":"")?>>Mar</option>
				<option value="04" <?=(($Month=="04")?"SELECTED":"")?>>Apr</option>
				<option value="05" <?=(($Month=="05")?"SELECTED":"")?>>May</option>
				<option value="06" <?=(($Month=="06")?"SELECTED":"")?>>Jun</option>
				<option value="07" <?=(($Month=="07")?"SELECTED":"")?>>Jul</option>
				<option value="08" <?=(($Month=="08")?"SELECTED":"")?>>Aug</option>
				<option value="09" <?=(($Month=="09")?"SELECTED":"")?>>Sep</option>
				<option value="10" <?=(($Month=="10")?"SELECTED":"")?>>Oct</option>
				<option value="11" <?=(($Month=="11")?"SELECTED":"")?>>Nov</option>
				<option value="12" <?=(($Month=="12")?"SELECTED":"")?>>Dec</option>
			</select>&nbsp;<SELECT NAME="Day">
				<? 
				for($j=1;$j<32;$j++) { ?>
					<option value="<? echo (($j<10)?"0":"").$j."\x22"; if ($j==(INT)$Day+0) echo " SELECTED"?>><? echo $j; ?></option>
				<? } ?>
			</select>
			<select name="Year">
				<? 
				 for($i=2000;$i<=(INT)date("Y");$i++) { ?> 
					<option value="<? echo $i."\x22 "; if ($i==(INT)date("Y") || $i==$Year) echo "SELECTED"?>><? echo $i; ?></option>
			        <?} ?> 
			</select>
			</TD>
			<TD align=right valign=top>
				<INPUT TYPE=TEXT NAME="Periods" SIZE=3 VALUE="<?=(($Periods=="")?"1":$Periods)?>">
			</TD>
			<TD valign=top>
				<SELECT NAME="Location">
					<OPTION VALUE="All">All</OPTION><?
				$y=mysql_query("Select ID,Name FROM Locations");
				for ($i=0;$i<mysql_num_rows($y);$i++) { ?>
					<OPTION VALUE="<?=mysql_result($y,$i,0)?>"><?=mysql_result($y,$i,1)?></OPTION><?
				}
				?>
				</SELECT>
			</TD>
		</TR><TR>
		<TD colspan=3>
		<INPUT TYPE=CHECKBOX NAME="notables" VALUE=1>Check this to only show the graphs
		</TD><TD align=right>
		<INPUT TYPE=HIDDEN NAME="SID" VALUE="<?=$SID?>">
		<INPUT TYPE=SUBMIT VALUE="Build Graphs">
		</TR></TABLE>
		<TABLE border=0 width=100%><TR><TD align=left>
		*The number of periods are the number of the largest factor being considered - i.e. Days, Weeks,or Months, depending upon the report type specified.
		</TD></TR></TABLE>
	</TR></TD></TABLE>
	</FORM><BR><BR>
<?
} //end form generation options

//pick a report and do it
switch  ((INT)$Report) {
case 0: //no report
	break;
case 1: //One day
	$x=mysql_query("
		SELECT
			MOD(HOUR(Time)+19,24) AS Hour,
			Locations_Stats.Name AS Name,
			AVG(Value) AS Value,
			MOD(WEEKDAY(DATE_SUB(Time,INTERVAL '5' HOUR))+1,7) AS ShDay
			".(($Periods==1)?",DATE_FORMAT(Time,'%W, %e %b %Y') AS Date":"")."
		FROM
			Locations_Data,
			Locations_Stats,
			Locations_Stats_Data
		WHERE
			".(($Location!="All")?"
			Locations_Data.LocationID=$Location AND
			":"")."
			Time>'$Year-$Month-$Day 00:00:00' AND
			".(($Periods>0)?"
			Time<DATE_ADD('$Year-$Month-$Day 00:00:00', INTERVAL $Periods DAY) AND
			":"")."
			Locations_Data.RecordID=Locations_Stats_Data.DataRef AND
			Locations_Stats.ID=Locations_Stats_Data.StatID
		GROUP BY
			Hour,Name
	");
	//MakeTable($x,1,1,1,1,"x");
	if ($Periods==1) {
		GraphDay($x,-1,"+");
	} else {
		GraphDay($x,-1,$Periods." Days from ".$Month." / ".$Day." / ".$Year);
	}
	break;
case 2: //One week
	$x=mysql_query("
		SELECT
			MOD(HOUR(Time)+19,24) AS Hour,
			Locations_Stats.Name AS Name,
			AVG(Value) AS Value,
			MOD(WEEKDAY(DATE_SUB(Time,INTERVAL '5' HOUR))+1,7) AS Day
			".(($Periods==1)?",DATE_FORMAT(Time,'%W, %e %b %Y') AS Date":"")."

		FROM
			Locations_Data,
			Locations_Stats,
			Locations_Stats_Data
		WHERE
			".(($Location!="All")?"
			Locations_Data.LocationID=$Location AND
			":"")."
			Time>'$Year-$Month-$Day 00:00:00' AND
			".(($Periods>0)?"
			Time<DATE_ADD('$Year-$Month-$Day 00:00:00', INTERVAL ".($Periods*7)." DAY) AND
			":"")."
			Locations_Data.RecordID=Locations_Stats_Data.DataRef AND
			Locations_Stats.ID=Locations_Stats_Data.StatID
		GROUP BY
			Hour,Name,Day
	");
	if ($Periods==1) { //just one period
		GraphDay($x,0,"+");
		?><BR><BR><?
		GraphDay($x,1,"+");
		?><BR><BR><?
		GraphDay($x,2,"+");
		?><BR><BR><?
		GraphDay($x,3,"+");
		?><BR><BR><?
		GraphDay($x,4,"+");
		?><BR><BR><?
		GraphDay($x,5,"+");
		?><BR><BR><?
		GraphDay($x,6,"+");
	} else {
		GraphDay($x,0,"Sunday");
		?><BR><BR><?
		GraphDay($x,1,"Monday");
		?><BR><BR><?
		GraphDay($x,2,"Tueday");
		?><BR><BR><?
		GraphDay($x,3,"Wednesday");
		?><BR><BR><?
		GraphDay($x,4,"Thursday");
		?><BR><BR><?
		GraphDay($x,5,"Friday");
		?><BR><BR><?
		GraphDay($x,6,"Saturday");
	}

	break;
case 3: //n months

for ($p=0;$p<$Periods;$p++) {

	$x=mysql_query("
		SELECT
			Locations_Stats.Name AS Name,
			CEILING(AVG(Value)) AS Value,
			DAYOFMONTH(DATE_SUB(Time,INTERVAL '5' HOUR)) AS Day
		FROM
			Locations_Data,
			Locations_Stats,
			Locations_Stats_Data
		WHERE
			".(($Location!="All")?"
			Locations_Data.LocationID=$Location AND
			":"")."
			Time>DATE_ADD('$Year-$Month-1 05:00:00', INTERVAL $p MONTH) AND
			Time<DATE_ADD('$Year-$Month-1 05:00:00', INTERVAL ".(1+$p)." MONTH) AND
			Locations_Data.RecordID=Locations_Stats_Data.DataRef AND
			Locations_Stats.ID=Locations_Stats_Data.StatID AND
			(Locations_Stats_Data.StatID=3 OR Locations_Stats_Data.StatID=3)
		GROUP BY
			Day,Name
	");
	GraphMonth($x,mysql_result(mysql_query("SELECT DATE_FORMAT(DATE_ADD('$Year-$Month-1 00:00:00', INTERVAL $p MONTH),'%M %Y')"),0,0));
	?><BR><BR><?
}

}

include "footer.php";
?>
