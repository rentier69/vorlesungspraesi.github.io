<?php
    session_start();
    if(!isset($_SESSION["dozent"])){
        header("Location:../index.php");
        die("Bitte melden Sie sich an");
    }
    require("functions.php");
    $vorlesung_id = $_GET['id'];

    $conn = sql_connect();
    $vorlesung_select = "SELECT * from vl_vorlesung where vorlesung_id = $vorlesung_id";
    $result = mysqli_fetch_assoc(mysqli_query($conn, $vorlesung_select));
    $vorlesung_name = $result['vorlesung_name'];
    
    if(isset($_POST['save'])){
        $errorMsgSave = "";
        $successSave = true;

        if($_POST['newNameTarget'] != $vorlesung_name){
            $name = $_POST['newNameTarget'];        
            $sql = "UPDATE `vl_vorlesung` SET `vorlesung_name`= '$name' WHERE vorlesung_id = $vorlesung_id";

            if (mysqli_query($conn, $sql)) {
                    //nichts zu tun. $successSave bereits true
                    $vorlesung_name = $name;
            } else {
                    $successSave = false;
                    $errorMsgSave = mysqli_error($conn);
            }
        }        
    }elseif(isset($_POST['save_question']))
    {        
        $successCreateQuestion = true;
        $errorMsgCreateQuestion = "";

        $successCreateQuestionOptions = true;
        $errorMsgCreateQuestionOptions = "";
        
        $question_text = $_POST['question_text'];
        $question_type = $_POST['question_type'];
        $question_options = $_POST['question_option'];

        $sql = "INSERT INTO `vl_vorlesung_frage`(`vorlesung_id`, `frage_titel`, `frage_typ_id`) VALUES ($vorlesung_id,'$question_text',$question_type)";

        if (mysqli_query($conn, $sql)) {
            if(isset($question_options)){
                $last_id = mysqli_insert_id($conn);
                foreach($question_options as $option){                    
                    unset($sql);
                    if(!empty($option)){
                        $sql = "INSERT INTO `vl_vorlesung_frage_antwortmoeglichkeiten`(`frage_id`, `antwort`) VALUES ($last_id,'$option')";
    
                        if (mysqli_query($conn, $sql)) {
                            //nichts zu tun, 
                        } else {
                            $successCreateQuestionOptions = false;
                            $errorMsgCreateQuestionOptions = mysqli_error($conn);
                        }
                    }                    
                }            
            }
        } else {
            $successCreateQuestion = false;
            $errorMsgCreateQuestion = mysqli_error($conn);
        }
    }
    mysqli_close($conn);

?>

<?php
    generate_header("Vorlesung bearbeiten", $vorlesung_name, $_SESSION['username'], '../');
?>

<div class="container-xl">

    <div class="row">
        <div class="col-sm">
            <form action="lectureedit.php?id=<?= $vorlesung_id?>" method="post">
                <div class="input-group mb-3">
                    <input type="hidden" name="newNameTarget" id="newNameTarget" class="form-control" value="<?= $vorlesung_name ?>" />
                    <input type="submit" class="form-control btn btn-success" name="save" value="Änderungen speichern">
                </div>
            </form>   
        </div>
        <div class="col-sm">
            <form action="lectures.php" method="post">
                <div class="input-group mb-3">
                    <input type="hidden" name="vorlesung_id" value="<?= $vorlesung_id ?>" />
                    <!-- Hier noch ein Hinweis, dass Fragen auch automatisch gelöscht wird -->
                    <input type="submit" class="form-control btn btn-light" name="lecture_delete" value="Vorlesung Löschen">                    
                </div>
            </form>
        </div>
        <div class="col-sm">            
            <a href="lectures.php" class="form-control btn btn-light">Schließen</a>
        </div>
    </div>

    <h3>Name ändern</h3>
    <?php
        if(isset($successCreateQuestion)){
            if($successCreateQuestion){
                ?>
                <div class="alert alert-success" role="alert">
                    Frage erstellt.
                </div>
                <?php
            }else{
                ?>
                <div class="alert alert-danger" role="alert">
                    Frage nicht gespeichert - versuchen Sie es erneut.<br>
                    Fehler: <?= $errorMsgCreateQuestion?>
                </div>
                <?php
            }
        }
        ?>
        <form action="lectureedit.php?id=<?= $vorlesung_id ?>" method="post">
            <div class="input-group mb-3">                
                <input type="text" name="newNameSource" id="newNameSource" class="form-control" placeholder="Kursname" value="<?= $vorlesung_name?>" onkeyup="fillInput('newNameSource','newNameTarget')" />
            </div>
        </form>

        <h3>Fragen</h3>
        <?php
        if(isset($successSave)){
            if($successSave){
                ?>
                <div class="alert alert-success" role="alert">
                    Änderungen gespeichert.
                </div>
                <?php
            }else{
                ?>
                <div class="alert alert-danger" role="alert">
                    Änderungen nicht gespeichert - versuchen Sie es erneut.<br>
                    Fehler: <?= $errorMsgSave?>
                </div>
                <?php
            }
        }
        ?>
        <form action="lecturequestion.php?mode=create&v_id=<?= $vorlesung_id ?>" method="post">
            <div class="input-group mb-3">
                <input type="hidden" name="vorlesung_name" value="<?= $vorlesung_name ?>" />
                <input type="submit" class="form-control btn btn-light" name="create_question" value="Neue Frage hinzufügen">                    
            </div>
        </form>
        <table class="table table-hover" id="questions">
            <thead>
            <tr>
                <th>ID</th>
                <th>Frage</th>
                <th>Typ</th>
                <th>Eigene Nummer</th>
            </tr>
            </thead>
            <tbody>
                <?php
                $conn = sql_connect();
                $vorlesung_select = "SELECT * from vl_vorlesung_frage where vorlesung_id = $vorlesung_id";
                $result = mysqli_query($conn, $vorlesung_select);
                while ($row = mysqli_fetch_assoc($result)){
                    ?>
                        <tr id="question_<?= $row['frage_id']?>">
                            <td><?= $row['frage_id']?></td>
                            <td><a href="lecturequestion.php?mode=edit&v_id=<?= $vorlesung_id ?>&q_id=<?= $row['frage_id']?>"><?= $row['frage_titel']?></td>
                            <td><?= $row['frage_typ_id']?></td>
                            <td><?= $row['fragenummer']?></td>
                        </tr>
                    <?php
                }
                mysqli_close($conn);
                ?>
            </tbody>
        </table>

</div>

<script>
function fillInput($source, $target){
    document.getElementById($target).value = document.getElementById($source).value;
}

function markDelete($id) {
    $idToRemove = ',' + $id + ';';
    if(document.getElementById("member_"+$id).hasAttribute("style")){
        document.getElementById("member_"+$id).removeAttribute("style");
        document.getElementById("to_delete").value = document.getElementById("to_delete").value.replace($idToRemove,"");
    }else{
        document.getElementById("member_"+$id).style.color = "lightgrey";
        document.getElementById("to_delete").value += $idToRemove;
    }
}

function markAdd($id) {
    $idToAdd = ',' + $id + ';';
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

