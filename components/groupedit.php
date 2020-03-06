<?php 
    require("functions.php");

    $gruppe_id = $_GET['id'];
    $conn = sql_connect();
    $group_select = "SELECT * FROM vl_gruppe WHERE Gruppe_ID = $gruppe_id";
    $current_group = mysqli_fetch_assoc(mysqli_query($conn, $group_select));
    $gruppe_name = $current_group['Gruppenname'];
    $gruppe_kurzel = $current_group['Gruppe_Kuerzel'];
    mysqli_close($conn);

    generate_header("Kurs bearbeiten", $gruppe_name, null, '../');
?>
<?php
if(isset($_POST['group_add_remove'])){
        $conn = sql_connect();
        
        $toDelete = explode(";", $_POST['to_delete']);
        $toAdd = explode(";", $_POST['to_add']);

        $group = $_GET['id'];

        foreach ($toDelete as $value){
            $sql = "DELETE FROM `vl_benutzer_gruppe_map` WHERE Benutzer_ID = $value AND Gruppe_ID = $group";
            mysqli_query($conn, $sql);
        }
        foreach ($toAdd as $value){
            $sql = "INSERT INTO `vl_benutzer_gruppe_map`(`Benutzer_ID`, `Gruppe_ID`) VALUES ($value,$group)";
            mysqli_query($conn, $sql);
        }
        mysqli_close($conn);
    }elseif(isset($_POST['group_edit_details'])){
        $conn = sql_connect();
        $kuerzel = $_POST['kuerzel'];
        $kursname = $_POST['kurs'];        
        $sql = "UPDATE `vl_gruppe` SET `Gruppe_Kuerzel`='$kuerzel',`Gruppenname`= '$kursname' WHERE `Gruppe_ID` = $gruppe_id";

        if (mysqli_query($conn, $sql)) {
            $successModify = true;
        } else {
            $successModify = false;
            $errorMsgModify = mysqli_error($conn);
        }
        mysqli_close($conn);
    }
?>

<div class="container">
    <h3>Name ändern</h3>
    <?php
        if(isset($successModify)){
            if($successModify){
                ?>
                <div class="alert alert-success" role="alert">
                    Änderungen gespeichert.
                </div>
                <?php
            }else{
                ?>
                <div class="alert alert-danger" role="alert">
                    Änderungen nicht gespeichert - versuchen Sie es erneut.<br>
                    Fehler: <?= $errorMsgModify?>
                </div>
                <?php
            }
        }
        ?>
        <form action="groupedit.php?id=<?= $gruppe_id ?>" method="post">
            <div class="input-group mb-3">                
                <input type="text" name="kuerzel" class="form-control" placeholder="Kürzel" value="<?= $gruppe_kurzel ?>" size="20" />
                <input type="text" name="kurs" class="form-control" placeholder="Kursname" value="<?= $gruppe_name?>"size="70" />
                <div class="input-group-append">
                    <input type="submit" class="form-control btn btn-success" name="group_edit_details" value="Änderungen speichern">
                </div>
            </div>
        </form>
    <h3>Mitglieder entfernen</h3> 
        <table class="table table-hover" id="members">
            <thead>
            <tr>
                <th>ID</th>
                <th>Benutzername</th>
                <th>Registriert am</th>
                <th>Zuletzt aktiv am</th>
                <th>Aus Gruppe entfernen</th>
            </tr>
            </thead>
            <tbody>
                <?php
                $conn = sql_connect();
                //$group_select = "SELECT Benutzer_ID, Benutzername, Datum_Registriert, Datum_LetzterLogin FROM vl_benutzer";
                $group_select = "SELECT Benutzer_ID, Benutzername, Datum_Registriert, Datum_LetzterLogin FROM vl_benutzer WHERE Benutzer_ID IN (SELECT Benutzer_ID FROM vl_benutzer_gruppe_map where Gruppe_ID = $gruppe_id)";
                $result = mysqli_query($conn, $group_select);
                while ($row = mysqli_fetch_assoc($result)){
                    ?>
                    <tr id="member_<?= $row['Benutzer_ID']?>"><td><?= $row['Benutzer_ID']?></td><td><?= $row['Benutzername']?></td><td><?= $row['Datum_Registriert']?></td><td><?= $row['Datum_LetzterLogin']?></td><td><i class="fas fa-user-minus" onclick="markDelete(<?= $row['Benutzer_ID']?>)"></i></td></tr>
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
                <th>Registriert am</th>
                <th>Zuletzt aktiv am</th>
                <th>Zu Gruppe hinzufügen</th>
            </tr>
            </thead>
            <tbody>
                <?php
                $conn = sql_connect();
                //$group_select = "SELECT Benutzer_ID, Benutzername, Datum_Registriert, Datum_LetzterLogin FROM vl_benutzer";
                $group_select = "SELECT Benutzer_ID, Benutzername, Datum_Registriert, Datum_LetzterLogin FROM vl_benutzer WHERE Benutzer_ID NOT IN (SELECT Benutzer_ID FROM vl_benutzer_gruppe_map where Gruppe_ID = $gruppe_id)";
                $result = mysqli_query($conn, $group_select);
                while ($row = mysqli_fetch_assoc($result)){
                    ?>
                    <tr id="noMember_<?= $row['Benutzer_ID']?>"><td><?= $row['Benutzer_ID']?></td><td><?= $row['Benutzername']?></td><td><?= $row['Datum_Registriert']?></td><td><?= $row['Datum_LetzterLogin']?></td><td ><i class="fas fa-user-plus" onclick="markAdd(<?= $row['Benutzer_ID']?>)"></i></td></tr>
                    <?php
                }
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
        <form action="groupedit.php?id=<?= $gruppe_id?>" method="post">
            <div class="input-group mb-3">                
                <input type="hidden" id="to_delete" name="to_delete" class="form-control" size="20" />
                <input type="hidden" id="to_add" name="to_add" class="form-control" placeholder="Kursname" size="70" />
                <div class="input-group-append">
                    <input type="submit" class="form-control btn btn-success" name="group_add_remove" value="Speichern">
                    <a href="groupmgmt.php" class="form-control btn btn-danger">Abbrechen</a>
                </div>
            </div>
        </form>

        <hr>
        <h3>Gruppe Löschen</h3>
        <form action="groupmgmt.php" method="post">
            <div class="input-group mb-3">
                <input type="hidden" name="gruppe_id" value="<?= $gruppe_id ?>" />
                <input type="submit" class="form-control btn btn-danger" name="group_delete" value="Gruppe Löschen">                    
            </div>
        </form>
</div>

<script>

function markDelete($id) {
    $idToRemove = $id + ';';
    if(document.getElementById("member_"+$id).hasAttribute("style")){
        document.getElementById("member_"+$id).removeAttribute("style");
        document.getElementById("to_delete").value = document.getElementById("to_delete").value.replace($idToRemove,"");
    }else{
        document.getElementById("member_"+$id).style.color = "lightgrey";
        document.getElementById("to_delete").value += $idToRemove;
    }
}

function markAdd($id) {
    $idToAdd = $id + ';';
    if(document.getElementById("noMember_"+$id).hasAttribute("style")){
        document.getElementById("noMember_"+$id).removeAttribute("style");
        document.getElementById("to_add").value = document.getElementById("to_add").value.replace($idToAdd,"");
    }else{
        document.getElementById("noMember_"+$id).style.background = "lightgrey";
        document.getElementById("to_add").value += $idToAdd;
    }
}
</script>

<?php
    generate_footer();
?>
