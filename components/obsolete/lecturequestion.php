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
<div class="container-xl">
    <?php
    if($_GET['mode'] == 'create'){        
    ?>
        <form method="post" action="lectureedit.php?v_id=<?= $vorlesung_id ?>">
        <div id="saveQuestion" class="row">
            <div class="col-sm">
                <input disabled="true" type="submit" class="form-control btn btn-success" name="save_question" id="save_question" value="Speichern">
            </div>
            <div class="col-sm">
                <input type="reset" class="form-control btn btn-danger" onclick="reset_page()">
            </div>
            <div class="col-sm">
            <a href="lectureedit.php?v_id=<?= $vorlesung_id ?>" class="form-control btn btn-light" role="button">Schließen</a>
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
            <a class="btn btn-primary text-white mt-1" onclick="question_option_add('question_options')">Weitere Antwortmöglichkeit</a>
        </div>
    </form>
    <?php
    }elseif($_GET['mode'] == 'edit'){
    ?>
        <div class="row">            
            <div class="col-sm order-2">
                <form action="lectureedit.php?v_id=<?= $vorlesung_id ?>" method="post">
                    <div class="input-group mb-3">
                        <input type="hidden" name="frage_id" value="<?= $frage_id ?>" />
                        <input type="submit" class="form-control btn btn-light" name="question_delete" value="Frage Löschen">
                    </div>
                </form>
            </div>
            <div class="col-sm order-3">
                <a href="lectureedit.php?id=<?= $vorlesung_id ?>" class="form-control btn btn-light">Schließen</a>
            </div>
            <div class="col-sm order-1">          
                    <div class="input-group mb-3">
                        <button class="form-control btn btn-success" name="save_modified_question" onclick="form_submit('editQuestion')">Änderungen speichern</button>
                    </div>
            </div>
        </div>
        <div class="alert alert-warning" role="alert">
        <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Achtung!</h4>
            <p>Änderungen an einer Frage führen zu einer neuen Version, damit bereits gespeicherte Antworten weiter zur korrekten Frage und den korrekten Antworten führen.</p>
            <hr>
            <p class="mb-0">Bei einer Änderung wird <b>nichts aus der Datenbank gelöscht.</b></p>
        </div>

        <form id="editQuestion" action="lectureedit.php?v_id=<?= $vorlesung_id ?>&q_id=<?= $frage_id?>" method="post">
        <!-- zur Identifikation der ausgeführten Funktion -->
        <input type="hidden" name="save_modified_question" value="Änderungen speichern">

        <h3>Frage bearbeiten</h3>
        <div class="input-group mb-3">
            <input readonly type="text" name="newTitle" id="newTitle" class="form-control" placeholder="Frage" value="<?= $frage_titel ?>"/>
            <div class="input-group-append">
                <button class="btn btn-secondary" type="button" onclick="enable('newTitle')"><i class="fas fa-edit"></i></button>
            </div>
        </div>
        <?php
            if($frage_typ != 1){
                ?>
                <h3>Fragentyp bearbeiten</h3>
                <div class="input-group mb-3">
                    <select disabled id="question_type" name="question_type" class="form-control">
                        <?php
                            if($frage_typ == 2){
                                ?>
                                <option selected value="2">Single Choice</option>
                                <option value="3">Multiple Choice</option>
                                <?php
                            }elseif($frage_typ == 3){
                                ?>
                                <option selected value="3">Multiple Choice</option>
                                <option value="2">Single Choice</option>
                                <?php
                            }
                        ?>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-secondary" type="button" onclick="enable('question_type')"><i class="fas fa-edit"></i></button>
                    </div>
                </div>

                <h3>Antwortoptionen bearbeiten</h3>
                <fieldset id="question_option">
                    <?php
                        $sql = "SELECT antwort from vl_vorlesung_frage_antwortmoeglichkeiten where frage_id = $frage_id";
                        $result = mysqli_query($conn, $sql);
                        $i = 1;
                        while ($row = mysqli_fetch_assoc($result)){
                            $input_id = "question_options_" . $i;
                    ?>                    
                        <div class="input-group mb-3">
                            <input readonly type="text" name="question_option[]" id="<?= $input_id?>" class="form-control" placeholder="Anwortmöglichkeit" value="<?= $row['antwort'] ?>"/>
                             <div class="input-group-append">
                                <button class="btn btn-secondary" type="button" onclick="enable('<?= $input_id ?>')"><i class="fas fa-edit"></i></button>
                            </div>
                        </div>
                    <?php
                        $i++;
                        }
                    ?>
                </fieldset>
                <a class="btn btn-primary text-white mt-1" onclick="question_option_add('question_option')"><i class="fas fa-plus"></i></a>
                <a class="btn btn-light mt-1" onclick="question_option_remove('question_option')"><i class="fas fa-minus"></i></a>
            </form>
                <?php
            }
        ?>
        
    <?php
        mysqli_close($conn);
        }
    ?>    
</div>

<script>
    function form_submit($form_id){
        document.getElementById($form_id).submit(); 
    }
    function typeSelected(){
        document.getElementById('question_text').setAttribute('readonly', 'readonly');
        document.getElementById('question_type').setAttribute('readonly', 'readonly');
        
        if(document.getElementById('question_type').value == 2 || document.getElementById('question_type').value == 3){
            document.getElementById('setOptions').removeAttribute('style');
        }
        document.getElementById('save_question').removeAttribute('disabled');
    }
    function question_option_add($div_id){
        var inp = document.createElement("input");
        inp.className = "form-control mt-1";
        inp.name = "question_option[]"
        inp.id = "question_options";
        inp.type = "text";        
        document.getElementById($div_id).appendChild(inp);
        inp.focus();

        console.log(document.getElementById("myForm").elements.length);
    }
    function question_option_remove($div_id){
        $div = document.getElementById($div_id);
        if($div.childNodes.length != 0){
            $div.removeChild($div.lastChild);
        }
        console.log(document.getElementById("myForm").elements.length);
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
        if(document.getElementById($div_id).hasAttribute('readonly')){
            document.getElementById($div_id).removeAttribute('readonly');
        }else if(document.getElementById($div_id).hasAttribute('disabled')){
            document.getElementById($div_id).removeAttribute('disabled');
        }
    }
</script>

<?php
    generate_footer();
?>