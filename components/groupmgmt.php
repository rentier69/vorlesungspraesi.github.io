<?php 
session_start();
if(!isset($_SESSION["dozent"])){
    header("Location:../index.php");
    die("Bitte melden Sie sich an");
}
    require("functions.php");
    generate_header("Kursverwaltung", null, $_SESSION['username'], '../');
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
<div class="container-xl">
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
        <input type="text" id="searchInput" class="form-control" onkeyup="search()" placeholder="Nach ID oder Kürzel suchen..">
        <table class="table table-hover" id="allGroups">
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
    td2 = tr[i].getElementsByTagName("td")[2];
    if (td0 ||td1 || td2) {
    
      txtValue0 = td0.textContent || td0.innerText;
      txtValue1 = td1.textContent || td1.innerText;
      txtValue2 = td2.textContent || td2.innerText;
      if (txtValue0.toUpperCase().indexOf(filter) > -1 || txtValue1.toUpperCase().indexOf(filter) > -1 || txtValue2.toUpperCase().indexOf(filter) > -1) {
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