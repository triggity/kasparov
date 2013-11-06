<HTML><BODY BGCOLOR=#000000>
<?
include "config.php";
//include "database.php";
//include "header.php";
?>
<TABLE width=100% height=100% border=0>
<TR height=60><TD height=60>
<?
echo "<CENTER>";
include "titlebar.php";
echo "</CENTER>";
?>
</TD></TR>
<TR><TD valign=middle align=center>


<TABLE border=0 cellpadding=4><TR><TD BGCOLOR=<?=$color_page_dk_bg?>>

<TABLE border=0 cellspacing=0 cellpadding=3 width=200>
<TR><TD bgcolor=<?=$color_table_title?>>
<?=$ofont_title?>Staff Login<?=$cfont_title?>
</TD></TR>
<TR><TD bgcolor=<?=$color_page_lt_bg?>>
<FORM METHOD=POST ACTION="central.php">
User Name:<BR>
<INPUT NAME="uname" SIZE=16 VALUE="<? echo $uname; ?>"><BR>
Password:<BR>
<INPUT TYPE=PASSWORD NAME="FORTYTWO" SIZE=11></TD></TR><TR><TD bgcolor=<?=$color_page_lt_bg?> align=right>
<INPUT TYPE=HIDDEN NAME="IsLoggedIn" VALUE="1">
<INPUT TYPE=SUBMIT NAME="Action" VALUE="Login">
</P>
</FORM>
</TD></TR></TABLE>
</TD></TR></TABLE>

</TD></TR></TABLE>

</BODY></HTML>
