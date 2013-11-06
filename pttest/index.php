<?
ini_set('display_errors', 1);

if (!($xmlparser = xml_parser_create()) )
{
die ('Cannot create parser');
}

function start_tag($parser, $name, $attribs)
{
	echo 'Current tag: '.$name.'<br />';
	if (is_array($attribs))
	{
		echo 'Attributes: <br />';
		while (list($key, $val) = each($attribs))
		{
			echo 'Attribute '.$key.' has value '.$val.'<br />';
		}
	}
}

function end_tag($parser, $name)
{
	echo 'Reached ending tag '.$name.'<br /><br />';
}

function tag_contents($parser, $data)
{
	echo 'Contents: '.$data.'<br />';
}

xml_set_element_handler($xmlparser, 'start_tag', 'end_tag');
xml_set_character_data_handler($xmlparser, 'tag_contents');

$filename = 'http://localhost:8081/helpdesk/pttest/getprinter.php';
if (!($fp = fopen($filename, 'r'))) {
	die('Error: cannot open '.$filename);
}

while ($data = fread($fp, 4096)){
   $data=eregi_replace(">"."[[:space:]]+"."< ",">< ",$data);
   if (!xml_parse($xmlparser, $data, feof($fp))) {
      $error = xml_error_string(xml_get_error_code($xmlparser));
      $error .= xml_get_current_line_number($xmlparser);
      die($error);
   }
}
xml_parser_free($xmlparser); 

?>
