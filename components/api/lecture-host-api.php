<?php
require_once('../functions.php');
$conn = sql_connect();
if (isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case 'deleteChat':
            $v_id = $_POST["v_id"];
            deleteChat($v_id);
            break;
        case 'closeLecture':
            $v_id = $_POST["v_id"];
            $sql = "DELETE FROM vl_vorlesung_aktiv WHERE vorlesung_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $v_id);
            mysqli_stmt_execute($stmt);
            deleteChat($v_id);
            break;
        default:
            die("Keine gültige Action gesetzt.");
            break;
    }
}

function deleteChat($v_id)
{
    $conn = sql_connect();
    $sql = "DELETE FROM vl_chat WHERE vorlesung_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $v_id);
    mysqli_stmt_execute($stmt);
}
