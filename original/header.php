<?
if (!defined("Header_Included")) { //protect from double inclusion
define("Header_Included",TRUE);
?>
<HTML>
	<HEAD>
		<TITLE><?
			//Setup page title
			if (strlen($title)>2) {
				echo $title." - ";
			}
			echo $default_page_title;
			?></TITLE>
		<META http-equiv="Content-Style-Type" content="text/css">
		<link rel="stylesheet" type="text/css" href="default.css" />
		<script type="text/javascript" src="calendar.js"></script>
	</HEAD>
<?
if(file_exists("versions.php") && file_exists("CHANGELOG")) {
	include("versions.php");
	$changelog = "CHANGELOG";
}
else if(file_exists("../versions.php") && file_exists("../CHANGELOG")) {
	include("../versions.php");
	$changelog = "../CHANGELOG";
}

if ($notables!=1 && 1==$IsLoggedIn) { ?>
	<BODY bgcolor="000000" style="<?=$body_style?>">
	<TABLE width=100% cellpadding=5 cellspacing=3>
	<TR><TD colspan=3 valign=middle align=center><? include "titlebar.php"; ?></TD></TR>
	<tr><td class="infobar" colspan=3 bgcolor=<?=$color_table_title?>>
		<table class="infobar" width=100%><tr>
			<td width="33%" align=left valign=middle><?=$fulluname?> [<a class="titlebar" href="logout.php?SID=<?=$SID?>&Action=Logout">Logout</a>]</td>
			<td width="33%" align=center valign=middle><?=date("g:ia  F j, Y")?></td>
			<td width="33%" align=right valign=middle><?echo $version_type, ' Version ', constant('version_'.$version_type);?></td>
		</tr></table>
	</td></tr>

	<TR>
	<?if ($sideless!=1) {?>
		<TD width=200 bgcolor=<?=$color_page_dk_bg?> valign=top>
		<?
		if (0==$IsLoggedIn) {
			include "leftcol.php";
		} else {
			include "leftcol.loggedin.php";
		}
		?>
		</TD><TD colspan=2 width=* bgcolor=<?=$color_page_lt_bg?> valign=top>
	<? } else { ?> <TD colspan=3 bgcolor=<?=$color_page_lt_bg?>> <?
	}
 }

else if (1==$notables) { ?><BODY BGCOLOR=#EEEEEE style="<?=$body_style?>"><? }
else { ?><BODY style="<?=$body_style?>"><?}

} //end inclusion block
?>

