<?php

session_start();

if(!isset($_SESSION['student_id'])){

    header("Location: login_student.php");
    exit();

}


include("db_connect.php");

$studentID = $_SESSION['student_id'];


$sql = "SELECT * FROM students WHERE student_id = ?";

$stmt = $conn->prepare($sql);

$stmt->execute([$studentID]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$student){

    echo "Student information not found.";
    exit();

}


$studentName = $student['full_name'] ?? '';

$balance = $student['current_balance'] ?? 0;

$status = $student['status'] ?? '';


$sql = "SELECT 
            mc.claim_id,
            mc.visit_date,
            mc.claim_amount,
            mc.diagnosis_summary,
            mc.claim_status,
            c.clinic_name

        FROM medical_claims mc

        LEFT JOIN clinics c
            ON mc.clinic_id = c.clinic_id

        WHERE mc.student_id = ?

        ORDER BY mc.visit_date DESC, mc.claim_id DESC

        LIMIT 3";


$stmt = $conn->prepare($sql);

$stmt->execute([$studentID]);

$recentVisits = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>

<html>

<head>

<title>e-Medical Card</title>

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


.top-info{
    display:grid;

    grid-template-columns:1fr 1fr;

    gap:20px;

    margin-bottom:20px;
}


.status-card{
    background:white;

    border-radius:15px;

    padding:25px;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);
}


.card-title{
    color:#2f6468;

    font-size:15px;

    font-weight:bold;

    margin-bottom:15px;
}


.status{
    display:inline-block;

    background:#f2f2f2;

    color:#2f6468;

    padding:7px 15px;

    border-radius:20px;

    font-size:13px;

    font-weight:bold;

    margin-bottom:10px;
}


.status-text{
    color:#777;

    font-size:13px;
}


.balance-card{
    background:#2f6468;

    border-radius:15px;

    padding:25px;

    color:white;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);
}


.balance-card .card-title{
    color:white;
}


.balance{
    font-size:34px;

    font-weight:bold;

    margin-bottom:8px;
}


.balance-description{
    font-size:13px;

    opacity:0.9;
}

.info-card{
    background:white;

    border-radius:15px;

    padding:25px;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

    margin-bottom:20px;
}


.info-card h2{
    color:#2f6468;

    font-size:19px;

    margin-bottom:18px;
}


.info-row{
    display:flex;

    justify-content:space-between;

    padding:13px 0;

    border-bottom:1px solid #f2f2f2;
}


.info-row:last-child{
    border-bottom:none;
}


.info-label{
    color:#777;

    font-size:13px;
}


.info-value{
    color:#2f6468;

    font-size:13px;

    font-weight:bold;

    text-align:right;

    max-width:65%;
}


.recent-card{
    background:white;

    border-radius:15px;

    padding:25px;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);
}


.recent-card h2{
    color:#2f6468;

    font-size:19px;

    margin-bottom:18px;
}


.visit-item{
    background:white;

    border:1px solid #eeeeee;

    border-radius:10px;

    padding:16px;

    margin-bottom:12px;

    transition:0.2s;
}


.visit-item:hover{
    border-color:#c9dddd;

    box-shadow:0 2px 6px rgba(0,0,0,0.05);
}


.visit-item.latest{
    background:#eef8f7;

    border:2px solid #5d8d87;

    box-shadow:0 3px 10px rgba(47,100,104,0.12);

    position:relative;
}


.latest-label{
    display:inline-block;

    background:#2f6468;

    color:white;

    padding:5px 10px;

    border-radius:15px;

    font-size:10px;

    font-weight:bold;

    margin-bottom:10px;
}


.visit-top{
    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:10px;
}


.visit-date{
    color:#777;

    font-size:12px;

    font-weight:bold;
}


.clinic-name{
    color:#2f6468;

    font-size:14px;

    font-weight:bold;

    text-align:right;
}


.diagnosis{
    color:#666;

    font-size:13px;

    line-height:1.5;

    margin-bottom:12px;
}


.visit-bottom{
    display:flex;

    justify-content:space-between;

    align-items:center;

    padding-top:10px;

    border-top:1px solid #f2f2f2;
}


.claim-amount{
    color:#2f6468;

    font-size:14px;

    font-weight:bold;
}


.claim-status{
    padding:6px 12px;

    border-radius:20px;

    font-size:11px;

    font-weight:bold;
}


.claim-status.approved{
    background:#e7f6ec;

    color:#218739;
}


.claim-status.pending{
    background:#fff4dc;

    color:#c47a00;
}


.claim-status.rejected{
    background:#fde8e8;

    color:#c62828;
}


.no-visit{
    background:#f2f2f2;

    padding:18px;

    border-radius:8px;

    text-align:center;

    color:#777;

    font-size:13px;
}


.history-link{
    display:block;

    margin-top:18px;

    color:#4b8d95;

    text-decoration:none;

    text-align:right;

    font-size:13px;

    font-weight:bold;
}


.history-link:hover{
    color:#234d50;

    text-decoration:underline;
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

        text-align:left;
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

    .top-info{
        grid-template-columns:1fr;
    }


    .header{
        padding:18px;
    }


    .uis-logo{
        width:50px;
    }


    .visit-top{
        flex-direction:column;

        align-items:flex-start;

        gap:5px;
    }


    .clinic-name{
        text-align:left;
    }


    .info-row{
        flex-direction:column;

        gap:5px;
    }


    .info-value{
        text-align:left;

        max-width:100%;
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


        <a href="dashboard.php" class="active">

            Dashboard

        </a>


        <a href="student_profile.php">

            View Profile

        </a>


        <a href="claim_history.php">

            Track Claim History

        </a>


        <a href="panel_clinic.php">

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

                    Hi, <?php echo htmlspecialchars($studentName); ?>!

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

            e-Medical Card

        </h1>


        <p>

            Your medical information at a glance.

        </p>


    </div>


    <div class="top-info">


        <div class="status-card">


            <div class="card-title">

                Medical Card Status

            </div>


            <div class="status">

                <?php echo htmlspecialchars($status); ?>

            </div>


            <div class="status-text">

                Your medical card is currently

                <?php echo strtolower(htmlspecialchars($status)); ?>.

            </div>


        </div>


        <div class="balance-card">


            <div class="card-title">

                Remaining Balance

            </div>


            <div class="balance">

                RM <?php echo number_format((float)$balance, 2); ?>

            </div>


            <div class="balance-description">

                Available for medical claims

            </div>


        </div>


    </div>


    <div class="info-card">


        <h2>

            Student Information

        </h2>


        <div class="info-row">


            <span class="info-label">

                Student ID

            </span>


            <span class="info-value">

                <?php echo htmlspecialchars($student['student_id'] ?? ''); ?>

            </span>


        </div>


        <div class="info-row">


            <span class="info-label">

                Full Name

            </span>


            <span class="info-value">

                <?php echo htmlspecialchars($student['full_name'] ?? ''); ?>

            </span>


        </div>


        <div class="info-row">


            <span class="info-label">

                Program

            </span>


            <span class="info-value">

                <?php echo htmlspecialchars($student['program'] ?? ''); ?>

            </span>


        </div>


        <div class="info-row">

            <span class="info-label">

                Semester

            </span>

            <span class="info-value">

                <?php echo htmlspecialchars($student['semester'] ?? ''); ?>

            </span>

        </div>

    </div>


    <div class="recent-card">


        <h2>

            Recent Clinic Visits

        </h2>


        <?php if(count($recentVisits) > 0): ?>


            <div class="visit-list">


                <?php foreach($recentVisits as $loopIndex => $visit): ?>


                    <div class="visit-item <?php echo ($loopIndex === 0) ? 'latest' : ''; ?>">


                        <?php if($loopIndex === 0): ?>

                            <div class="latest-label">

                                LATEST VISIT

                            </div>

                        <?php endif; ?>


                        <div class="visit-top">


                            <span class="visit-date">

                                <?php

                                if(!empty($visit['visit_date'])){

                                    echo date(
                                        "d M Y",
                                        strtotime($visit['visit_date'])
                                    );

                                }else{

                                    echo "Date not available";

                                }

                                ?>

                            </span>


                            <span class="clinic-name">

                                <?php

                                echo htmlspecialchars(
                                    $visit['clinic_name'] ?? 'Unknown Clinic'
                                );

                                ?>

                            </span>


                        </div>


                        <div class="diagnosis">


                            <?php

                            $diagnosis = trim(
                                $visit['diagnosis_summary'] ?? ''
                            );


                            if($diagnosis == ''){

                                echo "No diagnosis summary available.";

                            }else{


                                if(strlen($diagnosis) > 80){

                                    echo htmlspecialchars(
                                        substr($diagnosis, 0, 80)
                                    ) . "...";

                                }else{

                                    echo htmlspecialchars($diagnosis);

                                }

                            }

                            ?>


                        </div>


                        <div class="visit-bottom">


                            <span class="claim-amount">

                                RM

                                <?php

                                echo number_format(
                                    (float)($visit['claim_amount'] ?? 0),
                                    2
                                );

                                ?>


                            </span>


                            <?php

                            $claimStatus =
                                $visit['claim_status'] ?? 'Pending';

                            $statusClass =
                                strtolower($claimStatus);

                            ?>


                            <span class="claim-status <?php echo htmlspecialchars($statusClass); ?>">


                                <?php

                                echo htmlspecialchars($claimStatus);

                                ?>


                            </span>


                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


            <a
                href="claim_history.php"
                class="history-link"
            >

                View Claim History →

            </a>


        <?php else: ?>


            <div class="no-visit">

                No recent clinic visits.

            </div>


        <?php endif; ?>


    </div>


</div>


</body>

</html>