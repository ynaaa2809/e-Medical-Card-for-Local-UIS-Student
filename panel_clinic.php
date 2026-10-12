<?php

session_start();


if(!isset($_SESSION['student_id'])){

    header("Location: login_student.php");

    exit();

}


include("db_connect.php");

$studentID = $_SESSION['student_id'];

$sql = "SELECT full_name FROM students WHERE student_id = ?";

$stmt = $conn->prepare($sql);

$stmt->execute([$studentID]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

$studentName = $student['full_name'] ?? '';


$sql = "SELECT 
            clinic_name,
            address,
            contact_number,
            email,
            operating_hours
        FROM clinics";


$stmt = $conn->prepare($sql);

$stmt->execute();

$clinics = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>

<html>

<head>

<title>Panel Clinic</title>

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

}


.sidebar{

    position:fixed;

    left:0;

    top:0;

    width:240px;

    height:100vh;

    background:#5d8d87;

    padding:35px 25px;

    display:flex;

    flex-direction:column;

    box-shadow:3px 0 10px rgba(0,0,0,0.10);

}


.system-name{

    color:white;

    font-size:22px;

    font-weight:bold;

    line-height:1.3;

    margin-bottom:8px;

}


.system-subtitle{

    color:white;

    font-size:12px;

    opacity:0.9;

    margin-bottom:35px;

}


.sidebar-line{

    height:1px;

    background:rgba(255,255,255,0.3);

    margin-bottom:30px;

}


.menu{

    display:flex;

    flex-direction:column;

    gap:15px;

}


.menu a{

    text-decoration:none;

    background:#2f6468;

    color:white;

    padding:14px 20px;

    border-radius:30px;

    font-size:14px;

    font-weight:normal;

    text-align:left;

    display:block;

    transition:0.2s;

}


.menu a:hover{

    background:#234d50;

}


.menu a.active{

    background:#234d50;

    box-shadow:0 3px 8px rgba(0,0,0,0.15);

}


.sidebar-bottom{

    margin-top:auto;

    padding-top:20px;

    color:white;

}


.logout-link{

    display:block;

    text-decoration:none;

    background:#2f6468;

    color:white;

    padding:14px 20px;

    border-radius:30px;

    font-size:14px;

    font-weight:normal;

    text-align:left;

    transition:0.2s;

}


.logout-link:hover{

    background:#234d50;

}


.bottom-line{

    height:1px;

    background:rgba(255,255,255,0.3);

    margin:20px 0;

}


.help-text{

    color:white;

    font-size:12px;

    line-height:1.5;

}


.main{

    margin-left:240px;

    padding:30px 40px;

    min-height:100vh;

}


.header{

    background:white;

    border-radius:15px;

    padding:20px 25px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

    margin-bottom:25px;

}


.profile{

    display:flex;

    align-items:center;

}


.student-icon{

    width:75px;

    height:75px;

    object-fit:contain;

    margin-right:12px;

}


.profile-info{

    display:flex;

    flex-direction:column;

}


.profile-info h2{

    color:#2f6468;

    font-size:20px;

    margin-bottom:5px;

}


.profile-info p{

    color:#777;

    font-size:13px;

}


.uis-logo{

    width:55px;

    height:auto;

    object-fit:contain;

}


.page-title{

    margin-bottom:20px;

}


.page-title h1{

    color:#2f6468;

    font-size:26px;

    margin-bottom:5px;

}


.page-title p{

    color:#777;

    font-size:14px;

}


.clinic-grid{

    display:grid;

    grid-template-columns:repeat(2, 1fr);

    gap:20px;

}


.clinic-box{

    background:white;

    padding:25px;

    border-radius:15px;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

    transition:0.2s;

}


.clinic-box:hover{

    transform:translateY(-3px);

    box-shadow:0 6px 15px rgba(0,0,0,0.12);

}


.clinic-box h2{

    color:#2f6468;

    font-size:19px;

    margin-bottom:20px;

}

.clinic-info{

    display:flex;

    flex-direction:column;

}


.info-row{

    display:flex;

    justify-content:space-between;

    gap:20px;

    padding:12px 0;

    border-bottom:1px solid #f0f0f0;

}


.info-row:last-child{

    border-bottom:none;

}


.info-label{

    color:#777;

    font-size:13px;

    font-weight:bold;

    min-width:110px;

}


.info-value{

    color:#555;

    font-size:13px;

    text-align:right;

    line-height:1.5;

}


.no-data{

    background:white;

    padding:25px;

    border-radius:15px;

    text-align:center;

    color:#777;

    font-size:13px;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

}


@media(max-width:900px){

    .clinic-grid{

        grid-template-columns:1fr;

    }

}


@media(max-width:800px){

    .sidebar{

        position:relative;

        width:100%;

        height:auto;

        padding:25px;

    }

    .menu{

        flex-direction:row;

        flex-wrap:wrap;

    }

    .menu a{

        flex:1;

        min-width:150px;

    }

    .sidebar-bottom{

        margin-top:25px;

    }

    .main{

        margin-left:0;

        padding:25px;

    }

}


@media(max-width:600px){

    .main{

        padding:20px;

    }

    .header{

        padding:18px;

    }

    .uis-logo{

        width:50px;

    }

    .page-title h1{

        font-size:23px;

    }

    .clinic-box{

        padding:20px;

    }

    .info-row{

        flex-direction:column;

        gap:5px;

    }

    .info-value{

        text-align:left;

    }

}


</style>

</head>

<body>

<div class="sidebar">

    <div class="system-name">

        e-Medical Card

    </div>

    <div class="system-subtitle">

        UIS Local Student

    </div>

    <div class="sidebar-line"></div>

    <div class="menu">

        <a href="dashboard.php">

            Dashboard

        </a>

        <a href="student_profile.php">

            View Profile

        </a>

        <a href="claim_history.php">

            Track Claim History

        </a>

        <a href="panel_clinic.php" class="active">

            View Panel Clinic

        </a>

    </div>

    <div class="sidebar-bottom">

        <a href="logout.php" class="logout-link">

            Logout

        </a>

        <div class="bottom-line"></div>

        <div class="help-text">

            Need help?<br>

            Contact BHEP<br>

            Tel. No: 03-8911 7000 ext. 1118, 1117, 1434, 3337

        </div>

    </div>

</div>

<div class="main">

    <div class="header">

        <div class="profile">

            <img
                src="images/logoEMCS.png"
                class="student-icon"
                alt="EMCS Logo"
            >

            <div class="profile-info">

                <h2>

                    <?php echo htmlspecialchars($studentName); ?>

                </h2>

                <p>

                    Student ID:

                    <?php echo htmlspecialchars($studentID); ?>

                </p>

            </div>

        </div>

        <img
            src="images/logouis.png"
            class="uis-logo"
            alt="UIS Logo"
        >

    </div>

    <div class="page-title">

        <h1>

            Available Panel Clinics

        </h1>

        <p>

            View the clinics available for UIS student medical services.

        </p>

    </div>

    <?php if(count($clinics) > 0){ ?>

    <div class="clinic-grid">

        <?php foreach($clinics as $clinic){ ?>

        <div class="clinic-box">

            <h2>

                <?php

                echo htmlspecialchars(
                    $clinic['clinic_name'] ?? ''
                );

                ?>

            </h2>

            <div class="clinic-info">

                <div class="info-row">

                    <span class="info-label">

                        Address

                    </span>

                    <span class="info-value">

                        <?php

                        echo htmlspecialchars(
                            $clinic['address'] ?? ''
                        );

                        ?>

                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">

                        Contact Number

                    </span>

                    <span class="info-value">

                        <?php

                        echo htmlspecialchars(
                            $clinic['contact_number'] ?? ''
                        );

                        ?>

                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">

                        Email

                    </span>

                    <span class="info-value">

                        <?php

                        echo htmlspecialchars(
                            $clinic['email'] ?? ''
                        );

                        ?>

                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">

                        Operating Hours

                    </span>

                    <span class="info-value">

                        <?php

                        echo htmlspecialchars(
                            $clinic['operating_hours'] ?? ''
                        );

                        ?>

                    </span>

                </div>

            </div>

        </div>

        <?php } ?>

    </div>

    <?php } else { ?>

    <div class="no-data">

        No panel clinic available.

    </div>

    <?php } ?>

</div>

</body>

</html>