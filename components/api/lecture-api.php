<?php
require_once('../functions.php');
$link = sql_connect();
//header('Content-Type: application/json; charset=utf-8');

if (isset($_GET["action"])) {
    switch ($_GET["action"]) {
      
        case 'getActiveLectures':
            $select = "SELECT va.vorlesung_id, v.vorlesung_name, va.zeit_gestartet FROM vl_vorlesung_aktiv va INNER JOIN vl_vorlesung v ON (va.vorlesung_id = v.vorlesung_id)";
            $result = mysqli_query($link, $select);
            $resultArr = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $resultArr[] = $row;
            }
            echo json_encode($resultArr);
            break;
        case 'changePassword':
            mysqli_autocommit($link, false);
            session_start();
            $query_success = true;
            $user = $_SESSION["username"];
            $pw = $_POST["pw"];
            $sql = "UPDATE vl_benutzer SET password=md5(?) WHERE benutzername = ?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, 'ss', $pw, $user);
            if (!mysqli_stmt_execute($stmt)) {
                $query_success = false;
            }
            mysqli_stmt_close($stmt);
            if ($query_success) {
                mysqli_commit($link);
            } else {
                mysqli_rollback($link);
            }
            mysqli_autocommit($link, true);
            break;
        case 'postMessage':
            $v_id = $_POST["v_id"];
            $username = $_POST["username"];
            $nachricht = $_POST["nachricht"];
            if (strlen($nachricht) > 0) {
                echo $v_id . $username . $nachricht;
                $sql = "SELECT benutzer_id FROM vl_benutzer WHERE benutzername = ?";
                $stmt = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($stmt, 's', $username);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                while ($row = mysqli_fetch_assoc($result)) {
                    $user_id = $row["benutzer_id"];
                    // echo "yes";
                }
                $sql = "INSERT INTO vl_chat(vorlesung_id, benutzer_id, nachricht) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($stmt, 'iis', $v_id, $user_id, $nachricht);
                mysqli_stmt_execute($stmt);
            }
            break;
        case 'getMessage':
            $v_id = $_POST["v_id"];
            $mostRecentMessageID = $_POST["mostRecentMessageID"];
            // $resultArr = array();
            $sql = "SELECT nachricht_id, nachricht, benutzername, DATE_FORMAT(nachricht_zeitstempel, '%k:%i:%s') as nachricht_zeitstempel, frage FROM vl_chat LEFT JOIN vl_benutzer ON vl_chat.benutzer_id=vl_benutzer.benutzer_id WHERE vorlesung_id = ? AND nachricht_ID > ? ORDER BY nachricht_id ASC";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, 'ii', $v_id, $mostRecentMessageID);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
            // while ($row = mysqli_fetch_assoc($result)) {
            //     $resultArr[] = $row;
            // }
            // echo json_encode($resultArr);
            mysqli_stmt_close($stmt);
            break;
        case 'getChatQuestionById':
            $nachricht_id = $_GET["nachricht_id"];
            $sql = "SELECT * from vl_vorlesung_frage where frage_id = (SELECT frage_id FROM vl_chat_frage WHERE nachricht_id = ?)";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $nachricht_id);
            if (!mysqli_stmt_execute($stmt)) {
                $query_success = false;
            }else{
                $result = mysqli_stmt_get_result($stmt);
                echo json_encode(mysqli_fetch_assoc($result));
            }            
            mysqli_stmt_close($stmt);
            break;
        case 'getAllAnswerOptionsByQId':
            $q_id = $_GET["q_id"];
            $nachricht_id = $_GET["nachricht_id"];
            $sql = "SELECT antwort, ? AS 'nachricht_id'from vl_vorlesung_frage_antwortmoeglichkeiten where frage_id = ?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, 'ii', $nachricht_id, $q_id);
            if (!mysqli_stmt_execute($stmt)) {
                $query_success = false;
            }
            $result = mysqli_stmt_get_result($stmt);
            echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
            mysqli_stmt_close($stmt);
            break;
        case 'storeChatQuestionAnswer':
            $username = $_POST['username'];
            $q_id = "";
            $answers = "";

            //find question id
            foreach ($_POST as $key => $value) {
                if($key != "username"){
                    $q_id = $key;
                }
            }
            //array mit Antworten in $answers schreiben
            //muss kein Array sein, wenn es nur eine Antwort ist - deswegen if(is_array())
            $answers = $_POST[$key];

            if(isset($answers)){
                if(is_array($answers)){
                    //bei multi choice
                    foreach($answers as $answer){
                        echo $answer;
                        $sql = "INSERT INTO vl_vorlesung_frage_antworten (frage_id, benutzer_id, antwort) VALUES (?,(SELECT benutzer_id FROM vl_benutzer WHERE benutzername = ?),?)";
                        $stmt = mysqli_prepare($link, $sql);
                        mysqli_stmt_bind_param($stmt, 'iss', $q_id, $username, $answer);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                    }
                }else{
                    $sql = "INSERT INTO vl_vorlesung_frage_antworten (frage_id, benutzer_id, antwort) VALUES (?,(SELECT benutzer_id FROM vl_benutzer WHERE benutzername = ?),?)";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'iss', $q_id, $username, $answers);
                    mysqli_stmt_execute($stmt);
                }             
            }
            break;
        default:
            die("Keine gültige Action gesetzt.");
            break;
    }
}