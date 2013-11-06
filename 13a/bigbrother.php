<?
include "config.php";
include "database.php";

include "login.php";

	include "header.php";
	MustLogIn(1);
	
	?>
	
	<FRAMESET ROWS="*,0" BORDER=0 CELLPADDING="0">
  <FRAME NAME="ptz" SRC="129.210.196.138/view/view.shtml" marginHeight="0" marginWidth="0" SCROLLING="YES">
  <FRAME NAME="status_frame" SRC="status.html" SCROLLING="NO">
  <FRAME NAME="Temp" SRC="" marginHeight="0" marginWidth="0" SCROLLING="NO">
</FRAMESET>

<center><table cellspacing=0 celpadding=0 border=0>
	<tr>
	<td>	
	<!-- Cut from here to the end of image display comment -->
<!-- Note: If you do not see a JavaScript below in the view source window you must -->
<!-- first save the html file from your browser, then open the saved -->
<!-- file in a text editor, for instance Notepad.-->

<SCRIPT LANGUAGE="JavaScript">
 // Set the BaseURL to the url of your camera
 // Example:  var BaseURL = "http://172.21.1.122/";
 // Since this file is located inside the unit itself, no base url is specified here
 var BaseURL = "http://129.210.196.140/";

 // DisplayWidth & DisplayHeight specifies the displayed width & Height of the image.
 // You may change these numbers, the effect will be a strech or a shrink of the image
 var DisplayWidth = "320";
 var DisplayHeight = "240";

 // This is the filepath to the video generating file inside the camera itself
 var File = "cgi-bin/video320x240.mjpg";

 // No changes required below this point

var output = "";
if ((navigator.appName == "Microsoft Internet Explorer")&&(navigator.platform != "MacPPC")&&(navigator.platform != "Mac68k"))
{ 
 // If Internet Explorer for Windows then use ActiveX 
  output =  "<OBJECT ID=\"CamImage\" WIDTH="
  output += DisplayWidth;
  output += " HEIGHT=";
  output += DisplayHeight;
  output += " CLASSID=CLSID:917623D1-D8E5-11D2-BE8B-00104B06BDE3 ";
  output += "CODEBASE=\"";
  output += BaseURL;
  output += "activex/AxisCamControl.ocx#Version=1,0,1,34\">";
  output += "<PARAM NAME=\"URL\" VALUE=\"";
  output += BaseURL;
  output += File;
  output += "\"> <BR><B>Axis ActiveX Camera Control</B><BR>";
  output += "The AXIS ActiveX Camera Control, which enables you ";
  output += "to view live image streams in Microsoft Internet";
  output += " Explorer, could not be registered on your computer.";
  output += "<BR></OBJECT>"; 
} 
else 
{
  // If not IE for Windows use the browser itself to display
  output = "<IMG SRC=\"";
  output += BaseURL;
  output += File;
  output += "?dummy=garb\" HEIGHT=\"";
  // The above dummy cgi-parameter helps some versions of NS
  output += DisplayHeight;
  output += "\" WIDTH=\"";
  output += DisplayWidth;
  output += "\" ALT=\"Moving Image Stream\">";
} 

document.write(output);

</SCRIPT>	
	</td>
	<td>


	<!-- Cut from here to the end of image display comment -->
<!-- Note: If you do not see a JavaScript below in the view source window you must -->
<!-- first save the html file from your browser, then open the saved -->
<!-- file in a text editor, for instance Notepad.-->

<SCRIPT LANGUAGE="JavaScript"> 
 // Set the BaseURL to the url of your camera
 // Example:  var BaseURL = "http://172.21.1.122/";
 // Since this file is located inside the unit itself, no base url is specified here
 var BaseURL = "http://scu:scu@129.210.196.141/";

 // DisplayWidth & DisplayHeight specifies the displayed width & Height of the image.
 // You may change these numbers, the effect will be a strech or a shrink of the image
 var DisplayWidth = "320";
 var DisplayHeight = "240";

 // This is the filepath to the video generating file inside the camera itself
 var File = "cgi-bin/video320x240.mjpg";

 // No changes required below this point

var output = "";
if ((navigator.appName == "Microsoft Internet Explorer")&&(navigator.platform != "MacPPC")&&(navigator.platform != "Mac68k"))
{ 
 // If Internet Explorer for Windows then use ActiveX 
  output =  "<OBJECT ID=\"CamImage\" WIDTH="
  output += DisplayWidth;
  output += " HEIGHT=";
  output += DisplayHeight;
  output += " CLASSID=CLSID:917623D1-D8E5-11D2-BE8B-00104B06BDE3 ";
  output += "CODEBASE=\"";
  output += BaseURL;
  output += "activex/AxisCamControl.ocx#Version=1,0,1,34\">";
  output += "<PARAM NAME=\"URL\" VALUE=\"";
  output += BaseURL;
  output += File;
  output += "\"> <BR><B>Axis ActiveX Camera Control</B><BR>";
  output += "The AXIS ActiveX Camera Control, which enables you ";
  output += "to view live image streams in Microsoft Internet";
  output += " Explorer, could not be registered on your computer.";
  output += "<BR></OBJECT>"; 
} 
else 
{
  // If not IE for Windows use the browser itself to display
  output = "<IMG SRC=\"";
  output += BaseURL;
  output += File;
  output += "?dummy=garb\" HEIGHT=\"";
  // The above dummy cgi-parameter helps some versions of NS
  output += DisplayHeight;
  output += "\" WIDTH=\"";
  output += DisplayWidth;
  output += "\" ALT=\"Moving Image Stream\">";
} 

document.write(output);

</SCRIPT>	



</td></tr>
<tr><td>
<!-- Cut from here to the end of image display comment -->
<!-- Note: If you do not see a JavaScript below in the view source window you must -->
<!-- first save the html file from your browser, then open the saved -->
<!-- file in a text editor, for instance Notepad.-->

<SCRIPT LANGUAGE="JavaScript">
 // Set the BaseURL to the url of your camera
 // Example:  var BaseURL = "http://172.21.1.122/";
 // Since this file is located inside the unit itself, no base url is specified here
 var BaseURL = "http://129.210.196.142/";

 // DisplayWidth & DisplayHeight specifies the displayed width & Height of the image.
 // You may change these numbers, the effect will be a strech or a shrink of the image
 var DisplayWidth = "320";
 var DisplayHeight = "240";

 // This is the filepath to the video generating file inside the camera itself
 var File = "cgi-bin/video320x240.mjpg";

 // No changes required below this point

var output = "";
if ((navigator.appName == "Microsoft Internet Explorer")&&(navigator.platform != "MacPPC")&&(navigator.platform != "Mac68k"))
{ 
 // If Internet Explorer for Windows then use ActiveX 
  output =  "<OBJECT ID=\"CamImage\" WIDTH="
  output += DisplayWidth;
  output += " HEIGHT=";
  output += DisplayHeight;
  output += " CLASSID=CLSID:917623D1-D8E5-11D2-BE8B-00104B06BDE3 ";
  output += "CODEBASE=\"";
  output += BaseURL;
  output += "activex/AxisCamControl.ocx#Version=1,0,1,34\">";
  output += "<PARAM NAME=\"URL\" VALUE=\"";
  output += BaseURL;
  output += File;
  output += "\"> <BR><B>Axis ActiveX Camera Control</B><BR>";
  output += "The AXIS ActiveX Camera Control, which enables you ";
  output += "to view live image streams in Microsoft Internet";
  output += " Explorer, could not be registered on your computer.";
  output += "<BR></OBJECT>"; 
} 
else 
{
  // If not IE for Windows use the browser itself to display
  output = "<IMG SRC=\"";
  output += BaseURL;
  output += File;
  output += "?dummy=garb\" HEIGHT=\"";
  // The above dummy cgi-parameter helps some versions of NS
  output += DisplayHeight;
  output += "\" WIDTH=\"";
  output += DisplayWidth;
  output += "\" ALT=\"Moving Image Stream\">";
} 

document.write(output);

</SCRIPT>
</td>
<td>
	<!-- Cut from here to the end of image display comment -->
<!-- Note: If you do not see a JavaScript below in the view source window you must -->
<!-- first save the html file from your browser, then open the saved -->
<!-- file in a text editor, for instance Notepad.-->

<SCRIPT LANGUAGE="JavaScript">
 // Set the BaseURL to the url of your camera
 // Example:  var BaseURL = "http://172.21.1.122/";
 // Since this file is located inside the unit itself, no base url is specified here
 var BaseURL = "http://129.210.196.143/";

 // DisplayWidth & DisplayHeight specifies the displayed width & Height of the image.
 // You may change these numbers, the effect will be a strech or a shrink of the image
 var DisplayWidth = "320";
 var DisplayHeight = "240";

 // This is the filepath to the video generating file inside the camera itself
 var File = "cgi-bin/video320x240.mjpg";

 // No changes required below this point

var output = "";
if ((navigator.appName == "Microsoft Internet Explorer")&&(navigator.platform != "MacPPC")&&(navigator.platform != "Mac68k"))
{ 
 // If Internet Explorer for Windows then use ActiveX 
  output =  "<OBJECT ID=\"CamImage\" WIDTH="
  output += DisplayWidth;
  output += " HEIGHT=";
  output += DisplayHeight;
  output += " CLASSID=CLSID:917623D1-D8E5-11D2-BE8B-00104B06BDE3 ";
  output += "CODEBASE=\"";
  output += BaseURL;
  output += "activex/AxisCamControl.ocx#Version=1,0,1,34\">";
  output += "<PARAM NAME=\"URL\" VALUE=\"";
  output += BaseURL;
  output += File;
  output += "\"> <BR><B>Axis ActiveX Camera Control</B><BR>";
  output += "The AXIS ActiveX Camera Control, which enables you ";
  output += "to view live image streams in Microsoft Internet";
  output += " Explorer, could not be registered on your computer.";
  output += "<BR></OBJECT>"; 
} 
else 
{
  // If not IE for Windows use the browser itself to display
  output = "<IMG SRC=\"";
  output += BaseURL;
  output += File;
  output += "?dummy=garb\" HEIGHT=\"";
  // The above dummy cgi-parameter helps some versions of NS
  output += DisplayHeight;
  output += "\" WIDTH=\"";
  output += DisplayWidth;
  output += "\" ALT=\"Moving Image Stream\">";
} 

document.write(output);

</SCRIPT>	
</td></tr>	
</table></center>


	<?


	// echo "Good Morning, ".$fulluname;
?><P><P align=center>
<IMG SRC="/cgi-bin/indycam-half.cgi"></P><?

	include "footer.php";
	db_logout($hdb);


?>
