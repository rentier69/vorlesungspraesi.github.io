<?php
session_start();
if(isset($_SESSION["username"])){
    
}

require("components/functions.php");

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
            $sql = "SELECT COUNT(*)  as vorhanden, aktiv, benutzer_id FROM vl_benutzer WHERE benutzername = '" . $username . "' AND password = '" . $hash . "'";

            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                if ($row["vorhanden"] == 1 && $row["aktiv"] == 1) {
                    $anmeldung_ok = true;
                    require_once("configuration.php");
                    $_SESSION["username"]=$username;
                    //Timestamp letzter Login
                    mysqli_query($conn,"UPDATE vl_benutzer SET datum_letzterlogin = NOW() WHERE benutzer_id = ".$row['benutzer_id'].";");
                    //Prüfen ob Dozent
                    $sql="SELECT COUNT(*) AS Dozent FROM vl_benutzer_gruppe_map WHERE benutzer_id=".$row['benutzer_id']." AND gruppe_id=".appConfig::$defaultDozentGroup.";";
                    $result=mysqli_query($conn, $sql);
                    while($row=mysqli_fetch_assoc($result)){
                        if($row["Dozent"]==1){
                            $_SESSION["dozent"]=true;
                            header("Location: components/backend.php");
                        }else{
                            header("Location: components/frontend.php");
                        }
                    }
                    
                    
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

generate_header("Startseite", "Herzlich Willkomen zur Online-Vorlesungsplattform der DHBW Ravensburg", null, null);

// if(isset($_SESSION['gruppe'])){
//     header("Location: components/menu.php");
// }

mysqli_close($conn);

?>

<div class="container-xl">
    <div class="row justify-content-center">
    <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h4>Login</h4>
                </div>
                <div class="card-body">
                    <?php
                    if (isset($anmeldung_ok) && !$anmeldung_ok) {
                        ?>
                        <div class="alert alert-danger" role="alert">
                            Login fehlgeschlagen - <?= $errormsg ?>
                        </div>
                        <?php            
                    }
                    ?>
                    <form method="POST" action="index.php" class="was-validated">
                        <label for="benutzername"> Benutzername </label>
                        <input type="text" class="form-control" placeholder="Benutzername" name="username" required maxlength="50" id="username" autofocus/>
                        <div class="invalid-Feedback" id="error_username" hidden> Bitte Benutzername eingeben</div>

                        <label for="password"> Passwort </label>
                        <input type="password" class="form-control" placeholder="Passwort" name="password" required id="password" />
                        <div class="invalid-Feedback" id="error_password" hidden> Bitte Passwort eingeben</div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-6">
                            <input type="submit" class="btn btn-success btn-block" value="Login" name="submit" id="submit" disabled>
                        </div>
                        <div class="col-6">
                            <a href="components/register.php" class="btn btn-secondary btn-block">Registrieren</a>
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
    var username = document.getElementById('username');
    var password = document.getElementById('password');


    var checkForm = function() {
        document.getElementById("error_username").removeAttribute("hidden");
        document.getElementById("error_password").removeAttribute("hidden");
        if (checkUsername() && checkPassword()) {
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

    var checkPassword = function(){
        if(password.value.length>0){
            password.setCustomValidity('');
            return true;
        }else{
            password.setCustomValidity('Bitte Passwort eingeben');
            document.getElementById('submit').disabled = true;
            return false;
        }
        return false;
    };

    username.addEventListener('input', checkForm);
    password.addEventListener('input', checkForm);
</script>