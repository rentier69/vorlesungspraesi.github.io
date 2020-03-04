<?php 
    require("components/functions.php");
    generate_header("Startseite", "Herzlich Willkomen zur Online-Vorlesungsplatform der DHBW Ravensburg", null);
?>

<?php
    $conn = sql_connect();
    mysqli_close($conn);
?>

<?php
    generate_footer();
?>