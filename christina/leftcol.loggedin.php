<? include "functions.php";

/*
<TABLE width=100% cellspacing=0 cellpadding=3 border=0>
<FORM method=GET action="logout.php">
<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Staff Login:<?=$cfont_title?></TD></TR>
<TR><TD bgcolor=<?=$color_page_lt_bg?> align=center valign=middle>
Logged In As:<BR><B><? echo $fulluname; ?></B><BR>
<?=date("g:ia  j F Y")?><BR>
<INPUT TYPE=HIDDEN NAME="SID" VALUE=<?echo $SID;?>>

<INPUT Type=SUBMIT Name="Action" Value="Logout">
<? 

//Announce if we think we're in the computer lab
//Moved to new library -Removed by JT
//if ((((int)iptoint($REMOTE_ADDR)>>8) & ((0xFFFFFC))) == 0x81D2C4) {echo "<BR><B>In Computer Lab</B>";}

//Just some debugging
//echo "<BR>".$REMOTE_ADDR."<BR>";
//$a=(int)iptoint($REMOTE_ADDR);
//echo $a."<BR>";
//$b=0xFFFFFC0;
//echo dechex($a & ((0xFFFFFC)<<8))."<BR>";
//echo dechex($a);
?>
</TD></TR>
</FORM>
</TABLE>
<BR>
*/

?>
<TABLE width=100% cellspacing=0 cellpadding=3 border=0>
<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>My Info:<?=$cfont_title?></TD></TR>
<TR><TD bgcolor=<?=$color_page_lt_bg?>><B>

        <LI><A HREF="myinfo.php?&SID=<?echo $SID;?>">Account /
Schedule</A>
        <LI><A HREF="clientinfo.php?&SID=<?echo $SID;?>&CID=<?  echo
$CampusID;?>">General / Contact</A>
        <LI><A HREF="timecards.php?&SID=<?echo $SID;?>">Time Cards</A>
</B></TD></TR>
</TABLE>
<BR>

<!--Schedules-->
<TABLE width=100% cellspacing=0 cellpadding=3 border=0>
<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Schedules:<?=$cfont_title?></TD></TR>
<TR><TD bgcolor=<?=$color_page_lt_bg?>><B>

<?      //Go through the schedules we can view and list them
        for ($i=0;$i<mysql_num_rows($MySchedules);$i++) {
                ?>
                <LI><A HREF="schedules.php?day=<?=$day?>&printversion=1&Schedule=<?=mysql_result($MySchedules,$i,1)?>&SID=<?=$SID?>"><?=mysql_result($MySchedules,$i,0)?> Schedule</A>
                <?
        }

?>

</B></TD></TR>
</TABLE>
<BR>

<?
//Figure out who is on duty
$IamOnDuty=0; //Assume _I_ am not on duty
$ourday=time()-18000; //Pretend it's 5hrs ago (to make the midnight crossing easier)
$ourtime=date("H",$ourday)*4+floor(date("i",$ourday)/15); //figure out the point in the day from 5am in 15 minute increments

//echo ($ourtime/4+5)."&nbsp;&nbsp;".(date("g:ia",$ourday));

//Now get the list of who's on duty now
//echo ($ourtime/4+5);
$x=mysql_query("SELECT ".sql_nick("First","Nick","Last").",People.CampusID,Schedule FROM Schedule_Data, People WHERE People.CampusID=Schedule_Data.CampusID AND Schedule>0 AND Day=".date("w",$ourday)." AND Time=".$ourtime);

//build up the lists of who's on duty
for ($i=0;$i<mysql_num_rows($x);$i++) {
        if (mysql_result($x,$i,1)==$CampusID) {$IamOnDuty=mysql_result($x,$i,2);} // take not if I am on duty right now.
        if (strlen($WhoIsOnDuty[mysql_result($x,$i,2)])>0) {
                $WhoIsOnDuty[mysql_result($x,$i,2)].=", ";
        }
        $WhoIsOnDuty[mysql_result($x,$i,2)].="<A HREF=\x22clientinfo.php?SID=$SID&OnlyInfo=1&CID=".mysql_result($x,$i,1)."\x22>".mysql_result($x,$i,0)."</A>";
}

?><TABLE width=100% cellspacing=0 cellpadding=3 border=0>
<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Staff On Duty:<?=$cfont_title?></TD></TR>
<TR><TD bgcolor=<?=$color_page_lt_bg?>><?

//go through the schedules and print out the generated lists of who's where.
for ($i=0;$i<mysql_num_rows($MySchedules);$i++) {
        ?><B><?
        echo mysql_result($MySchedules,$i,0);
        ?>:</B> <?
        echo $WhoIsOnDuty[mysql_result($MySchedules,$i,1)]."<BR>\n";

}
?></TD></TR></TABLE><BR>

<!--Customer Tallies implemented 3/12/13-->
<TABLE width=100% cellspacing=0 cellpadding=3 border=0>
<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Customer Tallies:<?=$cfont_title?></TD></TR>
<TR><TD bgcolor=<?=$color_page_lt_bg?>><B>
        <LI><A HREF="librarycount.php?SID=<?echo $SID;?>">Submissions</A>
	<? if($userdata["IsAdmin"]=="Y"){ ?>
	<LI><A HREF="viewlibrarycount.php?SID=<?echo $SID;?>">Weekly Overview</A>
	<? } ?>
</B></TD></TR>
</TABLE>
<BR>

<? if($userdata["IsAdmin"]=="Y"){ ?>
<!--Only Show for Administrators-->
<TABLE width=100% cellspacing=0 cellpadding=3 border=0>
<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Adminstrative:<?=$cfont_title?></TD></TR>
<TR><TD bgcolor=<?=$color_page_lt_bg?>><B>
	<LI><A HREF="queries.php?SID=<?echo $SID;?>">Useful Queries</A>
	<LI><A HREF="bigbrother.php?SID=<?echo $SID;?>">Lab Cams</A>
	<LI><A HREF="callgraph.php?SID=<?echo $SID;?>">Call Statistics</A>
	<LI><A HREF="analog.php?SID=<?echo $SID;?>">Web Statistics</A>
	<LI><A HREF="schedules-manage.php?SID=<?echo $SID;?>">Manage Schedules</A>
	<LI><A HREF="locations-manage.php?SID=<?echo $SID;?>">Manage Locations</A>
	<LI><A HREF="timecards-manage.php?SID=<?echo $SID;?>">Manage Time Cards</A>
</B></TD></TR>
</TABLE>
<BR>



<? } ?>

<?
/* Removed by JT. Reason: Moved to new library. May restore it later but I see no use for it now.
<!--Show for Everyone-->
<TABLE width=100% cellspacing=0 cellpadding=3 border=0>
<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Lab Statistics:<?=$cfont_title?></TD></TR>
<TR><TD bgcolor=<?=$color_page_lt_bg?>><B>
	<LI><A HREF="location.php?SID=<?echo $SID;?>"><font size=+2>Enter Statistics</font></A>
	<LI><A TARGET="popper" HREF="" onclick="xyzzy=window.open('popper.php?SID=<?=$SID?>&notables=1','lcpopper','resizable=yes,menubar=no,scrollbars=no,status=no,width=320,height=240');xyzzy.focus();return false;">Pop-up lab counts</A>
	<LI><A HREF="newgraph.php?SID=<?echo $SID;?>">View Graphs</A>
	<LI><A HREF="graph.php?SID=<?echo $SID;?>">View <I>old</I> Graphs</A>
</B></TD></TR>
</TABLE>
<BR>
*/

if($userdata["IsHelpDesk"]=="Y" || $userdata["IsAdmin"]=="Y"){ ?>
<!--Only Show for Help Desk Staff-->
<TABLE width=100% cellspacing=0 cellpadding=3 border=0>
<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Help Desk:<?=$cfont_title?></TD></TR>
<TR><TD bgcolor=<?=$color_page_lt_bg?>><B>
	<LI><A HREF="helpdesk.php?SID=<?echo $SID;?>">Helpdesk Main</A>
	<LI><A HREF="search-ticket.php?SID=<?echo $SID;?>">Search Call Tickets</A>
</B></TD></TR>
</TABLE>
<BR>
<? }  ?>

<? if($userdata["IsFieldSupport"]=="Y" || $userdata["IsAdmin"]=="Y" ){ ?>
<!--Only Show for Field Support (RCCs)-->
<TABLE width=100% cellspacing=0 cellpadding=3 border=0>
<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Field Support:<?=$cfont_title?></TD></TR>
<TR><TD bgcolor=<?=$color_page_lt_bg?>><B>
	<LI><A HREF="fieldsupport.php?SID=<?echo $SID;?>">Field Support Main</A>
	<LI><A HREF="queries.php?QID=6&SID=<?echo $SID;?>">My Past Tickets</A>
	<? if ($userdata["IsHelpDesk"]=="N") { //Don't show this twice?>
	<LI><A HREF="search-ticket.php?SID=<?echo $SID;?>">Search Call Tickets</A>
	<? } ?>
</B></TD></TR>
</TABLE>
<BR>
<? } ?>

<?
/* Removed by JT. Reason: Not used. We may implement a messaging system later but currently email is our primary means of communication.
<!--Message Boards-->
<TABLE width=100% cellspacing=0 cellpadding=3 border=0>
<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Messages:<?=$cfont_title?></TD></TR>
<TR><TD bgcolor=<?=$color_page_lt_bg?>><B>
	<LI><A HREF="listnews.php?SID=<?echo $SID;?>">Read Messages</A>
	<LI><A HREF="postnews.php?SID=<?echo $SID;?>">Post Message</A>
	<LI><A HREF="queries.php?SID=<?echo $SID;?>&QID=8">My Past Messages</A></LI>
	<FORM METHOD=POST ACTION="searchnews.php">
	<INPUT TYPE=HIDDEN NAME="SID" VALUE="<?=$SID?>">
	<INPUT NAME="Phrase" TYPE="TEXT" SIZE=13 VALUE="<?=$Phrase?>"><BR>
	<INPUT TYPE=CHECKBOX NAME="ExactMatch" VALUE="Y" <?=(("Y"==$ExactMatch)?"CHECKED":"")?>>Exact&nbsp;&nbsp;<INPUT NAME="Action" TYPE="Submit" VALUE="Search">

</B></TD></TR>
</form>
</TABLE>
<BR>
*/

/*

//Figure out who is on duty
$x=mysql_query("SELECT IF(LENGTH(Nick)>0,concat(Nick,' ',Last),concat(First,' ',Last)) ,Position, p.CampusID FROM People as p, Schedule as s WHERE s.Time=".(((int)date("G")<7)?(((int)date("H") +   (((int)date("i")<30)?0:0.5)   )*14+(((int)date("w")+6)%7)):((int)date("H") +   (((int)date("i")<30)?0:0.5)  )*14+(((int)date("w"))%7))." AND p.CampusID=s.CampusID");

?>
<TABLE width=100% cellspacing=0 cellpadding=3 border=0>
<TR><TD bgcolor=<?=$color_table_title?>><?=$ofont_title?>Staff On Duty:<?=$cfont_title?></TD></TR>
<TR><TD bgcolor=<?=$color_page_lt_bg?>>
<B>Kenna:</B> <?
	$j=0;
	for ($i=0;$i<mysql_num_rows($x);$i++) {
		if (mysql_result($x,$i,1)=="KENNA") {
			if (1==$j) {echo ", ";} else {$j=1;}
			echo "<A HREF=\x22clientinfo.php?SID=$SID&OnlyInfo=1&CID=".mysql_result($x,$i,2)."\x22>".mysql_result($x,$i,0)."</A>";
		}
	}
?><BR>
<B>Orradre:</B> <?
	$j=0;
	for ($i=0;$i<mysql_num_rows($x);$i++) {
		if (mysql_result($x,$i,1)=="ORRADRE") {
			if (1==$j) {echo ", ";} else {$j=1;}
			echo "<A HREF=\x22clientinfo.php?SID=$SID&OnlyInfo=1&CID=".mysql_result($x,$i,2)."\x22>".mysql_result($x,$i,0)."</A>";
		}
	}
?><BR>
<B>Help Desk:</B> <?
	$j=0;
	for ($i=0;$i<mysql_num_rows($x);$i++) {
		if (mysql_result($x,$i,1)=="HELPDESK") {
			if (1==$j) {echo ", ";} else {$j=1;}
			echo "<A HREF=\x22clientinfo.php?SID=$SID&OnlyInfo=1&CID=".mysql_result($x,$i,2)."\x22>".mysql_result($x,$i,0)."</A>";
		}
	}
?><BR>
<B>Field Support:</B> <?
	$j=0;
	for ($i=0;$i<mysql_num_rows($x);$i++) {
		if (mysql_result($x,$i,1)=="RCC") {
			if (1==$j) {echo ", ";} else {$j=1;}
			echo "<A HREF=\x22clientinfo.php?SID=$SID&OnlyInfo=1&CID=".mysql_result($x,$i,2)."\x22>".mysql_result($x,$i,0)."</A>";
		}
	}

?><BR>
</TD></TR>
</TABLE>
<? */ ?>
