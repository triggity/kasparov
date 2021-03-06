<?
include "config.php";
include "database.php";
include "functions.php";

include "login.php";

$title ="Ticket Search";

include "header.php";


MustLogIn();

function StringOpts ($Field){
echo "<INPUT TYPE=RADIO NAME=\x22$Field-Opts\x22 VALUE=\x22I\x22>&nbsp;Exact&nbsp;&nbsp;\n";
echo "<INPUT TYPE=RADIO NAME=\x22$Field-Opts\x22 VALUE=\x22A\x22>&nbsp;Any&nbsp;&nbsp;\n";
echo "<INPUT TYPE=RADIO NAME=\x22$Field-Opts\x22 VALUE=\x22B\x22>&nbsp;Begins&nbsp;&nbsp;\n";
echo "<INPUT TYPE=RADIO NAME=\x22$Field-Opts\x22 VALUE=\x22E\x22>&nbsp;Ends&nbsp;&nbsp;\n";
echo "<INPUT TYPE=RADIO NAME=\x22$Field-Opts\x22 VALUE=\x22L\x22>&nbsp;LIKE&nbsp;&nbsp;\n";
echo "<INPUT TYPE=RADIO NAME=\x22$Field-Opts\x22 VALUE=\x22R\x22>&nbsp;RegExp&nbsp;&nbsp;\n";
echo "<INPUT TYPE=RADIO NAME=\x22$Field-Opts\x22 VALUE=\x22D\x22 CHECKED>&nbsp;Don't&nbsp;Care\n";
}

function DCOpt ($Field) {
echo "<INPUT TYPE=CHECKBOX NAME=\x22$Field-Opts\x22 VALUE=\x22I\x22>&nbsp;Include&nbsp;in&nbsp;Search\n";
}

?>
<FORM METHOD=POST ACTION="queries.php">
<INPUT TYPE=HIDDEN NAME="SID" VALUE="<?=$SID?>">
<INPUT TYPE=HIDDEN NAME="QID" VALUE="3">
<TABLE width=100%>
	<TR>
		<TD valign=top colspan=3 cellpadding=2 cellspacing=2 BGCOLOR=#<?=$color_table_title?>>
			<?=$ofont_title?>Search By Client<?=$cfont_title?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			First:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="First">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? StringOpts("First"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_lt_bg?>>
			Nick:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<INPUT TYPE=TEXT NAME="Nick">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<? StringOpts("Nick"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Middle:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="Middle">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? StringOpts("Middle"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_lt_bg?>>
			Last:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<INPUT TYPE=TEXT NAME="Last">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<? StringOpts("Last"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			CampusID:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="CID">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? DCOpt("CID"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_lt_bg?>>
			Student:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<SELECT NAME="Student">
				<OPTION>Don't Care</OPTION>
				<OPTION VALUE="Y">Yes</OPTION>
				<OPTION VALUE="N">No</OPTION>
			</SELECT>
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
				&nbsp;
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Faculty:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<SELECT NAME="Faculty">
				<OPTION>Don't Care</OPTION>
				<OPTION VALUE="Y">Yes</OPTION>
				<OPTION VALUE="N">No</OPTION>
			</SELECT>
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			&nbsp;
		</TD>
	</TR>

	<TR><TD valign=top colspan=3><BR></TD></TR>

	<TR>
		<TD valign=top colspan=3 cellpadding=2 cellspacing=2 BGCOLOR=#<?=$color_table_title?>>
			<?=$ofont_title?>Search By Location<?=$cfont_title?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Location:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="Location">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? StringOpts("Location"); ?>
		</TD>
	</TR>


	<TR><TD valign=top colspan=3><BR></TD></TR>

	<TR>
		<TD valign=top colspan=3 cellpadding=2 cellspacing=2 BGCOLOR=#<?=$color_table_title?>>
			<?=$ofont_title?>Search By Jack<?=$cfont_title?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Jack Num:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="Jack">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? StringOpts("Jack"); ?>
		</TD>
	</TR>

	<TR><TD valign=top colspan=3><BR></TD></TR>

	<TR>
		<TD valign=top colspan=3 cellpadding=2 cellspacing=2 BGCOLOR=#<?=$color_table_title?>>
			<?=$ofont_title?>Search By Computer<?=$cfont_title?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Brand:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="CBrand">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? StringOpts("CBrand"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_lt_bg?>>
			Line:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<INPUT TYPE=TEXT NAME="CLine">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<? StringOpts("CLine"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Model:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="CModel">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? StringOpts("CModel"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_lt_bg?>>
			OS:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<? OSes(1); ?>
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			&nbsp;
		</TD>
	</TR>

	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Version:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="Version">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? StringOpts("Version"); ?>
		</TD>
	</TR>


	<TR><TD colspan=3><BR></TD></TR>


	<TR>
		<TD valign=top colspan=3 cellpadding=2 cellspacing=2 BGCOLOR=#<?=$color_table_title?>>
			<?=$ofont_title?>Search By Network Interface Card<?=$cfont_title?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Brand:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="NBrand">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? StringOpts("NBrand"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_lt_bg?>>
			Line:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<INPUT TYPE=TEXT NAME="NLine">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<? StringOpts("NLine"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Model:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="NModel">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? StringOpts("NModel"); ?>
		</TD>
	</TR>


	<TR><TD colspan=3><BR></TD></TR>


	<TR>
		<TD valign=top colspan=3 cellpadding=2 cellspacing=2 BGCOLOR=#<?=$color_table_title?>>
			<?=$ofont_title?>Search In Ticket's FIRST Entry<?=$cfont_title?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Comment:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="Desc1">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? StringOpts("Desc1"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_lt_bg?>>
			Dept:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<SELECT NAME="Dept1">
				<OPTION>Don't Care</OPTION>
				<OPTION>IT</OPTION>
				<OPTION>LINC</OPTION>
			</SELECT>
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
				&nbsp;
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Creator CID:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="CCID1">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? DCOpt("CCID1"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			From:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? DateOpt("From1-",0); ?><BR>
			<? TimeOpt("From1-",0); ?>
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? DCOpt("From1"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_lt_bg?>>
			Till:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<? DateOpt("Till1-",0); ?><BR>
			<? TimeOpt("Till1-",0); ?>
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<? DCOpt("Till1"); ?>
		</TD>
	</TR>

	<TR><TD colspan=3><BR></TD></TR>


	<TR>
		<TD valign=top colspan=3 cellpadding=2 cellspacing=2 BGCOLOR=#<?=$color_table_title?>>
			<?=$ofont_title?>Search In Ticket's LATEST Entry<?=$cfont_title?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Comment:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="Desc2">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? StringOpts("Desc2"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_lt_bg?>>
			Dept:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<SELECT NAME="Dept2">
				<OPTION>Don't Care</OPTION>
				<OPTION>IT</OPTION>
				<OPTION>LINC</OPTION>
			</SELECT>
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
				&nbsp;
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Creator CID:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="CCID2">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? DCOpt("CCID2"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			From:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? DateOpt("From2-",0); ?><BR>
			<? TimeOpt("From2-",0); ?>
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? DCOpt("From2"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_lt_bg?>>
			Till:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<? DateOpt("Till2-",0); ?><BR>
			<? TimeOpt("Till2-",0); ?>
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<? DCOpt("Till2"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			State:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<SELECT NAME="State2">
				<OPTION>Don't Care</OPTION>
				<OPTION>OPEN</OPTION>
				<OPTION>ACTIVE</OPTION>
				<OPTION>CLOSED</OPTION>
			</SELECT>
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
				&nbsp;
		</TD>
	</TR>

	<TR><TD colspan=3><BR></TD></TR>


	<TR>
		<TD valign=top colspan=3 cellpadding=2 cellspacing=2 BGCOLOR=#<?=$color_table_title?>>
			<?=$ofont_title?>Search In ANY Ticket Entry<?=$cfont_title?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Comment:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="Desc3">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? StringOpts("Desc3"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_lt_bg?>>
			Dept:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<SELECT NAME="Dept3">
				<OPTION>Don't Care</OPTION>
				<OPTION>IT</OPTION>
				<OPTION>LINC</OPTION>
			</SELECT>
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
				&nbsp;
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			Creator CID:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<INPUT TYPE=TEXT NAME="CCID3">
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? DCOpt("CCID3"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_dk_bg?>>
			From:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? DateOpt("From3-",0); ?><BR>
			<? TimeOpt("From3-",0); ?>
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_dk_bg?>>
			<? DCOpt("From3"); ?>
		</TD>
	</TR>
	<TR>
		<TD valign=top align=right BGCOLOR=#<?=$color_table_lt_bg?>>
			Till:
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<? DateOpt("Till3-",0); ?><BR>
			<? TimeOpt("Till3-",0); ?>
		</TD>
		<TD valign=top BGCOLOR=#<?=$color_table_lt_bg?>>
			<? DCOpt("Till3"); ?>
		</TD>
	</TR>
</TABLE>
<INPUT TYPE=SUBMIT NAME="Action" VALUE="Search Tickets">
</FORM>
<?

include "footer.php";
db_logout($hdb);
?>
