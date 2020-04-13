<?php
session_start();
?>

<?php
if (isset($_SESSION['username'])) {
    if (isset($_SESSION["dozent"])) {
        if ($_SESSION["dozent"]) {
            //hier weitermachen
        } else {
            echo "Authentifizierung fehlgeschlagen";
            exit();
        }
    } else {
        echo "Authentifizierung fehlgeschlagen";
        exit();
    }
} else {
    echo "Authentifizierung fehlgeschlagen";
    exit();
}
?>

<?php
    require('functions.php');
    header('Content-Type: application/json; charset=utf-8');

    if(isset($_GET["mode"])){
            switch ($_GET["mode"]) {
            case 'lectures':
                lectures();
                break;
            case 'lecturequestion':
                lecturequestion();
                break;
            case 'users':
                users();
                break;
            case 'groups':
                groups();
                break;            
            default:
                die("Kein gültiger Modus gesetzt!");
                break;
        }
    }else{
        die("Kein Modus gesetzt!");
    }

    function lectures(){
        $conn = sql_connect();

        if (isset($_GET["action"])) {
            if($_GET["action"] == "getAll"){
                $resultArr = array();
                $lecture_select = "select vorlesung_id, vorlesung_name from vl_vorlesung";
                $result = mysqli_query($conn, $lecture_select);
                while ($row = mysqli_fetch_assoc($result)) {
                    $resultArr[] = $row;
                }
                echo json_encode($resultArr);
            }elseif($_GET["action"] == "getById"){
                $id = $_GET["v_id"];
                $select = "SELECT benutzer_id, vorlesung_name from vl_vorlesung where vorlesung_id = $id";
                $lecture = mysqli_fetch_assoc(mysqli_query($conn, $select));
                echo json_encode($lecture);                            
            }elseif($_GET["action"] == "getQuestions"){
                $id = $_GET["v_id"];
                //$question_select = "SELECT * from vl_vorlesung_frage where vorlesung_id = $id";
                $question_select = "SELECT frage_id, frage_titel, frage_typ_titel, aktiv, vorherige_version_id, fragenummer FROM vl_vorlesung_frage INNER JOIN vl_vorlesung_frage_typ ON (vl_vorlesung_frage.frage_typ_id = vl_vorlesung_frage_typ.frage_typ_id) WHERE vorlesung_id = $id order by fragenummer";
                $result = mysqli_query($conn, $question_select);
                $resultArr = array();
                while ($row = mysqli_fetch_assoc($result)){
                    $resultArr[] = $row;
                }
                echo json_encode($resultArr);                            
            }elseif($_GET["action"] == "getAssignedGroups"){
                $id = $_GET["v_id"];
                $resultArr = array();
                $select_assignedTo = "SELECT gruppe_id, gruppe_kuerzel, gruppenname, 1 AS assignedTo from vl_gruppe where gruppe_id in (select gruppe_id from vl_vorlesung_gruppe_map where vorlesung_id = $id)";
                $select_notAssignedTo = "SELECT gruppe_id, gruppe_kuerzel, gruppenname, 0 AS assignedTo from vl_gruppe where gruppe_id not in (select gruppe_id from vl_vorlesung_gruppe_map where vorlesung_id = $id)";
                $result_assignedTo = mysqli_query($conn, $select_assignedTo);
                while ($row = mysqli_fetch_assoc($result_assignedTo)) {
                    $resultArr[] = $row;
                }
                $result_notAssignedTo = mysqli_query($conn, $select_notAssignedTo);
                while ($row = mysqli_fetch_assoc($result_notAssignedTo)) {
                    $resultArr[] = $row;
                }
                echo json_encode($resultArr);
            }elseif ($_GET["action"] == "assignToGroup") {
                $v_id = $_POST["v_id"];
                $g_id = $_POST["g_id"];
                $insert = "INSERT INTO `vl_vorlesung_gruppe_map`(`vorlesung_id`, `gruppe_id`) values ($v_id,$g_id)";
                if (mysqli_query($conn, $insert)) {
                    echo "true";
                }else {
                    echo "false";
                }
            }elseif ($_GET["action"] == "unassignFromGroup") {
                $v_id = $_POST["v_id"];
                $g_id = $_POST["g_id"];
                $delete = "DELETE FROM `vl_vorlesung_gruppe_map` where vorlesung_id = $v_id and gruppe_id = $g_id";
                if (mysqli_query($conn, $delete)) {
                    echo "true";
                }else {
                    echo "false";
                }                
            }elseif ($_GET["action"] == "rename") {
                $name = $_POST["name"];
                $id = $_POST["id"];
                $update = "UPDATE `vl_vorlesung` set `vorlesung_name`='$name' where `vorlesung_id` = $id";

                if (mysqli_query($conn, $update)) {
                    echo "true";
                } else {
                    echo "false" . mysqli_error($conn);
                }
            }elseif ($_GET["action"] == "delete") {
                $id = $_POST["id"];
                $delete = "DELETE FROM vl_vorlesung where vorlesung_id = $id";

                if (mysqli_query($conn, $delete)) {
                    echo "true";
                } else {
                    echo "false" . mysqli_error($conn);
                }
            }elseif($_GET["action"] == "create"){
                // mit befüllung der benutzer_id
                //$sql = "INSERT INTO `vl_vorlesung`(`benutzer_id`, `vorlesung_name`) VALUES ((SELECT benutzer_id FROM vl_benutzer WHERE benutzername = '" . $_SESSION['username'] . "'),'" . $_POST['name'] . "')";
                
                $insert = "INSERT INTO `vl_vorlesung`(`vorlesung_name`) VALUES ('" . $_POST['name'] . "')";
                if (mysqli_query($conn, $insert)) {
                    $last_id = mysqli_insert_id($conn);
                    $result_select = "SELECT * from vl_vorlesung where vorlesung_id = $last_id";
                    $created_lecture = mysqli_fetch_assoc(mysqli_query($conn, $result_select));
                    echo json_encode($created_lecture);
                } else {
                    echo ("false " . mysqli_error($conn));
                }                
            }
        }
        mysqli_close($conn);
    }

    function lecturequestion(){
        $conn = sql_connect();

        if (isset($_GET["action"])) {
            if($_GET["action"] == "getQuestionTypes"){
                $resultArr = array();
                $typ_select = "SELECT * FROM vl_vorlesung_frage_typ";
                mysqli_set_charset($conn, "utf8");
                $result = mysqli_query($conn, $typ_select);
                while ($row = mysqli_fetch_assoc($result)) {
                    $resultArr[] = $row;
                }
                echo json_encode($resultArr);
            }elseif ($_GET["action"] == "getById") {
                $id = $_GET["q_id"];
                $select = "SELECT * from vl_vorlesung_frage where frage_id = $id";
                $question = mysqli_fetch_assoc(mysqli_query($conn, $select));
                echo json_encode($question);
            }elseif ($_GET["action"] == "setRank") {
                $q_id = $_POST["q_id"];
                $rank = $_POST["rank"];
                $update = "UPDATE vl_vorlesung_frage SET fragenummer=$rank WHERE frage_id = $q_id";
                if (mysqli_query($conn, $update)) {
                    //erfolg
                } else {
                    echo mysqli_error($conn);
                }
            }elseif ($_GET["action"] == "getAllAnswerOptionsByQId") {
                $id = $_GET["q_id"];
                $resultArr = array();
                $select = "SELECT antwort from vl_vorlesung_frage_antwortmoeglichkeiten where frage_id = $id";
                $result = mysqli_query($conn, $select);
                while ($row = mysqli_fetch_assoc($result)) {
                    $resultArr[] = $row['antwort'];
                }
                echo json_encode($resultArr);
            }elseif($_GET["action"] == "hasGivenAnswer"){
                $id = $_GET["q_id"];
                $select = "SELECT * from vl_vorlesung_frage_antworten where frage_id = $id";
                $result = mysqli_query($conn, $select);
                $row_cnt = mysqli_num_rows($result);
                if($row_cnt == 0){
                    echo "false";
                }elseif($row_cnt > 0){
                    echo "true";
                }
            }elseif($_GET["action"] == "delete"){
                $id = $_POST["q_id"];
                $sql = "DELETE FROM `vl_vorlesung_frage` WHERE frage_id = $id";
                if (mysqli_query($conn, $sql)) {
                    echo "true";
                } else {
                    echo mysqli_error($conn);
                }
            }elseif($_GET["action"] == "create"){
                $v_id = $_POST['v_id'];
                $question_text = $_POST['question_text'];
                $question_type = $_POST['question_type'];

                //bei Freitextfragen auf jeden Fall leer
                if(isset($_POST['question_option'])){
                    $question_options = $_POST['question_option'];
                }                

                $sql = "INSERT INTO `vl_vorlesung_frage`(`vorlesung_id`, `frage_titel`, `frage_typ_id`) VALUES ($v_id,'$question_text',$question_type)";
                if (mysqli_query($conn, $sql)) {
                    $last_id = mysqli_insert_id($conn);
                    if(isset($question_options)){                        
                        foreach($question_options as $option){                    
                            unset($sql);
                            if(!empty($option)){
                                $sql = "INSERT INTO `vl_vorlesung_frage_antwortmoeglichkeiten`(`frage_id`, `antwort`) VALUES ($last_id,'$option')";
                                if (mysqli_query($conn, $sql)) {
                                    //nichts zu tun, 
                                } else {
                                    echo mysqli_error($conn);
                                }
                            }                    
                        }            
                    }
                    $result_select = "SELECT * from vl_vorlesung_frage where frage_id = $last_id";
                    $created_question = mysqli_fetch_assoc(mysqli_query($conn, $result_select));
                    echo json_encode($created_question);
                } else {
                    echo mysqli_error($conn);
                }
            }elseif($_GET["action"] == "createNewVersion"){
                $q_id = $_POST['q_id'];
                $v_id = $_POST['v_id'];
                $question_text = $_POST['question_text'];
                $question_type = $_POST['question_type'];

                //bei Freitextfragen auf jeden Fall leer
                if(isset($_POST['question_option'])){
                    $question_options = $_POST['question_option'];
                }
                
                $successNewVersion = true;

                //alte frage deaktivieren
                $sql1 = "UPDATE vl_vorlesung_frage SET aktiv=false WHERE frage_id = $q_id";
                
                if (mysqli_query($conn, $sql1)) {
                    //erfolg
                } else {
                    $successNewVersion = false;
                }

                if($successNewVersion){
                    //neue frage einfügen
                    $sql2 = "INSERT INTO `vl_vorlesung_frage`(`vorlesung_id`, `frage_titel`, `frage_typ_id`,`vorherige_version_id`) VALUES ($v_id,'$question_text',$question_type,$q_id)";
                    if (mysqli_query($conn, $sql2)) {
                        $last_id = mysqli_insert_id($conn);
                        if(isset($question_options)){
                            foreach($question_options as $option){                    
                                unset($sql3);
                                if(!empty($option)){
                                    $sql3 = "INSERT INTO `vl_vorlesung_frage_antwortmoeglichkeiten`(`frage_id`, `antwort`) VALUES ($last_id,'$option')";
                                    if (mysqli_query($conn, $sql3)) {
                                        //erfolg
                                    } else {
                                        $successNewVersion = false;
                                    }
                                }                    
                            }
                        }
                        $result_select = "SELECT * from vl_vorlesung_frage where frage_id = $last_id";
                        $created_question = mysqli_fetch_assoc(mysqli_query($conn, $result_select));
                        echo json_encode($created_question);                      
                    } else {
                        $successNewVersion = false;
                    }
                }                            
            }elseif($_GET["action"] == "modifyExistingVersion"){
                $q_id = $_POST['q_id'];
                $v_id = $_POST['v_id'];
                $question_text = $_POST['question_text'];
                $question_type = $_POST['question_type'];

                //bei Freitextfragen auf jeden Fall leer
                if(isset($_POST['question_option'])){
                    $question_options = $_POST['question_option'];
                }

                if(isset($_POST['question_rank'])){
                    $question_rank = $_POST['question_rank'];
                }else{
                    $question_rank = "NULL";
                }
                
                $successModify = true;

                //alte frage löschen - antwortmöglichkeiten werden mit delete cascade mitgelöscht
                $sql1 = "DELETE FROM vl_vorlesung_frage WHERE frage_id = $q_id";
                
                if (mysqli_query($conn, $sql1)) {
                    //erfolg
                } else {
                    $successModify = false;
                }

                if($successModify){
                    //neue frage mit gleicher id einfügen 
                    $sql2 = "INSERT INTO `vl_vorlesung_frage`(`frage_id`,`vorlesung_id`, `frage_titel`, `frage_typ_id`, `fragenummer`) VALUES ($q_id, $v_id,'$question_text',$question_type,$question_rank)";
                    if (mysqli_query($conn, $sql2)) {
                        if(isset($question_options)){
                            foreach($question_options as $option){                    
                                unset($sql3);
                                if(!empty($option)){
                                    $sql3 = "INSERT INTO `vl_vorlesung_frage_antwortmoeglichkeiten`(`frage_id`, `antwort`) VALUES ($q_id,'$option')";
                                    if (mysqli_query($conn, $sql3)) {
                                        //erfolg
                                    } else {
                                        $successNewVersion = false;
                                    }
                                }                    
                            }
                        }
                        $result_select = "SELECT * from vl_vorlesung_frage where frage_id = $q_id";
                        $created_question = mysqli_fetch_assoc(mysqli_query($conn, $result_select));
                        echo json_encode($created_question);                       
                    } else {
                        $successNewVersion = false;
                    }
                }
            }
        }
        mysqli_close($conn);
    }

    function users(){
        $conn = sql_connect();

        if (isset($_GET["action"])) {
            if($_GET["action"] == "getAll"){
                $resultArr = array();
                $user_select = "select benutzer_id, benutzername, aktiv, datum_registriert, datum_letzterlogin from vl_benutzer";
                $result = mysqli_query($conn, $user_select);
                while ($row = mysqli_fetch_assoc($result)) {
                    $resultArr[] = $row;
                }
                echo json_encode($resultArr);
            }elseif ($_GET["action"] == "getById") {
                $id = $_GET["u_id"];
                $select = "SELECT benutzer_id, benutzername, aktiv from vl_benutzer where benutzer_id = $id";
                $user = mysqli_fetch_assoc(mysqli_query($conn, $select));
                echo json_encode($user);
            }elseif ($_GET["action"] == "addToGroup") {
                $u_id = $_POST["u_id"];
                $g_id = $_POST["g_id"];
                $insert = "INSERT INTO `vl_benutzer_gruppe_map`(`benutzer_id`, `gruppe_id`) values ($u_id,$g_id)";
                if (mysqli_query($conn, $insert)) {
                    echo "true";
                }else {
                    echo "false";
                }
            }elseif ($_GET["action"] == "removeFromGroup") {
                $u_id = $_POST["u_id"];
                $g_id = $_POST["g_id"];
                $delete = "DELETE FROM `vl_benutzer_gruppe_map` where gruppe_id = $g_id and benutzer_id = $u_id";
                if (mysqli_query($conn, $delete)) {
                    echo "true";
                }else {
                    echo "false";
                }                
            }elseif ($_GET["action"] == "getGroupMembership") {
                $id = $_GET["u_id"];
                $resultArr = array();
                $select_memberof = "SELECT gruppe_id, gruppe_kuerzel, gruppenname, 1 AS memberOf from vl_gruppe where gruppe_id in (select gruppe_id from vl_benutzer_gruppe_map where benutzer_id = $id)";
                $select_notmemberof = "SELECT gruppe_id, gruppe_kuerzel, gruppenname, 0 AS memberOf from vl_gruppe where gruppe_id not in (select gruppe_id from vl_benutzer_gruppe_map where benutzer_id = $id)";
                $result_memberof = mysqli_query($conn, $select_memberof);
                while ($row = mysqli_fetch_assoc($result_memberof)) {
                    $resultArr[] = $row;
                }
                $result_notmemberof = mysqli_query($conn, $select_notmemberof);
                while ($row = mysqli_fetch_assoc($result_notmemberof)) {
                    $resultArr[] = $row;
                }

                echo json_encode($resultArr);
            }elseif ($_GET["action"] == "rename") {
                $name = $_POST["name"];
                $id = $_POST["id"];
                $update = "UPDATE `vl_benutzer` set `benutzername`='$name' where `benutzer_id` = $id";

                if (mysqli_query($conn, $update)) {
                    echo "true";
                } else {
                    echo "false" . mysqli_error($conn);
                }
            }elseif ($_GET["action"] == "activate") {
                $id = $_POST["id"];
                $update = "UPDATE `vl_benutzer` set `aktiv`= 1 where `benutzer_id` = $id";

                if (mysqli_query($conn, $update)) {
                    echo "true";
                } else {
                    echo "false" . mysqli_error($conn);
                }
            }elseif ($_GET["action"] == "deactivate") {
                $id = $_POST["id"];
                $update = "UPDATE `vl_benutzer` set `aktiv`= 0 where `benutzer_id` = $id";

                if (mysqli_query($conn, $update)) {
                    echo "true";
                } else {
                    echo "false" . mysqli_error($conn);
                }
            }elseif ($_GET["action"] == "resetPw") {
                $id = $_POST["id"];
                $pw = $_POST["pw"];

                $update = "UPDATE vl_benutzer SET password='" . md5($pw) . "' WHERE benutzer_id = $id;";
                if (mysqli_query($conn, $update)) {
                    echo "true";
                } else {
                    echo "false" . mysqli_error($conn);
                }
            }elseif ($_GET["action"] == "create") {
                $name = $_POST['username'];
                $pw = md5($_POST['pw']);

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
                } else {
                    $successCreate = false;
                    $errorMsgCreate = mysqli_error($conn);
                }

                if($successCreate){
                    $result_select = "SELECT * from vl_benutzer where benutzer_id = $last_id";
                    $created_user = mysqli_fetch_assoc(mysqli_query($conn, $result_select));
                    echo json_encode($created_user);
                }
            }elseif ($_GET["action"] == "delete") {
                $id = $_POST["id"];

                $sql3 = "DELETE FROM vl_benutzer where benutzer_id = $id";

                if (mysqli_query($conn, $sql3)) {
                    echo "true";
                } else {
                    echo "false" . mysqli_error($conn);
                }
            }                
        }        
        mysqli_close($conn);
    }

    function groups(){
        $conn = sql_connect();

        if (isset($_GET["action"])) {
            if($_GET["action"] == "getAll"){
                $resultArr = array();
                $select = "select * from vl_gruppe";
                $result = mysqli_query($conn, $select);
                while ($row = mysqli_fetch_assoc($result)) {
                    $resultArr[] = $row;
                }
                echo json_encode($resultArr);
            }elseif ($_GET["action"] == "getById") {
                $id = $_GET["g_id"];
                $select = "SELECT * from vl_gruppe where gruppe_id = $id";
                $group = mysqli_fetch_assoc(mysqli_query($conn, $select));
                echo json_encode($group);
            }elseif ($_GET["action"] == "addToGroup") {
                $u_id = $_POST["u_id"];
                $g_id = $_POST["g_id"];
                $insert = "INSERT INTO `vl_benutzer_gruppe_map`(`benutzer_id`, `gruppe_id`) values ($u_id,$g_id)";
                if (mysqli_query($conn, $insert)) {
                    //erfolg
                }else {
                    echo mysqli_error($conn);
                }
            }elseif ($_GET["action"] == "removeFromGroup") {
                $u_id = $_POST["u_id"];
                $g_id = $_POST["g_id"];
                $delete = "DELETE FROM `vl_benutzer_gruppe_map` where gruppe_id = $g_id and benutzer_id = $u_id";
                if (mysqli_query($conn, $delete)) {
                    //erfolg
                }else {
                    echo mysqli_error($conn);
                }                
            }elseif ($_GET["action"] == "getGroupMembership") {
                $id = $_GET["g_id"];
                $resultArr = array();
                $select_members = "SELECT benutzer_id, benutzername, aktiv, datum_registriert, datum_letzterlogin, 1 AS memberOf from vl_benutzer where benutzer_id in (select benutzer_id from vl_benutzer_gruppe_map where gruppe_id = $id)";
                $select_notmembers = "SELECT benutzer_id, benutzername, aktiv, datum_registriert, datum_letzterlogin, 0 AS memberOf from vl_benutzer where benutzer_id not in (select benutzer_id from vl_benutzer_gruppe_map where gruppe_id = $id)";
                $result_members = mysqli_query($conn, $select_members);
                while ($row = mysqli_fetch_assoc($result_members)) {
                    $resultArr[] = $row;
                }
                $result_notmembers = mysqli_query($conn, $select_notmembers);
                while ($row = mysqli_fetch_assoc($result_notmembers)) {
                    $resultArr[] = $row;
                }
    
                echo json_encode($resultArr);
            }elseif ($_GET["action"] == "rename") {
                $id = $_POST["g_id"];
                $kuerzel = $_POST["kuerzel"];
                $name = $_POST["name"];
                $update = "UPDATE `vl_gruppe` set `gruppe_kuerzel`='$kuerzel',`gruppenname`= '$name' where `gruppe_id` = $id";
    
                if (mysqli_query($conn, $update)) {
                    //erfolg
                } else {
                    echo mysqli_error($conn);
                }
            }elseif ($_GET["action"] == "delete") {
                $id = $_POST["g_id"];
    
                $delete = "DELETE FROM vl_gruppe where gruppe_id = $id";
    
                if (mysqli_query($conn, $delete)) {
                    //erfolg
                } else {
                    echo mysqli_error($conn);
                }
            }elseif ($_GET["action"] == "create"){
                $kuerzel = $_POST['kuerzel'];
                $name = $_POST['name'];

                $insert = "INSERT INTO vl_gruppe(gruppe_kuerzel, gruppenname) VALUES ('$kuerzel', '$name')";
                
                if (mysqli_query($conn, $insert)) {
                    $last_id = mysqli_insert_id($conn);
                    $result_select = "SELECT * from vl_gruppe where gruppe_id = $last_id";
                    $created_group = mysqli_fetch_assoc(mysqli_query($conn, $result_select));
                    echo json_encode($created_group);
                } else {                    
                    echo mysqli_error($conn);
                }
            }
        }

        mysqli_close($conn);

    }
    
?>