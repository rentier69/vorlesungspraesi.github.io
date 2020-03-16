<?php 
session_start();
if(!isset($_SESSION["dozent"])){
    header("Location:../index.php");
    die("Bitte melden Sie sich an");
}
    require("functions.php");
    generate_header("Vorlesungsverwaltung", null, $_SESSION['username'], '../');
?>

<?php
    if(isset($_POST['lecture_create'])){
        $conn = sql_connect();
        $sql = "INSERT INTO `vl_vorlesung`(`benutzer_id`, `vorlesung_name`) VALUES ((SELECT benutzer_id FROM vl_benutzer WHERE benutzername = '" . $_SESSION['username'] . "'),'" . $_POST['name'] . "')";

        if (mysqli_query($conn, $sql)) {
            $successCreate = true;
        } else {
            $successCreate = false;
            $errorMsgCreate = mysqli_error($conn);
        }
        mysqli_close($conn);
    }elseif(isset($_POST['lecture_delete'])){
        $conn = sql_connect();        
        $vorlesung_id = $_POST['vorlesung_id'];
        
        $sql = "DELETE FROM vl_vorlesung where vorlesung_id = $vorlesung_id";

        if (mysqli_query($conn, $sql)) {
            $successDelete= true;
        } else {
            $successDelete = false;
            $errorMsgDelete = mysqli_error($conn);
        }
        mysqli_close($conn);
    }
?>
<div class="container-xl">
    <?php
        if(isset($successDelete)){
            if($successDelete){
                ?>
                <div class="alert alert-success" role="alert">
                    Vorlesung <?= $_POST['vorlesung_id']?> erfolgreich gelöscht.
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
    <h3>Vorlesung anlegen</h3>
        <?php
        if(isset($successCreate)){
            if($successCreate){
                ?>
                <div class="alert alert-success" role="alert">
                    Vorlesung <?= $_POST['name']?> erfolgreich angelegt.
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
        <form action="lectures.php" method="post">
            <div class="input-group mb-3">
                <input type="text" name="name" class="form-control" placeholder="Name der Vorlesung"/>
                <div class="input-group-append">
                    <input type="submit" class="form-control btn btn-primary" name="lecture_create" value="Vorlesung anlegen">
                </div>
            </div>
        </form>

        <hr>

        <!-- ggfs. noch auf angemeldeten Dozenten einschränken -->
        <h3>Alle Vorlesungen</h3>
        <input type="text" id="searchInput" class="form-control" onkeyup="search()" placeholder="Nach ID oder Name suchen..">
        <table class="table table-hover" id="allGroups">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <!--<th>Benutzer</th>-->
            </tr>
            </thead>
            <tbody>
                <?php
                $conn = sql_connect();
                $group_select = "SELECT * FROM vl_vorlesung";
                $result = mysqli_query($conn, $group_select);
                while ($row = mysqli_fetch_assoc($result)){
                    ?>
                    <tr><td><?= $row['vorlesung_id']?></td><td><a href="lectureedit.php?id=<?= $row['vorlesung_id']?>"><?= $row['vorlesung_name']?></a></td></tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
</div>
<script>
function search() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("searchInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("allGroups");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    
    td0 = tr[i].getElementsByTagName("td")[0];
    td1 = tr[i].getElementsByTagName("td")[1];
    if (td0 ||td1) {
    
      txtValue0 = td0.textContent || td0.innerText;
      txtValue1 = td1.textContent || td1.innerText;
      if (txtValue0.toUpperCase().indexOf(filter) > -1 || txtValue1.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
</script>

<?php
    generate_footer();
?>