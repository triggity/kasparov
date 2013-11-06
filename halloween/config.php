<?
if (!defined("Config_Included")) { //protect from double inclusion
define("Config_Included",TRUE);
foreach ($_REQUEST as $key => $value)
       $GLOBALS[$key] = $value;
foreach ($_SERVER as $key => $value)
       $GLOBALS[$key] = $value;

//Help Desk System configuration
$default_page_title = "Santa Clara University Technology Student Support Services";
$admin_email = "mmiller@scu.edu";
$admin_name = "Michael Miller";

//general system database
$database_url = "localhost";
$database_name = "scu_tech_help";
$database_uname = "stdsvc";
$database_upass = "skippy4u";

//blueform database
$forms_url = "129.210.9.248:3306";
$forms_name = "blueform";
$forms_uname = "blueform-r";
$forms_upass = "eulbdaer";

//Stable, Beta, Development, Unstable
$version_type = "Stable";

$news_first_limit=4;
$news_next_limit=15;
$news_sort = 0; //1 = Sort by priority, 0 = sort by date only
$news_expire = mktime(date("G"),date("i"),0,date("m"),date("d")+7,date("Y")); //default expiration
$news_noexpire = 0; //expire by default? 0=no, 1=yes
$news_default_priority = 1; //default priority
$news_showauthor = 1; //1=show name of author, 0=don't show author's name
$news_details = "Read More..."; //What to say for reading the full message
$news_date_limit = ""; //only show news dated more recent than this date - blank implies no limit
$news_items_shown = 0; //cleareds here to be set after news.php completes.
$news_default_spacer="<BR><BR>";
//What words to use to describe the various priorities
$news_priority[0] = "Low";
$news_priority[1] = "Normal";
$news_priority[2] = "High";
$news_priority[3] = "Highest";



$body_style="";
//$color_table_title = "EE4510"; //old orange
//$color_table_title = "990000"; //old red
$color_table_title = "9900CC"; //Title bar on tables
$color_table_lt_bg = "DFDFDF"; //light table background color
$color_table_dk_bg = "C8C8C8"; //dark table backround color
//$color_page_lt_bg = "EEEEEE"; //old light grey
$color_page_lt_bg = "CCCCCC"; //background of main column / tables in left column
$color_page_dk_bg = "000000"; //background of left column

//$ofont_title="<B>";
//$cfont_title="</B>";
$ofont_title = "<FONT color=#FFFFFF><B>"; //open font for table titles
$cfont_title = "</B></FONT>"; //close font for titles

//setttings for testing
/*$color_table_title = "220077";
$color_table_lt_bg = "6666FF";
$color_table_dk_bg = "0000FF";
$color_page_lt_bg = "9999FF";*/

$color_unassigned_schedule="BBBBBB";

//Settings for ted
if ($REMOTE_ADDR=="129.210.129.210" || $REMOTE_ADDR=="129.210.128.128") {
	$color_table_title="101099";
	$ofont_title = "<FONT color=#FFFFFF><B>"; //open font for table titles
	$cfont_title = "</B></FONT>"; //close font for titles
}

//reset variables to prevent hacks:
$Automatic=0;
$loginIncluded="N";

}
?>
