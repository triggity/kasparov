<?php   include "makegraph.php";
	include "config.php";
	include "database.php";
	include "login.php";
	include "functions.php";
?>

<HTML>
<HEAD>
<TITLE>Graph</TITLE>
</HEAD>
<BODY>
<H2 ALIGN=CENTER>Lab Counts</H2> 

<form method=get>
<table border=0 cellspacing=10 cellpadding=0>
	<tr>
		<td>View Lab Counts for:</td></tr>
	<tr>
		<td>Month&nbsp;<select name="month">
			<option value="">----------------</option>
			<option value="01">January</option>
			<option value="02">February</option>
			<option value="03">March</option>
			<option value="04">April</option>
			<option value="05">May</option>
			<option value="06">June</option>
			<option value="07">July</option>
			<option value="08">August</option>
			<option value="09">September</option>
			<option value="10">October</option>
			<option value="11">November</option>
			<option value="12">December</option>
			</select></td>
			<td > Day&nbsp;<select name="day">
			<option value="">-------------------</option>
			<? 
			for($j=1;$j<32;$j++) { ?>
			<option value="<? echo $j; ?>"><? echo $j; ?></option>
			<? } ?>
			</select>
			</td>
			<td> Year&nbsp;<select name="year">
			<option value="">-------------------</option>
			<? 
			 for($i=2000;$i<=(INT)date("Y");$i++) { ?> 
			<option value="<? echo $i; ?>"><? echo $i; ?></option>
		        <?} ?> 
			</select>
			</td>
			<td>Lab&nbsp;&nbsp;<select name="lab">
			<option value="">-------------------</option>
			<option value="KENNA">KENNA</option>
			<option value="ORRADRE">ORRADRE</option>
			</select></td>			
			</tr>
			<tr>
			<td colspan=4 align=right><input type=submit value="Chart Graph"></td></tr></table>
			</form>	





<?
	for ($i=0;$i<19;$i++) {
		$names[$i]=(($i+7)%24).":30";
		$Times[(($i<3 || $i>17)?"0":"").(($i==17)?"0":"").(($i+7)%24).":30:00"]=$i;
	}


/*---------------< begin loop >-------------------*/
for ($ddiff=0;$ddiff<7;$ddiff++) {

    $result=mysql_query("SELECT PC, MAC, UNIX, Date, Time FROM LabCounts WHERE Lab='$lab' AND Date=DATE_ADD('".$year."-".$month."-".$day."', INTERVAL $ddiff DAY) ") or die("doh");
//	MakeTable($result,1,1,1,1,"hello");
    // This is a double vertical graph where each entry has TWO value arrays and
   // TWO bars arrays. 
	for ($i=0;$i<19;$i++) {
		$values[$i]=0;
		$dvalues[$i]=0;
		$evalues[$i]=0;
	}

	error_reporting(0);
	$thisdate=mysql_result($result,0,"Date");
	error_reporting(511-10);

 
 

	for ($i=0;$i<mysql_num_rows($result);$i++) {
		//echo "Time: ".mysql_result($result,$i,"Time")."  Point:".$Times[mysql_result($result,$i,"Time")]."<BR>";
	if (strlen($Times[mysql_result($result,$i,"Time")])>0) { 	$values[$Times[mysql_result($result,$i,"Time")]]=mysql_result($result,$i,"PC");
//echo "Whereto insert: ".$Times[mysql_result($result,$i,"Time")]." and i=$i  put result for:".mysql_result($result,$i,"PC")."  What's there: ".$values[$Times[mysql_result($result,$i,"Time")]]."<BR>";
		$dvalues[$Times[mysql_result($result,$i,"Time")]]=mysql_result($result,$i,"UNIX");
		$evalues[$Times[mysql_result($result,$i,"Time")]]=mysql_result($result,$i,"MAC");
	}
//		$names[$i]=mysql_result($result,$i, "Time");
//		$values[$i]=mysql_result($result,$i,"UNIX");
	}
   

    $largest =  max($values);

    // You cannot use color codes in the vertical charts. For this 
    // reason we use only graphics in the bars and dbars array.
    $bars  = array();
    for( $i=0;$i<19;$i++ )
       {
        $bars[$i] = "hbar_blue.gif";
       }
    $dbars  = array();
    for( $i=0;$i<19;$i++ )
       {
        $dbars[$i] = "hbar_green.gif";
       }
	$ebars  = array();
    for( $i=0;$i<19;$i++ )
       {
        $ebars[$i] = "hbar_aqua.gif";
       }

	

   	$graph_vals = array("vlabel"=>"N<BR>u<BR>m<BR>b<BR>e<BR>r",
                        "hlabel"=>"$lab Lab Count ".date("l",strtotime($thisdate))." $thisdate",
                        "type"=>4,
                        "cellpadding"=>"1",
                        "cellspacing"=>"1",
                        "border"=>"",
                        "width"=>"",
                        "vfcolor"=>"#FFF3FF",
                        "hfcolor"=>"#FFF3FF",
                        "vbgcolor"=>"#EE4510",
                        "hbgcolor"=>"#EE4510",
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

 $usage=mysql_query("SELECT SUM(PC), SUM(MAC), SUM(UNIX), AVG(PC), AVG(MAC), AVG(UNIX) FROM LabCounts WHERE Lab='$lab' AND Date='$thisdate' ") or die("doh");
$Averages=mysql_result($usage,0,3) + mysql_result($usage,0,4) + mysql_result($usage,0,5);
if (0==$Averages) {$Averages=1;} //Avoid devide-by-zero.
?>

<!--- Legend --->

<table border=0 cellspacing=10 cellpadding=0>
<tr>
<td>
<IMG SRC="hbar_blue.gif" HEIGHT=10 WIDTH=10> -<B> PC &nbsp;&nbsp;&nbsp;&nbsp;<? echo mysql_result($usage, 0,0); ?></td><td>&nbsp;&nbsp;&nbsp;</td><td><B>Daily Average:&nbsp;<?echo round(mysql_result($usage, 0,3), 2); ?></td><td>&nbsp;&nbsp;&nbsp;</td><td><B>Percentage:&nbsp;<? echo round(mysql_result($usage, 0,3)/$Averages *100, 2);?>%</td></tr>
<tr><td>
<IMG SRC="hbar_aqua.gif" HEIGHT=10 WIDTH=10> -<B> MAC &nbsp;&nbsp; <? echo mysql_result($usage, 0,1); ?></td><td>&nbsp;&nbsp;&nbsp;</td><td><B>Daily Average:&nbsp;<?echo round(mysql_result($usage, 0,4), 2); ?></td><td>&nbsp;&nbsp;&nbsp;</td><td><B>Percentage:&nbsp;<? echo round(mysql_result($usage, 0,4)/$Averages *100, 2);?>%</td></tr>
<tr><td>
<IMG SRC="hbar_green.gif" HEIGHT=10 WIDTH=10> -<B> UNIX &nbsp;&nbsp; <? echo mysql_result($usage, 0,2); ?></td><td>&nbsp;&nbsp;&nbsp;</td><td><B>Daily Average:&nbsp;<?echo round(mysql_result($usage, 0,5), 2); ?></td><td>&nbsp;&nbsp;&nbsp;</td><td><B>Percentage:&nbsp;<? echo round(mysql_result($usage, 0,5)/$Averages *100,2);?>%</td></tr>
</table>
<BR><BR><BR><BR><BR>
<?
}
/*---------------< end loop >-------------------*/

?>



		
			</HTML>
