<?php

session_start();


// =========================
// CHECK CLINIC LOGIN
// =========================

if(!isset($_SESSION['clinic_id'])){

    header("Location: login_clinic.php");

    exit();

}


include("db_connect.php");


$clinic_id = $_SESSION['clinic_id'];

$clinic_name = isset($_SESSION['clinic_name'])
    ? $_SESSION['clinic_name']
    : "Clinic";


$sql = "SELECT 
            medical_claims.claim_id,
            medical_claims.student_id,
            medical_claims.claim_amount,
            medical_claims.visit_date,
            medical_claims.diagnosis_summary,
            medical_claims.claim_status,
            students.full_name,
            students.program

        FROM medical_claims

        INNER JOIN students

        ON medical_claims.student_id = students.student_id

        WHERE medical_claims.clinic_id = ?

        ORDER BY medical_claims.visit_date DESC";


$stmt = $conn->prepare($sql);

$stmt->execute([$clinic_id]);

$records = $stmt->fetchAll(PDO::FETCH_ASSOC);


$totalRecords = count($records);

$totalClaimAmount = 0;

foreach($records as $record){

    $totalClaimAmount += (float)$record['claim_amount'];

    $status = strtolower(
        trim($record['claim_status'])
    );


    if($status == "pending"){

        $pendingClaims++;

    }

    elseif($status == "approved"){

        $approvedClaims++;

    }

    elseif($status == "rejected"){

        $rejectedClaims++;

    }

}


$reportDate = date("d/m/Y");


?>

<!DOCTYPE html>

<html>

<head>

<title>Clinic Medical Report</title>


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


.clinic-icon{

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


.report-card{

    background:white;

    border-radius:15px;

    padding:30px;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

    margin-bottom:25px;

}


.report-info{

    display:flex;

    justify-content:space-between;

    align-items:center;

    background:#f2f2f2;

    padding:18px 20px;

    border-radius:10px;

    margin-bottom:25px;

}


.report-info-left{

    color:#555;

    font-size:13px;

    line-height:1.8;

}


.report-info-left strong{

    color:#2f6468;

}


.report-info-right{

    color:#555;

    font-size:13px;

    text-align:right;

    line-height:1.8;

}


.report-info-right strong{

    color:#2f6468;

}


.summary-title{

    color:#2f6468;

    font-size:18px;

    margin-bottom:15px;

}


.summary-grid{

    display:grid;

    grid-template-columns:repeat(2, 1fr);

    gap:15px;

    margin-bottom:30px;

}


.summary-box{

    background:#f8f8f8;

    border-radius:12px;

    padding:18px 15px;

    text-align:center;

    border:1px solid #eeeeee;

}


.summary-box h3{

    color:#2f6468;

    font-size:22px;

    margin-bottom:7px;

}


.summary-box p{

    color:#777;

    font-size:12px;

}


.summary-box.total{

    background:#e5f4ef;

    border-color:#d1ebe4;

}


.report-buttons{

    display:flex;

    justify-content:flex-end;

    align-items:center;

    margin-bottom:20px;

}


.print-btn{

    text-decoration:none;

    border:none;

    padding:14px 25px;

    border-radius:30px;

    font-size:14px;

    font-weight:bold;

    cursor:pointer;

    transition:0.2s;

}


.print-btn{

    background:#2f6468;

    color:white;

}


.print-btn:hover{

    background:#234d50;

}


.table-title{

    color:#2f6468;

    font-size:18px;

    margin-bottom:15px;

}


.table-container{

    width:100%;

    overflow-x:auto;

}


table{

    width:100%;

    border-collapse:collapse;

    min-width:1000px;

}


th{

    background:#2f6468;

    color:white;

    padding:13px 10px;

    text-align:center;

    font-size:13px;

}


td{

    padding:12px 10px;

    border:1px solid #ddd;

    text-align:center;

    color:#444;

    font-size:13px;

    background:white;

}


tr:nth-child(even) td{

    background:#f8f8f8;

}


tr:hover td{

    background:#f0f7f6;

}


.status{

    display:inline-block;

    padding:6px 12px;

    border-radius:20px;

    font-size:12px;

    font-weight:bold;

}


.no-record{

    text-align:center;

    padding:45px 20px;

}


.no-record-icon{

    width:55px;

    height:55px;

    margin:0 auto 15px;

    border-radius:50%;

    background:#f2f2f2;

    color:#7f8c8d;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:25px;

    font-weight:bold;

}


.no-record h3{

    color:#2f6468;

    font-size:18px;

    margin-bottom:8px;

}


.no-record p{

    color:#777;

    font-size:13px;

}


.report-note{

    margin-top:20px;

    padding:14px 16px;

    background:#f2f2f2;

    border-radius:10px;

    color:#777;

    font-size:12px;

    line-height:1.5;

}


.report-note strong{

    color:#2f6468;

}


@media(max-width:1000px){

    .summary-grid{

        grid-template-columns:repeat(3, 1fr);

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


    .report-card{

        padding:20px;

    }


    .report-info{

        flex-direction:column;

        align-items:flex-start;

        gap:10px;

    }


    .report-info-right{

        text-align:left;

    }


    .summary-grid{

        grid-template-columns:repeat(2, 1fr);

    }

}


@media(max-width:500px){

    .menu a{

        min-width:100%;

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


    .summary-grid{

        grid-template-columns:1fr;

    }


    .report-buttons{

        flex-direction:column;

        gap:12px;

        align-items:stretch;

    }


    .print-btn{

        text-align:center;

        width:100%;

    }

}


@media print{

    @page{

        size:A4;

        margin:14mm 12mm 16mm 12mm;

    }


    *{

        -webkit-print-color-adjust:exact !important;

        print-color-adjust:exact !important;

    }

    html,
    body{

        background:white !important;

        min-height:auto;

    }


    body{

        font-size:11px;

        color:#222;

    }

    .sidebar,
    .report-buttons{

        display:none !important;

    }


    .main{

        margin-left:0 !important;

        padding:0 !important;

        width:100% !important;

    }


    .header{

        box-shadow:none !important;

        border:none !important;

        border-radius:0 !important;

        margin:0 0 14px !important;

        padding:0 0 10px !important;

        border-bottom:2px solid #2f6468 !important;

    }


    .header-left{

        gap:10px;

    }


    .header-left img{

        width:42px !important;

        height:42px !important;

        object-fit:contain;

    }


    .header-title{

        font-size:18px !important;

        color:#2f6468 !important;

    }


    .header-subtitle{

        font-size:10px !important;

    }


    .uis-logo{

        width:55px !important;

        height:auto !important;

    }


    .page-title{

        margin:12px 0 14px !important;

        padding:0 !important;
        
    }


    .page-title h1{

        font-size:22px !important;

        color:#2f6468 !important;

        margin-bottom:4px !important;

    }


    .page-title p{

        font-size:10px !important;

        color:#666 !important;

    }


    .report-card{

        box-shadow:none !important;

        border:none !important;

        padding:0 !important;

        background:white !important;

    }


    .report-info{

        background:#f7fbfa !important;

        border:1px solid #cfe3e0 !important;

        border-radius:8px !important;

        padding:11px 13px !important;

        margin-bottom:12px !important;

        page-break-inside:avoid;

    }


    .report-info h2{

        font-size:13px !important;

        color:#2f6468 !important;

        margin-bottom:7px !important;

    }


    .report-info p,
    .report-info span{

        font-size:10px !important;

    }


    .summary-grid{

        display:grid !important;

        grid-template-columns:1fr 1fr !important;

        gap:10px !important;

        margin:0 0 14px !important;

        page-break-inside:avoid;

    }


    .summary-box{

        background:#f7fbfa !important;

        border:1px solid #cfe3e0 !important;

        border-radius:8px !important;

        padding:10px !important;

        min-height:52px !important;

    }


    .summary-box.total{

        background:#e8f5f2 !important;

        border-color:#c8e1dc !important;

    }


    .summary-box h3{

        font-size:16px !important;

        color:#2f6468 !important;

        margin-bottom:4px !important;

    }


    .summary-box p{

        font-size:9px !important;

        color:#666 !important;

    }


    table{

        width:100% !important;

        min-width:0 !important;

        border-collapse:collapse !important;

        table-layout:fixed !important;

        font-size:9px !important;

        margin-top:8px !important;

    }


    thead{

        display:table-header-group;

    }


    tr{

        page-break-inside:avoid;

    }


    th{

        background:#2f6468 !important;

        color:white !important;

        font-size:9px !important;

        font-weight:700 !important;

        padding:7px 5px !important;

        border:1px solid #2f6468 !important;

        vertical-align:middle !important;

    }


    td{

        font-size:9px !important;

        padding:7px 5px !important;

        border:1px solid #d7e2e0 !important;

        vertical-align:top !important;

        line-height:1.35 !important;

        word-wrap:break-word !important;

        overflow-wrap:anywhere !important;

    }


    tbody tr:nth-child(even) td{

        background:#f7fbfa !important;

    }


    .status{

        display:inline-block !important;

        background:#e4f3ee !important;

        color:#28665d !important;

        border:1px solid #bdddd5 !important;

        padding:3px 7px !important;

        border-radius:10px !important;

        font-size:8px !important;

        font-weight:700 !important;

        white-space:nowrap !important;

    }


    .report-note{

        background:#f7fbfa !important;

        border:1px solid #cfe3e0 !important;

        border-radius:8px !important;

        padding:9px 11px !important;

        margin-top:12px !important;

        page-break-inside:avoid;

    }


    .report-note p,
    .report-note li{

        font-size:8.5px !important;

        line-height:1.4 !important;

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


        <a href="dashboard_clinic.php">

            Dashboard

        </a>


        <a href="update_clinic_info.php">

            View Profile

        </a>


        <a href="submit_claim.php">

            Submit Medical Claim

        </a>


        <a href="report.php" class="active">

            Report

        </a>


    </div>


    <div class="logout-section">

        <a href="logout.php">

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

        <div class="profile">

            <img

                src="images/logoEMCS.png"

                class="clinic-icon"

                alt="EMCS Logo"

            >

            <div class="profile-info">

                <h2>
                    <?php echo htmlspecialchars($clinic_name); ?>
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

            Student Medical Report

        </h1>

        <p>

            View the medical records and claims submitted by your clinic.

        </p>

    </div>


    <div class="report-card">


        <div class="report-info">

            <div class="report-info-left">

                <strong>

                    Clinic:

                </strong>

                <?php

                echo htmlspecialchars($clinic_name);

                ?>

                <br>

                <strong>

                    Clinic ID:

                </strong>

                <?php

                echo htmlspecialchars($clinic_id);

                ?>

            </div>


            <div class="report-info-right">

                <strong>

                    Report Date:

                </strong>

                <?php

                echo $reportDate;

                ?>

                <br>

                <strong>

                    Total Records:

                </strong>

                <?php

                echo $totalRecords;

                ?>

            </div>

        </div>


        <h2 class="summary-title">

            Report Summary

        </h2>


        <div class="summary-grid">


            <div class="summary-box total">

                <h3>

                    <?php

                    echo $totalRecords;

                    ?>

                </h3>

                <p>

                    Total Records

                </p>

            </div>



            <div class="summary-box">

                <h3>

                    RM

                    <?php

                    echo number_format(

                        $totalClaimAmount,

                        2

                    );

                    ?>

                </h3>

                <p>

                    Total Claim Amount

                </p>

            </div>


        </div>


        <div class="report-buttons">


            <button

                type="button"

                onclick="window.print()"

                class="print-btn"

            >

                Print Report

            </button>


        </div>


        <h2 class="table-title">

            Medical Claim Records

        </h2>


        <?php if(count($records) > 0){ ?>


        <div class="table-container">


            <table>


                <thead>

                <tr>

                    <th>

                        No.

                    </th>

                    <th>

                        Student ID

                    </th>

                    <th>

                        Student Name

                    </th>

                    <th>

                        Program

                    </th>

                    <th>

                        Visit Date

                    </th>

                    <th>

                        Diagnosis / Medical Record

                    </th>

                    <th>

                        Claim Amount

                    </th>

                    <th>

                        Claim Status

                    </th>

                </tr>

                </thead>


                <tbody>


                <?php

                $no = 1;


                foreach($records as $record){

                ?>


                <tr>


                    <td>

                        <?php

                        echo $no++;

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(

                            $record['student_id']

                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(

                            $record['full_name']

                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(

                            $record['program']

                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(

                            $record['visit_date']

                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(

                            $record['diagnosis_summary']

                        );

                        ?>

                    </td>


                    <td>

                        RM

                        <?php

                        echo number_format(

                            $record['claim_amount'],

                            2

                        );

                        ?>

                    </td>


                    <td>


                        <?php

                        $status = strtolower(

                            trim(

                                $record['claim_status']

                            )

                        );


                        if($status == "pending"){

                            $statusClass = "status-pending";

                        }

                        elseif($status == "approved"){

                            $statusClass = "status-approved";

                        }

                        elseif($status == "rejected"){

                            $statusClass = "status-rejected";

                        }

                        else{

                            $statusClass = "";

                        }

                        ?>


                        <span

                            class="status <?php echo $statusClass; ?>"

                        >

                            <?php

                            echo htmlspecialchars(

                                $record['claim_status']

                            );

                            ?>

                        </span>


                    </td>


                </tr>


                <?php

                }

                ?>


                </tbody>


            </table>


        </div>


        <?php }else{ ?>


        <div class="no-record">


            <div class="no-record-icon">

                !

            </div>


            <h3>

                No Student Medical Records

            </h3>


            <p>

                No students have submitted medical claims

                through this clinic yet.

            </p>


        </div>


        <?php } ?>


        <div class="report-note">


            <strong>

                Report Information:

            </strong>


            This report displays the medical claims submitted

            through your clinic. Make sure the information is

            reviewed before printing the report.


        </div>


    </div>


</div>


</body>

</html>