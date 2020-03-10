<?php
session_start();
if(!isset($_SESSION['username'])){
   header("Location: ../index.php");
    die("Bitte melden Sie sich an"); 
}

require("functions.php");
generate_header("Menü","Bereich für Studenten",$_SESSION["username"],"../");
?>


<div class="container-fluid">
    Inhalt
</div>


<?php
generate_footer();
?>