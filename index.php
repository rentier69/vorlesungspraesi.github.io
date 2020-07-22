<?php require("components/functions.php"); ?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Homepage</title>
        <link rel="stylesheet" href="css/bootstrap.css" />
        <link rel="stylesheet" href="css/fontawesome/css/all.css">
        <link rel="stylesheet" href="css/design.css" />
        <script src="js/jquery-3.0.0.min.js"></script>
        <script src="js/bootstrap.min.js"> </script>
        <script src="js/main.js"></script>
    </head>

<?php

if (isset($_POST['submit'])) {
    $link = sql_connect();
    session_start();

    //Login validieren
    if (isset($_POST['username']) && $_POST['password']) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if (strlen($username) >= 4 && strlen($password) > 0) {
            //Daten validieren
            $hash = md5($password);
            $sql = "SELECT COUNT(*)  as vorhanden, aktiv, dozent, benutzername, benutzer_id FROM vl_benutzer WHERE benutzername = ? AND password = ?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, 'ss', $username, $hash);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            if ($row["vorhanden"] == 1 && $row["aktiv"] == 1 && mysqli_num_rows($result) == 1) {
                $anmeldung_ok = true;                
                $_SESSION["username"] = $row["benutzername"];

                //Update timestamp letzter Login
                $sql = "UPDATE vl_benutzer SET datum_letzterlogin = NOW() WHERE benutzer_id = ?;";
                $stmt = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($stmt, 'i', $row['benutzer_id']);
                mysqli_stmt_execute($stmt);
                
                //Prüfen ob Dozent
                if($row["dozent"]){
                    $_SESSION["dozent"] = true;
                }                    
                
            } elseif ($row["vorhanden"] == 0) {
                $anmeldung_ok = false;
                $errormsg = "Benutzername oder Passwort falsch";
            } elseif ($row["aktiv"] == 0) {
                $anmeldung_ok = false;
                $errormsg = "Benutzer gesperrt";
            }            
        } else {
            $anmeldung_ok = false;
            $errormsg = "Eingabe wiederholen";
        }
    } else {
        $anmeldung_ok = false;
        $errormsg = "Eingabe wiederholen";
    }
}else if (isset($_POST['submit_register'])) {
    //Benutzer anlegen
    $link = sql_connect();
    session_start();
    $successInsert = true;
    $errorMsgInsert = "Allgemeiner Fehler aufgetreten";

    if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["kurs"]) && isset($_POST["passwordRepeat"])) {
        mysqli_autocommit($link, FALSE);

        $username = $_POST['username'];
        $pw = md5($_POST['password']);
        $kurs = $_POST["kurs"];        

        //Prüfen ob Benutzer bereits vorhanden        
        $sql = "SELECT benutzer_id FROM vl_benutzer WHERE benutzername = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 's', $username);

        //prüfen ob erste query erfolgreich
        if(mysqli_stmt_execute($stmt)){
            //prüfen ob bereits ein datensatz mit dem benutzernamen existiert
            if (mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0) {
                $successInsert = false;
                mysqli_stmt_close($stmt);
                $errorMsgInsert = "Benutzer bereits vorhanden";
            }else {
                //Benutzer einfügen
                $sql = "INSERT INTO vl_benutzer(benutzername, password) VALUES (?,?);";
                $stmt = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($stmt, 'ss', $username, $pw);
                if (mysqli_stmt_execute($stmt)) {
                    $benutzer_id = mysqli_insert_id($link);
                    mysqli_stmt_close($stmt);
                    //Benutzer_Gruppe_MAP
                    $sql = "INSERT INTO vl_benutzer_gruppe_map(benutzer_id, gruppe_id) VALUES (?,?);";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'ii', $benutzer_id, $kurs);
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_commit($link);
                        mysqli_stmt_close($stmt);
                    } else {                    
                        $successInsert = false;
                        $errorMsgInsert = mysqli_error($link);
                        mysqli_rollback($link);
                    }                    
                } else {
                    $successInsert = false;
                    $errorMsgInsert = mysqli_error($link);
                    mysqli_rollback($link);
                    mysqli_stmt_close($stmt);
                }                
            }
        } else {
        $successInsert = false;
        $errorMsgInsert = "Angaben fehlerhaft";
        mysqli_rollback($link);
    }
    mysqli_stmt_close($stmt);
    mysqli_autocommit($link, true);

    if($successInsert){
        //session username setzen, damit direkt auf die Startseite gewechselt werden kann
        $_SESSION["username"] = $_POST["username"];
    }
    }
}
?>
    <body>
        <nav id="topheader" class="navbar navbar-dark navbar-expand-md fixed-top bg-dark flex-md-nowrap shadow py-0">
            <div class="navbar-toggler clickable" id="sidebarCollapse">                    
                <i class="fas fa-chevron-right"></i>
            </div>
            <a class="navbar-brand" href="index.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="129.6" height="53.9" viewBox="0 0 129.6 53.9">
                    <path opacity=".8" fill="#5C6971" d="M43.7 11.2h-9.9V20c0 .4-.4 1.2-.8 1.6l-9 9.1c-.4.4-.8.4-.8 0v1c0 .4.4.8.8.8h19.7c.4 0 .8-.4.8-.8V12c0-.5-.4-.8-.8-.8z"></path>
                    <path fill="#E2001A" d="M33 .3l-9 9.1c-.4.4-.8 1.2-.8 1.6v19.7c0 .4.4.4.8 0l9.1-9.1c.4-.4.8-1.2.8-1.6V.3c-.1-.4-.4-.4-.9 0z"></path>
                    <path opacity=".6" fill="#5C6971" d="M24 30.7l9.1-9.1c.4-.4.8-1.2.8-1.6v-8.9H24c-.4 0-.8.4-.8.8v18.7c0 .5.3.5.8.1z"></path>
                    <path opacity=".8" fill="#5C6971" d="M20.5 32.5h-9.8v10.7c0 .4.4.4.8 0l9.1-9.1c.4-.4.8-1.2.8-1.6v-.9c-.1.5-.5.9-.9.9z"></path>
                    <path fill="#E2001A" d="M20.5 11.2H.8c-.4 0-.8.3-.8.8v19.7c0 .4.4.8.8.8h19.7c.4 0 .8-.4.8-.8V12c0-.5-.4-.8-.8-.8z"></path>
                    <path opacity=".6" fill="#5C6971" d="M20.5 12.8l-9.1 9.1c-.4.4-.8 1.2-.8 1.6v9h9.8c.4 0 .8-.4.8-.8V12.8c.1-.4-.3-.4-.7 0z"></path>
                    <path class="hidden-xs" d="M58.3 50.4c0 .2 0 .5-.1.7-.1.2-.2.4-.4.5l-.6.3c-.2.1-.5.1-.7.1h-1.4v-5.4h1.5c.2 0 .4 0 .6.1.2.1.4.1.5.2.1.1.3.2.4.4.1.2.1.4.1.6 0 .1 0 .3-.1.4-.1.1-.1.3-.2.4l-.3.3c-.1.1-.2.1-.3.2.2 0 .3.1.4.2.1.1.3.2.4.3.1.1.2.2.3.4s-.1.2-.1.3zm-.7-2.4c0-.3-.1-.5-.3-.7-.2-.2-.4-.2-.7-.2h-.9V49h.9c.1 0 .3 0 .4-.1.1 0 .2-.1.3-.2.1-.1.2-.2.2-.3.1-.2.1-.3.1-.4zm.1 2.4c0-.3-.1-.6-.3-.7-.2-.2-.5-.3-.8-.3h-.9v2.1h.9c.2 0 .3 0 .4-.1.1-.1.3-.1.4-.2.1-.1.2-.2.2-.3.1-.2.1-.3.1-.5zm3.7 1.6v-.6s-.1.1-.1.2c-.1.1-.1.2-.2.2-.1.1-.3.2-.4.2-.1.1-.3.1-.4.1-.4 0-.6-.1-.9-.3-.2-.2-.3-.5-.3-.8 0-.2 0-.3.1-.5.1-.1.2-.3.4-.4.2-.1.3-.2.5-.3.2-.1.3-.1.5-.2l.8-.2v-.3c0-.3-.1-.5-.2-.7-.1-.2-.4-.3-.7-.3h-.3c-.1 0-.2.1-.3.1-.1 0-.2.1-.3.1-.1 0-.1.1-.1.1l-.2-.4s.1 0 .2-.1c.1 0 .2-.1.3-.1.1 0 .2-.1.4-.1h.4c.4 0 .7.1 1 .3.2.2.4.6.4 1v3h-.6zm0-2.1l-.7.1c-.1 0-.2.1-.4.1-.1.1-.2.1-.4.2l-.3.3c-.1.1-.1.2-.1.3 0 .2.1.4.2.5.1.1.3.2.5.2.1 0 .2 0 .4-.1s.2-.1.3-.2c.1-.1.2-.2.2-.3.1-.1.1-.2.1-.3v-.8zm4.2 2.1v-.6s-.1.1-.1.2c-.1.1-.1.2-.2.2-.1.1-.2.2-.3.2-.1.1-.2.1-.4.1-.3 0-.5-.1-.7-.2-.2-.1-.4-.3-.5-.5-.1-.2-.2-.5-.3-.7-.1-.3-.1-.5-.1-.8 0-.2 0-.5.1-.7.1-.2.2-.5.3-.7.1-.2.3-.4.5-.5.2-.1.4-.2.6-.2.1 0 .2 0 .4.1.1.1.2.1.3.2.1.1.2.1.2.2.1.1.1.1.1.2v-2.2h.6V52h-.5zm0-2.9s0-.1-.1-.2c0-.1-.1-.2-.2-.3s-.2-.2-.3-.2c-.1-.1-.2-.1-.4-.1s-.3 0-.4.1l-.3.3c-.1.1-.2.3-.2.5s-.1.4-.1.6c0 .2 0 .4.1.6 0 .2.1.4.2.6.1.2.2.3.3.4.1.1.3.2.5.2.1 0 .2 0 .3-.1.1-.1.2-.1.3-.2s.1-.2.2-.3c.1-.1.1-.1.1-.2v-1.7zm2.1 1c0 .2 0 .3.1.5s.1.4.2.5c.1.2.2.3.3.4.1.1.3.2.5.2h.3c.1 0 .2-.1.3-.1.1 0 .2-.1.2-.1.1 0 .1-.1.2-.1l.2.5s-.1 0-.2.1c-.1 0-.2.1-.3.1-.1 0-.2.1-.4.1h-.4c-.3 0-.6-.1-.8-.2-.2-.1-.4-.3-.5-.5-.1-.2-.2-.5-.3-.7 0-.3-.1-.5-.1-.8 0-.2 0-.4.1-.7.1-.2.2-.5.3-.7.1-.2.3-.4.5-.5.2-.1.4-.2.7-.2.3 0 .5.1.7.2.2.1.3.3.5.5.1.2.2.4.3.7.1.2.1.5.1.7v.2h-2.5zm1.9-.9c0-.2-.1-.3-.2-.5-.1-.1-.2-.3-.3-.4-.1-.1-.3-.2-.4-.2-.2 0-.3 0-.4.1-.1.1-.2.2-.3.4-.1.1-.1.3-.2.5 0 .2-.1.3-.1.4h1.9v-.3zm4.1 2.8v-2.7c0-.1 0-.2-.1-.3 0-.1-.1-.2-.1-.3-.1-.1-.1-.2-.2-.2-.1-.1-.2-.1-.3-.1-.1 0-.2 0-.4.1-.1.1-.2.1-.3.2-.1.1-.2.2-.2.3-.1.1-.1.2-.1.2V52h-.6v-4.1h.5v.6s0-.1.1-.1l.2-.2c.1-.1.2-.1.3-.2.1-.1.3-.1.4-.1.2 0 .4 0 .6.1.2.1.3.2.4.3.1.1.2.3.2.4.1.2.1.3.1.5V52h-.5zm1.7-2.2v-.5h1.7v.5h-1.7zm7.4 2.2h-.7L81 47.3 79.8 52h-.7l-1.2-5.4h.6l1 4.7 1.2-4.7h.7l1.2 4.7 1-4.7h.4L82.8 52zm4.4 0v-.6s-.1.1-.1.2c-.1.1-.1.2-.2.2-.1.1-.2.1-.3.2-.1.1-.3.1-.4.1-.2 0-.4 0-.6-.1-.2-.1-.3-.2-.4-.3-.1-.1-.2-.3-.2-.4 0-.2-.1-.3-.1-.5V48h.6v3.1c0 .1.1.2.1.3.1.1.1.2.2.2.1.1.2.1.4.1.1 0 .3 0 .4-.1.1-.1.2-.1.3-.2.1-.1.2-.2.2-.3.1-.1.1-.1.1-.2V48h.6v4h-.6zM86 46.8c0 .1 0 .2-.1.3-.1.1-.2.1-.3.1-.1 0-.2 0-.3-.1-.1-.1-.1-.2-.1-.3 0-.1 0-.2.1-.3.1-.1.2-.1.3-.1.1 0 .2 0 .3.1.1.1.1.2.1.3zm1.4 0c0 .1 0 .2-.1.3-.1.1-.2.1-.3.1-.1 0-.2 0-.3-.1-.1-.1-.1-.2-.1-.3 0-.1 0-.2.1-.3.1-.1.2-.1.3-.1.1 0 .2 0 .3.1.1.1.1.2.1.3zm3.4 1.7s-.1 0-.1-.1h-.2c-.1 0-.2 0-.3.1-.1.1-.2.1-.3.2-.1.1-.1.2-.2.2 0 .1-.1.1-.1.2V52H89v-4.1h.5v.6s0-.1.1-.1c0-.1.1-.1.2-.2s.2-.1.3-.2c.1-.1.2-.1.4-.1h.2c.1 0 .1.1.2.1l-.1.5zm2.8 3.5H93c-.2 0-.3 0-.4-.1-.1 0-.2-.1-.3-.2-.1-.1-.1-.2-.1-.3v-3.1h-.7v-.5h.7v-1.1h.6v1.1h.8v.5h-.8v2.8c0 .1 0 .2.1.3.1.1.1.2.3.2h.3c.1 0 .2-.1.2-.1l.2.5h-.3zm2.8 0h-.6c-.2 0-.3 0-.4-.1-.1 0-.2-.1-.3-.2-.1-.1-.1-.2-.1-.3v-3.1h-.7v-.5h.7v-1.1h.6v1.1h.8v.5h-.8v2.8c0 .1 0 .2.1.3.1.1.1.2.3.2h.3c.1 0 .2-.1.2-.1l.2.5h-.3zm1.4-1.9c0 .2 0 .3.1.5 0 .2.1.4.2.5.1.2.2.3.3.4.1.1.3.2.5.2h.3c.1 0 .2-.1.3-.1.1 0 .2-.1.2-.1.1 0 .1-.1.2-.1l.2.5s-.1 0-.2.1c-.1 0-.2.1-.3.1-.1 0-.2.1-.4.1h-.4c-.3 0-.6-.1-.8-.2-.2-.1-.4-.3-.5-.5-.1-.2-.2-.5-.3-.7 0-.3-.1-.5-.1-.8 0-.2 0-.4.1-.7.1-.2.2-.5.3-.7.1-.2.3-.4.5-.5.2-.1.4-.2.7-.2.3 0 .5.1.7.2.2.1.3.3.5.5.1.2.2.4.3.7.1.2.1.5.1.7v.2h-2.5zm1.9-.9c0-.2-.1-.3-.2-.5-.1-.1-.2-.3-.3-.4-.1-.1-.3-.2-.4-.2-.2 0-.3 0-.4.1-.1.1-.2.2-.3.4-.1.1-.1.3-.2.5 0 .2-.1.3-.1.4h1.9v-.3zm6.2 2.8v-3.1c0-.1-.1-.2-.1-.3-.1-.1-.1-.2-.2-.2-.1-.1-.2-.1-.3-.1-.1 0-.3 0-.4.1-.1.1-.2.1-.3.2l-.2.2c0 .1-.1.2-.1.2v3h-.6v-3.1c0-.1-.1-.2-.1-.3-.1-.1-.1-.2-.2-.2-.1-.1-.2-.1-.4-.1-.1 0-.2 0-.4.1-.1.1-.2.1-.3.2-.1.1-.1.2-.2.3 0 .1-.1.2-.1.2V52h-.6v-4.1h.5v.6s0-.1.1-.1l.2-.2c.1-.1.2-.1.3-.2.1-.1.3-.1.4-.1.2 0 .4 0 .5.1.1.1.2.1.3.2.1.1.2.1.2.2s.1.1.1.2l.1-.1.2-.2c.1-.1.2-.1.3-.2.1-.1.3-.1.4-.1.2 0 .4 0 .6.1.2.1.3.2.4.3.1.1.2.3.2.4 0 .2.1.3.1.5V52h-.4zm5-2.1c0 .3 0 .5-.1.8-.1.3-.2.5-.3.7-.2.2-.3.4-.6.5-.2.1-.5.2-.8.2h-.3c-.1 0-.3 0-.4-.1-.1 0-.3 0-.4-.1h-.2v-5.7h.6v2.2s.1-.1.1-.2l.2-.2c.1-.1.2-.1.3-.2.1-.1.2-.1.4-.1.3 0 .5.1.7.2.2.1.4.3.5.5.1.2.2.4.3.7v.8zm-.6 0c0-.2 0-.4-.1-.5 0-.2-.1-.4-.2-.5-.1-.2-.2-.3-.3-.4-.1-.1-.3-.1-.5-.1-.1 0-.2 0-.3.1-.1.1-.2.1-.3.2-.1.1-.2.2-.2.3-.1.1-.1.2-.1.2v2.5h.7c.2 0 .4-.1.5-.2l.4-.4c.1-.2.2-.4.2-.6.2-.3.2-.5.2-.6zm2 .2c0 .2 0 .3.1.5 0 .2.1.4.2.5.1.2.2.3.3.4.1.1.3.2.5.2h.3c.1 0 .2-.1.3-.1.1 0 .2-.1.2-.1.1 0 .1-.1.2-.1l.2.5s-.1 0-.2.1c-.1 0-.2.1-.3.1-.1 0-.2.1-.4.1h-.4c-.3 0-.6-.1-.8-.2-.2-.1-.4-.3-.5-.5-.1-.2-.2-.5-.3-.7 0-.3-.1-.5-.1-.8 0-.2 0-.4.1-.7.1-.2.2-.5.3-.7.1-.2.3-.4.5-.5.2-.1.4-.2.7-.2.3 0 .5.1.7.2.2.1.3.3.5.5.1.2.2.4.3.7.1.2.1.5.1.7v.2h-2.5zm1.8-.9c0-.2-.1-.3-.2-.5-.1-.1-.2-.3-.3-.4-.1-.1-.3-.2-.4-.2-.2 0-.3 0-.4.1-.1.1-.2.2-.3.4-.1.1-.1.3-.2.5 0 .2-.1.3-.1.4h1.9c.1 0 .1-.1 0-.3zm3.5-.7s-.1 0-.1-.1h-.2c-.1 0-.2 0-.3.1-.1.1-.2.1-.3.2-.1.1-.1.2-.2.2 0 .1-.1.1-.1.2V52h-.6v-4.1h.5v.6s0-.1.1-.1c0-.1.1-.1.2-.2s.2-.1.3-.2.2-.1.4-.1h.2c.1 0 .1.1.2.1l-.1.5zm3.2-.2l.1.1c0 .1.1.1.1.2s.1.2.1.3v.3c0 .2 0 .4-.1.6-.1.2-.2.3-.3.5-.1.1-.3.2-.4.3-.2.1-.4.1-.6.1h-.2c-.1 0-.1 0-.2.1-.1 0-.1.1-.1.1l-.1.1c0 .1.1.2.2.3.1.1.3.1.4.1.2 0 .4 0 .6.1s.4.1.5.2c.2.1.3.2.4.4.1.1.1.3.1.5 0 .1 0 .3-.1.4-.1.1-.2.3-.3.4-.1.1-.3.2-.5.3-.2.1-.4.1-.7.1-.2 0-.4 0-.6-.1-.2-.1-.4-.1-.5-.2-.1-.1-.3-.2-.3-.4-.1-.1-.1-.3-.1-.4 0-.2 0-.3.1-.4l.3-.3c.1-.1.2-.1.3-.2.1 0 .2-.1.3-.1h-.2c-.1 0-.1-.1-.2-.1s-.1-.1-.2-.2c0-.1-.1-.2-.1-.3 0-.1 0-.1.1-.2 0-.1.1-.1.2-.2.1 0 .1-.1.2-.1s.1-.1.2-.1c-.1 0-.2-.1-.3-.1-.1-.1-.2-.1-.3-.3-.1-.1-.2-.2-.2-.4-.1-.2-.1-.4-.1-.6 0-.2 0-.3.1-.5s.2-.3.3-.5c.1-.1.3-.2.4-.3.2-.1.4-.1.5-.1h.4c.1 0 .2 0 .2.1h1.2v.4h-.6zm.1 4.4c0-.2-.1-.4-.3-.5-.2-.1-.5-.2-.8-.2-.1 0-.3 0-.4.1-.1 0-.2.1-.3.2-.1.1-.2.1-.2.2-.1.1-.1.2-.1.3 0 .1 0 .2.1.3.1.1.1.2.2.3.1.1.2.1.3.2.1 0 .3.1.4.1.2 0 .3 0 .4-.1.1 0 .3-.1.4-.2.1-.1.2-.2.2-.3.1-.2.1-.3.1-.4zm-.3-3.5c0-.3-.1-.5-.2-.7-.1-.2-.4-.3-.6-.3-.1 0-.2 0-.4.1-.1.1-.2.1-.3.2s-.1.2-.2.3c0 .1-.1.2-.1.4v.4c0 .1.1.2.1.3.1.1.1.2.3.2.1.1.2.1.4.1s.3 0 .4-.1l.3-.3c.1-.1.1-.2.1-.3.2 0 .2-.1.2-.3zm-61.9-8.9c0 .4-.1.8-.2 1.1-.1.3-.3.6-.5.9-.2.3-.5.5-.7.6-.3.1-.6.2-.9.2h-1.3v-5.4h1.2c.3 0 .7.1 1 .2.3.1.6.3.8.5.2.2.4.5.5.8.1.3.1.7.1 1.1zm-.6 0c0-.3 0-.6-.1-.9-.1-.3-.2-.5-.4-.7-.2-.2-.3-.3-.6-.4-.2-.1-.4-.1-.7-.1h-.7v4.4h.7c.2 0 .5-.1.7-.2.2-.1.4-.3.6-.5.2-.2.3-.5.4-.7.1-.3.1-.6.1-.9zm3.9 2.8v-.6s-.1.1-.1.2c-.1.1-.1.2-.2.2-.1.1-.2.1-.3.2-.1.1-.3.1-.4.1-.2 0-.4 0-.6-.1-.2-.1-.3-.2-.4-.3-.1-.1-.2-.3-.2-.4 0-.2-.1-.3-.1-.5V39h.6v3.1c0 .1.1.2.1.3.1.1.1.2.2.2.1.1.2.1.4.1.1 0 .3 0 .4-.1.1-.1.2-.1.3-.2.1-.1.2-.2.2-.3l.1-.1v-3h.6v4.1H62zm4 0v-.6s-.1.1-.1.2c-.1.1-.1.2-.2.2-.1.1-.2.1-.4.2-.1.1-.3.1-.4.1-.4 0-.6-.1-.9-.3-.2-.2-.3-.5-.3-.8 0-.2 0-.3.1-.5.1-.1.2-.3.4-.4.2-.1.3-.2.5-.3.2-.1.3-.1.5-.2l.8-.2v-.3c0-.3-.1-.5-.2-.7-.1-.2-.4-.3-.7-.3h-.3c-.1 0-.2.1-.3.1-.1 0-.2.1-.3.1-.1 0-.1.1-.2.1l-.2-.4s.1 0 .2-.1c.1 0 .2-.1.3-.1.1 0 .2-.1.4-.1h.4c.4 0 .7.1 1 .3.2.2.4.6.4 1v2.8H66zm0-2.1l-.7.1c-.1 0-.2.1-.4.1-.1.1-.2.1-.4.2l-.3.3c-.1.1-.1.2-.1.3 0 .2.1.4.2.5.1.1.3.2.5.2.1 0 .2 0 .4-.1.1-.1.2-.1.3-.2.1-.1.2-.2.2-.3.1-.1.1-.2.1-.3V41zm3 2.2h-.3c-.2 0-.3 0-.4-.1-.1-.1-.2-.1-.2-.2-.1-.1-.1-.2-.1-.3v-5.3h.6v4.8c0 .2 0 .3.1.4 0 .1.1.2.2.2h.1c.1 0 .1-.1.1-.1l.2.5c-.1 0-.2 0-.3.1zm1.5-2c0 .2 0 .3.1.5 0 .2.1.4.2.5.1.2.2.3.3.4.1.1.3.2.5.2h.3c.1 0 .2-.1.3-.1.1 0 .2-.1.2-.1.1 0 .1-.1.2-.1l.2.5s-.1 0-.2.1c-.1 0-.2.1-.3.1-.1 0-.2.1-.4.1h-.4c-.3 0-.6-.1-.8-.2-.2-.1-.4-.3-.5-.5-.1-.4-.2-.6-.2-.9 0-.3-.1-.5-.1-.8 0-.2 0-.4.1-.7.1-.2.2-.5.3-.7.1-.2.3-.4.5-.5.2-.1.4-.2.7-.2.3 0 .5.1.7.2.2.1.3.3.5.5.1.2.2.4.3.7.1.2.1.5.1.7v.2h-2.6zm1.8-.9c0-.2-.1-.3-.2-.5-.1-.1-.2-.3-.3-.4-.1-.1-.3-.1-.4-.1-.2 0-.3 0-.4.1-.1.1-.2.2-.3.4-.1.1-.1.3-.2.4 0 .2-.1.3-.1.4h1.9v-.3zm7 2.8v-2.6H77v2.6h-.6v-5.4h.6V40h2.2v-2.3h.6v5.4h-.5zm5-2.1c0 .2 0 .5-.1.7-.1.3-.1.5-.3.7-.1.2-.3.4-.5.6-.2.1-.5.2-.8.2-.3 0-.6-.1-.8-.2-.2-.1-.4-.3-.5-.6-.1-.2-.2-.5-.3-.7-.1-.3-.1-.5-.1-.7 0-.2 0-.4.1-.6.1-.2.2-.5.3-.7.1-.2.3-.4.5-.5.2-.1.4-.2.7-.2.3 0 .6.1.8.2.2.1.4.3.5.5.1.2.2.4.3.7.2.2.2.4.2.6zm-.5 0v-.5c0-.2-.1-.3-.2-.5s-.2-.3-.3-.4c-.1-.1-.3-.2-.5-.2s-.3.1-.5.2-.3.2-.3.4c-.1.2-.1.3-.2.5 0 .2-.1.3-.1.5v.6c0 .2.1.4.2.6.1.2.2.3.3.5.1.1.3.2.5.2s.4-.1.5-.2c.1-.1.2-.3.3-.5.1-.2.1-.4.2-.6.1-.3.1-.5.1-.6zm4.1 2c-.1 0-.2.1-.3.1-.1 0-.2.1-.3.1h-.4c-.3 0-.5-.1-.7-.2-.2-.1-.4-.3-.5-.5-.1-.2-.2-.5-.3-.7-.1-.3-.1-.5-.1-.8 0-.2 0-.5.1-.7.1-.2.2-.5.3-.7.1-.2.3-.4.5-.5.2-.1.4-.2.7-.2h.3c.1 0 .2 0 .3.1.1 0 .2.1.2.1.1 0 .1.1.1.1l-.2.5s-.1 0-.1-.1c-.1 0-.1-.1-.2-.1s-.2-.1-.3-.1h-.2c-.2 0-.3 0-.4.1-.1.1-.2.2-.3.4-.1.2-.2.3-.2.5s-.1.4-.1.5c0 .2 0 .4.1.6 0 .2.1.4.2.6.1.2.2.3.3.4.1.1.3.2.5.2h.3c.1 0 .2 0 .3-.1.1 0 .2-.1.2-.1.1 0 .1-.1.2-.1l.2.5c-.1 0-.2 0-.2.1zm3.5.1v-2.7c0-.1 0-.2-.1-.3 0-.1-.1-.2-.1-.3-.1-.1-.1-.2-.2-.2-.1-.1-.2-.1-.3-.1-.1 0-.3 0-.4.1-.1.1-.2.1-.3.2-.1.1-.2.2-.2.3-.1.1-.1.2-.1.2v2.9H89v-5.7h.6v2.2s0-.1.1-.2l.2-.2c.1-.1.2-.1.3-.2.1-.1.3-.1.4-.1.2 0 .4 0 .6.1.2.1.3.2.4.3.1.1.2.3.2.4.1.2.1.3.1.5v2.8h-.5zm4.2-1.1c0 .2 0 .3-.1.5-.1.1-.2.3-.3.4-.1.1-.2.1-.4.2s-.3.1-.5.1h-.4c-.1 0-.3 0-.4-.1-.1 0-.2-.1-.3-.1-.1 0-.1-.1-.2-.1l.2-.5s.1 0 .2.1c.1 0 .2.1.3.1.1 0 .2.1.3.1h.3c.2 0 .4-.1.5-.2.2-.1.2-.3.2-.5 0-.1 0-.2-.1-.3-.1-.1-.2-.2-.4-.3-.1-.1-.3-.2-.5-.3-.2-.1-.3-.2-.5-.3-.1-.1-.3-.2-.4-.3-.1-.1-.1-.3-.1-.4 0-.2 0-.3.1-.5s.2-.3.3-.4c.1-.1.3-.2.4-.3.2-.1.3-.1.5-.1h.3c.1 0 .2 0 .3.1.1 0 .2.1.2.1.1 0 .1.1.1.1l-.2.4s-.1 0-.1-.1c-.1 0-.1-.1-.2-.1s-.2-.1-.3-.1h-.2c-.2 0-.4.1-.5.2-.1.1-.2.3-.2.5 0 .1 0 .2.1.3.1.1.2.2.4.3.1.1.3.2.5.3.2.1.3.2.5.3.1.1.3.2.4.3.2.3.2.4.2.6zm3.5 1c-.1 0-.1 0-.2.1-.1 0-.2.1-.3.1h-.4c-.3 0-.5-.1-.7-.2-.2-.1-.4-.3-.5-.5-.1-.2-.2-.5-.3-.7-.1-.3-.1-.5-.1-.8 0-.2 0-.5.1-.7.1-.2.2-.5.3-.7.1-.2.3-.4.5-.5.2-.1.4-.2.7-.2h.3c.1 0 .2 0 .3.1.1 0 .2.1.2.1.1 0 .1.1.1.1l-.1.5s-.1 0-.1-.1c-.1 0-.1-.1-.2-.1s-.2-.1-.3-.1h-.2c-.2 0-.3 0-.4.1-.1.1-.2.2-.3.4-.1.2-.2.3-.2.5s-.1.4-.1.5c0 .2 0 .4.1.6 0 .2.1.4.2.6.1.2.2.3.3.4.1.1.3.2.5.2h.3c.1 0 .2 0 .3-.1.1 0 .2-.1.2-.1.1 0 .1-.1.1-.1l.2.5c-.2 0-.2 0-.3.1zm3.5.1v-2.7c0-.1 0-.2-.1-.3 0-.1-.1-.2-.2-.3-.1-.1-.1-.2-.2-.2-.1-.1-.2-.1-.3-.1-.1 0-.3 0-.4.1-.1.1-.2.1-.3.2-.1.1-.2.2-.2.3-.1.1-.1.2-.1.2v2.9h-.6v-5.7h.6v2.2s0-.1.1-.2l.2-.2c.1-.1.2-.1.3-.2.1-.1.3-.1.4-.1.2 0 .4 0 .6.1.2.1.3.2.4.3.1.1.2.3.2.4.1.2.1.3.1.5v2.8h-.5zm4.2 0v-.6s-.1.1-.1.2c-.1.1-.1.2-.2.2-.1.1-.2.1-.3.2-.1.1-.3.1-.4.1-.2 0-.4 0-.6-.1-.2-.1-.3-.2-.4-.3-.1-.1-.2-.3-.2-.4 0-.2-.1-.3-.1-.5V39h.6v3.1c0 .1.1.2.1.3.1.1.1.2.2.2.1.1.2.1.4.1.1 0 .3 0 .4-.1.1-.1.2-.1.3-.2.1-.1.2-.2.2-.3.1-.1.1-.1.1-.2V39h.6v4.1h-.6zm3 .1h-.3c-.2 0-.3 0-.4-.1-.1-.1-.2-.1-.2-.2-.1-.1-.1-.2-.1-.3v-5.3h.6v4.8c0 .2 0 .3.1.4 0 .1.1.2.2.2h.1c.1 0 .1-.1.1-.1l.2.5c-.1 0-.2 0-.3.1zm1.4-2c0 .2 0 .3.1.5 0 .2.1.4.2.5.1.2.2.3.3.4.1.1.3.2.5.2h.3c.1 0 .2-.1.3-.1.1 0 .2-.1.2-.1.1 0 .1-.1.2-.1l.2.5s-.1 0-.2.1c-.1 0-.2.1-.3.1-.1 0-.2.1-.4.1h-.4c-.3 0-.6-.1-.8-.2-.2-.1-.4-.3-.5-.5-.1-.2-.2-.5-.3-.7 0-.3-.1-.5-.1-.8 0-.2 0-.4.1-.7.1-.2.2-.5.3-.7.1-.2.3-.4.5-.5.2-.1.4-.2.7-.2.3 0 .5.1.7.2.2.1.3.3.5.5.1.2.2.4.2.7.1.2.1.5.1.7v.2h-2.4zm1.9-.9c0-.2-.1-.3-.2-.5-.1-.1-.2-.3-.3-.4-.1-.1-.3-.1-.4-.1-.2 0-.3 0-.4.1-.1.1-.2.2-.3.4-.1.1-.1.3-.2.4 0 .2-.1.3-.1.4h1.9v-.3z" fill="#5C6971"></path>
                    <path fill="#E2001A" d="M70.8 21.4c0 1.6-.2 3.1-.7 4.4-.5 1.4-1.1 2.5-1.9 3.5-.8 1-1.8 1.8-3 2.3-1.2.6-2.5.8-4 .8h-5.3c-.2 0-.4-.1-.6-.2-.1-.2-.2-.3-.2-.6V12c0-.2.1-.4.2-.6.1-.2.3-.2.6-.2h5.3c1.4 0 2.8.2 4 .7 1.2.5 2.2 1.2 3.1 2.1.8.9 1.5 2 2 3.2.3 1.2.5 2.6.5 4.2zm-3.8 0c0-1-.1-2-.4-2.9-.3-.9-.6-1.6-1.1-2.3-.5-.6-1.1-1.1-1.8-1.5-.7-.4-1.5-.6-2.4-.6h-2.4v15.4H61c.9 0 1.7-.2 2.4-.6.7-.4 1.3-1 1.9-1.7.5-.7.9-1.6 1.2-2.6.3-.9.5-2 .5-3.2zm22.1 10.9c-.1.1-.3.2-.6.2h-2.2c-.2 0-.4-.1-.6-.2-.1-.1-.2-.3-.2-.6v-8.8h-7.4v8.8c0 .2-.1.4-.2.6-.1.1-.3.2-.6.2h-2.2c-.2 0-.4-.1-.6-.2-.1-.2-.2-.3-.2-.6V12c0-.2.1-.4.2-.6.1-.2.3-.2.6-.2h2.2c.2 0 .4.1.6.2.1.1.2.3.2.6v7.8h7.4V12c0-.2.1-.4.2-.6.1-.2.3-.2.6-.2h2.2c.2 0 .4.1.6.2.1.1.2.3.2.6v19.7c0 .2-.1.4-.2.6z"></path>
                    <path fill="#5C6971" d="M106.6 26.4c0 1-.2 1.8-.6 2.6-.4.8-.9 1.4-1.6 1.9-.7.5-1.5.9-2.3 1.2-.9.3-1.8.4-2.8.4h-4.9c-.1 0-.3-.1-.4-.2-.1-.1-.2-.2-.2-.4V11.7c0-.1.1-.3.2-.4.1-.1.2-.1.4-.1h5.3c.8 0 1.6.1 2.4.3.8.2 1.5.5 2 1 .6.4 1.1 1 1.4 1.7.4.7.5 1.5.5 2.4 0 .6-.1 1.1-.4 1.6-.3.5-.6 1-1 1.4-.4.4-.8.8-1.3 1.1-.5.3-.9.5-1.3.6.6.1 1.2.3 1.7.6s1 .7 1.4 1.1c.4.4.7 1 1 1.5.4.6.5 1.2.5 1.9zm-2.9-9.8c0-1.1-.4-1.9-1.1-2.6-.7-.6-1.7-1-2.9-1h-3.5v7.4h3.4c.5 0 1-.1 1.5-.3.5-.2.9-.5 1.3-.8.4-.3.7-.7.9-1.2.3-.4.4-.9.4-1.5zm.5 9.7c0-1.2-.4-2.2-1.3-2.9-.8-.7-1.9-1.1-3.3-1.1h-3.5v8.3h3.5c.6 0 1.2-.1 1.8-.3.6-.2 1-.5 1.5-.9.4-.4.7-.8 1-1.4.2-.5.3-1.1.3-1.7zm21.3 5.6c0 .1-.1.3-.2.4-.1.1-.2.2-.4.2h-1.6c-.1 0-.3-.1-.4-.2-.1-.1-.2-.2-.2-.4l-3.5-17.8-3.5 17.8c0 .1-.1.3-.2.4-.1.1-.3.2-.4.2h-1.6c-.1 0-.3-.1-.4-.2-.1-.1-.2-.2-.2-.4l-4.1-20.2c0-.1 0-.3.1-.4.1-.1.2-.1.4-.1h1.3c.3 0 .6.2.6.5l3.2 17.9 3.5-17.9c.1-.3.3-.5.6-.5h1.6c.3 0 .5.2.6.5l3.5 17.9 3.2-17.9c.1-.3.3-.5.6-.5h1.3c.1 0 .3 0 .4.1.1.1.1.2.1.4l-4.3 20.2z"></path>
                </svg>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <?php
                if(isset($_SESSION["username"])){
                    ?>
                    <div class="form-inline my-2 my-lg-0 justify-content-end w-100">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user"></i> <?= $_SESSION["username"]?>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupUserMenu">
                                <a class="dropdown-item"  data-toggle="modal" data-target="#userMenu" href="#">
                                    Benutzermenü
                                </a>
                                <a class="dropdown-item" href="components/logout.php">
                                    Logout <i class="fas fa-sign-out-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                    // an dieser Stelle, da das modal sonst auch bei nicht angemeldeten Benutzern im HTML Dok auftacht
                    generate_modal_usermenu();
                }
                    ?>
                
                               
            </div>
        </nav>
        <div class="container-fluid">
            <div class="row h-100">               
                <div role="main" class="col no-gutters">
                    <div id="inAppNotifications" class="inAppNotifications p-1 row">
                    </div>
                    <div id="loadingOverlay">
                        <div id="loadingSpinner" style="width: 3rem; height: 3rem; display:none;"></div>
                    </div>
                    <div id="main" class="col h-100 justify-content-center d-flex">

                    <?php
                    if(isset($_SESSION["username"])){
                    ?>
                        <div class="card-deck align-self-center">
                            <div class="card text-center shadow bg-light border-0 rounded-0">                                  
                                <div class="card-body">
                                    <h1 class="card-title text-muted">
                                        <i class="fas fa-sign-in-alt fa-4x"></i>
                                    </h1>
                                    <h5 class="card-title">An Vorlesung teilnehmen</h5>
                                    <p class="card-text">Einen Eintrag aus der Liste wählen und auf den Button klicken, um einer Vorlesung beizutreten.</p>
                                    <form action="lecture.php" method="post">
                                        <div class="form-row">
                                            <div class="col">
                                                <select class="custom-select form-control bg-light" onchange="enableInput(['joinLecture'])" name="lectureToJoin" id="lectureToJoin">
                                                    <option selected disabled>-</option>
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <input disabled type="submit" id="joinLecture" class="btn btn-primary form-control" value="Go">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php
                                //nur ausführen, wenn angemeldeter Benutzer Dozent ist
                                if(isset($_SESSION["dozent"]) && $_SESSION["dozent"]){
                                ?>
                                    <div class="card text-center shadow bg-light border-0 rounded-0">
                                <div class="card-body">
                                    <h1 class="card-title text-muted">
                                        <i class="fas fa-desktop fa-4x"></i>
                                    </h1>
                                    <h5 class="card-title">Vorlesung starten</h5>
                                    <p class="card-text">Einen Eintrag aus der Liste wählen und auf den Button klicken, um eine Vorlesung zu starten.</p>
                                    <form action="lecture-host.php" method="post">
                                        <div class="form-row">
                                            <div class="col">
                                                <select class="custom-select form-control bg-light" name="lectureToStart" id="lectureToStart"></select>
                                            </div>
                                            <div class="col-4">
                                                <input disabled type="submit" class="btn btn-primary form-control" value="Go">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card text-center shadow bg-light border-0 rounded-0">
                                <div class="card-body">
                                    <h1 class="card-title text-muted">
                                        <i class="fas fa-tools fa-4x"></i>
                                    </h1>
                                    <h5 class="card-title">Verwaltungsoberfläche</h5>
                                    <p class="card-text">Hier klicken um zur Verwaltungsoberfläche zu gelangen.</p><br>
                                    <a href="components/backend.php" class="btn btn-primary form-control">Go</a>
                                </div>
                            </div>
                                <?php
                                }
                            ?>                            
                        </div>                    
                    <?php
                    } else if (isset($_GET["register"])) {
                        //Registrierungsformular
                    ?>
                        <div class="col-sm-3 align-self-center">
                            <div class="card shadow bg-light border-0 rounded-0">
                                <div class="card-body">
                                    <form method="POST" action="index.php" class="was-validated" id="form_register">
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Benutzername" required id="username" minlength="4" maxlength="50" name="username" autofocus oninput="checkField('username', 'Benutzername', 'submit_register', 'form_register', true);">
                                            <div class="invalid-feedback" id="error_username" hidden>Benutzername eingeben</div>
                                            <div class="valid-feedback" id="valid_username"> Benutzername verfügbar </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" placeholder="Passwort" required id="password" name="password" oninput="checkField('password', 'Passwort', 'submit_register', 'form_register');">
                                            <div class="invalid-Feedback" id="error_password" hidden> Passwort eingeben</div>
                                            <div class="valid-feedback" id="valid_password"></div>
                                            <input type="password" class="form-control" placeholder="Passwort wiederholen" required id="passwordRepeat" name="passwordRepeat" oninput="checkField('password', 'Passwort', 'submit_register', 'form_register');">
                                            <div class="invalid-Feedback" id="error_passwordRepeat" hidden> Passwörter müssen übereinstimmen</div>
                                            <div class="valid-feedback" id="valid_passwordRepeat"></div>
                                        </div>
                                        <div class="form-group">
                                            <select id="kurs" class="form-control" name="kurs" required oninput="checkKurs('submit_register','form_register')">
                                                <option value="" disabled selected>Kurs auswählen</option>
                                                <?php
                                                //kein prep stmt notwendig, da keine formulareingaben
                                                $link = sql_connect();
                                                $kurs_select = "SELECT gruppenname, gruppe_id, gruppe_kuerzel FROM vl_gruppe";
                                                $result = mysqli_query($link, $kurs_select);
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo ('<option value="' . $row["gruppe_id"] . '">' . $row["gruppe_kuerzel"] . '</option>');
                                                }
                                                mysqli_close($link);
                                                ?>
                                            </select>
                                            <div class="invalid-Feedback" id="error_kurs" hidden> Bitte Kurs auswählen </div>
                                        </div>
                                </div>
                                <div class="card-footer">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-8">
                                                <input type="submit" class="btn btn-success form-control" value="Registrieren" id="submit_register" name="submit_register" disabled />
                                            </div>
                                            <div class="col-4">
                                                <a class="btn btn-danger form-control" href="index.php">Abbrechen</a>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php

                    }else{
                    //Loginformular

                    ?>
                    <div class="col-sm-3 align-self-center">
                        <div class="card shadow bg-light border-0 rounded-0">                                    
                            <div class="card-body">
                                <?php
                                if (isset($anmeldung_ok) && !$anmeldung_ok) {
                                    ?>
                                    <div class="alert alert-danger" role="alert">
                                        Login fehlgeschlagen - <?= $errormsg ?>
                                    </div>
                                    <?php            
                                }
                                if (isset($successInsert) && !$successInsert) {
                                    ?>
                                    <div class="alert alert-danger" role="alert">
                                        Registrierung fehlgeschlagen - <?= $errorMsgInsert ?>
                                    </div>
                                    <?php            
                                }
                                ?>
                                <form method="POST" action="index.php" class="">
                                    <label for="benutzername"> Benutzername </label>
                                    <input type="text" class="form-control" placeholder="Benutzername" name="username" required maxlength="50" id="username" autofocus/>

                                    <label for="password"> Passwort </label>
                                    <input type="password" class="form-control" placeholder="Passwort" name="password" required id="password" />
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-6">
                                        <input type="submit" class="btn btn-success btn-block" value="Login" name="submit" id="submit">
                                    </div>
                                    <div class="col-6">
                                        <a href="index.php?register" class="btn btn-secondary btn-block">Registrieren</a>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>                        
                    </div>                
                </div>
            </div>
        </div>
        <script type="text/javascript">     
            var checkKurs = function(submit_id,form_id) {
                var kurs = document.getElementById('kurs');
                if (kurs.value == '') {
                    kurs.setCustomValidity('Kurs auswählen!');
                } else {
                    kurs.setCustomValidity('');
                }
                changeSubmitButton(submit_id,"form");
            };        
        </script>
    </body>
</html>