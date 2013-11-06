<?php   include "makegraph.php";
	include "config.php";
	include "database.php";
	include "login.php";
	include "functions.php";
	$title="Call Fequency";
	include "header.php";
	MustLogin();
?>

<H2 ALIGN=CENTER>Call Frequency</H2> 


<CENTER>

<?
	for ($i=0;$i<19;$i++) {
		$names[$i]=(($i+7)%12).":00";
		$Times[(($i+7)%24)]=$i;
	}


/*---------------< begin loop >-------------------*/
for ($ddiff=-1;$ddiff<7;$ddiff++) {

    $result=mysql_query("SELECT COUNT(*), DATE_FORMAT(Creation,'%k') AS MyTime, DATE_FORMAT(Creation,'%W') FROM PaperTrail WHERE ".(($ddiff==-1)?"":"DATE_FORMAT(Creation,'%w')=$ddiff AND ")."IsFirst='Y' AND Creation!='2000-10-31 14:30:00' GROUP BY MyTime") or die("doh");
    $result2=mysql_query("SELECT COUNT(*), DATE_FORMAT(Creation,'%k') AS MyTime FROM PaperTrail WHERE ".(($ddiff==-1)?"":"DATE_FORMAT(Creation,'%w')=$ddiff AND ")."IsLast='Y' AND DATE_FORMAT(Creation,'%Y-%m-%d')!='2001-01-25' AND DATE_FORMAT(Creation,'%Y-%m-%d')!='2001-01-27' GROUP BY MyTime") or die("doh");
//	MakeTable($result,1,1,1,1,"hello");
    // This is a double vertical graph where each entry has TWO value arrays and
   // TWO bars arrays. 
	for ($i=0;$i<19;$i++) {
		$values[$i]=0;
		$dvalues[$i]=0;
//		$evalues[$i]=0;
	}

//	error_reporting(0);
//	$thisdate=mysql_result($result,0,"Date");
//	error_reporting(511-10);

 
 

	$total_in=0;//tickets received
	$total_out=0;//tickets completed
	for ($i=0;$i<mysql_num_rows($result);$i++) {
	if (strlen($Times[mysql_result($result,$i,1)])>0) {
		$total_in+=($values[$Times[mysql_result($result,$i,1)]]=mysql_result($result,$i,0));
	}
	}
	for ($i=0;$i<mysql_num_rows($result2);$i++) {
	if (strlen($Times[mysql_result($result2,$i,1)])>0) {
		$total_out+=($dvalues[$Times[mysql_result($result2,$i,1)]]=mysql_result($result2,$i,0));
	}
	}
   

    $largest =  (max($values)>max($dvalues))?max($values):max($dvalues);
    //$total_in = array_sum($values);
    //$total_out = array_sum($dvalues);

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
                        "hlabel"=>"Calls ".(($ddiff==-1)?"All Week":"for ".mysql_result($result,0,2))." (".$total_in."/".$total_out.")",
                        "type"=>3,
                        "cellpadding"=>"1",
                        "cellspacing"=>"1",
                        "border"=>"",
                        "width"=>"",
                        "vfcolor"=>"#FFF3FF",
                        "hfcolor"=>"#FFF3FF",
                        "vbgcolor"=>"#".$color_table_title,
                        "hbgcolor"=>"#".$color_table_title,
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

// $usage=mysql_query("SELECT SUM(PC), SUM(MAC), SUM(UNIX), AVG(PC), AVG(MAC), AVG(UNIX) FROM LabCounts WHERE Lab='$lab' AND Date='$thisdate' ") or die("doh");
//$Averages=mysql_result($usage,0,3) + mysql_result($usage,0,4) + mysql_result($usage,0,5);
//if (0==$Averages) {$Averages=1;} //Avoid devide-by-zero.
?>

<!--- Legend --->
<!--
<table border=0 cellspacing=10 cellpadding=0>
<tr>
<td>
<IMG SRC="hbar_blue.gif" HEIGHT=10 WIDTH=10> -<B> PC &nbsp;&nbsp;&nbsp;&nbsp;<? echo mysql_result($usage, 0,0); ?></td><td>&nbsp;&nbsp;&nbsp;</td><td><B>Daily Average:&nbsp;<?echo round(mysql_result($usage, 0,3), 2); ?></td><td>&nbsp;&nbsp;&nbsp;</td><td><B>Percentage:&nbsp;<? echo round(mysql_result($usage, 0,3)/$Averages *100, 2);?>%</td></tr>
<tr><td>
<IMG SRC="hbar_aqua.gif" HEIGHT=10 WIDTH=10> -<B> MAC &nbsp;&nbsp; <? echo mysql_result($usage, 0,1); ?></td><td>&nbsp;&nbsp;&nbsp;</td><td><B>Daily Average:&nbsp;<?echo round(mysql_result($usage, 0,4), 2); ?></td><td>&nbsp;&nbsp;&nbsp;</td><td><B>Percentage:&nbsp;<? echo round(mysql_result($usage, 0,4)/$Averages *100, 2);?>%</td></tr>
<tr><td>
<IMG SRC="hbar_green.gif" HEIGHT=10 WIDTH=10> -<B> UNIX &nbsp;&nbsp; <? echo mysql_result($usage, 0,2); ?></td><td>&nbsp;&nbsp;&nbsp;</td><td><B>Daily Average:&nbsp;<?echo round(mysql_result($usage, 0,5), 2); ?></td><td>&nbsp;&nbsp;&nbsp;</td><td><B>Percentage:&nbsp;<? echo round(mysql_result($usage, 0,5)/$Averages *100,2);?>%</td></tr>
</table>
<BR><BR>
-->
<BR><BR><BR>
<?
}
/*---------------< end loop >-------------------*/
?></CENTER><?
include "footer.php";
