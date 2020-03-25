<?php
    require('functions.php');
    header("Content-Type: application/json");

    if(isset($_GET["mode"])){
            switch ($_GET["mode"]) {
            case 'lectures':
                lectures();
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

    }
    
?>