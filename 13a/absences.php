<?
include "config.php";
include "database.php";
include "functions.php";

include "login.php";

MustLogIn();

$title = "Absence Schedule";

include "header.php";

include "footer.php";
db_logout($hdb);
?>
