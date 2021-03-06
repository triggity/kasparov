<?
$thecurrentdir=getcwd(); //store where we are now
chdir("/var/www/html/sts/helpdesk");
include "config.php";
include "database.php";
include "functions.php";
include "login.php";

//Highlight text from search phrase
function HighlightText($Text, $Words) {

	if ("Y"==$GLOBALS["ExactMatch"]) {
		$MoreWords[0]=$Words;
	} else {
		$MoreWords=explode(" ",$Words);
	}

	for ($i=0;$i<count($MoreWords);$i++) {
		if (strlen($MoreWords[$i])>0) {
			$Text=eregi_replace($MoreWords[$i],"<font style=\x22background: yellow; color: black\x22>".$MoreWords[$i]."</font>",$Text);
		}
	}
	return $Text;

}

//First, clean that which should have expired:
$x=mysql_query("SELECT ID FROM Messages WHERE Expires < NOW()");
for ($i=0;$i<mysql_num_rows($x);$i++) {

	$y=mysql_query("LOCK TABLES Messages WRITE");
	//Remove the message itself, so others don't
	$y=mysql_query("DELETE FROM Messages WHERE ID=".mysql_result($x,$i,0));

	//the Remove children
	$y=mysql_query("DELETE FROM Messages WHERE Parent=".mysql_result($x,$i,0));
	$y=mysql_query("UNLOCK TABLES");

}


//Find world bits
$WorldBits=mysql_result(mysql_query("SELECT Sum(1<<Num) FROM Boards WHERE WorldReadable='Y'"),0,0);

if ("Edit"==$Action || "Post Update"==$Action || "Delete"==$Action || "Really Delete"==$Action || "Comment"==$Action) { //If the user wants to edit a message, make sure it exists and they can do so.


	$mq="	SELECT
			Title AS Title,
			Messages.ID AS ID,
			Author AS CID,
			Parent AS Parent
		FROM
			Messages
		WHERE
			".(("Y"==$userdata["IsAdmin"])?"":("Comment"==$Action?"Boards & ".($userdata["BoardBits"]+0)." = Boards AND":"$CampusID = Messages.Author AND"))."
			Messages.ID = $ID";

//	echo $mq."<BR>";
	$message=mysql_query($mq);

	if (0==mysql_num_rows($message)) {

		?><H2>You do not have permission to modify this message</H2><?
	
		$Action="Read";
		//$mode="List";

	}

	
	$Parent=mysql_result($message,0,"Parent");
	$CommentMode=0;
	if ("Comment"==$Action) {$CommentMode=1;} //How to edit the message

	if ("Edit"==$Action) { //Get Ready to Edit

		if (mysql_result($message,0,"Parent")!=$ID) {
			$CommentMode=1;
		}

		//Load existing values for editing
		$message=mysql_query("
		SELECT
			Title,
			Icon,
			Priority,
			Expires,
			Boards,
			Abstract,
			Body
		FROM
			Messages
		WHERE
			Messages.ID = $ID
		GROUP BY Messages.ID
		ORDER BY Messages.Created DESC
		");
		$Title=mysql_result($message,0,"Title");
		$Icon=mysql_result($message,0,"Icon");
		$Abstract=mysql_result($message,0,"Abstract");
		$news_default_priority=mysql_result($message,0,"Priority");
		$Body=mysql_result($message,0,"Body");
		$news_boards=mysql_result($message,0,"Boards");
		if (mysql_result($message,0,"Expires")!="") {
			$news_expire = strtotime(mysql_result($message,0,"Expires"));
		} else {
			$news_noexpire=1;
		}
	} else if ("Delete"==$Action) {
	?>
		<CENTER>
		<FORM METHOD=POST>
		<H2>Are you sure you want to delete message <?=$ID?> (<?=mysql_result($message,0,"Title")?>)?</H2>
		<INPUT TYPE=HIDDEN NAME="SID" VALUE="<?=$SID?>">
		<INPUT TYPE=HIDDEN NAME="ID" VALUE="<?=$ID?>">
		<INPUT TYPE=SUBMIT NAME="Action" VALUE="Really Delete">
		</FORM>
		</CENTER>
	<?
		$news_items_shown=1; //not exactly true, but needed for correct behavior.
		$mode="";
		$Action="";
	} else if ("Really Delete"==$Action) {
		$y=mysql_query("LOCK TABLES Messages WRITE");
		//Remove the message itself, so others don't
		$y=mysql_query("DELETE FROM Messages WHERE ID=".$ID);

		//the Remove children
		$y=mysql_query("DELETE FROM Messages WHERE Parent=".$ID);
		$y=mysql_query("UNLOCK TABLES");


		?><H2>Message Deleted.</H2><?
		$mode="List";
		$Action="";
	}

}

if ("Post Message"==$Action || "Post Update"==$Action) { //Post or update a message

	MustLogIn();


	//Create the proper bitmap for where we are posting
	$BBits=0;
	for ($i=0;$i<count($Boards);$i++) {
		$BBits+=(1<<$Boards[$i]);
	}


	if ("Y"!=$userdata["IsAdmin"]) { //Ordinary users must have their proper bits set.
		$BBits&=$userdata["BoardBits"];
	}

	if ((0==$BBits && 0==$Parent)|| ""==$Abstract) { //if it's not going anywhere, it can't be posted.
		?><H1>Missing Information...</H1><?
	} else {

		if ("Y"==$NoExpire) { //figure out what to do about expiration
			$Expires="NULL";
		} else {
			if (12==$Hour) {$Hour=0;} // adjust for midnight
			$Expires="'$Year-$Month-$Day ".(("PM"==$HalfDay)?($Hour+12):$Hour).":$Minute:00'";
		}

		if ("Y"!=$userdata["IsAdmin"] && 3==$Priority) { //Limit highest priority
			$Priority=2;
		}

		if ("Post Message"==$Action) {
 			if ("LFtoBR"==$Convert) {
				$Body=ereg_replace("\n","<BR> \n",$Body);
				$Abstract=ereg_replace("\n","<BR> \n",$Abstract);
			}
			$x=mysql_query("
			INSERT INTO Messages
			(
				Boards,
				Author,
				Title,
				Abstract,
				Body,
				Icon,
				Expires,
				Priority,
				Created,
				Parent
			)
			VALUES
			(
				$BBits,
				$CampusID,
				'$Title',
				'$Abstract',
				'$Body',
				".(int)$Icon.",
				$Expires,
				".(int)$Priority.",
				NULL,
				".(int)$Parent."
			)
			");

			$ID=mysql_insert_id();
			if (0==$Parent) {
				$x=mysql_query("UPDATE Messages SET Parent=$ID WHERE ID=$ID");
			}

		} else { //Updating Message
			$x=mysql_query("
			UPDATE Messages
			SET
				Boards=$BBits,
				Title='$Title',
				Abstract='$Abstract',
				Body='$Body',
				Icon='".(int)$Icon."',
				Expires=$Expires,
				Priority=".(int)$Priority.",
				Created=NULL
			WHERE
				ID = $ID
			");
		}
		$Action="Read";
	}
}


if ("Read"==$Action) {
	$mq="
		SELECT
 			".sql_nick("People.First","People.Nick","People.Last")." AS Creator,
			Messages.Title AS Title,

			Messages.Abstract AS Abstract,
			Icons.File AS Icon,
			".sql_daytime("Messages.Created")." AS Created,
			".sql_daytime("Messages.Expires")." AS Expiration,
			Messages.Priority AS Priority,
			Messages.ID AS ID,
			Messages.Author AS CID,
			Messages.Body AS Body,
			Messages.Boards AS Boards,
			Messages.Parent AS Parent
		FROM
			Messages, People, Icons, Messages AS m2
		WHERE
			(m2.Boards & ($WorldBits | ".("Y"==$userdata["IsAdmin"]?"-1":($userdata["BoardBits"]+0))."))> 0 AND
			People.CampusID = Messages.Author AND
			Icons.ID = Messages.Icon AND
			Messages.ID = $ID AND
			m2.ID = Messages.Parent
		GROUP BY Messages.ID
		ORDER BY Messages.Created DESC
	";
	//echo $mq."<BR>";
	$message=mysql_query($mq);
	if (0==mysql_num_rows($message)) {
	?>
		<H2>Message <?=$ID?> Not Found.</H2>
		<I>Perhaps the message has expired or you do not have sufficient permissions to read it.</I>
	<?
	} else {
		$news_items_shown=1; //we are displaying one message
		//Log reads by people other than the author
		if ($CampusID!=mysql_result($message,0,"CID")) {
			if (1==$IsLoggedIn) {
				$x=mysql_query("UPDATE Messages SET StaffCount=StaffCount+1,Created=Created WHERE ID=$ID");
			} else {
//				$x=mysql_query("UPDATE Messages SET WorldCount=StaffCount+1, Created=Created WHERE ID=$ID");
			}
		}
	?>
		<TABLE width=100% cellspacing=0 border=0>
		<TR>
		<TD align=left BGCOLOR=<?=$color_table_title?>><?=$ofont_title?>
		<?=HighlightText(mysql_result($message,0,"Title"),$Phrase);?>&nbsp;
		<?=$cfont_title?></TD>
		<TD align=right BGCOLOR=<?=$color_table_title?>><?=$ofont_title?>
		<?=$news_priority[mysql_result($message,0,"Priority")];?>
		<?=$cfont_title?></TD>
		</TR>
		<TR><TD colspan=2 BGCOLOR=<?=$color_table_lt_bg?>>
		<?
		if (strlen(mysql_result($message,0,"Icon"))>0) {
			echo "<IMG align=right border=0 SRC=\x22";
			echo mysql_result($message,0,"Icon");
			echo "\x22>";
		}
		?>
		<B>
		<? if (1==$news_showauthor) {?>
		Posted By <?=mysql_result($message,0,"Creator")?> on 
		<? } ?>
		<?=mysql_result($message,0,"Created")?></B>
		<?=(strlen(mysql_result($message,0,"Expiration"))==0?"":"<BR>Expires: ".mysql_result($message,0,"Expiration"))?><BR><BR>
		<?=HighlightText(mysql_result($message,0,"Abstract"),$Phrase)?>
		<BR><BR>
		<?=HighlightText(mysql_result($message,0,"Body"),$Phrase)?><BR><BR>

		<? if (0<$IsLoggedIn) { ?>
		<B>(
			<? if (mysql_result($message,0,"Parent")!=$ID ) { ?>
				<A HREF="<?=$SCRIPT_NAME."?SID=".$SID."&mode=List&Action=Read&ID=".mysql_result($message,0,"Parent")."\x22>"?>View Parent</A>
			<? } else if ((((int)$userdata["BoardBits"] & (int)mysql_result($message,0,"Boards"))==(int)mysql_result($message,0,"Boards")|| "Y"==$userdata["IsAdmin"])) { ?>
				<A HREF="<?=$SCRIPT_NAME."?SID=".$SID."&mode=List&Action=Comment&ID=$ID\x22>"?>Post Comment</A>
			<? } ?>
			<? if ("Y"==$userdata["IsAdmin"] || mysql_result($message,0,"CID")==$CampusID) { ?>
				|
				<A HREF="<?=$SCRIPT_NAME."?SID=".$SID."&mode=List&Action=Delete&ID=$ID\x22>"?>Delete</A>
				|
				<A HREF="<?=$SCRIPT_NAME."?SID=".$SID."&mode=$mode&Action=Edit&ID=$ID\x22>"?>Edit</A>
			<? } ?>
		)</B>
		<? } ?>
		</TD></TR>
		</TABLE>

		<?
			$comments=mysql_query("
				SELECT
					concat(IF(LENGTH(Messages.Title)>0,concat('<B>',Messages.Title,'</B><BR>'),''),'Posted By <I>',".sql_nick("People.First","People.Nick","People.Last").",'</I> on ',".sql_daytime("Messages.Created").",'<BR><BR>',Messages.Abstract) AS Message,
					concat('(',LENGTH(Body),' bytes in body)') AS x,
					concat('$SCRIPT_NAME?Action=Read&ID=',Messages.ID) AS Hyperlink
				FROM
					Messages, People
				WHERE
					People.CampusID = Messages.Author AND
					Messages.Parent = $ID AND
					Messages.ID != $ID
				GROUP BY Messages.ID
				ORDER BY Messages.Created DESC
			");
			if (mysql_num_rows($comments)>0) {
				?><BR><BR><?
			 	MakeTable($comments,1,1,1,0,"Comments");
			}
		
		?>
		<BR><BR>
		
	<?
	}

} else {

if ("Post"==$mode || "Edit"==$Action || "Comment"==$Action) { //Show message posting form
	MustLogIn();
	$news_items_shown=1; //We're showing one news item.
	if ("Edit"==$Action) {
		?><H2>Edit Message:</H2><?
	} else if ("Comment"==$Action) {
		?><H2>Post Comment:</H2><?
	} else {
		?><H2>Post New Message:</H2><?
	}

	?>
	<FORM METHOD=POST>
	<INPUT TYPE=HIDDEN NAME="SID" VALUE="<?=$SID?>">
	<INPUT TYPE=HIDDEN NAME="ID" VALUE="<?=$ID?>">
	<TABLE cellspacing=4>
	<TR>
	<TD align=right>Title:</TD>
	<TD> <INPUT NAME="Title" SIZE=32 VALUE="<?=$Title?>" maxlength=96> </TD>
	<? if (1==$CommentMode) { ?>
		<!--This is a comment, so the board doesn't matter, just the parent.-->
		<INPUT TYPE=HIDDEN NAME="Parent" VALUE="<?=$Parent?>">

	<? } else { ?>
	<TD rowspan=4 valign=top align=right>
	<table border=0><TR><TD>
	Where to Post:<BR>
	<SELECT NAME="Boards[]" SIZE=8 MULTIPLE>
	<?
		$groups=mysql_query("
			SELECT Name, Num
			FROM Boards
			".(("Y"!=$userdata["IsAdmin"])?("WHERE ((1<<Num) & ".$userdata["BoardBits"].")>0"):"")."
		");
		for ($i=0;$i<mysql_num_rows($groups);$i++) {
			echo "<OPTION VALUE=\x22".mysql_result($groups,$i,"Num")."\x22 ";
			if (((1<<mysql_result($groups,$i,"Num")) & $news_boards) >0) {
				echo "SELECTED";
			}
			echo ">";
			echo mysql_result($groups,$i,"Name");
			echo "</OPTION>";
		}
	?>
	</SELECT>
	</TR></TD></TABLE>

	</TD>
	<? } ?>

	</TR><TR>
	<? if (0==$CommentMode) { //the following is only for non comments?>
	<TD align=right>Icon:</TD>
	<TD>
	<SELECT NAME="Icon">
		<?
		$icons=mysql_query("SELECT Name, ID From Icons ORDER BY ID");
		for ($i=0;$i<mysql_num_rows($icons);$i++) {
			echo "<OPTION VALUE=\x22".mysql_result($icons,$i,"ID")."\x22 ";
			if (mysql_result($icons,$i,"ID")==$Icon) {
				echo "SELECTED";
			}
			echo ">";
			echo mysql_result($icons,$i,"Name");
			echo "</OPTION>";
		}
		?>
	</SELECT>
	</TD>
	</TR><TR>
	<TD align=right>Priority:</TD>
	<TD>
	<SELECT NAME="Priority">
		<OPTION VALUE=0 <?=(0==$news_default_priority)?"SELECTED":""?>><?=$news_priority[0]?></OPTION>
		<OPTION VALUE=1 <?=(1==$news_default_priority)?"SELECTED":""?>><?=$news_priority[1]?></OPTION>
		<OPTION VALUE=2 <?=(2==$news_default_priority)?"SELECTED":""?>><?=$news_priority[2]?></OPTION>
		<? if ("Y"==$userdata["IsAdmin"]) { //Only Admins can do Highest ?>
		<OPTION VALUE=3 <?=(3==$news_default_priority)?"SELECTED":""?>><?=$news_priority[3]?></OPTION>
		<? } ?>
	</SELECT>
	</TD>
	</TR><TR>
	<? } //end non-comment part?>
	<TD align=right valign=top>Expires:</TD>
	<TD>
	<?
		$x=DateOpt("",$news_expire);
		?>&nbsp;&nbsp;&nbsp;&nbsp;<?
		$x=TimeOpt("",$news_expire);
	?><BR>
	<INPUT TYPE=CHECKBOX NAME="NoExpire" VALUE="Y" <?=(1==$news_noexpire)?"CHECKED":""?>> No Expiration
	</TD>
	</TR><TR>
	<TD align=right valign=top>Abstract:<BR>(255 Characters max)</TD>
	<TD colspan=2><TEXTAREA NAME="Abstract" COLS=52 ROWS=4 maxlength=255><?=$Abstract?></TEXTAREA></TD>
	</TR><TR>
	<TD align=right valign=top>Body:<BR>(optional)</TD>
	<TD colspan=2><TEXTAREA NAME="Body" COLS=52 ROWS=11 maxlength=65535><?=$Body?></TEXTAREA></TD>
	</TR>
	<TR><TD colspan=3 align=right>
	<BR>
	<? if ("Edit"==$Action) { ?>
	<INPUT TYPE="SUBMIT" NAME="Action" VALUE="Post Update">
	<? } else { ?>
		<INPUT TYPE="CHECKBOX" NAME="Convert" VALUE="LFtoBR" CHECKED>&nbsp;Translate New-Lines into HTML &lt;BR&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<INPUT TYPE="SUBMIT" NAME="Action" VALUE="Post Message">
	<? } ?>
	</TD></TD>
	</TABLE>
	</FORM>
	<?

} else if ("List"==$mode || "Search"==$mode) {

	$MBits=$Bits;
	if ("Y"!=$userdata["IsAdmin"]) { //Limit was people can see
		$MBits=(int)$Bits & (int)((int)$userdata["BoardBits"] | (int)$WorldBits);
	}
	//echo $userdata["IsAdmin"].$MBits."<BR>".$userdata["BoardBits"].":".$Bits."<BR>";

	if ($startpoint>0) { //determine how many items to show
		$mylimit=$news_next_limit;
	} else {
		$startpoint=0;
		$mylimit=$news_first_limit;
	}

	$SearchString=""; // clear variable just in case
	if ("Search"==$mode) { //What to search for if we're searching
		$SearchString =" AND (";

		//Make the string SQL Search Friendly
		$KeyWords=str_replace("%","\%",$Phrase);
		$KeyWords=str_replace("_","\_",$KeyWords);

		//Tokenize!

		if ("Y"==$ExactMatch) { //are we looking for an exact phrase?
			$Keys[0]=$KeyWords;
		} else {
			$Keys=explode(" ", $KeyWords);

		}

		for ($i=0;$i<count($Keys);$i++) {
		   if (strlen($Keys[$i])>0) { //only act if the string is non-null
			$SearchString.="Messages.Title LIKE '%".$Keys[$i]."%' OR ";
			$SearchString.="Messages.Abstract LIKE '%".$Keys[$i]."%' OR ";
			$SearchString.="Messages.Body LIKE '%".$Keys[$i]."%' OR ";
			$SearchString.="m2.Title LIKE '%".$Keys[$i]."%' OR ";
			$SearchString.="m2.Body LIKE '%".$Keys[$i]."%' OR ";
			$SearchString.="m2.Abstract LIKE '%".$Keys[$i]."%' OR ";

		   }
		}
		

		$SearchString.=" 1=0 ) "; //cheezy way of dealing with the last "OR"
	}

	//Read up list of messages
	//FYI: m2 = Messages' Children - used to count the # of comments
	//     m3 = messages meeting date criteria
	$mq="
		SELECT
			".sql_nick("People.First","People.Nick","People.Last")." AS Creator,
			Messages.Title AS Title,
			Messages.Abstract AS Abstract,
			Icons.File AS Icon,
			".sql_daytime("Messages.Created")." AS Created,
			Messages.Priority AS Priority,
			Messages.ID AS ID,
			Messages.Author AS CID,
			LENGTH(Messages.Body) AS Body,
			COUNT(*)-1 AS Comments
		FROM
			Messages, People, Icons, Messages as m2 ".(($news_date_limit=="")?"":", Messages as m3")."
		WHERE
			(Messages.Boards & $Bits & $MBits)>0 AND
			People.CampusID = Messages.Author AND
			Icons.ID = Messages.Icon AND
			m2.Parent = Messages.ID
			".$SearchString."
			".(($news_date_limit=="")?"":"AND m3.Parent=Messages.ID AND m3.Created > $news_date_limit")."
		GROUP BY Messages.ID
		ORDER BY ".((1==$news_sort)?"Priority DESC,":"")." Messages.Created DESC
		LIMIT $startpoint, ".($mylimit+1)."
	";
	//echo $mq."<BR>";
	$messages=mysql_query($mq);
	$news_items_shown=mysql_num_rows($messages); //set this variable for forms that will use it after news.php is done.

	for ($i=0;$i<mysql_num_rows($messages) && $i<$mylimit;$i++) {
		?><TABLE width=100% cellspacing=0 border=0>
		<TR>
		<TD align=left BGCOLOR=<?=$color_table_title?>><?=$ofont_title?>
		<?=HighlightText(mysql_result($messages,$i,"Title"),$Phrase);?>&nbsp;
		<?=$cfont_title?></TD>
		<TD align=right BGCOLOR=<?=$color_table_title?>><?=$ofont_title?>
		<?=$news_priority[mysql_result($messages,$i,"Priority")];?>
		<?=$cfont_title?></TD>
		</TR>
		<TR><TD colspan=2 BGCOLOR=<?=$color_table_lt_bg?>>
		<?
		if (strlen(mysql_result($messages,$i,"Icon"))>0) {
			echo "<IMG align=right border=0 SRC=\x22";
			echo mysql_result($messages,$i,"Icon");
			echo "\x22>";
		}
		?>
		<B>
		<? if (1==$news_showauthor) {?>
		Posted By <?=mysql_result($messages,$i,"Creator")?> on 
		<? } ?>
		<?=mysql_result($messages,$i,"Created")?></B><BR><BR>
		<?=HighlightText(mysql_result($messages,$i,"Abstract"),$Phrase)?>
		<BR><BR>
		<? if (0<$IsLoggedIn || mysql_result($messages,$i,"Body")>3 || mysql_result($messages,$i,"CID")==$CampusID) { ?>
		<B>(
		<A HREF="<?=$SCRIPT_NAME."?SID=".$SID."&mode=List&Action=Read&ID=".mysql_result($messages,$i,"ID")."&Phrase=".str_replace(" ","%20",$Phrase)."&ExactMatch=".$ExactMatch."\x22>".$news_details?></A>
		<?
		if (mysql_result($messages,$i,"Body")>3 && 1==$IsLoggedIn)  {
			echo "| </B>".mysql_result($messages,$i,"Body")." bytes in body <B>";
		}
		if (1==mysql_result($messages,$i,"Comments"))  {
			if (""==$news_date_limit) {
				echo "| </B>".mysql_result($messages,$i,"Comments")." comment <B>";
			} else {
				echo "| </B>1 new comment<B>";
			}
		}
		if (1<mysql_result($messages,$i,"Comments"))  {
			if (""==$news_date_limit) {
				echo "| </B>".mysql_result($messages,$i,"Comments")." comments <B>";
			} else {
				echo "| </B>new comments <B>";
			}
		}
		?>)</B>
		<? } ?>
		</TD></TR>
		</TABLE>
		<?
		echo $news_default_spacer;
	}

	?><P align = right><B><?
	if ($startpoint>0) { //all ready been browsing
		$prevpoint=$startpoint-$news_next_limit;
		if ($prevpoint<0) {$prevpoint=0;} //can only go back to 0

		echo "<A HREF=\x22".$SCRIPT_NAME."?SID=".$SID."&mode=List&startpoint=".$prevpoint;
		echo "\x22>Previous Messages</A>";

		if (mysql_num_rows($messages) > $mylimit) {echo "  |  ";} //make it look pretty
		
	}

	if (mysql_num_rows($messages) > $mylimit) {
		echo "<A HREF=\x22".$SCRIPT_NAME."?SID=".$SID."&mode=List&startpoint=".($startpoint+mysql_num_rows($messages)-1);
		echo "\x22>More Messages...</A>";
	}
	?></B></P><?
		
}
}

chdir($thecurrentdir); //go back to where we were
?>
