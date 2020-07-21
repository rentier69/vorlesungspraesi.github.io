<?php

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



if (isset($_GET["kuerzel"]) || isset($_GET["newKuerzelSource"])) {
    //prepared statements!
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
    //prepared statements!
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
    //prepared stmt !
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

function generate_modal_usermenu()
{
?>
    <!-- Modal Passwort ändern-->
    <div id="passwordChangeModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= $_SESSION["username"] ?> - Passwort ändern</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form class="was-validated" method="POST" id="formChangePassword">
                        <div class="form-group">
                            <input type="password" name="passwordChange" id="passwordChange" class="form-control" placeholder="Passwort" size="25" />
                            <div class="invalid-Feedback" id="error_passwordChange" hidden> Passwort eingeben</div>
                            <div class="valid-Feedback" id="valid_passwordChange" hidden> </div>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password2Change" id="password2Change" class="form-control" placeholder="Passwort wiederholen" size="25" />
                            <div class="invalid-Feedback" id="error_password2Change" hidden> Passwörter müssen übereinstimmen</div>
                            <div class="valid-Feedback" id="valid_password2Change" hidden> </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal" name="submitPasswordChange" id="submitPasswordChange" disabled onclick="changePassword()">Passwort ändern</button>
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
                    <h4 class="modal-title"><?= $_SESSION["username"] ?> - Benutzermenü</h4>
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
            document.getElementById("password2Change").setAttribute("required", "true");
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