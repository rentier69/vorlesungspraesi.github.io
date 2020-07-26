<?php
require_once('../functions.php');
$conn = sql_connect();
//header('Content-Type: application/json; charset=utf-8');

if (isset($_GET["action"])) {
    switch ($_GET["action"]) {
      
        case 'getActiveLectures':
            $select = "SELECT va.vorlesung_id, v.vorlesung_name, va.zeit_gestartet FROM vl_vorlesung_aktiv va INNER JOIN vl_vorlesung v ON (va.vorlesung_id = v.vorlesung_id)";
            $result = mysqli_query($conn, $select);
            $resultArr = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $resultArr[] = $row;
            }
            echo json_encode($resultArr);
            break;
        case 'changePassword':
            mysqli_autocommit($conn, false);
            session_start();
            $query_success = true;
            $user = $_SESSION["username"];
            $pw = $_POST["pw"];
            $sql = "UPDATE vl_benutzer SET password=md5(?) WHERE benutzername = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ss', $pw, $user);
            if (!mysqli_stmt_execute($stmt)) {
                $query_success = false;
            }
            mysqli_stmt_close($stmt);
            if ($query_success) {
                mysqli_commit($conn);
            } else {
                mysqli_rollback($conn);
            }
            mysqli_autocommit($conn, true);
            break;
        case 'postMessage':
            $v_id = $_POST["v_id"];
            $username = $_POST["username"];
            $nachricht = $_POST["nachricht"];
            if (strlen($nachricht) > 0) {
                echo $v_id . $username . $nachricht;
                $sql = "SELECT benutzer_id FROM vl_benutzer WHERE benutzername = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 's', $username);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                while ($row = mysqli_fetch_assoc($result)) {
                    $user_id = $row["benutzer_id"];
                    echo "yes";
                }
                $sql = "INSERT INTO vl_chat(vorlesung_id, benutzer_id, nachricht) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 'iis', $v_id, $user_id, $nachricht);
                mysqli_stmt_execute($stmt);
            }
            break;
        case 'getMessage':
            $v_id = $_POST["v_id"];
            $resultArr = array();
            $sql = "SELECT nachricht, benutzername FROM vl_chat LEFT JOIN vl_benutzer ON vl_chat.benutzer_id=vl_benutzer.benutzer_id WHERE vorlesung_id = ? ORDER BY nachricht_zeitstempel ASC";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $v_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $resultArr[] = $row;
            }
            echo json_encode($resultArr);
            break;
        default:
            die("Keine gültige Action gesetzt.");
            break;
    }
}