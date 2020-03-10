<?php
session_start();
require("functions.php");
$erfolg;
if(session_destroy()){
$erfolg="Sie wurden erfolgreich ausgeloggt";
}else{
$erfolg="Es ist ein Fehler aufgetreten";
}
generate_header("Logout", $erfolg,null,"../");
?>
<div class="container-fluid">
    <a href="../index.php" class="btn btn-info">Zurück zur Startseite</a>
</div>

<?php
generate_footer();
?>