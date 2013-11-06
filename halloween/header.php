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
?>


<SCRIPT TYPE="text/javascript">
 /**
  * You may use this code for free on any web page provided that 
  * these comment lines and the following credit remain in the code.
  * Compact Cross Browser Ghosts effect by http://www.JavaScript-fx.com
  */
if(!window.JSFX) JSFX=new Object();
JSFX.ghostImages = new Array( 
	"<img src='ghost0.gif'>",
	"<img src='ghost1.gif'>",
	"<img src='ghost2.gif'>",
	"<img src='ghost2.gif'>"
);
var ns4 = document.layers;
var ie4 = document.all;
JSFX.makeLayer = function(id)
{
	var el = 	document.getElementById	? document.getElementById(id) :
			document.all 		? document.all[id] :
							  document.layers[id];
	if(ns4) el.style=el;
	el.sP=function(x,y){this.style.left = x;this.style.top=y;};
	el.show=function(){ this.style.visibility = "visible"; } 
	el.hide=function(){ this.style.visibility = "hidden"; } 
	if(ns4 || window.opera) 
		el.sO = function(pc){return 0;};
	else if(ie4)
		el.sO = function(pc)
		{
			if(this.style.filter=="")
				this.style.filter="alpha(opacity=100);";
			this.filters.alpha.opacity=pc;
		}
	else
		el.sO = function(pc){this.style.MozOpacity=pc/100;}

	return el;
}

if(window.innerWidth)
{
	gX=function(){return innerWidth;};
	gY=function(){return innerHeight;};
}
else
{
	gX=function(){return document.body.clientWidth-30;};
	gY=function(){return document.body.clientHeight-30;};
}
JSFX.ghostOutput=function()
{
	for(var i=0 ; i<JSFX.ghostImages.length ; i++)
		document.write(ns4 ? "<LAYER  NAME='gh"+i+"'>"+JSFX.ghostImages[i]+"</LAYER>" : 
					   "<DIV id='gh"+i+"' style='position:absolute'>"+JSFX.ghostImages[i]+"</DIV>" );
	
}
JSFX.ghostSprites = new Array();
JSFX.ghostStartAni = function()
{
	for(var i=0 ;i<JSFX.ghostImages.length;i++)
	{
		var el=JSFX.makeLayer("gh"+i);
		el.x=Math.random()*gX();
		el.y=Math.random()*gY();
		el.tx=Math.random()*gX();
		el.ty=Math.random()*gY();
		el.dx=-5+Math.random()*10;
		el.dy=-5+Math.random()*10;
		el.state="off";
		el.op=0;
		el.sO(el.op);
		el.hide();
		JSFX.ghostSprites[i] = el;
	}
	setInterval("JSFX.ghostAni()", 40);
}
JSFX.ghostAni = function()
{
	for(var i=0 ;i<JSFX.ghostSprites.length;i++)
	{
		el=JSFX.ghostSprites[i];

		if(el.state == "off")
		{
			if(Math.random() > .99)
			{
				el.state="up";
				el.show();
			}
		}
		else if(el.state == "on")
		{
			if(Math.random() > .98)
				el.state="down";
		}
		else if(el.state == "up")
		{
			el.op += 2;
			el.sO(el.op);
			if(el.op==100)
				el.state = "on";
		}
		else if(el.state == "down")
		{
			el.op -= 2;
			if(el.op==0)
			{
				el.hide();
				el.state = "off";
			}
			else
				el.sO(el.op);
		}

		var X = (el.tx - el.x);
		var Y = (el.ty - el.y);
		var len = Math.sqrt(X*X+Y*Y);
		if(len < 1) len = 1;
		var dx = 20 * (X/len);
		var dy = 20 * (Y/len);
		var ddx = (dx - el.dx)/10;
		var ddy = (dy - el.dy)/10;
		el.dx += ddx;
		el.dy += ddy;
		el.sP(el.x+=el.dx,el.y+=el.dy);

		if(Math.random() >.95 )
		{
			el.tx = Math.random()*gX();
			el.ty = Math.random()*gY();
		}

	}
}
JSFX.ghostStart = function()
{
	if(JSFX.ghostLoad)JSFX.ghostLoad();
	JSFX.ghostStartAni();
}
JSFX.ghostOutput();
JSFX.ghostLoad=window.onload;
window.onload=JSFX.ghostStart;

</SCRIPT>

<?
} //end inclusion block
?>

