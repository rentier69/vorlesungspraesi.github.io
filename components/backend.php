<?php
session_start();
require("functions.php");

if (isset($_SESSION['username'])) {
    if (isset($_SESSION["dozent"])) {
        if ($_SESSION["dozent"]) {
            //hier weitermachen
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


?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Backend</title>
        <link rel="stylesheet" href="../css/bootstrap.css" />
        <link rel="stylesheet" href="../css/fontawesome/css/all.css">
        <link rel="stylesheet" href="../css/design.css" />
        <link rel="stylesheet" href="../css/backend.css" />
        <script src="../js/jquery-3.0.0.min.js"></script>
        <script src="../js/bootstrap.min.js"> </script>
        <script src="../js/main.js"></script>
        <script src="../js/backend.js"></script>
    </head>
    <body>
        <nav id="topheader" class="navbar navbar-dark navbar-expand-md fixed-top bg-dark flex-md-nowrap shadow py-0">
            <div class="navbar-toggler clickable" id="sidebarCollapse">                    
                <i class="fas fa-chevron-right"></i>
            </div>
            <a class="navbar-brand" href="backend.php">
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
                <div class="form-inline my-2 my-lg-0 justify-content-end w-100">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user"></i> <?= $_SESSION["username"]?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupUserMenu">
                            <a class="dropdown-item"  data-toggle="modal" data-target="#userMenu" href="#">
                                Benutzermenü
                            </a>
                            <a class="dropdown-item" href="logout.php">
                                Logout <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>                
            </div>
        </nav>
        <div class="container-fluid">
            <div class="row">
                <nav class="col-md-2 bg-light sidebar" id="sidebar">
                    <div class="sidebar-sticky">
                        <ul class="nav flex-column pt-2">
                            <li class="nav-item">
                                <a class="nav-link" href="../index.php"><i class="fas fa-home fa-fw mr-1"></i>Home</a>
                            </li>                            
                        </ul>
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            Verwaltungsfunktionen
                        </h6>
                        <ul class="nav flex-column" id="nav">
                            <li class="nav-item">
                                <a class="nav-link active" id="nav_lectures" href="#" onclick="changeMode('lectures')"><i class="fas fa-chalkboard fa-fw mr-1"></i>Vorlesungen</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="nav_users" href="#" onclick="changeMode('users')"><i class="fas fa-user fa-fw mr-1"></i>Benutzer</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="nav_groups" href="#" onclick="changeMode('groups')"><i class="fas fa-users fa-fw mr-1"></i>Gruppen</a>
                            </li>
                        </ul>
                    </div>
                </nav>
                
                <div role="main" class="col no-gutters">
                    <div id="inAppNotifications" class="inAppNotifications p-1 row">
                    </div>
                    <div id="loadingOverlay">
                        <div id="loadingSpinner" style="width: 3rem; height: 3rem; display:none;"></div>
                    </div>
                    <div id="main" class="col"></div>                
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function () {
                changeMode('lectures');

                //eventlistener für responsive sidebar
                $('#sidebarCollapse').on('click', function () {
                    $('.sidebar').toggleClass('inactive');
                });
                $('#nav').on('click', function () {
                    //soll nur bei mobile devices ausgeführt werden
                    //selber wert wie in media query in backend.css
                    if ($(window).width() < 768 ){
                        $('.sidebar').toggleClass('inactive');
                    }                    
                });
                $('#main').on('click', function () {
                    //soll nur bei mobile devices ausgeführt werden
                    //selber wert wie in media query in backend.css
                    if ($(window).width() < 768 ){
                        $('.sidebar').toggleClass('inactive',false);
                    }  
                });
            });
        </script>

        <?php generate_modal_usermenu() ?>
    </body>
</html>