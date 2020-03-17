<?php
require("functions.php");
generate_header("Benutzer registrieren", "Herzlich Willkomen zur Online-Vorlesungsplattform der DHBW Ravensburg", null, '../');


$conn = sql_connect();
$successInsert;
$errorMsgInsert = "Allgemeiner Fehler aufgetreten";


if (isset($_POST["submit"])) {
    if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["kurs"]) && isset($_POST["passwordRepeat"])) {

        mysqli_autocommit($conn, FALSE);
        $hash = md5($_POST['password']);
        //Prüfen ob Benutzer bereits vorhanden
        $sql = "SELECT benutzer_id FROM vl_benutzer WHERE benutzername = '" . $_POST['username'] . "'";
        $result = mysqli_query($conn, $sql);
        if ($row = mysqli_fetch_assoc($result)) {
            $successInsert = false;
            $errorMsgInsert = "Benutzer bereits vorhanden";
        } else {



            //Benutzer einfügen
            $sql = "INSERT INTO vl_benutzer(benutzername, Password) VALUES ('" . $_POST['username'] . "','" . $hash . "');";
            if (mysqli_query($conn, $sql)) {
                $benutzer_id;
                $sql = "SELECT benutzer_id FROM vl_benutzer WHERE benutzername = '" . $_POST['username'] . "'";
                $result = mysqli_query($conn, $sql);

                if ($row = mysqli_fetch_assoc($result)) {
                    $benutzer_id = $row["benutzer_id"];
                    //Benutzer_Gruppe_MAP
                    $sql = "INSERT INTO vl_benutzer_gruppe_map(benutzer_id, gruppe_id) VALUES (" . $benutzer_id . ", " . $_POST['kurs'] . ");";
                    require_once("../configuration.php");
                    $sql2 = "INSERT INTO vl_benutzer_gruppe_map(benutzer_id, gruppe_id) VALUES (" . $benutzer_id . ", " . appconfig::$defaultStudentGroup . ");";
                    if (mysqli_query($conn, $sql)) {
                        if (mysqli_query($conn, $sql2)) {
                            $successInsert = true;
                            mysqli_commit($conn);
                            mysqli_autocommit($conn, true);
                        } else {
                            mysqli_rollback($conn);
                            $successInsert = false;
                            $errorMsgInsert = mysqli_error($conn);
                        }
                    } else {
                        mysqli_rollback($conn);
                        $successInsert = false;
                        $errorMsgInsert = mysqli_error($conn);
                    }
                } else {
                    $successInsert = false;
                    mysqli_rollback($conn);
                }
            } else {
                $successInsert = false;
                $errorMsgInsert = mysqli_error($conn);
            }
        }
    } else {
        $successInsert = false;
        $errorMsgInsert = "Angaben fehlerhaft";
    }
    mysqli_autocommit($conn, true);
}


if (isset($successInsert)) {
    if ($successInsert) {
?>

        <div class="alert alert-success" role="alert">
            Benutzer <?=$_POST["username"]?> registriert.
            <a href="../index.php" class="btn btn-success">Zum Login</a>
        </div>
    <?php
    } else {
    ?>

        <div class="alert alert-danger" role="alert">
            Benutzer nicht registriert - versuchen Sie es erneut.<br>
            Fehler: <?= $errorMsgInsert ?>
        </div>
<?php
    }
}

mysqli_close($conn);
?>

<div class="container-xl">
    <div class="row justify-content-center">
        <div class="col-sm-8">
            <div class="card">
                <div class="card-header">
                    <h4>Registrieren</h4>
                </div>
                <div class="card-body">

                    <form method="POST" action="register.php" class="was-validated" id="form">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Benutzername" required id="username" minlength="4" maxlength="50" name="username">
                            <div class="invalid-feedback" id="error_username" hidden>Benutzername eingeben</div>
                            <div class="valid-feedback" id="valid_username"> Benutzername verfügbar </div>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="Passwort" required id="password" name="password">
                            <div class="invalid-Feedback" id="error_password" hidden> Passwort eingeben</div>
                            <input type="password" class="form-control" placeholder="Passwort wiederholen" required id="passwordRepeat" name="passwordRepeat">
                            <div class="invalid-Feedback" id="error_passwordRepeat" hidden> Passwörter müssen übereinstimmen</div>
                        </div>
                        <div class="form-group">
                            <select id="kurs" class="form-control" name="kurs" required>
                                <option value="" disabled selected>Kurs auswählen</option>
                                <?php
                                $conn = sql_connect();
                                $kurs_select = "SELECT gruppenname, gruppe_id, gruppe_kuerzel FROM vl_gruppe WHERE gruppe_id > 2";
                                $result = mysqli_query($conn, $kurs_select);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo ('<option value="' . $row["gruppe_id"] . '">' . $row["gruppe_kuerzel"] . '</option>');
                                }
                                mysqli_close($conn);
                                ?>
                            </select>
                            <div class="invalid-Feedback" id="error_kurs" hidden> Bitte Kurs auswählen </div>
                        </div>
                </div>
                <div class="card-footer">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-8">
                                <input type="submit" class="btn btn-success form-control" value="Registrieren" id="submit" name="submit" disabled />
                            </div>
                            <div class="col-4">
                                <a class="btn btn-danger form-control" href="../index.php">Abbrechen</a>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>





<?php
generate_footer();
?>
<script>
    //Formular prüfen

    var password = document.getElementById('password');
    var passwordRepeat = document.getElementById('passwordRepeat');
    var username = document.getElementById('username');
    var form = document.getElementById('form');
    var kurs = document.getElementById('kurs');


    var checkForm = function() {

        document.getElementById("error_password").removeAttribute("hidden");
        document.getElementById("error_passwordRepeat").removeAttribute("hidden");
        document.getElementById("error_kurs").removeAttribute("hidden");

        checkPassword("password","passwordRepeat");
        checkKurs();
        checkField("username", "Benutzername", "submit", false, null, "form");
    };

    var checkKurs = function() {
        if (kurs.value == '') {
            kurs.setCustomValidity('Kurs auswählen!');
        } else {
            kurs.setCustomValidity('');
        }
    };

    username.addEventListener('input', checkForm);
    password.addEventListener('input', checkForm);
    passwordRepeat.addEventListener('input', checkForm);
    kurs.addEventListener('input', checkForm);
</script>