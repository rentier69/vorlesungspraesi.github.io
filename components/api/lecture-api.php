<?php
require_once('../functions.php');

//header('Content-Type: application/json; charset=utf-8');
$conn = sql_connect();

if (isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case 'closeLecture':
            $v_id = $_POST["v_id"];
            $delete = "DELETE FROM `vl_vorlesung_aktiv` WHERE vorlesung_id = $v_id";
            if (mysqli_query($conn, $delete)) {
                echo "true";
            } else {
                echo "false";
            }
            break;
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
            case 'closeLecture':
            break;        
        default:
            die("Keine gültige Action gesetzt.");
            break;
    }
}