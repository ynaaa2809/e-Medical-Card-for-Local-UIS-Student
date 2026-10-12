<?php

session_start();


if (!isset($_SESSION['admin_id'])) {

    header("Location: login_admin.php");

    exit();

}


include("db_connect.php");


$admin_id = $_SESSION['admin_id'];


$full_name = isset($_SESSION['full_name'])
    ? $_SESSION['full_name']
    : "Admin";


$studentStmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM students
");


$studentStmt->execute();

$totalStudents = (int)$studentStmt->fetchColumn();


$clinicStmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM clinics
");


$clinicStmt->execute();

$totalClinics = (int)$clinicStmt->fetchColumn();


$claimStmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM medical_claims
");


$claimStmt->execute();

$totalClaims = (int)$claimStmt->fetchColumn();

?>


<!DOCTYPE html>

<html>

<head>

<title>Admin Dashboard</title>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<style>


*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}


body{
    background:#5d8d87;
    min-height:100vh;
    padding-bottom:40px;
}


.header{
    width:90%;
    margin:30px auto 20px auto;

    background:white;

    padding:20px 30px;

    border-radius:15px;

    display:flex;
    justify-content:space-between;
    align-items:center;

    box-shadow:0 3px 10px rgba(0,0,0,0.12);
}


.left-header{
    display:flex;
    align-items:center;
}


.profile{
    width:75px;
    height:75px;
    margin-right:15px;
}


.left-header h2{
    color:#2f6468;

    font-size:20px;

    margin-bottom:5px;
}


.admin-text{
    color:#777;

    font-size:13px;
}


.logo{
    width:55px;
}


.page-title{
    width:90%;

    margin:30px auto 20px auto;
}


.page-title h1{
    color:white;

    font-size:28px;

    margin-bottom:6px;
}


.page-title p{
    color:white;

    font-size:14px;
}


.stats{
    width:90%;

    margin:auto;

    display:grid;

    grid-template-columns:repeat(3, 1fr);

    gap:20px;
}


.stat-card{
    background:white;

    padding:25px;

    border-radius:15px;

    box-shadow:0 3px 10px rgba(0,0,0,0.12);

    transition:0.2s;
}


.stat-card:hover{

    transform:translateY(-3px);

    box-shadow:0 6px 15px rgba(0,0,0,0.15);

}


.stat-card h3{
    color:#777;

    font-size:13px;

    font-weight:normal;

    margin-bottom:12px;
}


.stat-number{
    color:#2f6468;

    font-size:32px;

    font-weight:bold;
}


.stat-card p{
    color:#4b8d95;

    font-size:12px;

    margin-top:7px;
}


.management{
    width:90%;

    margin:25px auto;

    background:white;

    padding:25px;

    border-radius:15px;

    box-shadow:0 3px 10px rgba(0,0,0,0.12);
}


.management h2{
    color:#2f6468;

    font-size:20px;

    margin-bottom:20px;
}


.management-grid{
    display:grid;

    grid-template-columns:repeat(3, 1fr);

    gap:15px;
}


.management-card{
    text-decoration:none;

    background:#f2f2f2;

    padding:22px;

    border-radius:12px;

    border:1px solid #f2f2f2;

    transition:0.2s;
}


.management-card:hover{
    background:#4b8d95;

    border-color:#4b8d95;

    transform:translateY(-2px);
}


.management-card h3{
    color:#2f6468;

    font-size:16px;

    margin-bottom:7px;
}


.management-card p{
    color:#777;

    font-size:12px;

    line-height:1.5;
}


.management-card:hover h3,
.management-card:hover p{
    color:white;
}


.bottom-section{
    width:90%;

    margin:0 auto;

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:20px;
}


.activity{
    background:white;

    padding:25px;

    border-radius:15px;

    box-shadow:0 3px 10px rgba(0,0,0,0.12);
}


.activity h2{
    color:#2f6468;

    font-size:19px;

    margin-bottom:15px;
}


.activity-item{
    padding:12px 0;

    border-bottom:1px solid #f2f2f2;

    color:#555;

    font-size:13px;
}


.activity-item:last-child{
    border-bottom:none;
}


.system-info{
    background:white;

    padding:25px;

    border-radius:15px;

    box-shadow:0 3px 10px rgba(0,0,0,0.12);
}


.system-info h2{
    color:#2f6468;

    font-size:19px;

    margin-bottom:15px;
}


.system-info p{
    color:#666;

    font-size:13px;

    line-height:1.7;
}


.system-name{
    color:#2f6468;

    font-weight:bold;
}


@media(max-width:850px){

    .stats{
        grid-template-columns:1fr;
    }


    .management-grid{
        grid-template-columns:1fr 1fr;
    }


    .bottom-section{
        grid-template-columns:1fr;
    }

}


@media(max-width:600px){

    .header{
        width:94%;

        padding:18px;
    }


    .page-title{
        width:94%;
    }


    .stats{
        width:94%;
    }


    .management{
        width:94%;
    }


    .management-grid{
        grid-template-columns:1fr;
    }


    .bottom-section{
        width:94%;
    }


    .left-header h2{
        font-size:17px;
    }


    .logo{
        width:55px;
    }

}

</style>

</head>


<body>


<div class="header">


    <div class="left-header">


        <img src="images/logoEMCS.png" class="profile">


        <div>

            <h2>

                Hi <?php echo htmlspecialchars($full_name); ?>

            </h2>


            <p class="admin-text">

                Admin ID:

                <?php echo htmlspecialchars($admin_id); ?>

            </p>

        </div>


    </div>


    <img src="images/logouis.png" class="logo">


</div>


<div class="page-title">

    <h1>

        Admin Dashboard

    </h1>


    <p>

        Manage the e-Medical Card system.

    </p>

</div>


<div class="stats">


    <div class="stat-card">

        <h3>

            TOTAL STUDENTS

        </h3>


        <div class="stat-number">

            <?php echo $totalStudents; ?>

        </div>


        <p>

            Registered students

        </p>

    </div>


    <div class="stat-card">

        <h3>

            PANEL CLINICS

        </h3>


        <div class="stat-number">

            <?php echo $totalClinics; ?>

        </div>


        <p>

            Registered clinics

        </p>

    </div>


    <div class="stat-card">

        <h3>

            MEDICAL CLAIMS

        </h3>


        <div class="stat-number">

            <?php echo $totalClaims; ?>

        </div>


        <p>

            Submitted claims

        </p>

    </div>


</div>


<div class="management">


    <h2>

        Management

    </h2>


    <div class="management-grid">


        <a href="register_student.php" class="management-card">

            <h3>

                Register Student

            </h3>


            <p>

                Add a new student to the e-Medical system.

            </p>

        </a>


        <a href="view_student.php" class="management-card">

            <h3>

                View Students

            </h3>


            <p>

                View and manage registered students.

            </p>

        </a>


        <a href="register_clinic.php" class="management-card">

            <h3>

                Register Clinic

            </h3>


            <p>

                Add a new panel clinic to the system.

            </p>

        </a>


        <a href="view_clinic.php" class="management-card">

            <h3>

                View Clinics

            </h3>


            <p>

                View and manage panel clinics.

            </p>

        </a>


        <a href="admin_report.php" class="management-card">

            <h3>

                Reports

            </h3>


            <p>

                View and generate system reports.

            </p>

        </a>


        <a href="logout.php" class="management-card">

            <h3>

                Logout

            </h3>


            <p>

                Sign out from the admin account.

            </p>

        </a>


    </div>

</div>


<div class="bottom-section">


    <div class="activity">

        <h2>

            Recent Activity

        </h2>


        <div class="activity-item">

            Total students:

            <strong>

                <?php echo $totalStudents; ?>

            </strong>

        </div>


        <div class="activity-item">

            Total clinics:

            <strong>

                <?php echo $totalClinics; ?>

            </strong>

        </div>


        <div class="activity-item">

            Total medical claims:

            <strong>

                <?php echo $totalClaims; ?>

            </strong>

        </div>


    </div>


    <div class="system-info">

        <h2>

            System Information

        </h2>


        <p>

            <span class="system-name">

                e-Medical Card

            </span>

            is a web-based system for managing

            medical card information, student claims

            and panel clinics for UIS local students.

        </p>


    </div>


</div>


</body>

</html>