<?php
/*
//This program inserts the lab counts into the database from a cvs file

include "config.php";
include "database.php";




$fp= fopen("labcounts.csv", "r");

while($data = fgetcsv ($fp, 1000, ",")){

$stamp=$data[0];
$temp=strtotime($stamp);



	$x=mysql_query("INSERT INTO LabCounts(Lab, Date, Time, PC, MAC, UNIX, CampusID, ExactTime)VALUES('$data[1]', '$data[0]', '".date("G",$temp).":30:00', '$data[2]', '$data[3]', '$data[4]', 0, '".ereg_replace("[^0-9]","",$data[0])."')");
	


}
fclose ($fp)
*/
?>
