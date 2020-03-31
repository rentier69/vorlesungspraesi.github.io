<?php
session_start();
if (!isset($_SESSION["dozent"])) {
    header("Location:../index.php");
    die("Bitte melden Sie sich an");
}
require("functions.php");
$benutzer_id = $_GET['id'];

$conn = sql_connect();
$user_select = "SELECT * from vl_benutzer where benutzer_id = $benutzer_id";
$current_user = mysqli_fetch_assoc(mysqli_query($conn, $user_select));
$user_name = $current_user['benutzername'];
$user_aktiv = $current_user['aktiv'];
mysqli_close($conn);

if (isset($_POST['save'])) {
    $conn = sql_connect();
    $errorMsgSave = "";
    $successSave = true;

    //if(isset($_POST['to_delete'])){
    if (!empty($_POST['to_delete'])) {
        $toDelete = explode(";", str_replace(',', '', rtrim($_POST['to_delete'], ";")));
        foreach ($toDelete as $value) {
            $sql1 = "DELETE FROM `vl_benutzer_gruppe_map` where gruppe_id = $value and benutzer_id = $benutzer_id";
            if (mysqli_query($conn, $sql1)) {
                //nichts zu tun. $successSave bereits true
            } else {
                $successSave = false;
                $errorMsgSave . mysqli_error($conn);
            }
        }
    }
    //if(isset($_POST['to_add'])){
    if (!empty($_POST['to_add'])) {
        $toAdd = explode(";", str_replace(',', '', rtrim($_POST['to_add'], ";")));
        foreach ($toAdd as $value) {
            $sql2 = "INSERT INTO `vl_benutzer_gruppe_map`(`benutzer_id`, `gruppe_id`) values ($benutzer_id,$value)";
            if (mysqli_query($conn, $sql2)) {
                //nichts zu tun. $successSave bereits true
            } else {
                $successSave = false;
                $errorMsgSave . mysqli_error($conn);
            }
        }
    }
    if ($_POST['newNameTarget'] != $user_name) {
        $name = $_POST['newNameTarget'];
        $sql3 = "UPDATE `vl_benutzer` set `benutzername`='$name' where `benutzer_id` = $benutzer_id";

        if (mysqli_query($conn, $sql3)) {
            //nichts zu tun. $successSave bereits true
            $user_name = $name;
        } else {
            $successSave = false;
            $errorMsgSave . mysqli_error($conn);
        }
    }
    mysqli_close($conn);
}

if (isset($_POST['user_deactivate'])) {
    $conn = sql_connect();
    $errorMsgSave = "";

    $sql4 = "UPDATE `vl_benutzer` set `aktiv`= 0 where `benutzer_id` = $benutzer_id";

    if (mysqli_query($conn, $sql4)) {
        $successSave = true;
        $user_aktiv = 0;
    } else {
        $successSave = false;
        $errorMsgSave . mysqli_error($conn);
    }
    mysqli_close($conn);
} elseif (isset($_POST['user_activate'])) {
    $conn = sql_connect();
    $errorMsgSave = "";

    $sql4 = "UPDATE `vl_benutzer` set `aktiv`= 1 where `benutzer_id` = $benutzer_id";

    if (mysqli_query($conn, $sql4)) {
        $successSave = true;
        $user_aktiv = 1;
    } else {
        $successSave = false;
        $errorMsgSave . mysqli_error($conn);
    }
    mysqli_close($conn);
}

if (isset($_POST["submitPasswordReset"])) {
    if (isset($_POST["password1"]) && isset($_POST["password2"])) {
        $password1 = $_POST["password1"];
        $password2 = $_POST["password2"];
        if (strlen($password1) >= 6 && $password1 == $password2) {
            $passwordHash = md5($password1);
            $conn = sql_connect();
            $sqlPassword = "UPDATE vl_benutzer SET password='" . $passwordHash . "' WHERE benutzer_id = $benutzer_id;";
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
<?php
if ($user_aktiv != 1) {
    generate_header("Benutzer bearbeiten", $user_name . ' (inaktiv)', $_SESSION['username'], '../');
} else {
    generate_header("Benutzer bearbeiten", $user_name, $_SESSION['username'], '../');
}
?>
<div id="passwordResetModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?= $user_name ?> - Passwort zurücksetzen</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <form class="was-validated" method="POST" action="useredit.php?id=<?= $benutzer_id ?>" id="formPasswordReset">
                    <div class="form-group" >
                        <input type="password" name="password1" id="password1" class="form-control" placeholder="Passwort" size="25" required />
                        <div class="invalid-Feedback" id="error_password1" hidden> Passwort eingeben</div>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password2" id="password2" class="form-control" placeholder="Passwort wiederholen" size="25" required />
                        <div class="invalid-Feedback" id="error_password2" hidden> Passwörter müssen übereinstimmen</div>
                    </div>

            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success" name="submitPasswordReset" id="submitPasswordReset" disabled>Passwort ändern</button>
                </form>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Abbrechen</button>
            </div>
        </div>

    </div>
</div>




<div class="container-xl">
    <?php
    if (isset($successSave)) {
        if ($successSave) {
    ?>
            <div class="alert alert-success" role="alert">
                Änderungen gespeichert.
            </div>
        <?php
        } else {
        ?>
            <div class="alert alert-danger" role="alert">
                Änderungen nicht gespeichert - versuchen Sie es erneut.<br>
                Fehler: <?= $errorMsgSave ?>
            </div>
        <?php
        }
    }

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
    ?>
    <div class="row">
        <div class="col-sm">
            <form action="useredit.php?id=<?= $benutzer_id ?>" method="post">
                <div class="input-group mb-3">
                    <input type="hidden" id="to_delete" name="to_delete" class="form-control" />
                    <input type="hidden" id="to_add" name="to_add" class="form-control" />
                    <input type="hidden" name="newNameTarget" id="newNameTarget" class="form-control" value="<?= $user_name ?>" />
                    <input type="submit" class="form-control btn btn-success" name="save" value="Alle Änderungen speichern">
                </div>
            </form>
        </div>
        <div class="col-sm">
            <form action="useredit.php?id=<?= $benutzer_id ?>" method="post">
                <div class="input-group mb-3">
                    <input type="hidden" name="benutzer_id" value="<?= $benutzer_id ?>" />
                    <?php
                    if ($user_aktiv == 1) {
                        echo '<input type="submit" class="form-control btn btn-light" name="user_deactivate" value="Benutzer deaktivieren">';
                    } else {
                        echo '<input type="submit" class="form-control btn btn-light" name="user_activate" value="Benutzer aktivieren">';
                    }
                    ?>

                </div>
            </form>
        </div>
        <div class="col-sm">
            <form action="usermgmt.php" method="post">
                <div class="input-group mb-3">
                    <input type="hidden" name="benutzer_id" value="<?= $benutzer_id ?>" />
                    <input type="submit" class="form-control btn btn-light" name="user_delete" value="Benutzer löschen">
                </div>
            </form>
        </div>
        <div class="col-sm">
            <button type="button" class="btn btn-light" data-toggle="modal" data-target="#passwordResetModal">Passwort zurücksetzen</button>
        </div>
        <div class="col-sm">
            <a href="usermgmt.php" class="form-control btn btn-light">Schließen</a>
        </div>
    </div>

    <h3>Name ändern</h3>
    <form action="useredit.php?id=<?= $benutzer_id ?>" method="post" class="was-validated" id="formNameAendern">
        <div class="form-group">
            <div class="input-group mb-3">
                <input type="text" name="newNameSource" id="newNameSource" class="form-control" onkeyup="fillInput('newNameSource','newNameTarget')" placeholder="Name" value="<?= $user_name ?>" size="20" />
                <div class="invalid-feedback" id="error_newNameSource" hidden>Benutzername eingeben</div>
                <div class="valid-feedback" id="valid_newNameSource" hidden> Benutzername verfügbar </div>
            </div>
        </div>
    </form>
    <h3>Mitglied von</h3>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kürzel</th>
                <th>Name</th>
                <th>Aus Gruppe entfernen</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $conn = sql_connect();
            $group_select = "SELECT gruppe_id, gruppe_kuerzel, gruppenname from vl_gruppe where gruppe_id in (select gruppe_id from vl_benutzer_gruppe_map where benutzer_id = $benutzer_id)";
            $result = mysqli_query($conn, $group_select);
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <tr id="memberOf_<?= $row['gruppe_id'] ?>">
                    <td><?= $row['gruppe_id'] ?></td>
                    <td><?= $row['gruppe_kuerzel'] ?></a></td>
                    <td><?= $row['gruppenname'] ?></td>
                    <td><i class="fas fa-user-minus" onclick="markRemove(<?= $row['gruppe_id'] ?>)"></i></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

    <h3>Alle Gruppen</h3>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kürzel</th>
                <th>Name</th>
                <th>Zu Gruppe hinzufügen</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $conn = sql_connect();
            $group_select = "SELECT gruppe_id, gruppe_kuerzel, gruppenname from vl_gruppe where gruppe_id not in (select gruppe_id from vl_benutzer_gruppe_map where benutzer_id = $benutzer_id)";
            $result = mysqli_query($conn, $group_select);
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <tr id="noMemberOf_<?= $row['gruppe_id'] ?>">
                    <td><?= $row['gruppe_id'] ?></td>
                    <td><?= $row['gruppe_kuerzel'] ?></a></td>
                    <td><?= $row['gruppenname'] ?></td>
                    <td><i class="fas fa-user-plus" onclick="markAdd(<?= $row['gruppe_id'] ?>)"></i></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

</div>

<script>
    function fillInput($source, $target) {
        document.getElementById($target).value = document.getElementById($source).value;
    }

    function markRemove($id) {
        $idToRemove = ',' + $id + ';';
        if (document.getElementById("memberOf_" + $id).hasAttribute("style")) {
            document.getElementById("memberOf_" + $id).removeAttribute("style");
            document.getElementById("to_delete").value = document.getElementById("to_delete").value.replace($idToRemove, "");
        } else {
            document.getElementById("memberOf_" + $id).style.color = "lightgrey";
            document.getElementById("to_delete").value += $idToRemove;
        }
    }

    function markAdd($id) {
        $idToAdd = ',' + $id + ';';
        if (document.getElementById("noMemberOf_" + $id).hasAttribute("style")) {
            document.getElementById("noMemberOf_" + $id).removeAttribute("style");
            document.getElementById("to_add").value = document.getElementById("to_add").value.replace($idToAdd, "");
        } else {
            document.getElementById("noMemberOf_" + $id).style.background = "lightgrey";
            document.getElementById("to_add").value += $idToAdd;
        }
    }



    var checkForm = function() {
        checkField("newNameSource", "Benutzername", null, true, "<?php echo $user_name ?>", "formNameAendern");
    };

    var checkPasswordReset = function() {
        checkPassword("password1", "password2");
        changeSubmitButton("submitPasswordReset", "formPasswordReset");
    };
    username = document.getElementById("newNameSource");
    username.addEventListener("input", checkForm);
    document.getElementById("password1").addEventListener("input", checkPasswordReset);
    document.getElementById("password2").addEventListener("input", checkPasswordReset);

 
  $("#passwordResetModal").on('shown.bs.modal', function(){
    document.getElementById("password1").focus();
  });

</script>

<?php
generate_footer();
?>