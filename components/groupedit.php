<?php
session_start();
if (!isset($_SESSION["dozent"])) {
    header("Location:../index.php");
    die("Bitte melden Sie sich an");
}
require("functions.php");
$gruppe_id = $_GET['id'];

$conn = sql_connect();
$group_select = "SELECT * from vl_gruppe where gruppe_id = $gruppe_id";
$current_group = mysqli_fetch_assoc(mysqli_query($conn, $group_select));
$gruppe_name = $current_group['gruppenname'];
$gruppe_kurzel = $current_group['gruppe_kuerzel'];


if (isset($_POST['save'])) {
    $errorMsgSave = "";
    $successSave = true;

    if (!empty($_POST['to_delete'])) {
        $toDelete = explode(";", str_replace(',', '', rtrim($_POST['to_delete'], ";")));
        foreach ($toDelete as $value) {
            $sql1 = "DELETE FROM `vl_benutzer_gruppe_map` where benutzer_id = $value and gruppe_id = $gruppe_id";
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
            $sql2 = "INSERT INTO `vl_benutzer_gruppe_map`(`benutzer_id`, `gruppe_id`) values ($value,$gruppe_id)";
            if (mysqli_query($conn, $sql2)) {
                //nichts zu tun. $successSave bereits true
            } else {
                $successSave = false;
                $errorMsgSave . mysqli_error($conn);
            }
        }
    }

    if ($_POST['newNameTarget'] != $gruppe_name || $_POST['newKuerzelTarget'] != $gruppe_kurzel) {
        $kuerzel = $_POST['newKuerzelTarget'];
        $kursname = $_POST['newNameTarget'];
        $sql3 = "UPDATE `vl_gruppe` set `gruppe_kuerzel`='$kuerzel',`gruppenname`= '$kursname' where `gruppe_id` = $gruppe_id";

        if (mysqli_query($conn, $sql3)) {
            //nichts zu tun. $successSave bereits true
            $gruppe_name = $kursname;
            $gruppe_kurzel = $kuerzel;
        } else {
            $successSave = false;
            $errorMsgSave . mysqli_error($conn);
        }
    }
}
mysqli_close($conn);
/*
        $toDelete = explode(";", str_replace(',', '', rtrim($_POST['to_delete'],";")));
        $toAdd = explode(";", str_replace(',', '', rtrim($_POST['to_add'], ";")));

        foreach ($toDelete as $value){
            $sql = "DELETE FROM `vl_benutzer_gruppe_map` where benutzer_id = $value and gruppe_id = $gruppe_id";
            mysqli_query($conn, $sql);
        }
        foreach ($toAdd as $value){
            $sql = "INSERT INTO `vl_benutzer_gruppe_map`(`benutzer_id`, `gruppe_id`) values ($value,$gruppe_id)";
            mysqli_query($conn, $sql);
        }
        mysqli_close($conn);

    }elseif(isset($_POST['save'])){
        $conn = sql_connect();
        $kuerzel = $_POST['kuerzel'];
        $kursname = $_POST['kurs'];        
        $sql = "UPDATE `vl_gruppe` set `gruppe_kuerzel`='$kuerzel',`gruppenname`= '$kursname' where `gruppe_id` = $gruppe_id";

        if (mysqli_query($conn, $sql)) {
            $successModify = true;
        } else {
            $successModify = false;
            $errorMsgModify = mysqli_error($conn);
        }
        mysqli_close($conn);
    }
    */
?>

<?php
generate_header("Kurs bearbeiten", $gruppe_name, $_SESSION['username'], '../');
?>

<div class="container-xl">

    <div class="row">
        <div class="col-sm">
            <form action="groupedit.php?id=<?= $gruppe_id ?>" method="post">
                <div class="input-group mb-3">
                    <input type="hidden" id="to_delete" name="to_delete" class="form-control" />
                    <input type="hidden" id="to_add" name="to_add" class="form-control" />
                    <input type="hidden" name="newKuerzelTarget" id="newKuerzelTarget" class="form-control" value="<?= $gruppe_kurzel ?>" />
                    <input type="hidden" name="newNameTarget" id="newNameTarget" class="form-control" value="<?= $gruppe_name ?>" />
                    <input type="submit" class="form-control btn btn-success" name="save" value="Änderungen speichern">
                </div>
            </form>
        </div>
        <div class="col-sm">
            <form action="groupmgmt.php" method="post">
                <div class="input-group mb-3">
                    <input type="hidden" name="gruppe_id" value="<?= $gruppe_id ?>" />
                    <input type="submit" class="form-control btn btn-light" name="group_delete" value="Gruppe Löschen">
                </div>
            </form>
        </div>
        <div class="col-sm">
            <a href="groupmgmt.php" class="form-control btn btn-light">Schließen</a>
        </div>
    </div>

    <h3>Name ändern</h3>
    <?php
    if (isset($successModify)) {
        if ($successModify) {
    ?>
            <div class="alert alert-success" role="alert">
                Änderungen gespeichert.
            </div>
        <?php
        } else {
        ?>
            <div class="alert alert-danger" role="alert">
                Änderungen nicht gespeichert - versuchen Sie es erneut.<br>
                Fehler: <?= $errorMsgModify ?>
            </div>
    <?php
        }
    }
    ?>
    <form action="groupedit.php?id=<?= $gruppe_id ?>" method="post" class="was-validated">
        <div class="input-group mb-3">
            <div class="form-group">
                <input type="text" name="newKuerzelSource" id="newKuerzelSource" class="form-control" placeholder="Kürzel" value="<?= $gruppe_kurzel ?>" onkeyup="fillInput('newKuerzelSource','newKuerzelTarget')" />
                <div class="invalid-feedback" id="error_kuerzel" hidden>Kürzel eingeben</div>
                <div class="valid-feedback" id="valid_kuerzel" hidden> Kürzel verfügbar </div>
            </div>
            <div class="form-group">
                <input type="text" name="newNameSource" id="newNameSource" class="form-control" placeholder="Kursname" value="<?= $gruppe_name ?>" onkeyup="fillInput('newNameSource','newNameTarget')" />
                <div class="invalid-feedback" id="error_kursname" hidden>Kursname eingeben</div>
                <div class="valid-feedback" id="valid_kursname" hidden> Kursname verfügbar </div>
            </div>
        </div>
    </form>
    <h3>Mitglieder entfernen</h3>
    <table class="table table-hover" id="members">
        <thead>
            <tr>
                <th>ID</th>
                <th>Benutzername</th>
                <th>Aktiviert</th>
                <th>Registriert am</th>
                <th>Zuletzt aktiv am</th>
                <th>Aus Gruppe entfernen</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $conn = sql_connect();
            $group_select = "SELECT benutzer_id, benutzername, aktiv, datum_registriert, datum_letzterlogin from vl_benutzer where benutzer_id in (select benutzer_id from vl_benutzer_gruppe_map where gruppe_id = $gruppe_id)";
            $result = mysqli_query($conn, $group_select);
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <tr id="member_<?= $row['benutzer_id'] ?>">
                    <td><?= $row['benutzer_id'] ?></td>
                    <td><?= $row['benutzername'] ?></td>
                    <td><?= $row['aktiv'] ?></td>
                    <td><?= $row['datum_registriert'] ?></td>
                    <td><?= $row['datum_letzterlogin'] ?></td>
                    <td><i class="fas fa-user-minus" onclick="markDelete(<?= $row['benutzer_id'] ?>)"></i></td>
                </tr>
            <?php
            }
            mysqli_close($conn);
            ?>
        </tbody>
    </table>
    <h3>Mitglieder hinzufügen</h3>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Benutzername</th>
                <th>Aktiviert</th>
                <th>Registriert am</th>
                <th>Zuletzt aktiv am</th>
                <th>Zu Gruppe hinzufügen</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $conn = sql_connect();
            $group_select = "select benutzer_id, benutzername, aktiv, datum_registriert, datum_letzterlogin from vl_benutzer where benutzer_id not in (select benutzer_id from vl_benutzer_gruppe_map where gruppe_id = $gruppe_id)";
            $result = mysqli_query($conn, $group_select);
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <tr id="noMember_<?= $row['benutzer_id'] ?>">
                    <td><?= $row['benutzer_id'] ?></td>
                    <td><?= $row['benutzername'] ?></td>
                    <td><?= $row['aktiv'] ?></td>
                    <td><?= $row['datum_registriert'] ?></td>
                    <td><?= $row['datum_letzterlogin'] ?></td>
                    <td><i class="fas fa-user-plus" onclick="markAdd(<?= $row['benutzer_id'] ?>)"></i></td>
                </tr>
            <?php
            }
            mysqli_close($conn);
            ?>
        </tbody>
    </table>

</div>

<script>
    kuerzel = document.getElementById("newKuerzelSource");
    kursname = document.getElementById("newNameSource");

    function fillInput($source, $target) {
        document.getElementById($target).value = document.getElementById($source).value;
    }

    function markDelete($id) {
        $idToRemove = ',' + $id + ';';
        if (document.getElementById("member_" + $id).hasAttribute("style")) {
            document.getElementById("member_" + $id).removeAttribute("style");
            document.getElementById("to_delete").value = document.getElementById("to_delete").value.replace($idToRemove, "");
        } else {
            document.getElementById("member_" + $id).style.color = "lightgrey";
            document.getElementById("to_delete").value += $idToRemove;
        }
    }

    function markAdd($id) {
        $idToAdd = ',' + $id + ';';
        if (document.getElementById("noMember_" + $id).hasAttribute("style")) {
            document.getElementById("noMember_" + $id).removeAttribute("style");
            document.getElementById("to_add").value = document.getElementById("to_add").value.replace($idToAdd, "");
        } else {
            document.getElementById("noMember_" + $id).style.background = "lightgrey";
            document.getElementById("to_add").value += $idToAdd;
        }
    }

    var checkKuerzel = function() {

        if (kuerzel.value == '') {
            kuerzel.setCustomValidity('Kürzel eingeben');
            var oldDiv = document.querySelector('#error_kuerzel');
            var newDiv = document.createElement('div');
            newDiv.appendChild(document.createTextNode("Kürzel eingeben"));
            oldDiv.parentNode.replaceChild(newDiv, oldDiv);
            newDiv.setAttribute('id', 'error_kuerzel');
            newDiv.setAttribute('class', 'invalid-Feedback');
        } else {
            if (kuerzel.value.length < 2) {
                kuerzel.setCustomValidity('Kürzel muss mind. 2 Zeichen lang sein');
                var oldDiv = document.querySelector('#error_kuerzel');
                var newDiv = document.createElement('div');
                newDiv.appendChild(document.createTextNode("Kürzel muss mind. 2 Zeichen lang sein"));
                oldDiv.parentNode.replaceChild(newDiv, oldDiv);
                newDiv.setAttribute('id', 'error_kuerzel');
                newDiv.setAttribute('class', 'invalid-Feedback');
            } else {
                //falls neues Kürzel = altes Kürzel
                if (kuerzel.value == "<?php echo $gruppe_kurzel ?>") {
                    kuerzel.setCustomValidity('');
                    document.getElementById("valid_kuerzel").setAttribute("hidden", "true");
                } else {
                    //Prüfen, ob Kürzel bereits in DB. Liefert true falls ja
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "functions.php?kuerzel=" + kuerzel.value, true);
                    xhr.send();

                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            if (!xhr.responseText) {
                                kuerzel.setCustomValidity('');
                                document.getElementById("valid_kuerzel").removeAttribute("hidden");
                            } else {
                                kuerzel.setCustomValidity('Kürzel bereits vergeben');
                                var oldDiv = document.querySelector('#error_kuerzel');
                                var newDiv = document.createElement('div');
                                newDiv.appendChild(document.createTextNode("Kürzel bereits vergeben"));
                                oldDiv.parentNode.replaceChild(newDiv, oldDiv);
                                newDiv.setAttribute('id', 'error_kuerzel');
                                newDiv.setAttribute('class', 'invalid-Feedback');
                            }
                        }
                    };
                }
            }
        }
    };


    var checkKursname = function() {

        if (kursname.value == '') {
            kursname.setCustomValidity('Kursnname eingeben');
            var oldDiv = document.querySelector('#error_kursname');
            var newDiv = document.createElement('div');
            newDiv.appendChild(document.createTextNode("Kursnname eingeben"));
            oldDiv.parentNode.replaceChild(newDiv, oldDiv);
            newDiv.setAttribute('id', 'error_kursname');
            newDiv.setAttribute('class', 'invalid-Feedback');
        } else {
            if (kursname.value.length < 4) {
                kursname.setCustomValidity('Kursnname muss mind. 4 Zeichen lang sein');
                var oldDiv = document.querySelector('#error_kursname');
                var newDiv = document.createElement('div');
                newDiv.appendChild(document.createTextNode("Kursnname muss mind. 4 Zeichen lang sein"));
                oldDiv.parentNode.replaceChild(newDiv, oldDiv);
                newDiv.setAttribute('id', 'error_kursname');
                newDiv.setAttribute('class', 'invalid-Feedback');
            } else {
                //falls neuer Kursname = alter Kursnname
                if (kursname.value == "<?php echo $gruppe_name ?>") {
                    kursname.setCustomValidity('');
                    document.getElementById("valid_kursname").setAttribute("hidden", "true");
                } else {
                    //Prüfen, ob Username bereits in DB. Liefert true falls ja
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "functions.php?kursname=" + kursname.value, true);
                    xhr.send();

                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            if (!xhr.responseText) {
                                kursname.setCustomValidity('');
                                document.getElementById("valid_kursname").removeAttribute("hidden");
                            } else {
                                kursname.setCustomValidity('Kursnname bereits vergeben');
                                var oldDiv = document.querySelector('#error_kursname');
                                var newDiv = document.createElement('div');
                                newDiv.appendChild(document.createTextNode("Kursnname bereits vergeben"));
                                oldDiv.parentNode.replaceChild(newDiv, oldDiv);
                                newDiv.setAttribute('id', 'error_kursname');
                                newDiv.setAttribute('class', 'invalid-Feedback');
                            }

                        }

                    };
                }
            }

        }
    };

    kuerzel.addEventListener("input", checkKuerzel);
    kursname.addEventListener("input", checkKursname);
</script>

<?php
generate_footer();
?>