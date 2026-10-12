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


$sql = "

SELECT

    medical_claims.claim_id,

    medical_claims.claim_amount,

    medical_claims.visit_date,

    medical_claims.diagnosis_summary,

    medical_claims.claim_status,

    clinics.clinic_name

FROM medical_claims

INNER JOIN clinics

    ON medical_claims.clinic_id = clinics.clinic_id

WHERE medical_claims.student_id = ?

ORDER BY medical_claims.visit_date DESC

";


$stmt = $conn->prepare($sql);

$stmt->execute([$studentID]);

$claims = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>

<html>

<head>

<title>Claim History</title>

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


.claim-card{

    background:white;

    border-radius:15px;

    padding:25px;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

    margin-bottom:20px;

}


.claim-card h2{

    color:#2f6468;

    font-size:19px;

    margin-bottom:18px;

}


.table-wrapper{

    width:100%;

    overflow-x:auto;

}


table{

    width:100%;

    border-collapse:collapse;

    min-width:750px;

}


th{

    background:#2f6468;

    color:white;

    padding:14px 12px;

    font-size:13px;

    text-align:center;

}


th:first-child{

    border-radius:8px 0 0 8px;

}


th:last-child{

    border-radius:0 8px 8px 0;

}


td{

    padding:15px 12px;

    text-align:center;

    border-bottom:1px solid #f0f0f0;

    color:#555;

    font-size:13px;

}


tr:last-child td{

    border-bottom:none;

}


tr:hover td{

    background:#fafafa;

}


.amount{

    color:#2f6468;

    font-weight:bold;

}


.status{

    display:inline-block;

    padding:7px 14px;

    border-radius:20px;

    font-size:11px;

    font-weight:bold;

    background:#f2f2f2;

    color:#2f6468;

}


.status.approved{

    background:#e5f4ef;

    color:#28745e;

}


.status.pending{

    background:#fff4df;

    color:#9a6b20;

}


.status.rejected{

    background:#fbe8e8;

    color:#a04444;

}


.no-claim{

    background:#f2f2f2;

    padding:18px;

    border-radius:8px;

    text-align:center;

    color:#777;

    font-size:13px;

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

    .claim-card{

        padding:18px;

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

        <a href="claim_history.php" class="active">

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

            Medical Claim History

        </h1>

        <p>

            View your previous clinic visits and claim information.

        </p>

    </div>

    <div class="claim-card">

        <h2>

            Claim History

        </h2>

        <?php if(count($claims) > 0){ ?>

        <div class="table-wrapper">

            <table>

                <tr>

                    <th>

                        Clinic Name

                    </th>

                    <th>

                        Claim Amount

                    </th>

                    <th>

                        Visit Date

                    </th>

                    <th>

                        Diagnosis

                    </th>

                    <th>

                        Claim Status

                    </th>

                </tr>

                <?php foreach($claims as $claim){ ?>

                <tr>

                    <td>

                        <?php

                        echo htmlspecialchars(
                            $claim['clinic_name']
                        );

                        ?>

                    </td>

                    <td class="amount">

                        RM

                        <?php

                        echo number_format(
                            $claim['claim_amount'],
                            2
                        );

                        ?>

                    </td>

                    <td>

                        <?php

                        echo htmlspecialchars(
                            $claim['visit_date']
                        );

                        ?>

                    </td>

                    <td>

                        <?php

                        echo htmlspecialchars(
                            $claim['diagnosis_summary']
                        );

                        ?>

                    </td>

                    <td>

                        <?php

                        $status = strtolower(
                            trim($claim['claim_status'])
                        );

                        $statusClass = '';

                        if($status == 'approved'){

                            $statusClass = 'approved';

                        }

                        elseif($status == 'pending'){

                            $statusClass = 'pending';

                        }

                        elseif($status == 'rejected'){

                            $statusClass = 'rejected';

                        }

                        ?>

                        <span class="status <?php echo $statusClass; ?>">

                            <?php

                            echo htmlspecialchars(
                                $claim['claim_status']
                            );

                            ?>

                        </span>

                    </td>

                </tr>

                <?php } ?>

            </table>

        </div>

        <?php } else { ?>

        <div class="no-claim">

            No claim history found.

        </div>

        <?php } ?>

    </div>

</div>

</body>

</html>