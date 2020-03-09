<?php 
    require("functions.php");
    generate_header("Kursverwaltung", "Löschen, Anlegen, Zuordnen", null, '../');
?>

<?php
   

    if(isset($_POST['group_create'])){
        $conn = sql_connect();        
        $kuerzel = $_POST['kuerzel'];
        $kursname = $_POST['kurs'];
        $sql = "INSERT INTO vl_gruppe(gruppe_kuerzel, gruppenname) VALUES ('$kuerzel', '$kursname')";

        if (mysqli_query($conn, $sql)) {
            $successCreate = true;
        } else {
            $successCreate = false;
            $errorMsgCreate = mysqli_error($conn);
        }
        mysqli_close($conn);
    }elseif(isset($_POST['group_delete'])){
        $conn = sql_connect();        
        $gruppe_id = $_POST['gruppe_id'];
        
        //ergänzen: delete from vl_benutzer_gruppe_map
        $sql = "DELETE FROM vl_gruppe where gruppe_id = $gruppe_id";

        if (mysqli_query($conn, $sql)) {
            $successDelete= true;
        } else {
            $successDelete = false;
            $errorMsgDelete = mysqli_error($conn);
        }
        mysqli_close($conn);
    }

    

?>
<div class="container">
    <?php
        if(isset($successDelete)){
            if($successDelete){
                ?>
                <div class="alert alert-success" role="alert">
                    Kurs <?= $_POST['gruppe_id']?> erfolgreich gelöscht.
                </div>
                <?php
            }else{
                ?>
                <div class="alert alert-danger" role="alert">
                    Löschen nicht erfolgreich - versuchen Sie es erneut.<br>
                    Fehler: <?= $errorMsgDelete?>
                </div>
                <?php
            }
        }
    ?>
    <h3>Kurs anlegen</h3>
        <?php
        if(isset($successCreate)){
            if($successCreate){
                ?>
                <div class="alert alert-success" role="alert">
                    Kurs <?= $_POST['kuerzel']?> erfolgreich angelegt.
                </div>
                <?php
            }else{
                ?>
                <div class="alert alert-danger" role="alert">
                    Anlage nicht erfolgreich - versuchen Sie es erneut.<br>
                    Fehler: <?= $errorMsgCreate?>
                </div>
                <?php
            }
        }
        ?>
        <form action="groupmgmt.php" method="post">
            <div class="input-group mb-3">                
                <input type="text" name="kuerzel" class="form-control" placeholder="Kürzel" size="20" />
                <input type="text" name="kurs" class="form-control" placeholder="Kursname" size="70" />
                <div class="input-group-append">
                    <input type="submit" class="form-control btn btn-primary" name="group_create" value="Kurs anlegen">
                </div>
            </div>
        </form>

        <hr>

        <h3>Alle Kurse</h3>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>ID</th>
                <th>Kürzel</th>
                <th>Name</th>
            </tr>
            </thead>
            <tbody>
                <?php
                $conn = sql_connect();
                $group_select = "SELECT * FROM vl_gruppe";
                $result = mysqli_query($conn, $group_select);
                while ($row = mysqli_fetch_assoc($result)){
                    ?>
                    <tr><td><?= $row['gruppe_id']?></td><td><a href="groupedit.php?id=<?= $row['gruppe_id']?>"><?= $row['gruppe_kuerzel']?></a></td><td><?= $row['gruppenname']?></td></tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
</div>



<?php
    generate_footer();
?>