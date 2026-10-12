<?php

session_start();

if(isset($_GET['action']) && $_GET['action'] === 'confirm'){

    if(isset($_SESSION['student_id'])){
        session_destroy();
        header("Location: login_student.php");
        exit();
    }

    if(isset($_SESSION['clinic_id'])){
        session_destroy();
        header("Location: login_clinic.php");
        exit();
    }

    if(isset($_SESSION['admin_id'])){
        session_destroy();
        header("Location: login_admin.php");
        exit();
    }

    session_destroy();
    header("Location: index.php");
    exit();
}


$cancelPage = 'index.php';

if(isset($_SESSION['student_id'])){
    $cancelPage = 'dashboard.php';
}
elseif(isset($_SESSION['clinic_id'])){
    $cancelPage = 'dashboard_clinic.php';
}
elseif(isset($_SESSION['admin_id'])){
    $cancelPage = 'dashboard_admin.php';
}

?>


<!DOCTYPE html>
<html>
<head>

<title>Confirm Logout</title>

<style>


*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}


body{
    background:#f2f2f2;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}


.popup{
    background:white;
    width:380px;
    max-width:90%;
    padding:30px;
    border-radius:15px;
    text-align:center;
    box-shadow:0 5px 20px rgba(0,0,0,0.20);
}


.popup-icon{
    width:55px;
    height:55px;
    margin:0 auto 15px;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:28px;
    font-weight:bold;
    background:#e5f4ef;
    color:#2f6468;
}


.popup h3{
    color:#333;
    font-size:19px;
    margin-bottom:10px;
}


.popup p{
    color:#777;
    font-size:14px;
    line-height:1.5;
    margin-bottom:20px;
}


.popup-buttons{
    display:flex;
    justify-content:center;
    gap:10px;
}


.popup button,
.popup a{
    border:none;
    padding:11px 30px;
    border-radius:25px;
    background:#2f6468;
    color:white;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
    text-decoration:none;
}


.popup button:hover,
.popup a:hover{
    background:#234d50;
}


.popup .cancel-btn{
    background:#777;
}


.popup .cancel-btn:hover{
    background:#555;
}


</style>

</head>

<body>

<div class="popup">

    <div class="popup-icon">?</div>

    <h3>Confirm Logout</h3>

    <p>
        Are you sure you want to logout from your<br>
        e-Medical Card account?
    </p>

    <div class="popup-buttons">

        <a href="<?php echo htmlspecialchars($cancelPage); ?>" class="cancel-btn">
            Cancel
        </a>

        <button type="button" onclick="confirmLogout()">
            Logout
        </button>

    </div>

</div>


<script>

function confirmLogout(){

    window.location.href = "logout.php?action=confirm";

}

</script>

</body>

</html>