<?php
require_once('../functions.php');
if (isset($_GET["action"])) {

    $link = sql_connect();
    mysqli_autocommit($link, false);
    $query_success = true;

    switch ($_GET["action"]) {
        case 'deleteChat':
            $v_id = $_POST["v_id"];
            $sql = "DELETE FROM vl_chat WHERE vorlesung_id = ?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $v_id);

            if(!mysqli_stmt_execute($stmt)){
                $query_success = false;
            }
            break;
        case 'closeLecture':
            $v_id = $_POST["v_id"];
            $sql = "DELETE FROM vl_vorlesung_aktiv WHERE vorlesung_id = ?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $v_id);
            if(!mysqli_stmt_execute($stmt)){
                $query_success = false;
            }
            // deleteChat($v_id, $link); //wegen delete cascade unnötig
            break;
        case 'postChatQuestion':
            $v_id = $_POST["v_id"];
            $username = $_POST["username"];
            $q_id = $_POST["q_id"];
            $frage = 1;

            //ggfs direkt in unteres statement integrieren
            $sql = "SELECT benutzer_id FROM vl_benutzer WHERE benutzername = ?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);

            //Eintrag in vl_chat erzeugen
            $sql = "INSERT INTO vl_chat(vorlesung_id, benutzer_id, frage) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, 'iii', $v_id, $user["benutzer_id"], $frage);

            if (mysqli_stmt_execute($stmt)) {
                //Eintrag in vl_chat_frage erzeugen
                $nachricht_id = mysqli_insert_id($link);
                $sql = "INSERT INTO vl_chat_frage(nachricht_id, frage_id) VALUES (?, ?)";
                $stmt = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($stmt, 'ii', $nachricht_id, $q_id);
                if (mysqli_stmt_execute($stmt)) {
                    //nichts zu tun
                } else {
                    $query_success = false;
                }
            } else {
                $query_success = false;
            }
            mysqli_stmt_close($stmt);
            break;
        case 'deactivateChatQuestion':
            $nachricht_id = $_POST['nachricht_id'];
            $sql = "UPDATE `vl_chat_frage` SET frage_aktiv = 0 WHERE nachricht_id = ?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $nachricht_id);
            if (mysqli_stmt_execute($stmt)) {
                //nichts zu tun
            } else {
                $query_success = false;
            }
            break;
        default:
            die("Keine gültige Action gesetzt.");
            break;
    }
    if ($query_success) {
        mysqli_commit($link);
    } else {
        mysqli_rollback($link);
    }
    mysqli_autocommit($link, true);
    mysqli_close($link);
}
?>