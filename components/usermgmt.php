<?php 
    require("functions.php");
    generate_header("Benutzerverwaltung", null, null, '../');
?>

<?php
    if(isset($_POST['user_create'])){
        $conn = sql_connect();        
        $name = $_POST['name'];
        $pw = md5($_POST['password1']);
        $sql = "INSERT INTO vl_benutzer(`benutzername`, `password`) VALUES ('$name', '$pw')";
        
        if (mysqli_query($conn, $sql)) {
            $successCreate = true;
            $last_id = mysqli_insert_id($conn);
        } else {
            $successCreate = false;
            $errorMsgCreate = mysqli_error($conn);
        }
        
        if($_POST['user_type'] == 'dozent'){
            $defaultDozentGroup = appConfig::$defaultDozentGroup;
            $sql1 = "INSERT INTO `vl_benutzer_gruppe_map`(`benutzer_id`, `gruppe_id`) values ($last_id, $defaultDozentGroup)";
        }else{
            $defaultStudentGroup = appConfig::$defaultStudentGroup;
            $sql1 = "INSERT INTO `vl_benutzer_gruppe_map`(`benutzer_id`, `gruppe_id`) values ($last_id, $defaultStudentGroup)";
        }

        if (mysqli_query($conn, $sql1)) {
            $successCreate = true;
            $last_id = mysqli_insert_id($conn);
        } else {
            $successCreate = false;
            $errorMsgCreate = mysqli_error($conn);
        }
        mysqli_close($conn);
    }elseif(isset($_POST['user_delete'])){
        $conn = sql_connect();        
        $benutzer_id = $_POST['benutzer_id'];
        
        $sql2 = "DELETE FROM `vl_benutzer_gruppe_map` WHERE benutzer_id = $benutzer_id";
        $sql3 = "DELETE FROM vl_benutzer where benutzer_id = $benutzer_id";

        if (mysqli_query($conn, $sql2) && mysqli_query($conn, $sql3)) {
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
                    Benutzer <?= $_POST['benutzer_id']?> erfolgreich gelöscht.
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
    <h3>Benutzer anlegen</h3>
        <?php
        if(isset($successCreate)){
            if($successCreate){
                ?>
                <div class="alert alert-success" role="alert">
                    Benutzer <?= $_POST['name']?> erfolgreich angelegt.
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
        <form action="usermgmt.php" method="post">
        <div  id="passwordAlert" style="visibility: hidden; height: 0;" role="alert">
                    Passwörter stimmen nicht überein
                </div>  
            <div class="input-group mb-3">                
                <input type="text" name="name" class="form-control" placeholder="Benutzername" size="20" />
                <input type="password" name="password1" id="password1" class="form-control" placeholder="Password" size="70" />
                <input type="password" name="password2" id="password2" class="form-control" placeholder="Password wiederholen" onkeyup="checkPassword()" size="70" />
                <select class="form-control" name="user_type">
                    <option selected value="student">Student</option>
                    <option value="dozent">Dozent</option>
                </select> 
                <div class="input-group-append">
                    <input type="submit" disabled class="form-control btn btn-primary" name="user_create" id="user_create" value="Benutzer anlegen">
                </div>
            </div>
        </form>

        <hr>
        <h3>Alle User</h3>
        <input type="text" id="searchInput" class="form-control" onkeyup="search()" placeholder="Nach ID oder Name suchen..">
        
        <table class="table table-hover" id="allUsers">
            <thead>
            <tr>
                <th>ID</th>
                <th>Benutzername</th>
                <th>Aktiviert</th>
                <th>Registriert am</th>
                <th>Zuletzt aktiv am</th>                
            </tr>
            </thead>
            <tbody>
                <?php
                $conn = sql_connect();
                $group_select = "select benutzer_id, benutzername, aktiv, datum_registriert, datum_letzterlogin from vl_benutzer";
                $result = mysqli_query($conn, $group_select);
                while ($row = mysqli_fetch_assoc($result)){
                    ?>
                    <tr><td><?= $row['benutzer_id']?></td><td><a href="useredit.php?id=<?= $row['benutzer_id']?>"><?= $row['benutzername']?></td><td><?= $row['aktiv']?></td><td><?= $row['datum_registriert']?></td><td><?= $row['datum_letzterlogin']?></td></tr>
                    <?php
                }
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
</div>


<script>

function checkPassword(){
    alertDiv = document.getElementById("passwordAlert");
    if(document.getElementById("password1").value != document.getElementById("password2").value){
        alertDiv.style.height = "";
        alertDiv.style.visibility = "visible";
        alertDiv.className = "alert alert-danger";
        document.getElementById("user_create").setAttribute("disabled", "true");
    }else{
        alertDiv.style.height = "0";
        alertDiv.style.visibility = "hidden";
        alertDiv.className = "";
        document.getElementById("user_create").removeAttribute("disabled");
    }
}


function search() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("searchInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("allUsers");
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