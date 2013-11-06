<?php

include "config.php";
include "database.php";
include "functions.php";
include "login.php";

$title = 'Help Counts';
//include "header.php";


	$today = date("Y-m-d");
	$date = getdate();
	
//$query = mysql_query("SELECT * from HelpCounts where Date = '$today' ");
$query = mysql_query("SELECT * from HelpCounts where Date = '$today' ");
$entries = mysql_num_rows($query);
$currentRow = 0;

$data[0] = '';
$data[1] = 'Helped';

while($row = mysql_fetch_array($query)) {
	$data[$row['Hour']] = $row['Count'];
}
print_r($data);

//echo " {$row['hour']}";
//echo "helldsk $entries falksdfjaldf";
?>
<table border="1" width="100%">

<tr>
<?php
for ($i = 8; $i < 24; $i++) {
?>
	
	<td><? 
		echo $data[$i]; 
	?></td>
	
	<?php
}
$currentHr = date("G");
if ($currentHr == 24) {
        $currentHr = 0;
}
	


?>
</tr>
</table>
<?php
echo $currentHr;
?>