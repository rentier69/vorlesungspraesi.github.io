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
    require('lecture-api.php');
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
        if (isset($_GET["action"])) {

            $link = sql_connect();
            mysqli_autocommit($link, false);
            $query_success = true;

            switch ($_GET["action"]) {
                case 'getAll':
                    $resultArr = array();
                    $sql = "select vorlesung_id, vorlesung_name from vl_vorlesung";
                    $stmt = mysqli_prepare($link, $sql);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    $result = mysqli_stmt_get_result($stmt);
                    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
                    mysqli_stmt_close($stmt);
                    break;
                case 'getById':
                    $v_id = $_GET["v_id"];
                    $sql = "SELECT benutzer_id, vorlesung_name from vl_vorlesung where vorlesung_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $v_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    $result = mysqli_stmt_get_result($stmt);
                    echo json_encode(mysqli_fetch_assoc($result));
                    mysqli_stmt_close($stmt);
                    
                    break;
                case 'getQuestions':
                    $v_id = $_GET["v_id"];
                    $sql = "SELECT frage_id, frage_titel, frage_typ_titel, aktiv, vorherige_version_id, fragenummer FROM vl_vorlesung_frage INNER JOIN vl_vorlesung_frage_typ ON (vl_vorlesung_frage.frage_typ_id = vl_vorlesung_frage_typ.frage_typ_id) WHERE vorlesung_id = ? order by fragenummer";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $v_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    $result = mysqli_stmt_get_result($stmt);
                    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
                    mysqli_stmt_close($stmt);
                    break;
                case 'getAssignedGroups':
                    $v_id = $_GET["v_id"];
                    $resultArr = array();
                    $sql_assignedTo = "SELECT gruppe_id, gruppe_kuerzel, gruppenname, 1 AS assignedTo from vl_gruppe where gruppe_id in (select gruppe_id from vl_vorlesung_gruppe_map where vorlesung_id = ?)";
                    $sql_notAssignedTo = "SELECT gruppe_id, gruppe_kuerzel, gruppenname, 0 AS assignedTo from vl_gruppe where gruppe_id not in (select gruppe_id from vl_vorlesung_gruppe_map where vorlesung_id = ?)";

                    $stmt_assignedTo = mysqli_prepare($link, $sql_assignedTo);
                    $stmt_notAssignedTo = mysqli_prepare($link, $sql_notAssignedTo);
    
                    mysqli_stmt_bind_param($stmt_assignedTo, 'i', $v_id);
                    mysqli_stmt_bind_param($stmt_notAssignedTo, 'i', $v_id);
    
                    if (!mysqli_stmt_execute($stmt_assignedTo)) {
                        $query_success = false;
                    }
                    $result_assignedTo = mysqli_stmt_get_result($stmt_assignedTo);
    
                    if (!mysqli_stmt_execute($stmt_notAssignedTo)) {
                        $query_success = false;
                    }
                    $result_notAssignedTo = mysqli_stmt_get_result($stmt_notAssignedTo);
    
                    while ($row = mysqli_fetch_assoc($result_assignedTo)) {
                        $resultArr[] = $row;
                    }
                    while ($row = mysqli_fetch_assoc($result_notAssignedTo)) {
                        $resultArr[] = $row;
                    }
                    
                    echo json_encode($resultArr);
                    mysqli_stmt_close($stmt_notAssignedTo);
                    mysqli_stmt_close($stmt_assignedTo);
                    break;
                case 'assignToGroup':
                    $v_id = $_POST["v_id"];
                    $g_id = $_POST["g_id"];
                    $sql = "INSERT INTO `vl_vorlesung_gruppe_map`(`vorlesung_id`, `gruppe_id`) values (?,?)";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'ii', $v_id, $g_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);

                    break;
                case 'unassignFromGroup':
                    $v_id = $_POST["v_id"];
                    $g_id = $_POST["g_id"];
                    $sql = "DELETE FROM `vl_vorlesung_gruppe_map` where vorlesung_id = ? and gruppe_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'ii', $v_id, $g_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);

                    break;
                case 'rename':
                    $name = $_POST["name"];
                    $v_id = $_POST["v_id"];
                    $sql = "UPDATE `vl_vorlesung` set `vorlesung_name`= ? where `vorlesung_id` = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'si', $name, $v_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);
                    break;
                case 'delete':
                    $v_id = $_POST["v_id"];
                    $sql = "DELETE FROM vl_vorlesung where vorlesung_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $v_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);
                    break;
                case 'create':
                    $name = $_POST['name'];
                    // mit befüllung der benutzer_id
                    $sql = "INSERT INTO `vl_vorlesung`(`benutzer_id`, `vorlesung_name`) VALUES ((SELECT benutzer_id FROM vl_benutzer WHERE benutzername = ?),?)";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'ss', $_SESSION['username'], $name);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $last_id = mysqli_insert_id($link);
                        mysqli_stmt_close($stmt);

                        $sql = "SELECT * from vl_vorlesung where vorlesung_id = ?";
                        $stmt = mysqli_prepare($link, $sql);
                        mysqli_stmt_bind_param($stmt, 'i', $last_id);
                        if (mysqli_stmt_execute($stmt)) {
                            $result = mysqli_stmt_get_result($stmt);
                            echo json_encode(mysqli_fetch_assoc($result));
                        } else{
                            $query_success = false;
                        }
                        mysqli_stmt_close($stmt);                     
                    } else{
                        $query_success = false;
                    }
                    break;
                default:
                    die("Keine gültige Action gesetzt!");
                    break;
            }
            if ($query_success) {
                mysqli_commit($link);
            }else {
                mysqli_rollback($link);
            }
            mysqli_autocommit($link,true);
            mysqli_close($link);      
        }        
    }

    function lecturequestion(){
        if (isset($_GET["action"])) {

            $link = sql_connect();
            mysqli_autocommit($link, false);
            $query_success = true;

            switch ($_GET["action"]) {
                case 'getQuestionTypes':
                    $sql = "SELECT * FROM vl_vorlesung_frage_typ";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_set_charset($link, "utf8");
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    $result = mysqli_stmt_get_result($stmt);
                    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
                    mysqli_stmt_close($stmt);
                    break;   
                case 'getById':
                    $q_id = $_GET["q_id"];
                    $sql = "SELECT * from vl_vorlesung_frage where frage_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $q_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    $result = mysqli_stmt_get_result($stmt);
                    echo json_encode(mysqli_fetch_assoc($result));
                    mysqli_stmt_close($stmt);
                    break;
                case 'setRank':
                    $q_id = $_POST["q_id"];
                    $rank = $_POST["rank"];
                    $sql = "UPDATE vl_vorlesung_frage SET fragenummer=? WHERE frage_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'ii', $rank, $q_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);
                    break;
                case 'getAllAnswerOptionsByQId':
                    $q_id = $_GET["q_id"];
                    $sql = "SELECT antwort from vl_vorlesung_frage_antwortmoeglichkeiten where frage_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $q_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    $result = mysqli_stmt_get_result($stmt);
                    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
                    mysqli_stmt_close($stmt);
                    break;
                case 'hasGivenAnswer':
                    $q_id = $_GET["q_id"];
                    $sql = "SELECT * from vl_vorlesung_frage_antworten where frage_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $q_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    $result = mysqli_stmt_get_result($stmt);
                    $row_cnt = mysqli_num_rows($result);
                    if($row_cnt == 0){
                        echo "false";
                    }elseif($row_cnt > 0){
                        echo "true";
                    }
                    mysqli_stmt_close($stmt);
                    break;            
                case 'delete':
                    $q_id = $_POST["q_id"];
                    $sql = "DELETE FROM `vl_vorlesung_frage` WHERE frage_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $q_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);
                    break;
                case 'create':
                    $v_id = $_POST['v_id'];
                    $question_text = $_POST['question_text'];
                    $question_type = $_POST['question_type'];

                    //bei Freitextfragen auf jeden Fall leer
                    if(isset($_POST['question_option'])){
                        $question_options = $_POST['question_option'];
                    }                

                    $sql = "INSERT INTO `vl_vorlesung_frage`(`vorlesung_id`, `frage_titel`, `frage_typ_id`) VALUES (?,?,?)";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'isi', $v_id, $question_text, $question_type);

                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);
                        $last_id = mysqli_insert_id($link);

                        if(isset($question_options)){                        
                            foreach($question_options as $option){                    
                                unset($sql);
                                if(!empty($option)){
                                    $sql = "INSERT INTO `vl_vorlesung_frage_antwortmoeglichkeiten`(`frage_id`, `antwort`) VALUES (?,?)";
                                    $stmt = mysqli_prepare($link, $sql);
                                    mysqli_stmt_bind_param($stmt, 'is', $last_id, $option);
                                    if (mysqli_stmt_execute($stmt)) {
                                        //nichts zu tun, 
                                    } else {
                                        $query_success = false;
                                    }
                                }      
                                mysqli_stmt_close($stmt);              
                            }            
                        }

                        $sql = "SELECT * from vl_vorlesung_frage where frage_id = ?";
                        $stmt = mysqli_prepare($link, $sql);
                        mysqli_stmt_bind_param($stmt, 'i', $last_id);
                        if (!mysqli_stmt_execute($stmt)) {
                            $query_success = false;
                        }
                        $result = mysqli_stmt_get_result($stmt);
                        echo json_encode(mysqli_fetch_assoc($result));
                        mysqli_stmt_close($stmt);
                    }else{
                        $query_success = false;
                    }
                    break;
                case 'createNewVersion':
                    $q_id = $_POST['q_id'];
                    $v_id = $_POST['v_id'];
                    $question_text = $_POST['question_text'];
                    $question_type = $_POST['question_type'];

                    //bei Freitextfragen auf jeden Fall leer
                    if(isset($_POST['question_option'])){
                        $question_options = $_POST['question_option'];
                    }
                                    
                    //alte frage deaktivieren
                    $sql = "UPDATE vl_vorlesung_frage SET aktiv=false WHERE frage_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $q_id);

                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                        mysqli_stmt_close($stmt);
                        break;
                    }
                    mysqli_stmt_close($stmt);

                    //neue frage einfügen
                    $sql = "INSERT INTO `vl_vorlesung_frage`(`vorlesung_id`, `frage_titel`, `frage_typ_id`,`vorherige_version_id`) VALUES (?,?,?,?)";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'isii', $v_id,$question_text,$question_type,$q_id);

                    if (mysqli_stmt_execute($stmt)) {
                        $last_id = mysqli_insert_id($link);
                        mysqli_stmt_close($stmt);
                        if(isset($question_options)){
                        foreach ($question_options as $option) {                    
                                unset($sql);
                                if(!empty($option)){
                                    $sql = "INSERT INTO `vl_vorlesung_frage_antwortmoeglichkeiten`(`frage_id`, `antwort`) VALUES (?,?)";
                                    $stmt = mysqli_prepare($link, $sql);
                                    mysqli_stmt_bind_param($stmt, 'is', $last_id, $option);
                                    if (!mysqli_stmt_execute($stmt)) {
                                        $query_success = false;
                                        mysqli_stmt_close($stmt);
                                        break;
                                    }
                                }
                                mysqli_stmt_close($stmt);                
                            }
                        }

                        $sql = "SELECT * from vl_vorlesung_frage where frage_id = ?";
                        $stmt = mysqli_prepare($link, $sql);
                        mysqli_stmt_bind_param($stmt, 'i', $last_id);
                        if (!mysqli_stmt_execute($stmt)) {
                            $query_success = false;
                        }
                        $result = mysqli_stmt_get_result($stmt);
                        echo json_encode(mysqli_fetch_assoc($result));
                        mysqli_stmt_close($stmt);
                    } else{
                        $query_success = false;
                    }
                    break;
                case 'modifyExistingVersion':
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
                    
                    //alte frage löschen - antwortmöglichkeiten werden mit delete cascade mitgelöscht
                    $sql = "DELETE FROM vl_vorlesung_frage WHERE frage_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $q_id);
                    
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                        mysqli_stmt_close($stmt);
                        break;
                    }
                    mysqli_stmt_close($stmt);
                    
                    //neue frage mit gleicher id einfügen 
                    $sql = "INSERT INTO `vl_vorlesung_frage`(`frage_id`,`vorlesung_id`, `frage_titel`, `frage_typ_id`, `fragenummer`) VALUES (?,?,?,?,?)";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'iisii', $q_id, $v_id, $question_text, $question_type, $question_rank);
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);
                        if(isset($question_options)){
                            foreach($question_options as $option){                    
                                unset($sql);
                                if(!empty($option)){
                                    $sql = "INSERT INTO `vl_vorlesung_frage_antwortmoeglichkeiten`(`frage_id`, `antwort`) VALUES (?,?)";
                                    $stmt = mysqli_prepare($link, $sql);
                                    mysqli_stmt_bind_param($stmt, 'is', $q_id, $option);
                                    if (!mysqli_stmt_execute($stmt)) {
                                        $query_success = false;
                                        mysqli_stmt_close($stmt);
                                        break;
                                    }
                                }
                                mysqli_stmt_close($stmt);                
                            }
                        }

                        $sql = "SELECT * from vl_vorlesung_frage where frage_id = ?";
                        $stmt = mysqli_prepare($link, $sql);
                        mysqli_stmt_bind_param($stmt, 'i', $q_id);
                        if (!mysqli_stmt_execute($stmt)) {
                            $query_success = false;
                        }
                        $result = mysqli_stmt_get_result($stmt);
                        echo json_encode(mysqli_fetch_assoc($result));
                        mysqli_stmt_close($stmt);
                    } else{
                        $query_success = false;
                    }    
                    break;                                    
                default:
                    die("Keine gültige Action gesetzt!");
                    break;
            }            
            if ($query_success) {
                mysqli_commit($link);
            }else {
                mysqli_rollback($link);
            }
            mysqli_autocommit($link,true);
            mysqli_close($link);
        }
    }

    function users(){
        if (isset($_GET["action"])) {

            $link = sql_connect();
            mysqli_autocommit($link, false);
            $query_success = true;
            
            switch ($_GET["action"]) {
                case 'getAll':
                    $resultArr = array();
                    $sql = "select benutzer_id, benutzername, aktiv, datum_registriert, datum_letzterlogin from vl_benutzer";
                    $stmt = mysqli_prepare($link, $sql);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    $result = mysqli_stmt_get_result($stmt);
                    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
                    mysqli_stmt_close($stmt);
                    break;
                case 'getById':
                    $u_id = $_GET["u_id"];
                    $sql = "SELECT benutzer_id, benutzername, aktiv from vl_benutzer where benutzer_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $u_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    $result = mysqli_stmt_get_result($stmt);
                    echo json_encode(mysqli_fetch_assoc($result));
                    mysqli_stmt_close($stmt);
                    break;
                case 'addToGroup':
                    $u_id = $_POST["u_id"];
                    $g_id = $_POST["g_id"];
                    $sql = "INSERT INTO `vl_benutzer_gruppe_map`(`benutzer_id`, `gruppe_id`) values (?,?)";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'ii', $u_id, $g_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);

                    break;
                case 'removeFromGroup':
                    $u_id = $_POST["u_id"];
                    $g_id = $_POST["g_id"];
                    $sql = "DELETE FROM `vl_benutzer_gruppe_map` where gruppe_id = ? and benutzer_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'ii', $g_id, $u_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);

                    break;
                case 'getGroupMembership':
                    $u_id = $_GET["u_id"];
                    $resultArr = array();
                    $sql_memberof = "SELECT gruppe_id, gruppe_kuerzel, gruppenname, 1 AS memberOf from vl_gruppe where gruppe_id in (select gruppe_id from vl_benutzer_gruppe_map where benutzer_id = ?)";
                    $sql_notmemberof = "SELECT gruppe_id, gruppe_kuerzel, gruppenname, 0 AS memberOf from vl_gruppe where gruppe_id not in (select gruppe_id from vl_benutzer_gruppe_map where benutzer_id = ?)";
                    $stmt_memberof = mysqli_prepare($link, $sql_memberof);
                    $stmt_notmemberof = mysqli_prepare($link, $sql_notmemberof);

                    mysqli_stmt_bind_param($stmt_memberof, 'i', $u_id);
                    mysqli_stmt_bind_param($stmt_notmemberof, 'i', $u_id);
                    
                    if (!mysqli_stmt_execute($stmt_memberof)) {
                        $query_success = false;
                    }
                    $result_members = mysqli_stmt_get_result($stmt_memberof);

                    if (!mysqli_stmt_execute($stmt_notmemberof)) {
                        $query_success = false;
                    }
                    $result_notmembers = mysqli_stmt_get_result($stmt_notmemberof);

                    while ($row = mysqli_fetch_assoc($result_members)) {
                        $resultArr[] = $row;
                    }
                    while ($row = mysqli_fetch_assoc($result_notmembers)) {
                        $resultArr[] = $row;
                    }
                    
                    echo json_encode($resultArr);
                    mysqli_stmt_close($stmt_notmemberof);
                    mysqli_stmt_close($stmt_memberof);
                    break;
                case 'rename':
                    $name = $_POST["name"];
                    $u_id = $_POST["id"];

                    $sql = "UPDATE `vl_benutzer` set `benutzername`=? where `benutzer_id` = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'si', $name, $u_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);
                    break;
                case 'activate':
                    $u_id = $_POST["id"];
                    $sql = "UPDATE `vl_benutzer` set `aktiv`= 1 where `benutzer_id` = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $u_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);
                    break;
                case 'deactivate':
                    $u_id = $_POST["id"];
                    $sql = "UPDATE `vl_benutzer` set `aktiv`= 0 where `benutzer_id` = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $u_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);
                    break;
                case 'resetPw':
                    $u_id = $_POST["id"];
                    $pw = $_POST["pw"];

                    $sql = "UPDATE vl_benutzer SET password=md5(?) WHERE benutzer_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'si', $pw, $u_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);
                    break;
                case 'create':
                    $name = $_POST['username'];
                    $pw = md5($_POST['pw']);
                    $user_type = $_POST['user_type'];

                    $sql = "INSERT INTO vl_benutzer(`benutzername`, `password`) VALUES (?, ?)";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'ss', $name, $pw);

                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                        mysqli_stmt_close($stmt);
                        break;
                    }
                    mysqli_stmt_close($stmt);

                    switch ($user_type) {
                        case 'dozent':
                            $defaultGroup = appConfig::$defaultDozentGroup;
                            break;
                        
                        case 'student':
                            $defaultGroup = appConfig::$defaultStudentGroup;
                            break;
                    }
                    $insert_id = mysqli_insert_id($link);
                    $sql = "INSERT INTO `vl_benutzer_gruppe_map`(`benutzer_id`, `gruppe_id`) values (?,?)";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'ii', $insert_id, $defaultGroup);

                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                        mysqli_stmt_close($stmt);
                        break;
                    }
                    mysqli_stmt_close($stmt);

                    if($query_success){
                        $sql = "SELECT * from vl_benutzer where benutzer_id = ?";
                        $stmt = mysqli_prepare($link, $sql);
                        mysqli_stmt_bind_param($stmt, 'i', $insert_id);
                        if (!mysqli_stmt_execute($stmt)) {
                            $query_success = false;
                            mysqli_stmt_close($stmt);
                            break;
                        }
                        $result = mysqli_stmt_get_result($stmt);
                        echo json_encode(mysqli_fetch_assoc($result));
                        mysqli_stmt_close($stmt);
                    }
                    break;
                case 'delete':
                    $u_id = $_POST["id"];

                    $sql = "DELETE FROM vl_benutzer where benutzer_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $u_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);
                    break;            
                default:
                    die("Keine gültige Action gesetzt!");
                    break;
            }
            
            if ($query_success) {
                mysqli_commit($link);
            }else {
                mysqli_rollback($link);
            }
            mysqli_autocommit($link,true);
            mysqli_close($link);
        }
    }

    function groups(){
        if (isset($_GET["action"])) {

            $link = sql_connect();
            mysqli_autocommit($link, false);
            $query_success = true;

            switch ($_GET["action"]) {
                case 'getAll':
                    $sql = "select * from vl_gruppe";
                    $stmt = mysqli_prepare($link, $sql);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    $result = mysqli_stmt_get_result($stmt);
                    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
                    mysqli_stmt_close($stmt);

                    break;
                case 'getById':
                    $id = $_GET["g_id"];
                    $sql = "SELECT * from vl_gruppe where gruppe_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    $result = mysqli_stmt_get_result($stmt);
                    echo json_encode(mysqli_fetch_assoc($result));
                    mysqli_stmt_close($stmt);
                    
                    break;
                case 'addToGroup':
                    $u_id = $_POST["u_id"];
                    $g_id = $_POST["g_id"];
                    $sql = "INSERT INTO `vl_benutzer_gruppe_map`(`benutzer_id`, `gruppe_id`) values (?,?)";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'ii', $u_id, $g_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);

                    break;
                case 'removeFromGroup':
                    $u_id = $_POST["u_id"];
                    $g_id = $_POST["g_id"];
                    $sql = "DELETE FROM `vl_benutzer_gruppe_map` where gruppe_id = ? and benutzer_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'ii', $g_id, $u_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);
                    break;
                case 'getGroupMembership':
                    $g_id = $_GET["g_id"];
                    $resultArr = array();
                    
                    $sql_members = "SELECT benutzer_id, benutzername, aktiv, datum_registriert, datum_letzterlogin, 1 AS memberOf from vl_benutzer where benutzer_id in (select benutzer_id from vl_benutzer_gruppe_map where gruppe_id = ?)";
                    $sql_notmembers = "SELECT benutzer_id, benutzername, aktiv, datum_registriert, datum_letzterlogin, 0 AS memberOf from vl_benutzer where benutzer_id not in (select benutzer_id from vl_benutzer_gruppe_map where gruppe_id = ?)";
                    
                    $stmt_members = mysqli_prepare($link, $sql_members);
                    $stmt_notmembers = mysqli_prepare($link, $sql_notmembers);

                    mysqli_stmt_bind_param($stmt_members, 'i', $g_id);
                    mysqli_stmt_bind_param($stmt_notmembers, 'i', $g_id);
                    
                    if (!mysqli_stmt_execute($stmt_members)) {
                        $query_success = false;
                    }
                    $result_members = mysqli_stmt_get_result($stmt_members);

                    if (!mysqli_stmt_execute($stmt_notmembers)) {
                        $query_success = false;
                    }
                    $result_notmembers = mysqli_stmt_get_result($stmt_notmembers);

                    while ($row = mysqli_fetch_assoc($result_members)) {
                        $resultArr[] = $row;
                    }
                    while ($row = mysqli_fetch_assoc($result_notmembers)) {
                        $resultArr[] = $row;
                    }
                    
                    echo json_encode($resultArr);
                    mysqli_stmt_close($stmt_notmembers);
                    mysqli_stmt_close($stmt_members);
                    break;
                case 'rename':
                    $g_id = $_POST["g_id"];
                    $kuerzel = $_POST["kuerzel"];
                    $name = $_POST["name"];
                    $sql = "UPDATE `vl_gruppe` set `gruppe_kuerzel`=?,`gruppenname`= ? where `gruppe_id` = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'ssi', $kuerzel, $name, $g_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);
                    break;
                case 'delete':
                    $g_id = $_POST["g_id"];    
                    $sql = "DELETE FROM vl_gruppe where gruppe_id = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $g_id);
                    if (!mysqli_stmt_execute($stmt)) {
                        $query_success = false;
                    }
                    mysqli_stmt_close($stmt);
                    break;
                case 'create':
                    $kuerzel = $_POST['kuerzel'];
                    $name = $_POST['name'];

                    $sql = "INSERT INTO vl_gruppe(gruppe_kuerzel, gruppenname) VALUES (?,?)";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'ss', $kuerzel, $name);
                    if (mysqli_stmt_execute($stmt)) {
                        $last_id = mysqli_insert_id($link);
                        $result_select = "SELECT * from vl_gruppe where gruppe_id = $last_id";
                        $created_group = mysqli_fetch_assoc(mysqli_query($link, $result_select));
                        echo json_encode($created_group);
                    }else{
                        $query_success = false;
                    }
                    break;            
                default:
                die("Keine gültige Action gesetzt!");
                    break;
            }        
            if ($query_success) {
                mysqli_commit($link);
            }else {
                mysqli_rollback($link);
            }
            mysqli_autocommit($link,true);
            mysqli_close($link);
        }
    }
?>