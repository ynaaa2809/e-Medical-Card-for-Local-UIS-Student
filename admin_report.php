<?php

session_start();

include("db_connect.php");

if(!isset($_SESSION['admin_id'])){

    header("Location: login_admin.php");

    exit();

}


$selectedYear = isset($_GET['year'])
    ? (int)$_GET['year']
    : (int)date("Y");


if($selectedYear < 2000 || $selectedYear > 2100){

    $selectedYear = (int)date("Y");

}


$yearStmt = $conn->prepare("

    SELECT DISTINCT YEAR(visit_date) AS report_year

    FROM medical_claims

    WHERE visit_date IS NOT NULL

    ORDER BY report_year DESC

");


$yearStmt->execute();

$availableYears = $yearStmt->fetchAll(PDO::FETCH_COLUMN);


if(!in_array($selectedYear, $availableYears)){

    $availableYears[] = $selectedYear;

}


rsort($availableYears);


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


$monthlyReports = [];


$monthStmt = $conn->prepare("

    SELECT

        COUNT(*) AS total_claims,

        COALESCE(SUM(claim_amount), 0) AS total_amount,

        SUM(

            CASE

                WHEN LOWER(TRIM(claim_status)) = 'completed'

                THEN 1

                ELSE 0

            END

        ) AS completed_claims,

        COALESCE(

            SUM(

                CASE

                    WHEN LOWER(TRIM(claim_status)) = 'completed'

                    THEN claim_amount

                    ELSE 0

                END

            ),

            0

        ) AS completed_amount

    FROM medical_claims

    WHERE MONTH(visit_date) = ?

    AND YEAR(visit_date) = ?

");


foreach($months as $monthNumber => $monthName){

    $monthStmt->execute([

        $monthNumber,

        $selectedYear

    ]);


    $data = $monthStmt->fetch(PDO::FETCH_ASSOC);


    $monthlyReports[$monthNumber] = [

        "month" => $monthName,

        "total_claims" => (int)$data['total_claims'],

        "total_amount" => (float)$data['total_amount'],

        "completed_claims" => (int)$data['completed_claims'],

        "completed_amount" => (float)$data['completed_amount']

    ];

}


$yearlyTotalClaims = 0;

$yearlyTotalAmount = 0;

$yearlyCompletedClaims = 0;

$yearlyCompletedAmount = 0;


foreach($monthlyReports as $report){

    $yearlyTotalClaims += $report['total_claims'];

    $yearlyTotalAmount += $report['total_amount'];

    $yearlyCompletedClaims += $report['completed_claims'];

    $yearlyCompletedAmount += $report['completed_amount'];

}

?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Medical Report - EMCS</title>


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

    width:1100px;

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


.filter-box{

    background:#f2f2f2;

    padding:18px 20px;

    border-radius:12px;

    margin-bottom:25px;

}


.filter-row{

    display:flex;

    align-items:end;

    gap:15px;

}


.filter-group{

    flex:1;

}


.filter-group label{

    display:block;

    color:#444;

    font-size:13px;

    font-weight:bold;

    margin-bottom:7px;

}


.filter-group select{

    width:100%;

    padding:12px 14px;

    border:1px solid #ccc;

    border-radius:10px;

    background:white;

    color:#444;

    font-size:14px;

    outline:none;

}


.filter-group select:focus{

    border-color:#2f6468;

    box-shadow:0 0 0 3px rgba(47,100,104,0.10);

}


.filter-btn{

    padding:12px 25px;

    border:none;

    border-radius:30px;

    background:#2f6468;

    color:white;

    font-size:14px;

    font-weight:bold;

    cursor:pointer;

}


.filter-btn:hover{

    background:#234d50;

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


.month-title{

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

    min-width:700px;

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

    font-size:11px;

    font-weight:bold;

}


.status-completed{

    background:#d4edda;

    color:#155724;

}


.view-btn{

    display:inline-block;

    padding:8px 16px;

    border-radius:20px;

    background:#2f6468;

    color:white;

    text-decoration:none;

    font-size:12px;

    font-weight:bold;

}


.view-btn:hover{

    background:#234d50;

}


.no-claims{

    color:#999;

    font-size:12px;

}


.button-area{

    display:flex;

    gap:12px;

    margin-top:25px;

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


    .filter-row{

        flex-direction:column;

        align-items:stretch;

    }


    .filter-btn{

        width:100%;

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


    .emcs-logo{

        width:65px;

        height:65px;

        margin-right:10px;

    }


    .uis-logo{

        width:45px;

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


    .filter-box,
    .button-area,
    .view-btn{

        display:none !important;

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


    .month-title{

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


    .no-claims{

        font-size:8px;

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


    thead{

        display:table-header-group;

    }


    tr{

        page-break-inside:avoid;

        break-inside:avoid;

    }

}

</style>

</head>


<body>


<div class="container">


    <div class="header">

        <div class="header-left">

            <img
                src="images/logoEMCS.png"
                class="emcs-logo"
            >

            <div>

                <h1>
                    E-MEDICAL CARD
                </h1>

                <p>
                    ADMIN MEDICAL REPORT
                </p>

            </div>

        </div>


        <img
            src="images/logouis.png"
            class="uis-logo"
        >

    </div>


    <div class="page-title">

        <h2>

            Monthly Medical Claim Report

        </h2>

        <p>

            View medical claim statistics and detailed reports by month.

        </p>

    </div>


    <div class="filter-box">

        <form method="GET">

            <div class="filter-row">

                <div class="filter-group">

                    <label>

                        Select Year

                    </label>

                    <select name="year">

                        <?php foreach($availableYears as $year){ ?>

                            <option

                                value="<?php echo $year; ?>"

                                <?php

                                if($year == $selectedYear){

                                    echo "selected";

                                }

                                ?>

                            >

                                <?php echo $year; ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>


                <button

                    type="submit"

                    class="filter-btn"

                >

                    View Report

                </button>

            </div>

        </form>

    </div>


    <h2 class="summary-title">

        <?php echo $selectedYear; ?> Annual Summary

    </h2>


    <div class="summary-grid">


        <div class="summary-box total">

            <h3>

                <?php echo $yearlyTotalClaims; ?>

            </h3>

            <p>

                Total Claims

            </p>

        </div>


        <div class="summary-box completed">

            <h3>

                <?php echo $yearlyCompletedClaims; ?>

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

                RM <?php echo number_format($yearlyTotalAmount,2); ?>

            </p>

        </div>


        <div class="amount-box">

            <h4>

                COMPLETED AMOUNT

            </h4>

            <p>

                RM <?php echo number_format($yearlyCompletedAmount,2); ?>

            </p>

        </div>


    </div>


    <h2 class="month-title">

        Monthly Reports - <?php echo $selectedYear; ?>

    </h2>


    <div class="table-container">

        <table>

            <thead>

            <tr>

                <th>No.</th>

                <th>Month</th>

                <th>Total Claims</th>

                <th>Completed</th>

                <th>Total Amount</th>

                <th>Action</th>

            </tr>

            </thead>


            <tbody>

            <?php

            $no = 1;

            foreach($monthlyReports as $monthNumber => $report){

            ?>

                <tr>

                    <td>

                        <?php echo $no++; ?>

                    </td>


                    <td>

                        <strong>

                            <?php echo htmlspecialchars($report['month']); ?>

                        </strong>

                    </td>


                    <td>

                        <?php echo $report['total_claims']; ?>

                    </td>


                    <td>

                        <span class="status status-completed">

                            <?php echo $report['completed_claims']; ?>

                        </span>

                    </td>


                    <td>

                        RM <?php echo number_format(

                            $report['total_amount'],

                            2

                        ); ?>

                    </td>


                    <td>

                        <?php if($report['total_claims'] > 0){ ?>

                            <a

                                href="admin_monthly_report.php?month=<?php echo $monthNumber; ?>&year=<?php echo $selectedYear; ?>"

                                class="view-btn"

                            >

                                View Report

                            </a>

                        <?php }else{ ?>

                            <span class="no-claims">

                                No Claims

                            </span>

                        <?php } ?>

                    </td>

                </tr>

            <?php

            }

            ?>

            </tbody>

        </table>

    </div>


    <div class="button-area">

        <a

            href="dashboard_admin.php"

            class="back-btn"

        >

            Back to Dashboard

        </a>


        <button

            type="button"

            onclick="window.print()"

            class="print-btn"

        >

            Print Annual Summary

        </button>

    </div>


    <div class="info-box">

        <strong>

            Report Information:

        </strong>

        This report provides a monthly summary of medical claims

        submitted through all registered clinics. Select

        "View Report" for a month to see the detailed claims

        for that specific month.

    </div>


    <div class="footer">

        e-Medical Card &nbsp; | &nbsp; UIS Local Student System

    </div>


</div>


</body>

</html>