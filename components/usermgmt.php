<?php
session_start();
if (!isset($_SESSION["dozent"])) {
    header("Location:../index.php");
    die("Bitte melden Sie sich an");
}
require("functions.php");
generate_header("Benutzerverwaltung", null, $_SESSION['username'], '../');
?>

<?php
if (isset($_POST['user_create'])) {
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

    if ($_POST['user_type'] == 'dozent') {
        $defaultDozentGroup = appConfig::$defaultDozentGroup;
        $sql1 = "INSERT INTO `vl_benutzer_gruppe_map`(`benutzer_id`, `gruppe_id`) values ($last_id, $defaultDozentGroup)";
    } else {
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
} elseif (isset($_POST['user_delete'])) {
    $conn = sql_connect();
    $benutzer_id = $_POST['benutzer_id'];

    $sql3 = "DELETE FROM vl_benutzer where benutzer_id = $benutzer_id";

    if (mysqli_query($conn, $sql3)) {
        $successDelete = true;
    } else {
        $successDelete = false;
        $errorMsgDelete = mysqli_error($conn);
    }
    mysqli_close($conn);
}
?>
<div class="container-xl">
    <?php
    if (isset($successDelete)) {
        if ($successDelete) {
    ?>
            <div class="alert alert-success" role="alert">
                Benutzer <?= $_POST['benutzer_id'] ?> erfolgreich gelöscht.
            </div>
        <?php
        } else {
        ?>
            <div class="alert alert-danger" role="alert">
                Löschen nicht erfolgreich - versuchen Sie es erneut.<br>
                Fehler: <?= $errorMsgDelete ?>
            </div>
    <?php
        }
    }
    ?>
    <h3>Benutzer anlegen</h3>
    <?php
    if (isset($successCreate)) {
        if ($successCreate) {
    ?>
            <div class="alert alert-success" role="alert">
                Benutzer <?= $_POST['name'] ?> erfolgreich angelegt.
            </div>
        <?php
        } else {
        ?>
            <div class="alert alert-danger" role="alert">
                Anlage nicht erfolgreich - versuchen Sie es erneut.<br>
                Fehler: <?= $errorMsgCreate ?>
            </div>
    <?php
        }
    }
    ?>
    <form action="usermgmt.php" method="post" class="was-validated">
        <div class="input-group mb-3">
            <div class="form-group">
                <input type="text" name="name" id="username" class="form-control" placeholder="Benutzername" size="30" required />
                <div class="invalid-feedback" id="error_username" hidden>Benutzername eingeben</div>
                <div class="valid-feedback" id="valid_username"> Benutzername verfügbar </div>
            </div>
            <div class="form-group">
            <input type="password" name="password1" id="password1" class="form-control" placeholder="Passwort" size="25" required />
            <div class="invalid-Feedback" id="error_password1" hidden> Passwort eingeben</div>
            </div>
            <div class="form-group">
            <input type="password" name="password2" id="password2" class="form-control" placeholder="Passwort wiederholen" size="25" required />
            <div class="invalid-Feedback" id="error_password2" hidden> Passwörter müssen übereinstimmen</div>
            </div>
            <select class="form-control" name="user_type">
                <option selected value="student">Student</option>
                <option value="dozent">Dozent</option>
            </select>
            <div class="input-group-append">
                <div class="form-group">
                    <input type="submit" disabled class=" btn btn-primary btn-block" name="user_create" id="user_create" value="Benutzer anlegen">
                </div>
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
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <tr>
                    <td><?= $row['benutzer_id'] ?></td>
                    <td><a href="useredit.php?id=<?= $row['benutzer_id'] ?>"><?= $row['benutzername'] ?></td>
                    <td><?= $row['aktiv'] ?></td>
                    <td><?= $row['datum_registriert'] ?></td>
                    <td><?= $row['datum_letzterlogin'] ?></td>
                </tr>
            <?php
            }
            mysqli_close($conn);
            ?>
        </tbody>
    </table>
</div>


<script>
username = document.getElementById("username");
password= document.getElementById("password1");
passwordRepeat=document.getElementById("password2");

var checkForm = function() {

document.getElementById("error_password1").removeAttribute("hidden");
document.getElementById("error_password2").removeAttribute("hidden");


checkPassword();
checkUsername();
};

  
      var checkPassword = function() {
        if (password.value.length != 0) {
            if (password.value.length < 6) {
                password.setCustomValidity('Passwort muss mind. 6 Zeichen lang sein');
                var oldDiv = document.querySelector('#error_password1');
                var newDiv = document.createElement('div');
                newDiv.appendChild(document.createTextNode("Passwort muss mind. 6 Zeichen lang sein"));
                oldDiv.parentNode.replaceChild(newDiv, oldDiv);
                newDiv.setAttribute('id', 'error_password1');
                newDiv.setAttribute('class', 'invalid-Feedback');
            } else {
                if (password.value == passwordRepeat.value) {
                    password.setCustomValidity('');
                    passwordRepeat.setCustomValidity('');
                } else {
                    password.setCustomValidity('');
                    passwordRepeat.setCustomValidity('Passwörter müssen übereinstimmen');
                    var oldDiv = document.querySelector('#error_password2');
                    var newDiv = document.createElement('div');
                    newDiv.appendChild(document.createTextNode("Passwörter müssen übereinstimmen"));
                    oldDiv.parentNode.replaceChild(newDiv, oldDiv);
                    newDiv.setAttribute('id', 'error_password2');
                    newDiv.setAttribute('class', 'invalid-Feedback');
                }
            }
        } else {
            password.setCustomValidity('Passwort eingeben');
            var oldDiv = document.querySelector('#error_password1');
            var newDiv = document.createElement('div');
            newDiv.appendChild(document.createTextNode("Passwort eingeben"));
            oldDiv.parentNode.replaceChild(newDiv, oldDiv);
            newDiv.setAttribute('id', 'error_password1');
            newDiv.setAttribute('class', 'invalid-Feedback');
        }
    };


  

  

    var checkUsername = function() {
        changeSubmitButton();
        if (username.value == '') {
            username.setCustomValidity('Benutzername eingeben');
            var oldDiv = document.querySelector('#error_username');
            var newDiv = document.createElement('div');
            newDiv.appendChild(document.createTextNode("Benutzername eingeben"));
            oldDiv.parentNode.replaceChild(newDiv, oldDiv);
            newDiv.setAttribute('id', 'error_username');
            newDiv.setAttribute('class', 'invalid-Feedback');
        } else {
            if (username.value.length < 4) {
                username.setCustomValidity('Benutzername muss mind. 4 Zeichen lang sein');
                var oldDiv = document.querySelector('#error_username');
                var newDiv = document.createElement('div');
                newDiv.appendChild(document.createTextNode("Benutzername muss mind. 4 Zeichen lang sein"));
                oldDiv.parentNode.replaceChild(newDiv, oldDiv);
                newDiv.setAttribute('id', 'error_username');
                newDiv.setAttribute('class', 'invalid-Feedback');
            } else {
                //Prüfen, ob Username bereits in DB. Liefert true falls ja
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "functions.php?username=" + username.value, true);
                xhr.send();

                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        if (!xhr.responseText) {
                            username.setCustomValidity('');

                        } else {
                            username.setCustomValidity('Benutzername bereits vergeben');
                            var oldDiv = document.querySelector('#error_username');
                            var newDiv = document.createElement('div');
                            newDiv.appendChild(document.createTextNode("Benutzername bereits vergeben"));
                            oldDiv.parentNode.replaceChild(newDiv, oldDiv);
                            newDiv.setAttribute('id', 'error_username');
                            newDiv.setAttribute('class', 'invalid-Feedback');
                        }
                        changeSubmitButton();

                    }

                };
            }

        }
    };

    //wird in CheckUsername aufgerufen
    var changeSubmitButton = function() {
        if (document.querySelector(':invalid') === null) {
            document.getElementById('user_create').disabled = false;
        } else {
            document.getElementById('user_create').disabled = true;
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
            if (td0 || td1) {

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

    username.addEventListener("input", checkForm);
    password.addEventListener("input", checkForm);
    passwordRepeat.addEventListener("input", checkForm);
</script>
<?php
generate_footer();
?>