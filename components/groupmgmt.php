<?php 
    require("functions.php");
    generate_header("Kursverwaltung", "Löschen, Anlegen, Zuordnen", null, '../');
?>

<?php
   

    if(isset($_POST['group_create'])){
        $conn = sql_connect();        
        $kuerzel = $_POST['kuerzel'];
        $kursname = $_POST['kurs'];
        $sql = "INSERT INTO vl_Gruppe(Gruppe_Kuerzel, Gruppenname) VALUES ('$kuerzel', '$kursname')";

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
        
        
        $sql = "DELETE FROM vl_Gruppe WHERE Gruppe_ID = $gruppe_id";

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
                $group_select = "SELECT * FROM vl_Gruppe";
                $result = mysqli_query($conn, $group_select);
                while ($row = mysqli_fetch_assoc($result)){
                    ?>
                    <tr><td><?= $row['Gruppe_ID']?></td><td><a href="groupedit.php?id=<?= $row['Gruppe_ID']?>"><?= $row['Gruppe_Kuerzel']?></a></td><td><?= $row['Gruppenname']?></td></tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
</div>



<?php
    generate_footer();
?>