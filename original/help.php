<?
include "config.php";
include "database.php";
include "login.php";
$title = "Help!";
include "header.php";

?>
<table width=100%><TR><TD>
<H2>On-line Documentation</H2></TD><TD align=right><img align=right src="/images/help.gif"></TD></TR>
</TABLE>
<BR>
<UL>
<LI><A HREF="help-general.php?SID=<?=$SID?>"><B>General Information</B></A> - Information on logging in, account settings, message board posting, etc.
<LI><A HREF="help-scheduling.php?SID=<?=$SID?>"><B>Scheduling Information</B></A> - Information regarding the scheding system - how to set your available times and view the current schedule.
<LI><A HREF="help-tas.php?SID=<?=$SID?>"><B>Information for Lab TAs</B></A> - Entering lab counts, general lab policies, etc.
<LI><A HREF="help-support.php?SID=<?=$SID?>"><B>Information for Support Staff</B></A> - Working with call tickets, general support policies, etc.

</UL>
<TABLE style="border-collapse: collapse; border: solid;">
    <TR><TD style="border-right: hidden; border-bottom: hidden">foo</TD>
        <TD style="border: solid">bar</TD></TR>
    <TR><TD style="border: none">foo</TD>
        <TD style="border: solid">bar</TD></TR>
    </TABLE>
<?	

include "footer.php";
db_logout($hdb);


?>
