<?
include "config.php";
include "database.php";
include "header.php";

?>
Welcome to LINC / whatever<P>
Prpblems? Call x1705 - hours: M-Th 8am - 2am, Fri 8am-8pm, Sat-Sun 10am-12am...maybe an animated GIF advertisement?
<P><P><P>
<TABLE width=100% cellspacing=0 cellpadding=3 border=0>
<TR><TD bgcolor=#EE4510><TT>Recent News / Advisories:</TT></TD></TR>
<TR><TD bgcolor=#EEEEEE>Here goes some news or whatever - i.e. There's a new virus that seems to be popular on campus, or Help desk hours are changing or something? This stuff will be pulled from recent entries in a database...though the ticket tracking system is 1st priority</TD></TR>
</TABLE>
<P><P>
<TABLE width=100% cellspacing=0 cellpadding=3 border=0>
<TR><TD bgcolor=#EE4510><TT>Recent How-to's / Tips:</TT></TD></TR>
<TR><TD bgcolor=#EEEEEE>Hello World</TD></TR>
</TABLE>

<?

include "footer.php";
db_logout($hdb);
?>
