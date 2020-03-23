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
            if($_GET["action"] == "displayAll"){
                $resultArr = array();
                $user_select = "select benutzer_id, benutzername, aktiv, datum_registriert, datum_letzterlogin from vl_benutzer";
                $result = mysqli_query($conn, $user_select);
                while ($row = mysqli_fetch_assoc($result)) {
                    $resultArr[] = $row;
                }
                echo json_encode($resultArr);
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
            }                   
        }        
        mysqli_close($conn);
    }

    function groups(){

    }
    
?>