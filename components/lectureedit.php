<?php
    session_start();
    if(!isset($_SESSION["dozent"])){
        header("Location:../index.php");
        die("Bitte melden Sie sich an");
    }
    require("functions.php");
    $vorlesung_id = $_GET['v_id'];

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
    }elseif(isset($_POST['save_question'])){        
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
    }elseif(isset($_POST['question_delete'])){
        $successDelete = true;
        $errorMsgDelete = "";

        $frage_id = $_POST['frage_id'];
        $sql = "DELETE FROM `vl_vorlesung_frage` WHERE frage_id = $frage_id";
        if (mysqli_query($conn, $sql)) {
            //nichts zu tun. $successDelete bereits true
        } else {
            $successDelete = false;
            $errorMsgDelete = mysqli_error($conn);
        }
    }elseif(isset($_POST['save_modified_question'])){
        $frage_id = $_GET['q_id'];
        $sql = "SELECT antwort from vl_vorlesung_frage_antwortmoeglichkeiten where frage_id = $frage_id";
        $result = mysqli_query($conn, $sql);
        $row_count = mysqli_num_rows($result);

        $sql = "SELECT frage_titel,frage_typ_id from vl_vorlesung_frage where frage_id = $frage_id";
        $current_frage = mysqli_fetch_assoc(mysqli_query($conn, $sql));
        $frage_titel = $current_frage['frage_titel'];
        $frage_typ = $current_frage['frage_typ_id'];
    
        $successModify = true;
        $errorMsgModify = "";
    
        $new_question_text = $_POST['newTitle'];
        //falls nicht gesetzt alter typ
        $question_type = $frage_typ;
    
    
        if(isset($_POST['question_type']) || $new_question_text != $frage_titel || count($_POST['question_option']) != $row_count){
            if(isset($_POST['question_type'])){
                //wenn im Formular geändert, neu belegen
                $question_type = $_POST['question_type'];
            }
            //alte frage deaktivieren
            $sql1 = "UPDATE vl_vorlesung_frage SET aktiv=false WHERE frage_id = $frage_id";
            mysqli_query($conn, $sql1);
            
            //neue frage einfügen
            $sql2 = "INSERT INTO `vl_vorlesung_frage`(`vorlesung_id`, `frage_titel`, `frage_typ_id`,`vorherige_version_id`) VALUES ($vorlesung_id,'$new_question_text',$question_type,$frage_id)";
            if (mysqli_query($conn, $sql2)) {
                if(isset($_POST['question_option'])){
                    $last_id = mysqli_insert_id($conn);
                    foreach($_POST['question_option'] as $option){                    
                        unset($sql3);
                        if(!empty($option)){
                            $sql3 = "INSERT INTO `vl_vorlesung_frage_antwortmoeglichkeiten`(`frage_id`, `antwort`) VALUES ($last_id,'$option')";
        
                            if (mysqli_query($conn, $sql3)) {
                                //nichts zu tun, 
                            } else {
                                $successModify = false;
                                $errorMsgModify = $errorMsgModify . mysqli_error($conn);
                            }
                        }                    
                    }            
                }
            } else {
                    $successModify = false;
                    $errorMsgModify = $errorMsgModify . mysqli_error($conn);
            }
    
        }else{
            //keine Änderungen
        }
    }
    mysqli_close($conn);

?>

<?php
    generate_header("Vorlesung bearbeiten", $vorlesung_name, $_SESSION['username'], '../');
?>

<div class="container-xl">
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
    <?php
        if(isset($successDelete)){
            if($successDelete){
                ?>
                <div class="alert alert-success" role="alert">
                    Frage ID <?= $frage_id ?> gelöscht.
                </div>
                <?php
            }else{
                ?>
                <div class="alert alert-danger" role="alert">
                    Frage nicht gelöscht - versuchen Sie es erneut.<br>
                    Fehler: <?= $errorMsgDelete?>
                </div>
                <?php
            }
        }
        ?>

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
                <th>Aktiv</th>
                <th>Vorherige Version ID</th>
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
                            <td><?= $row['aktiv']?></td>
                            <td><?= $row['vorherige_version_id']?></td>
                            <td><?= $row['fragenummer']?></td>
                        </tr>
                    <?php
                }
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
        <h3>Kurse</h3>
        <p>Hier Zuordung zu Kursen ergänzen</p> 
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