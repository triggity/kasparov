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
	</HEAD>
<?
if ($notables!=1 && 1==$IsLoggedIn) { ?>
	<BODY BGCOLOR=#000000 style="<?=$body_style?>">
	<TABLE width=100% cellpadding=5 cellspacing=3>
	<TR><TD colspan=3 valign=middle align=center><? include "titlebar.php"; ?></TD></TR><TR>
	<?if ($sideless!=1) {?>
		<TD width=200 bgcolor=<?=$color_page_dk_bg?> valign=top>
		<?
		if (0==$IsLoggedIn) {
			include "leftcol.php";
		} else {
			include "leftcol.loggedin.php";
		}
		?>
		</TD><TD width=* bgcolor=<?=$color_page_lt_bg?> valign=top>
	<? } else { ?> <TD colspan=3 bgcolor=<?=$color_page_lt_bg?>> <?
	}
 }

else if (1==$notables) { ?><BODY BGCOLOR=#EEEEEE style="<?=$body_style?>"><? }
else { ?><BODY style="<?=$body_style?>"><?}

} //end inclusion block
?>

