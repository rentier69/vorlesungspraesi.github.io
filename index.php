<?php
require("components/functions.php");
generate_header("Startseite", "Herzlich Willkomen zur Online-Vorlesungsplattform der DHBW Ravensburg", null, null);
?>

<?php
$conn = sql_connect();
$groups = array();



if (isset($_POST['submit'])) {
    if (isset($_POST['username']) && $_POST['password']) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if (strlen($username) >= 4 && strlen($password) > 0) {
            //Daten validieren
            $hash = md5($password);
            $sql = "SELECT COUNT(*)  as vorhanden, aktiv FROM vl_benutzer WHERE benutzername = '" . $username . "' AND password = '" . $hash . "'";

            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {

                if ($row["vorhanden"] == 1 && $row["aktiv"] == 1) {
                    $anmeldung_ok = true;
                } elseif ($row["vorhanden"] == 0) {
                    $anmeldung_ok = false;
                    $errormsg = "Benutzername oder Passwort falsch";
                } elseif ($row["aktiv"] == 0) {
                    $anmeldung_ok = false;
                    $errormsg = "Benutzer gesperrt";
                }
            }
        } else {
            $anmeldung_ok = false;
            $errormsg = "Eingabe wiederholen";
        }
    } else {
        $anmeldung_ok = false;
        $errormsg = "Eingabe wiederholen";
    }
}



if (isset($anmeldung_ok)) {
    if ($anmeldung_ok) {
        $sql = "      SELECT gruppe.gruppenname, gruppe.gruppe_id 
                FROM vl_gruppe as gruppe, vl_benutzer_gruppe_map as map, vl_benutzer as benutzer 
                WHERE   gruppe.gruppe_id=map.gruppe_id AND
                        benutzer.benutzer_id=map.Benutzer_id AND
                        benutzer.benutzername='" . $_POST['username'] . "';";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($groups, $row);
        }


?>

        <div class="alert alert-success" role="alert">
            Login erfolgreich - Benutzergruppe
            <?php foreach ($groups as $group) {
                echo ($group["gruppenname"] . "<br/>");
            } ?>
        </div>
    <?php
    } else {
    ?>

        <div class="alert alert-danger" role="alert">
            Login fehlgeschlagen - <?= $errormsg ?>
        </div>
<?php

    }
}





mysqli_close($conn);
?>

<div class="container-fluid">
    <form method="POST" action="index.php" class="was-validated">

        <div class="form-group">
            <label for="benutzername"> Benutzername </label>


            <input type="text" class="form-control" placeholdDiver="Benutzername" name="username" required maxlength="50" id="username" />

            <div class="invalid-Feedback" id="error_username"> Bitte Benutzername eingeben</div>

            <label for="password"> Passwort </label>
            <input type="password" class="form-control" placeholdDiver="Passwort" name="password" required id="password" />
            <div class="invalid-Feedback"> Bitte Passwort eingeben</div>
        </div>

        <div class="row">
            <div class="col-6">
                <input type="submit" class="btn btn-success btn-block" value="Login" name="submit" id="submit" disabled>
            </div>

            <div class="col-6">
                <a href="components/register.php" class="btn btn-secondary btn-block">Registrieren</a>
            </div>

    </form>
</div>





<?php
generate_footer();
?>

<script>
    var username = document.getElementById('username');
    var password = document.getElementById('password');

    var checkForm = function() {


        if (checkUsername() && password.value.length > 0) {
            document.getElementById('submit').disabled = false;
        } else {
            document.getElementById('submit').disabled = true;
        }
    };

    var checkUsername = function() {

        if (username.value.length == 0) {
            username.setCustomValidity('Bitte Benutzername eingeben');
            var oldDivDiv = document.querySelector('#error_username');
            var newDiv = document.createElement('div');
            newDiv.appendChild(document.createTextNode("Bitte Benutzername eingeben"));
            oldDivDiv.parentNode.replaceChild(newDiv, oldDivDiv);
            newDiv.setAttribute('id', 'error_username');
            newDiv.setAttribute('class', 'invalid-Feedback');
            document.getElementById('submit').disabled = true;
        } else if (username.value.length < 4) {
            username.setCustomValidity('Benutzername muss mind. 4 Zeichen lang sein!');
            var oldDiv = document.querySelector('#error_username');
            var newDiv = document.createElement('div');
            newDiv.appendChild(document.createTextNode("Benutzername muss mind. 4 Zeichen lang sein!"));
            oldDiv.parentNode.replaceChild(newDiv, oldDiv);
            newDiv.setAttribute('id', 'error_username');
            newDiv.setAttribute('class', 'invalid-Feedback');
            document.getElementById('submit').disabled = true;
        } else {
            username.setCustomValidity('');
            return true;
        }
        return false;


    };



    username.addEventListener('input', checkForm);
    password.addEventListener('input', checkForm);
</script>