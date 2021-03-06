<?php

if (isset($_GET["kuerzel"]) || isset($_GET["newKuerzelSource"])) {
    $kuerzel;
    if (isset($_GET["kuerzel"])) {
        $kuerzel = $_GET["kuerzel"];
    } else {
        $kuerzel = $_GET["newKuerzelSource"];
    }
    $conn = sql_connect();
    $sql = "SELECT gruppe_kuerzel FROM vl_gruppe WHERE gruppe_kuerzel='" . $kuerzel . "';";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        echo true;
    } else {
        echo false;
    }
    mysqli_close($conn);
}

if (isset($_GET["kursname"]) || isset($_GET["newNameSource"])) {
    $kursname;
    if (isset($_GET["kursname"])) {
        $kursname = $_GET["kursname"];
    } else {
        $kursname = $_GET["newNameSource"];
    }
    $conn = sql_connect();
    $sql = "SELECT gruppenname FROM vl_gruppe WHERE gruppenname='" . $kursname . "';";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        echo true;
    } else {
        echo false;
    }
    mysqli_close($conn);
}


if (isset($_GET["username"]) || isset($_GET["newName"])) {
    $username;
    if (isset($_GET["username"])) {
        $username = $_GET["username"];
    } else {
        $username = $_GET["newName"];
    }
    $conn = sql_connect();
    $sql = "SELECT benutzername FROM vl_benutzer WHERE benutzername='" . $username . "';";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        echo true;
    } else {
        echo false;
    }
    mysqli_close($conn);
}

function sql_connect()
{
    require_once(dirname(__DIR__) . "/configuration.php");
    // Create connection
    $conn = mysqli_connect(appConfig::$host, appConfig::$user, appConfig::$password);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    mysqli_select_db($conn, appConfig::$db);
    //echo 'erfolgreich';
    return $conn;
}

//$dirsUp wird benötigt, wenn aus einer Datei in einem Unterordner functions aufgerufen wird.
//Bsp:     generate_header("Startseite", "Herzlich Willkomen zur Online-Vorlesungsplatform der DHBW Ravensburg", null, '../');
function generate_header($title, $jumbotron_lead, $loggedOnUser, $dirsUp)
{
    if (isset($_POST["passwordChange"]) && isset($_POST["passwordChange"]) && isset($_POST["submitPasswordChange"])) {
        $passwordChange = $_POST["passwordChange"];
        $password2Change = $_POST["password2Change"];
        if (strlen($passwordChange) >= 6 && $passwordChange == $password2Change) {
            $passwordHash = md5($passwordChange);
            $conn = sql_connect();
            $result = mysqli_query($conn, "SELECT benutzer_id FROM vl_benutzer WHERE benutzername='" . $_SESSION['username'] . "';");
            if ($benutzer_id = mysqli_fetch_assoc($result)) {
                $sqlPassword = "UPDATE vl_benutzer SET password='" . $passwordHash . "' WHERE benutzer_id =" . $benutzer_id['benutzer_id'] . ";";
               $errorMsgPassword="";
                if (mysqli_query($conn, $sqlPassword)) {
                    $successPassword = true;
                } else {
                    $successPassword = false;
                    $errorMsgPassword . mysqli_error($conn);
                }
            } else {
                $successPassword = false;
                $errorMsgPassword = "Angaben fehlerhaft";
            }
        } else {
            $successPassword = false;
            $errorMsgPassword = "Angaben fehlerhaft";
        }
    }
?>
    <html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?= $title ?></title>
        <link rel="stylesheet" href="<?= $dirsUp ?>css/bootstrap.css" />
        <link rel="stylesheet" href="<?= $dirsUp ?>css/fontawesome/css/all.css">
        <link rel="stylesheet" href="<?= $dirsUp ?>css/custom.css" />
        <script src="<?= $dirsUp ?>js/jquery-3.0.0.min.js"></script>
        <script src="<?= $dirsUp ?>js/bootstrap.min.js"> </script>
        <script src="<?= $dirsUp ?>js/lecture.js"></script>
        <script src="<?= $dirsUp ?>js/lecture.js"></script>
        <style>
        </style>
        <script>
        </script>
    </head>

    <body>

        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <a class="navbar-brand">
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
            <?php
            if ($title != "Startseite" && $title != "Logout" && $title != "Benutzer registrieren") {
            ?>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav mr-auto">                        
                            <?php
                            if (isset($_SESSION['dozent'])) {
                                //Navbar für Anwender
                                ?>                                
                                <li class="nav-item active">
                                    <a class="nav-link" href="doz_bereich.php">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="usermgmt.php">Benutzer</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="groupmgmt.php">Gruppen</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="lectures.php">Vorlesungen</a>
                                </li>
                                <?php
                            } else {
                                //Navbar für Studenten
                                ?>
                                <li class="nav-item active">
                                    <a class="nav-link" href="stud_bereich.php">Home</a>
                                </li>
                                <?php
                            }
                            ?>
                    </ul>
                    <form class="form-inline my-2 my-lg-0" action="logout.php" method="post">
                        <button type="button" class="btn" data-toggle="modal" data-target="#userMenu"> <span style="color: white;"><i class="fas fa-user"></i>&nbsp;<?= $loggedOnUser ?> &nbsp;</span> </button>
                        <button type="submit" class="btn btn-light my-2 my-sm-0">Logout <i class="fas fa-sign-out-alt"></i></button>
                    </form>
                </div>
                <!-- Modal Passwort ändern-->
                <div id="passwordChangeModal" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title"><?= $loggedOnUser ?> - Passwort zurücksetzen</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <form class="was-validated" method="POST" id="formChangePassword">
                                    <div class="form-group">
                                        <input type="password" name="passwordChange" id="passwordChange" class="form-control" placeholder="Passwort" size="25"  />
                                        <div class="invalid-Feedback" id="error_passwordChange" hidden> Passwort eingeben</div>
                                        <div class="valid-Feedback" id="valid_passwordChange" hidden> </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password2Change" id="password2Change" class="form-control" placeholder="Passwort wiederholen" size="25"  />
                                        <div class="invalid-Feedback" id="error_password2Change" hidden> Passwörter müssen übereinstimmen</div>
                                        <div class="valid-Feedback" id="valid_password2Change" hidden> </div>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success" name="submitPasswordChange" id="submitPasswordChange" disabled>Passwort ändern</button>
                                </form>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Abbrechen</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Usermenü -->
                <div id="userMenu" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title"><?= $loggedOnUser ?> - Benutzermenü</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#passwordChangeModal" data-dismiss="modal">Passwort ändern </button>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    $("#passwordChangeModal").on('shown.bs.modal', function() {
                        document.getElementById("passwordChange").focus();
                        document.getElementById("passwordChange").setAttribute("required", "true");
                        document.getElementById("password2Change").setAttribute("required","true");
                    });

                    var checkPasswordChange = function() {
                        checkPassword("passwordChange", "password2Change");
                        changeSubmitButton("submitPasswordChange");
                        if (document.querySelector('#formChangePassword:invalid') === null) {
            document.getElementById("submitPasswordChange").disabled = false;
        } else {
            document.getElementById("submitPasswordChange").disabled = true;
        }
                    };

                   
                    document.getElementById("passwordChange").addEventListener("input", checkPasswordChange);
                    document.getElementById("password2Change").addEventListener("input", checkPasswordChange);
                </script>
                
            <?php
            }
            ?>

        </nav>
        <div class="jumbotron">
            <div class="container">
                <h1 class="display-4"><?= $title ?></h1>
                <p class="lead"><?= $jumbotron_lead ?></p>
            </div>
        </div>



        <?php
        
        if (isset($successPassword)) {
            if ($successPassword) {
        ?>
                <div class="alert alert-success" role="alert">
                    Passwort gespeichert.
                </div>
            <?php
            } else {
            ?>
                <div class="alert alert-danger" role="alert">
                    Passwort nicht gespeichert - versuchen Sie es erneut.<br>
                    Fehler: <?= $errorMsgPassword ?>
                </div>
        <?php
            }
        }
    }
    function generate_footer()
    {
        $year = date('Y');
        ?>
        <footer class="footer-custom">
            <div class="container">
                <span class="text-muted">© <?= $year ?> Projektteam 19 Jahrgang 2017</span>
            </div>
        </footer>
    </body>

    </html>

<?php
    }
