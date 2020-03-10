<?php
session_start();

if (isset($_SESSION['username'])) {
    if (isset($_SESSION["dozent"])) {
        if ($_SESSION["dozent"]) {
            require("functions.php");
            generate_header("Menü", "Bereich für Dozenten", $_SESSION["username"], "../");
            echo ('
            <div class="container-fluid">
                <a href="groupmgmt.php" class="btn btn-primary"> Gruppenverwaltung </a>
                <a href="usermgmt.php" class="btn btn-primary"> Userverwaltung </a>
            </div>
');
        } else {
            header("Location: ../index.php");
            die("Bitte melden Sie sich als Dozent an");
        }
    } else {
        header("Location: ../index.php");
        die("Bitte melden Sie sich als Dozent an");
    }
} else {
    header("Location: ../index.php");
    die("Bitte melden Sie sich an");
}

generate_footer();
