<?
include "config.php";
include "database.php";
include "functions.php";

include "login.php";

include "header.php";


MustLogIn();
?>



<?

include "footer.php";
db_logout($hdb);
?>
