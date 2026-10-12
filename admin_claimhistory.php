<?php

session_start();

include("db_connect.php");

if(!isset($_SESSION['admin_id'])){

    header("Location: login_admin.php");

    exit();

}

if(!isset($_GET['student_id']) || empty($_GET['student_id'])){

    header("Location: view_student.php");

    exit();

}


$studentID = trim($_GET['student_id']);

$sqlStudent = "

SELECT
    student_id,
    full_name,
    ic_number,
    email,
    phone_number,
    program,
    status,
    address,
    current_balance

FROM students

WHERE student_id = ?

";


$stmtStudent = $conn->prepare($sqlStudent);

$stmtStudent->execute([$studentID]);

$student = $stmtStudent->fetch(PDO::FETCH_ASSOC);


if(!$student){

    header("Location: view_student.php");

    exit();

}


$sql = "

SELECT

    medical_claims.claim_id,
    medical_claims.claim_amount,
    medical_claims.visit_date,
    medical_claims.diagnosis_summary,
    medical_claims.claim_status,
    clinics.clinic_name

FROM medical_claims

LEFT JOIN clinics

ON medical_claims.clinic_id = clinics.clinic_id

WHERE medical_claims.student_id = ?

ORDER BY medical_claims.visit_date DESC

";


$stmt = $conn->prepare($sql);

$stmt->execute([$studentID]);

$claims = $stmt->fetchAll(PDO::FETCH_ASSOC);


$totalClaimAmount = 0;

foreach($claims as $claim){

    if(strtolower(trim($claim['claim_status'])) === 'approved'){

        $totalClaimAmount += (float)$claim['claim_amount'];

    }

}


$approvedClaims = 0;

foreach($claims as $claim){

    if(strtolower(trim($claim['claim_status'])) === 'approved'){

        $approvedClaims++;

    }

}


?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Medical Claim History - EMCS</title>

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

    padding:30px 20px;

}


.container{

    width:100%;

    max-width:1100px;

    margin:0 auto;

    background:white;

    padding:35px 40px;

    border-radius:20px;

    box-shadow:0 8px 25px rgba(0,0,0,0.20);

}


.header{

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding-bottom:25px;

    margin-bottom:25px;

    border-bottom:1px solid #eeeeee;

}


.header-left{

    display:flex;

    align-items:center;

}


.emcs-logo{

    width:75px;

    height:75px;

    object-fit:contain;

    margin-right:15px;

}


.uis-logo{

    width:55px;

    height:auto;

    object-fit:contain;

}


.header h1{

    color:#2f6468;

    font-size:28px;

    margin-bottom:4px;

}


.header p{

    color:#777;

    font-size:13px;

    letter-spacing:2px;

}


.page-title{

    margin-bottom:22px;

}


.page-title h2{

    color:#2f6468;

    font-size:22px;

    margin-bottom:6px;

}


.page-title p{

    color:#777;

    font-size:13px;

}


.student-card{

    background:#f2f2f2;

    border-radius:15px;

    padding:22px;

    margin-bottom:25px;

    border-left:5px solid #2f6468;

}


.student-card-title{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:18px;

}


.student-card-title h3{

    color:#2f6468;

    font-size:18px;

}


.student-id{

    background:#2f6468;

    color:white;

    padding:7px 14px;

    border-radius:20px;

    font-size:12px;

    font-weight:bold;

}


.student-details{

    display:grid;

    grid-template-columns:repeat(3,1fr);

    gap:15px;

}


.detail-box{

    background:white;

    padding:13px 15px;

    border-radius:10px;

    border:1px solid #e5e5e5;

}


.detail-label{

    color:#999;

    font-size:11px;

    margin-bottom:5px;

}


.detail-value{

    color:#444;

    font-size:13px;

    font-weight:bold;

    word-break:break-word;

}


.summary{

    display:grid;

    grid-template-columns:repeat(3,1fr);

    gap:15px;

    margin-bottom:25px;

}


.summary-box{

    background:#f8f8f8;

    padding:18px;

    border-radius:12px;

    text-align:center;

    border:1px solid #eeeeee;

}


.summary-number{

    color:#2f6468;

    font-size:22px;

    font-weight:bold;

    margin-bottom:5px;

}


.summary-label{

    color:#777;

    font-size:12px;

}


.section-title{

    color:#2f6468;

    font-size:18px;

    margin-bottom:15px;

}


.claim-card{

    background:white;

    border:1px solid #e2e2e2;

    border-radius:15px;

    padding:20px;

    margin-bottom:15px;

    box-shadow:0 3px 10px rgba(0,0,0,0.06);

    transition:0.2s;

}


.claim-card:hover{

    box-shadow:0 5px 15px rgba(0,0,0,0.10);

    transform:translateY(-2px);

}


.claim-header{

    display:flex;

    justify-content:space-between;

    align-items:flex-start;

    margin-bottom:17px;

    padding-bottom:13px;

    border-bottom:1px solid #eeeeee;

}


.claim-number{

    color:#2f6468;

    font-size:15px;

    font-weight:bold;

}


.status{

    display:inline-block;

    padding:6px 13px;

    border-radius:20px;

    font-size:11px;

    font-weight:bold;

}


.pending{

    background:#fff3cd;

    color:#856404;

}


.approved{

    background:#d4edda;

    color:#155724;

}


.rejected{

    background:#f8d7da;

    color:#721c24;

}


.action-buttons{

    display:flex;

    gap:8px;

    margin-top:10px;

}


.approve-btn,
.reject-btn{

    display:inline-flex;

    align-items:center;

    justify-content:center;

    padding:7px 12px;

    border-radius:20px;

    text-decoration:none;

    font-size:11px;

    font-weight:bold;

    transition:0.2s;

}


.approve-btn{

    background:#d4edda;

    color:#155724;

}


.approve-btn:hover{

    background:#b9dfc1;

}


.reject-btn{

    background:#f8d7da;

    color:#721c24;

}


.reject-btn:hover{

    background:#f1bfc4;

}


.claim-details{

    display:grid;

    grid-template-columns:repeat(2,1fr);

    gap:15px;

}


.claim-detail{

    background:#f8f8f8;

    padding:13px 15px;

    border-radius:10px;

}


.claim-detail.full{

    grid-column:1 / -1;

}


.claim-label{

    color:#888;

    font-size:11px;

    margin-bottom:5px;

}


.claim-value{

    color:#444;

    font-size:13px;

    font-weight:bold;

}


.claim-amount{

    color:#2f6468;

    font-size:18px;

    font-weight:bold;

}


.no-claim{

    background:#f8f8f8;

    border:1px solid #eeeeee;

    border-radius:15px;

    padding:45px 20px;

    text-align:center;

}


.no-claim-icon{

    width:60px;

    height:60px;

    margin:0 auto 15px;

    border-radius:50%;

    background:#e8eeee;

    color:#7f8c8d;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:25px;

}


.no-claim h3{

    color:#2f6468;

    font-size:18px;

    margin-bottom:7px;

}


.no-claim p{

    color:#777;

    font-size:13px;

}


.button-area{

    display:flex;

    gap:12px;

    margin-top:25px;

    padding-top:22px;

    border-top:1px solid #eeeeee;

}


.back-btn{

    display:inline-flex;

    align-items:center;

    justify-content:center;

    padding:13px 25px;

    background:#7f8c8d;

    color:white;

    text-decoration:none;

    border-radius:30px;

    font-size:14px;

    font-weight:bold;

}


.back-btn:hover{

    background:#666;

}


.footer{

    text-align:center;

    margin-top:20px;

    color:#999;

    font-size:11px;

}


@media(max-width:800px){

    body{

        padding:20px 15px;

    }


    .container{

        padding:25px 20px;

    }


    .student-details{

        grid-template-columns:1fr 1fr;

    }


    .summary{

        grid-template-columns:1fr;

    }

}


@media(max-width:600px){

    .header h1{

        font-size:22px;

    }


    .student-card-title{

        flex-direction:column;

        align-items:flex-start;

        gap:10px;

    }


    .student-details{

        grid-template-columns:1fr;

    }


    .claim-details{

        grid-template-columns:1fr;

    }


    .claim-detail.full{

        grid-column:auto;

    }


    .claim-header{

        align-items:flex-start;

        gap:10px;

        flex-direction:column;

    }


    .action-buttons{

        width:100%;

    }


    .approve-btn,
    .reject-btn{

        flex:1;

    }


    .back-btn{

        width:100%;

    }


    .emcs-logo{

        width:65px;

        height:65px;

        margin-right:10px;

    }


    .uis-logo{

        width:45px;

    }

}


</style>

</head>

<body>

<div class="container">


<div class="header">

    <div class="header-left">

        <img src="images/logoEMCS.png" class="emcs-logo">

        <div>

            <h1>E-MEDICAL CARD</h1>

            <p>STUDENT CLAIM MANAGEMENT</p>

        </div>

    </div>


    <img src="images/logouis.png" class="uis-logo">

</div>


<div class="page-title">

    <h2>Medical Claim History</h2>

    <p>View the medical claim records for this student.</p>

</div>


<div class="student-card">

    <div class="student-card-title">

        <h3>Student Information</h3>

        <div class="student-id">

            <?php echo htmlspecialchars($student['student_id']); ?>

        </div>

    </div>


    <div class="student-details">


        <div class="detail-box">

            <div class="detail-label">

                FULL NAME

            </div>

            <div class="detail-value">

                <?php echo htmlspecialchars($student['full_name']); ?>

            </div>

        </div>


        <div class="detail-box">

            <div class="detail-label">

                IC NUMBER

            </div>

            <div class="detail-value">

                <?php echo htmlspecialchars($student['ic_number']); ?>

            </div>

        </div>


        <div class="detail-box">

            <div class="detail-label">

                PROGRAM

            </div>

            <div class="detail-value">

                <?php echo htmlspecialchars($student['program']); ?>

            </div>

        </div>


        <div class="detail-box">

            <div class="detail-label">

                EMAIL

            </div>

            <div class="detail-value">

                <?php echo htmlspecialchars($student['email']); ?>

            </div>

        </div>


        <div class="detail-box">

            <div class="detail-label">

                PHONE NUMBER

            </div>

            <div class="detail-value">

                <?php echo htmlspecialchars($student['phone_number'] ?? 'Not provided'); ?>

            </div>

        </div>


        <div class="detail-box">

            <div class="detail-label">

                ADDRESS

            </div>

            <div class="detail-value">

                <?php echo htmlspecialchars($student['address'] ?? 'Not provided'); ?>

            </div>

        </div>


    </div>

</div>


<div class="summary">


    <div class="summary-box">

        <div class="summary-number">

            <?php echo count($claims); ?>

        </div>

        <div class="summary-label">

            Total Claims

        </div>

    </div>


    <div class="summary-box">

        <div class="summary-number">

            RM <?php echo number_format($totalClaimAmount,2); ?>

        </div>

        <div class="summary-label">

            Total Approved Claim Amount

        </div>

    </div>


    <div class="summary-box">

        <div class="summary-number">

            RM <?php echo number_format((float)$student['current_balance'],2); ?>

        </div>

        <div class="summary-label">

            Current Balance

        </div>

    </div>


</div>


<h3 class="section-title">

    Claim Records

</h3>


<?php if(count($claims) > 0){ ?>


    <?php foreach($claims as $claim){ ?>


        <?php

        $status = strtolower(trim($claim['claim_status']));

        ?>


        <div class="claim-card">


            <div class="claim-header">


                <div class="claim-number">

                    Claim #<?php echo htmlspecialchars($claim['claim_id']); ?>

                </div>


                <div>


                    <span class="status <?php echo htmlspecialchars($status); ?>">

                        <?php echo htmlspecialchars($claim['claim_status']); ?>

                    </span>


                    <?php if($status === 'pending'){ ?>


                        <div class="action-buttons">


                            <a

                                href="update_claim_status.php?claim_id=<?php echo urlencode($claim['claim_id']); ?>&action=approve"

                                class="approve-btn"

                                onclick="return confirm('Are you sure you want to approve this claim?');"

                            >

                                ✓ Approve

                            </a>


                            <a

                                href="update_claim_status.php?claim_id=<?php echo urlencode($claim['claim_id']); ?>&action=reject"

                                class="reject-btn"

                                onclick="return confirm('Are you sure you want to reject this claim?');"

                            >

                                ✕ Reject

                            </a>


                        </div>


                    <?php } ?>


                </div>


            </div>


            <div class="claim-details">


                <div class="claim-detail">

                    <div class="claim-label">

                        CLINIC

                    </div>

                    <div class="claim-value">

                        <?php

                        echo htmlspecialchars(

                            $claim['clinic_name'] ?? 'Unknown Clinic'

                        );

                        ?>

                    </div>

                </div>


                <div class="claim-detail">

                    <div class="claim-label">

                        VISIT DATE

                    </div>

                    <div class="claim-value">

                        <?php echo htmlspecialchars($claim['visit_date']); ?>

                    </div>

                </div>


                <div class="claim-detail">

                    <div class="claim-label">

                        CLAIM AMOUNT

                    </div>

                    <div class="claim-amount">

                        RM <?php echo number_format((float)$claim['claim_amount'],2); ?>

                    </div>

                </div>


                <div class="claim-detail">

                    <div class="claim-label">

                        STATUS

                    </div>

                    <div class="claim-value">

                        <?php echo htmlspecialchars($claim['claim_status']); ?>

                    </div>

                </div>


                <div class="claim-detail full">

                    <div class="claim-label">

                        DIAGNOSIS / SUMMARY

                    </div>

                    <div class="claim-value">

                        <?php

                        echo htmlspecialchars(

                            $claim['diagnosis_summary'] ?? 'No diagnosis information.'

                        );

                        ?>

                    </div>

                </div>


            </div>


        </div>


    <?php } ?>


<?php }else{ ?>


    <div class="no-claim">


        <div class="no-claim-icon">

            +

        </div>


        <h3>

            No Claim History

        </h3>


        <p>

            This student does not have any medical claim records yet.

        </p>


    </div>


<?php } ?>


<div class="button-area">

    <a href="view_student.php" class="back-btn">

        ← Back To Student List

    </a>

</div>


<div class="footer">

    e-Medical Card &nbsp; | &nbsp; UIS Local Student System

</div>

</div>

</body>

</html>