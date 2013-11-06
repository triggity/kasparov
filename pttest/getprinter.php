<?
header("Content-type: text/xml");

$id = 1;

$printers[1][0] = 'http://172.18.12.129/cgi-bin/dynamic/PrinterStatus.html';
$printers[1][1] = 1;

$url = $printers[$id][0];

//switch ($printers[$id][1])
//{
//      case 1:
                        $data = file_get_contents($url);

                        $regex = '/~(.+?)%/';
                        preg_match_all($regex, $data, $toner_match, PREG_SET_ORDER);

                        $regex = '/<TD><P>Tray (.+?)<\/P><\/TD>/';
                        preg_match_all($regex, $data, $traynum_match, PREG_SET_ORDER);

                        $regex = '/height="1"><tr><td><b>(.+?)<\/b>/';
                        preg_match_all($regex, $data, $traystatus_match, PREG_SET_ORDER);

                        $result = '';
                        $result .= '<?xml version="1.0"?>';
                        $result .= '<printer>';
                        $result .= '<type>Dell 5310n</type>';
                        $result .= '<name>Upper Info Commons B/W</name>';
                        $result .= '<location>Upper Info Commons</location>';
                        $result .= '<toner>';
                        $result .= '<black>'.$toner_match[0][1].'</black>';
                        $result .= '</toner>';
                        $result .= '<paper>';
                        for($i = 0; $i < count($traynum_match); $i++)
                        {
                                        $result .= '<tray'.$traynum_match[$i][1].'>'.$traystatus_match[$i][1].'</tray'.$traynum_match[$i][1].'>';
                        }
                        $result .= '</paper>';
                        $result .= '</printer>';
//                      break;

//      case 2:
//}
echo $result;
?>

