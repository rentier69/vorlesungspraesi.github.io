<?php
    require('functions.php');
    header('Content-Type: application/json; charset=utf-8');
    $conn = sql_connect();

    if (isset($_GET["action"])) {
        if($_GET["action"] == "closeLecture"){
            $v_id = $_POST["v_id"];
            $delete = "DELETE FROM `vl_vorlesung_aktiv` WHERE vorlesung_id = $v_id";
            if (mysqli_query($conn, $delete)) {
                echo "true";
            }else {
                echo "false";
            }
        }else if($_GET["action"] == "getActiveLectures"){
            $select = "SELECT va.vorlesung_id, v.vorlesung_name, va.zeit_gestartet FROM vl_vorlesung_aktiv va INNER JOIN vl_vorlesung v ON (va.vorlesung_id = v.vorlesung_id)";
            $result = mysqli_query($conn, $select);
            $resultArr = array();
            while ($row = mysqli_fetch_assoc($result)){
                $resultArr[] = $row;
            }
            echo json_encode($resultArr);
        }
    }
?>