<?php

session_start();

include("db_connect.php");

if(!isset($_SESSION['admin_id'])){

    header("Location: login_admin.php");

    exit();

}


$selectedMonth = isset($_GET['month'])
    ? (int)$_GET['month']
    : (int)date("n");

$selectedYear = isset($_GET['year'])
    ? (int)$_GET['year']
    : (int)date("Y");

if($selectedMonth < 1 || $selectedMonth > 12){

    $selectedMonth = (int)date("n");

}


if($selectedYear < 2000 || $selectedYear > 2100){

    $selectedYear = (int)date("Y");

}


$months = [

    1  => "January",
    2  => "February",
    3  => "March",
    4  => "April",
    5  => "May",
    6  => "June",
    7  => "July",
    8  => "August",
    9  => "September",
    10 => "October",
    11 => "November",
    12 => "December"

];

$monthName = $months[$selectedMonth];


$sql = "

    SELECT

        medical_claims.claim_id,

        medical_claims.student_id,

        medical_claims.clinic_id,

        medical_claims.claim_amount,

        medical_claims.visit_date,

        medical_claims.diagnosis_summary,

        medical_claims.claim_status,

        students.full_name,

        students.program,

        clinics.clinic_name

    FROM medical_claims

    INNER JOIN students

        ON medical_claims.student_id = students.student_id

    LEFT JOIN clinics

        ON medical_claims.clinic_id = clinics.clinic_id

    WHERE MONTH(medical_claims.visit_date) = ?

    AND YEAR(medical_claims.visit_date) = ?

    ORDER BY medical_claims.visit_date ASC

";


$stmt = $conn->prepare($sql);

$stmt->execute([

    $selectedMonth,

    $selectedYear

]);


$records = $stmt->fetchAll(PDO::FETCH_ASSOC);


$totalClaims = count($records);

$totalAmount = 0;

$completedClaims = 0;

$completedAmount = 0;


foreach($records as $record){

    $amount = (float)$record['claim_amount'];

    $totalAmount += $amount;


    $status = strtolower(

        trim($record['claim_status'])

    );


    if($status == "completed"){

        $completedClaims++;

        $completedAmount += $amount;

    }

}


$reportDate = date("d/m/Y");

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>

Admin Monthly Report - EMCS

</title>


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

    width:1150px;

    max-width:100%;

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

    margin-bottom:25px;

}


.page-title h2{

    color:#2f6468;

    font-size:21px;

    margin-bottom:6px;

}


.page-title p{

    color:#777;

    font-size:13px;

}


.report-info{

    display:flex;

    justify-content:space-between;

    align-items:center;

    background:#f2f2f2;

    padding:18px 20px;

    border-radius:12px;

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

    font-size:19px;

    margin-bottom:15px;

}


.summary-grid{

    display:grid;

    grid-template-columns:repeat(2,1fr);

    gap:15px;

    margin-bottom:25px;

}


.summary-box{

    background:#f8f8f8;

    border:1px solid #eeeeee;

    border-radius:12px;

    padding:18px 15px;

    text-align:center;

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


.summary-box.completed{

    background:#e8f5e9;

    border-color:#d0e8d2;

}


.amount-summary{

    display:grid;

    grid-template-columns:repeat(2,1fr);

    gap:15px;

    margin-bottom:30px;

}


.amount-box{

    padding:16px;

    background:#f8f8f8;

    border-radius:12px;

    border:1px solid #eeeeee;

}


.amount-box h4{

    color:#777;

    font-size:12px;

    margin-bottom:7px;

}


.amount-box p{

    color:#2f6468;

    font-size:18px;

    font-weight:bold;

}


.table-title{

    color:#2f6468;

    font-size:19px;

    margin-bottom:15px;

}


.table-container{

    width:100%;

    overflow-x:auto;

}


table{

    width:100%;

    border-collapse:collapse;

    min-width:1100px;

}


th{

    background:#2f6468;

    color:white;

    padding:13px 10px;

    text-align:center;

    font-size:12px;

}


td{

    padding:12px 10px;

    border:1px solid #ddd;

    text-align:center;

    color:#444;

    font-size:12px;

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

    font-size:11px;

    font-weight:bold;

}


.status-completed{

    background:#d4edda;

    color:#155724;

}


.status-other{

    background:#e2e3e5;

    color:#383d41;

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


/* =========================
   BUTTON AREA
========================= */

.button-area{

    display:flex;

    gap:12px;

    margin-top:28px;

    padding-top:22px;

    border-top:1px solid #eeeeee;

}


.back-btn,
.print-btn{

    flex:1;

    padding:14px;

    border:none;

    border-radius:30px;

    text-align:center;

    text-decoration:none;

    font-size:14px;

    font-weight:bold;

    cursor:pointer;

    transition:0.2s;

}


.back-btn{

    background:#7f8c8d;

    color:white;

}


.back-btn:hover{

    background:#666;

}


.print-btn{

    background:#2f6468;

    color:white;

}


.print-btn:hover{

    background:#234d50;

}


/* =========================
   INFORMATION BOX
========================= */

.info-box{

    margin-top:20px;

    padding:14px 16px;

    background:#f2f2f2;

    border-radius:10px;

    color:#777;

    font-size:12px;

    line-height:1.5;

}


.info-box strong{

    color:#2f6468;

}


.footer{

    text-align:center;

    margin-top:20px;

    color:#999;

    font-size:11px;

}


@media(max-width:900px){

    .summary-grid{

        grid-template-columns:repeat(2,1fr);

    }


    .amount-summary{

        grid-template-columns:repeat(2,1fr);

    }

}


@media(max-width:700px){

    body{

        padding:20px 15px;

    }


    .container{

        padding:25px 20px;

    }


    .header h1{

        font-size:23px;

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

        grid-template-columns:1fr;

    }


    .amount-summary{

        grid-template-columns:1fr;

    }


    .button-area{

        flex-direction:column;

    }


    .back-btn,
    .print-btn{

        width:100%;

    }

}


@page{

    size:A4 landscape;

    margin:12mm;

}


@media print{

    *{

        -webkit-print-color-adjust:exact !important;

        print-color-adjust:exact !important;

    }


    body{

        background:white;

        padding:0;

        color:#333;

    }


    .container{

        width:100%;

        max-width:none;

        margin:0;

        padding:0;

        background:white;

        box-shadow:none;

        border-radius:0;

    }


    .header{

        padding:0 0 12px 0;

        margin-bottom:15px;

        border-bottom:2px solid #2f6468;

    }


    .emcs-logo{

        width:62px;

        height:62px;

        margin-right:12px;

    }


    .uis-logo{

        width:48px;

    }


    .header h1{

        font-size:22px;

        color:#2f6468;

    }


    .header p{

        font-size:10px;

        letter-spacing:1.5px;

    }


    .page-title{

        margin-bottom:15px;

    }


    .page-title h2{

        font-size:18px;

        margin-bottom:4px;

    }


    .page-title p{

        font-size:10px;

    }


    .report-info{

        background:#f7faf9;

        border:1px solid #d9e4e3;

        border-radius:8px;

        padding:11px 13px;

        margin-bottom:12px;

        page-break-inside:avoid;

    }


    .report-info-left,
    .report-info-right{

        font-size:10px;

        line-height:1.6;

    }


    .summary-title{

        font-size:15px;

        margin-bottom:9px;

    }


    .summary-grid{

        grid-template-columns:repeat(2,1fr);

        gap:8px;

        margin-bottom:12px;

    }


    .summary-box{

        padding:10px 8px;

        border-radius:8px;

    }


    .summary-box h3{

        font-size:17px;

        margin-bottom:4px;

    }


    .summary-box p{

        font-size:9px;

    }


    .amount-summary{

        grid-template-columns:repeat(2,1fr);

        gap:8px;

        margin-bottom:16px;

    }


    .amount-box{

        padding:9px 10px;

        border-radius:8px;

    }


    .amount-box h4{

        font-size:8px;

        margin-bottom:4px;

    }


    .amount-box p{

        font-size:13px;

    }


    .table-title{

        font-size:15px;

        margin-bottom:9px;

    }


    .table-container{

        width:100%;

        overflow:visible;

    }


    table{

        width:100%;

        min-width:0;

        border-collapse:separate;

        border-spacing:0;

        border:1px solid #d9e4e3;

        border-radius:8px;

        overflow:hidden;

    }


    thead{

        display:table-header-group;

    }


    tr{

        page-break-inside:avoid;

        break-inside:avoid;

    }


    th{

        background:#2f6468 !important;

        color:white !important;

        padding:8px 7px;

        font-size:9px;

        border:none;

    }


    td{

        padding:7px 6px;

        font-size:9px;

        border:0;

        border-top:1px solid #e5eaea;

        background:white !important;

    }


    tr:nth-child(even) td{

        background:#f7faf9 !important;

    }


    .status{

        padding:4px 8px;

        border-radius:12px;

        font-size:8px;

    }


    .status-completed{

        background:#d4edda !important;

        color:#155724 !important;

    }


    .status-other{

        background:#e2e3e5 !important;

        color:#383d41 !important;

    }


    .no-record{

        padding:35px 20px;

    }


    .no-record-icon{

        width:45px;

        height:45px;

        font-size:20px;

        margin-bottom:10px;

    }


    .no-record h3{

        font-size:15px;

        margin-bottom:5px;

    }


    .no-record p{

        font-size:9px;

    }


    .info-box{

        margin-top:12px;

        padding:9px 11px;

        background:#f7faf9 !important;

        border:1px solid #d9e4e3;

        border-left:3px solid #2f6468;

        border-radius:6px;

        color:#666;

        font-size:8px;

        line-height:1.4;

    }


    .info-box strong{

        color:#2f6468;

    }


    .footer{

        margin-top:10px;

        padding-top:7px;

        border-top:1px solid #e5eaea;

        color:#888;

        font-size:8px;

    }


    .button-area{

        display:none !important;

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

                <h1>
                    E-MEDICAL CARD
                </h1>

                <p>
                    ADMIN MONTHLY MEDICAL REPORT
                </p>

            </div>

        </div>

        <img src="images/logouis.png" class="uis-logo">

    </div>


    <div class="page-title">


        <h2>

            <?php echo htmlspecialchars($monthName); ?>

            <?php echo htmlspecialchars($selectedYear); ?>

            Medical Claim Report

        </h2>


        <p>

            Detailed medical claim records for

            <?php echo htmlspecialchars($monthName); ?>

            <?php echo htmlspecialchars($selectedYear); ?>.

        </p>


    </div>


    <div class="report-info">


        <div class="report-info-left">


            <strong>

                Report Type:

            </strong>


            Monthly Medical Claim Report


            <br>


            <strong>

                Reporting Month:

            </strong>


            <?php echo htmlspecialchars($monthName); ?>

            <?php echo htmlspecialchars($selectedYear); ?>


        </div>


        <div class="report-info-right">


            <strong>

                Report Date:

            </strong>


            <?php echo $reportDate; ?>


            <br>


            <strong>

                Total Records:

            </strong>


            <?php echo $totalClaims; ?>


        </div>


    </div>


    <h2 class="summary-title">

        <?php echo htmlspecialchars($monthName); ?>

        Monthly Summary

    </h2>


    <div class="summary-grid">


        <div class="summary-box total">


            <h3>

                <?php echo $totalClaims; ?>

            </h3>


            <p>

                Total Claims

            </p>


        </div>


        <div class="summary-box completed">


            <h3>

                <?php echo $completedClaims; ?>

            </h3>


            <p>

                Completed Claims

            </p>


        </div>


    </div>


    <div class="amount-summary">


        <div class="amount-box">


            <h4>

                TOTAL CLAIM AMOUNT

            </h4>


            <p>

                RM <?php echo number_format(

                    $totalAmount,

                    2

                ); ?>

            </p>


        </div>



        <div class="amount-box">


            <h4>

                COMPLETED AMOUNT

            </h4>


            <p>

                RM <?php echo number_format(

                    $completedAmount,

                    2

                ); ?>

            </p>


        </div>


    </div>


    <h2 class="table-title">

        Medical Claim Records -

        <?php echo htmlspecialchars($monthName); ?>

        <?php echo htmlspecialchars($selectedYear); ?>

    </h2>


    <?php if($totalClaims > 0){ ?>


    <div class="table-container">


        <table>


            <thead>


                <tr>


                    <th>

                        No.

                    </th>


                    <th>

                        Claim ID

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

                        Clinic

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

                        Status

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

                        <?php echo $no++; ?>

                    </td>


                    <td>

                        <?php echo htmlspecialchars(

                            $record['claim_id']

                        ); ?>

                    </td>


                    <td>

                        <?php echo htmlspecialchars(

                            $record['student_id']

                        ); ?>

                    </td>


                    <td>

                        <?php echo htmlspecialchars(

                            $record['full_name']

                        ); ?>

                    </td>


                    <td>

                        <?php echo htmlspecialchars(

                            $record['program']

                        ); ?>

                    </td>


                    <td>

                        <?php echo htmlspecialchars(

                            $record['clinic_name'] ?? 'N/A'

                        ); ?>

                    </td>


                    <td>

                        <?php echo htmlspecialchars(

                            $record['visit_date']

                        ); ?>

                    </td>


                    <td>

                        <?php echo htmlspecialchars(

                            $record['diagnosis_summary']

                        ); ?>

                    </td>


                    <td>

                        RM

                        <?php echo number_format(

                            (float)$record['claim_amount'],

                            2

                        ); ?>

                    </td>


                    <td>


                        <?php

                        $status = strtolower(

                            trim(

                                $record['claim_status']

                            )

                        );


                        if($status == "completed"){

                            $statusClass = "status-completed";

                        }

                        else{

                            $statusClass = "status-other";

                        }

                        ?>


                        <span

                            class="status <?php echo $statusClass; ?>"

                        >

                            <?php echo htmlspecialchars(

                                $record['claim_status']

                            ); ?>

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

                No Medical Claims

            </h3>


            <p>

                There are no medical claims recorded for

                <?php echo htmlspecialchars($monthName); ?>

                <?php echo htmlspecialchars($selectedYear); ?>.

            </p>


        </div>


    <?php } ?>


    <div class="button-area">


        <a

            href="admin_report.php?year=<?php echo $selectedYear; ?>"

            class="back-btn"

        >

            Back to Annual Report

        </a>


        <button

            type="button"

            onclick="window.print()"

            class="print-btn"

        >

            Print Monthly Report

        </button>


    </div>


    <div class="info-box">


        <strong>

            Report Information:

        </strong>


        This report displays all medical claims submitted

        during <?php echo htmlspecialchars($monthName); ?>

        <?php echo htmlspecialchars($selectedYear); ?>.

        The report includes the student, clinic, visit date,

        diagnosis, claim amount and claim status.


    </div>


    <div class="footer">

        e-Medical Card &nbsp; | &nbsp; UIS Local Student System

    </div>


</div>


</body>

</html>