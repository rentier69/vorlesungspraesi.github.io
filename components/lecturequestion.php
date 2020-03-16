<?php
    session_start();
    if(!isset($_SESSION["dozent"])){
        header("Location:../index.php");
        die("Bitte melden Sie sich an");
    }
    require("functions.php");
    $vorlesung_id = $_GET['v_id'];

    if($_GET['mode'] == 'create'){
        $vorlesung_name = $_POST['vorlesung_name'];
        generate_header("Frage hinzufügen", "Vorlesung: $vorlesung_name", $_SESSION['username'], '../');
    }elseif($_GET['mode'] == 'edit'){
        $frage_id = $_GET['q_id'];

        $conn = sql_connect();
        $frage_select = "SELECT * from vl_vorlesung_frage where frage_id = $frage_id";
        $current_frage = mysqli_fetch_assoc(mysqli_query($conn, $frage_select));
        $frage_titel = $current_frage['frage_titel'];
        $frage_typ = $current_frage['frage_typ_id'];
        generate_header("Frage bearbeiten", $frage_titel, $_SESSION['username'], '../');
    }
?>

<?php
?>

<div class="container-xl">
    <?php
    if($_GET['mode'] == 'create'){        
    ?>
        <form method="post" action="lectureedit.php?id=<?= $vorlesung_id ?>">
        <div id="saveQuestion" class="row">
            <div class="col-sm">
                <input disabled="true" type="submit" class="form-control btn btn-success" name="save_question" id="save_question" value="Speichern">
            </div>
            <div class="col-sm">
                <input type="reset" class="form-control btn btn-danger" onclick="reset_page()">
            </div>
            <div class="col-sm">
            <a href="lectureedit.php?id=<?= $vorlesung_id ?>" class="form-control btn btn-light" role="button">Schließen</a>
            </div>  
        </div>
        <label for="question_text">1. Schritt: Frage formulieren</label>
        <input type="text" class="form-control" name="question_text" id="question_text">
        
        <label for="question_type">2. Schritt: Fragentyp wählen</label>
        <select  id="question_type" name="question_type" class="form-control" onchange="typeSelected()">
            <option disabled selected value="bla">Fragentyp wählen</option>
            <?php
                $conn = sql_connect();
                $typ_select = "SELECT * FROM vl_vorlesung_frage_typ";
                $result = mysqli_query($conn, $typ_select);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo ('<option value="' . $row["frage_typ_id"] . '">' . $row["frage_typ_titel"] . '</option>');
                }
                mysqli_close($conn);
            ?>
        </select> 
        <small id="typeHelp" class="form-text text-muted">Hier Beschreibung des Fragentyps</small>
        <div id="setOptions" style="visibility: hidden; height: 0;">
            <label>3. Schritt: Anwortmöglichkeiten hinzufügen</label>
            <fieldset id="question_options">
                <input type="text" class="form-control" name="question_option[]" id="question_option">
            </fieldset>
            <a class="btn btn-primary text-white mt-1" onclick="question_option_add()">Weitere Antwortmöglichkeit</a>
            </div>
    </form>
    <?php
    }elseif($_GET['mode'] == 'edit'){
    ?>
        <div class="row">
            <div class="col-sm">
            <form action="lecturequestion.php?mode=edit&v_id=<?= $vorlesung_id ?>&q_id=<?= $frage_id?>" method="post">
                    <div class="input-group mb-3">
                        <input type="hidden" id="to_delete" name="to_delete" class="form-control" />
                        <input type="hidden" name="newTitleTarget" id="newTitleTarget" class="form-control" value="<?= $frage_titel ?>" />
                        <input type="submit" class="form-control btn btn-success" name="save" value="Änderungen speichern">
                    </div>
                </form>
            </div>
            <div class="col-sm">
                <form action="lectureedit.php?id=<?= $vorlesung_id ?>" method="post">
                    <div class="input-group mb-3">
                        <input type="hidden" name="gruppe_id" value="<?= $gruppe_id ?>" />
                        <input type="submit" class="form-control btn btn-light" name="question_delete" value="Frage Löschen">
                    </div>
                </form>
            </div>
            <div class="col-sm">
                <a href="lectureedit.php?id=<?= $vorlesung_id ?>" class="form-control btn btn-light">Schließen</a>
            </div>
        </div>
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
        <h3>Frage bearbeiten</h3>
        <input readonly type="text" name="newTitleSource" id="newTitleSource" class="form-control" placeholder="Frage" value="<?= $frage_titel ?>" onclick="enable('newTitleSource')" onkeyup="fillInput('newTitleSource','newTitleTarget')" />
        

        <?php
            if($frage_typ != 1){
                ?>
                <h3>Antwortoptionen bearbeiten</h3>
                <?php
                $conn = sql_connect();     
                $sql = "SELECT antwort from vl_vorlesung_frage_antwortmoeglichkeiten where frage_id = $frage_id";
                $result = mysqli_query($conn, $sql);
                $i = 1;
                while ($row = mysqli_fetch_assoc($result)){
                    $input_id = "question_option_" . $i;
                ?>
                    <input readonly type="text" name="question_option[]" id="<?= $input_id?>" class="form-control mt-1" placeholder="Anwortmöglichkeit" value="<?= $row['antwort'] ?>" onclick="enable('<?= $input_id ?>')"/>
                <?php
                $i++;
                }
                mysqli_close($conn);
                ?>

                <?php
            }
        ?>
        
    <?php
    }
    ?>
    
</div>

<script>
    function typeSelected(){
        document.getElementById('question_text').setAttribute('readonly', 'readonly');
        document.getElementById('question_type').setAttribute('readonly', 'readonly');
        
        if(document.getElementById('question_type').value == 2 || document.getElementById('question_type').value == 3){
            document.getElementById('setOptions').removeAttribute('style');
        }
        document.getElementById('save_question').removeAttribute('disabled');
    }

    function question_option_add(){
        var inp = document.createElement("input");
        inp.className = "form-control mt-1";
        inp.name = "question_option[]"
        inp.id = "question_option"
        inp.type = "text";
        
        document.getElementById('question_options').appendChild(inp);
        inp.focus();
    }
    function reset_page(){
        document.getElementById('question_text').removeAttribute('readonly');
        document.getElementById('question_type').removeAttribute('readonly');
        document.getElementById('setOptions').style.visibility = 'hidden';
        document.getElementById('setOptions').style.height = 0;
        document.getElementById('save_question').setAttribute('disabled', 'true');
        document.getElementById('question_options').innerHTML = '<input type="text" class="form-control" name="question_option[]" id="question_option">';
    }
    function fillInput($source, $target) {
        document.getElementById($target).value = document.getElementById($source).value;
    }

    function enable($div_id){
        document.getElementById($div_id).removeAttribute('readonly');
    }

</script>

<?php
    generate_footer();
?>