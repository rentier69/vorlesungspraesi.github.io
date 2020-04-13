<?php
session_start();
if(!isset($_SESSION['username'])){
   header("Location: ../index.php");
    die("Bitte melden Sie sich an"); 
}

require("functions.php");
generate_header("Menü","Bereich für Studenten",$_SESSION["username"],"../");
?>


<div class="container-fluid">
    <form action="lecture.php" method="post">
        <div class="form-row">
            <div class="col-md-5">
                <select class="custom-select form-control" onchange="enableInput(['joinLecture'])" name="lectureToJoin" id="lectureToJoin">
                    <option selected disabled>-</option>
                </select>
            </div>
            <div class="col-md-2">
                <input disabled type="submit" id="joinLecture" class="btn btn-primary form-control" value="Teilnehmen">
            </div>        
    </form>
</div>

<script>
    $(document).ready(function () {
        getActiveLectures();
    });
</script>

<?php
generate_footer();
?>
