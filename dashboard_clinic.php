<?php

session_start();

if(!isset($_SESSION['clinic_id'])){

    header("Location: login_clinic.php");

    exit();

}


$clinic_id = $_SESSION['clinic_id'];

$clinic_name = $_SESSION['clinic_name'];

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Clinic Dashboard</title>


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


.logout-section{

    margin-top:auto;

}


.logout-section a{

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


.logout-section a:hover{

    background:#234d50;

}


.sidebar-bottom{

    margin-top:20px;

    padding-top:20px;

    border-top:1px solid rgba(255,255,255,0.3);

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

    width:100%;

    background:white;

    padding:20px 30px;

    border-radius:20px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

    margin-bottom:30px;

}


.header-left{

    display:flex;

    align-items:center;

}


.emcs-logo{

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

    margin-bottom:25px;

}


.page-title h1{

    color:#2f6468;

    font-size:28px;

    margin-bottom:7px;

}


.page-title p{

    color:#777;

    font-size:14px;

}


.top-section{

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:25px;

    margin-bottom:25px;

}


.welcome-card{

    height:280px;

    background:white;

    border-radius:20px;

    padding:40px;

    display:flex;

    flex-direction:column;

    justify-content:center;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

}


.welcome-card:before{

    content:"🏥";

    font-size:50px;

    margin-bottom:20px;

}


.welcome-card h2{

    color:#2f6468;

    line-height:1.5;

    font-size:23px;

}


.welcome-card p{

    color:#777;

    font-size:14px;

    line-height:1.6;

    margin-top:12px;

}


.overview-card{

    height:280px;

    background:white;

    border-radius:20px;

    padding:30px;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

}


.overview-card h2{

    color:#2f6468;

    font-size:20px;

    margin-bottom:20px;

}


.overview-status{

    background:#f2f2f2;

    border-radius:12px;

    padding:15px;

    margin-bottom:15px;

}


.overview-status h3{

    color:#2f6468;

    font-size:15px;

    margin-bottom:5px;

}


.overview-status p{

    color:#777;

    font-size:13px;

    line-height:1.5;

}


.overview-id{

    background:#f2f2f2;

    border-radius:12px;

    padding:15px;

}


.overview-id h3{

    color:#2f6468;

    font-size:15px;

    margin-bottom:5px;

}


.overview-id p{

    color:#777;

    font-size:13px;

}


.info-card{

    background:white;

    border-radius:20px;

    padding:30px;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

    margin-bottom:25px;

}


.info-card h2{

    color:#2f6468;

    font-size:20px;

    margin-bottom:20px;

}


.info-row{

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:14px 0;

    border-bottom:1px solid #eeeeee;

}


.info-row:last-child{

    border-bottom:none;

}


.info-label{

    color:#777;

    font-size:14px;

}


.info-value{

    color:#2f6468;

    font-size:14px;

    font-weight:bold;

}


.announcement{

    background:white;

    padding:30px;

    border-radius:20px;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

}


.announcement h2{

    color:#2f6468;

    font-size:20px;

    margin-bottom:15px;

}


.announcement hr{

    border:none;

    border-top:1px solid #eeeeee;

    margin-bottom:15px;

}


.announcement p{

    margin:12px 0;

    color:#555;

    font-size:14px;

    line-height:1.5;

}


@media(max-width:900px){

    .top-section{

        grid-template-columns:1fr;

    }


    .welcome-card,
    .overview-card{

        height:auto;

        min-height:250px;

    }

}


@media(max-width:700px){

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

        text-align:center;

    }


    .sidebar-bottom{

        margin-top:25px;

    }


    .main{

        margin-left:0;

        padding:25px;

    }


    .header{

        padding:18px;

    }


    .profile-info h2{

        font-size:17px;

    }


    .uis-logo{

        width:45px;

    }


    .logout-section{

        margin-top:25px;

    }


    .logout-section a{

        text-align:center;

    }

}


@media(max-width:500px){

    .main{

        padding:15px;

    }


    .header{

        margin-bottom:20px;

    }


    .welcome-card{

        padding:25px;

    }


    .overview-card{

        padding:25px;

    }


    .info-card,
    .announcement{

        padding:25px;

    }


    .info-row{

        flex-direction:column;

        align-items:flex-start;

        gap:5px;

    }


    .menu a{

        min-width:100%;

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

        UIS Clinic Portal

    </div>


    <div class="sidebar-line"></div>


    <div class="menu">


        <a
            href="dashboard_clinic.php"
            class="active"
        >

            Dashboard

        </a>


        <a
            href="clinic_profile.php"
        >

            View Profile

        </a>


        <a
            href="submit_claim.php"
        >

            Submit Medical Claim

        </a>


        <a
            href="report.php"
        >

            Report

        </a>


    </div>


    <div class="logout-section">

        <a
            href="logout.php"
        >

            Logout

        </a>

    </div>


    <div class="sidebar-bottom">

        Need help?<br>

        Contact BHEP<br>

        Tel. No: 03-8911 7000 ext. 1118, 1117, 1434, 3337

    </div>


</div>


<div class="main">


    <div class="header">

        <div class="header-left">

            <img
                src="images/logoEMCS.png"
                class="emcs-logo"
                alt="EMCS Logo"
            >

            <div class="profile-info">

                <h2>
                    Hi, <?php echo htmlspecialchars($clinic_name); ?>!
                </h2>

                <p>
                    Clinic ID:
                    <?php echo htmlspecialchars($clinic_id); ?>
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

            Clinic Dashboard

        </h1>

        <p>

            Manage your clinic activities in the e-Medical Card system.

        </p>

    </div>


    <div class="top-section">


        <div class="welcome-card">


            <h2>

                Welcome to<br>

                e-Medical Card<br>

                Clinic Portal!

            </h2>


            <p>

                Thank you for providing healthcare services

                to UIS local students.

            </p>


        </div>


        <div class="overview-card">


            <h2>

                Clinic Overview

            </h2>


            <div class="overview-status">


                <h3>

                    Panel Clinic

                </h3>


                <p>

                    Your clinic is registered as a panel

                    clinic for UIS local students.

                </p>


            </div>


            <div class="overview-id">


                <h3>

                    Clinic ID

                </h3>


                <p>

                    <?php echo htmlspecialchars($clinic_id); ?>

                </p>


            </div>


        </div>


    </div>


    <div class="info-card">


        <h2>

            Clinic Information

        </h2>


        <div class="info-row">


            <span class="info-label">

                Clinic Name

            </span>


            <span class="info-value">

                <?php echo htmlspecialchars($clinic_name); ?>

            </span>


        </div>


        <div class="info-row">


            <span class="info-label">

                Clinic ID

            </span>


            <span class="info-value">

                <?php echo htmlspecialchars($clinic_id); ?>

            </span>


        </div>


        <div class="info-row">


            <span class="info-label">

                Account Type

            </span>


            <span class="info-value">

                Panel Clinic

            </span>


        </div>


    </div>


    <div class="announcement">


        <h2>

            Latest Announcement

        </h2>


        <hr>


        <p>

            • Welcome to the Clinic Dashboard.

        </p>


        <p>

            • Please submit student claims within 7 days.

        </p>


        <p>

            • Keep clinic information updated.

        </p>


        <p>

            • Contact BHEP for assistance if needed.

        </p>


    </div>


</div>


</body>

</html>