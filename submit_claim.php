<?php

session_start();

if(!isset($_SESSION['clinic_id'])){

    header("Location: login_clinic.php");

    exit();

}

include("db_connect.php");

$clinicID = $_SESSION['clinic_id'];

$popupType = "";
$popupMessage = "";


if(isset($_GET['check_student'])){

    header('Content-Type: application/json');

    $studentID = trim($_GET['check_student']);

    if($studentID === ""){

        echo json_encode([
            "status" => "error",
            "message" => "Student ID is required."
        ]);

        exit();

    }

    try{

        $stmt = $conn->prepare("
            SELECT
                student_id,
                full_name,
                current_balance
            FROM students
            WHERE student_id = ?
            LIMIT 1
        ");

        $stmt->execute([$studentID]);

        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$student){

            echo json_encode([
                "status" => "not_found",
                "message" => "Student Not Found"
            ]);

            exit();

        }

        echo json_encode([
            "status" => "success",
            "student_id" => $student['student_id'],
            "name" => $student['full_name'],
            "balance" => (float)$student['current_balance']
        ]);

        exit();

    }catch(PDOException $e){

        echo json_encode([
            "status" => "error",
            "message" => "Unable to verify student."
        ]);

        exit();

    }

}


if($_SERVER["REQUEST_METHOD"] === "POST"){

    $studentID = trim($_POST['student_id'] ?? '');

    $claimAmount =
        isset($_POST['claim_amount'])
        ? (float)$_POST['claim_amount']
        : 0;

    $visitDate =
        trim($_POST['visit_date'] ?? '');

    $diagnosis =
        trim($_POST['diagnosis'] ?? '');


    if($studentID === ""){

        $popupType = "student_error";

        $popupMessage =
            "Please enter Student ID.";

    }

    elseif($claimAmount <= 0){

        $popupType = "system_error";

        $popupMessage =
            "Please enter a valid claim amount.";

    }

    elseif($visitDate === ""){

        $popupType = "system_error";

        $popupMessage =
            "Please select the visit date.";

    }

    elseif($diagnosis === ""){

        $popupType = "system_error";

        $popupMessage =
            "Please enter the diagnosis.";

    }

    else{

        try{

            $conn->beginTransaction();


            $stmt = $conn->prepare("
                SELECT
                    student_id,
                    full_name,
                    status,
                    current_balance
                FROM students
                WHERE student_id = ?
                FOR UPDATE
            ");

            $stmt->execute([$studentID]);

            $student =
                $stmt->fetch(PDO::FETCH_ASSOC);


            if(!$student){

                $conn->rollBack();

                $popupType =
                    "student_error";

                $popupMessage =
                    "Student ID is not registered in the system.";

            }

            else{

                $status =
                    $student['status'] ?? '';

                $currentBalance =
                    (float)($student['current_balance'] ?? 0);


                if($status !== "Active"){

                    $conn->rollBack();

                    $popupType =
                        "inactive_student";

                    $popupMessage =
                        "Student account is inactive.";

                }

                elseif($currentBalance <= 0){

                    $conn->rollBack();

                    $popupType =
                        "balance_error";

                    $popupMessage =
                        "The student's remaining balance is insufficient.";

                }

                else{

                    $newBalance =
                        $currentBalance - $claimAmount;


                    if($newBalance < 0){

                        $newBalance = 0;

                    }


                    $insert =
                        $conn->prepare("
                            INSERT INTO medical_claims
                            (
                                student_id,
                                clinic_id,
                                visit_date,
                                claim_amount,
                                diagnosis_summary,
                                claim_status
                            )
                            VALUES
                            (?, ?, ?, ?, ?, 'Completed')
                        ");


                    $insert->execute([

                        $studentID,

                        $clinicID,

                        $visitDate,

                        $claimAmount,

                        $diagnosis

                    ]);


                    $update =
                        $conn->prepare("
                            UPDATE students
                            SET current_balance = ?
                            WHERE student_id = ?
                        ");


                    $update->execute([

                        $newBalance,

                        $studentID

                    ]);


                    $conn->commit();


                    $popupType =
                        "claim_success";

                    $popupMessage =
                        "Medical claim submitted successfully.";

                }

            }

        }

        catch(Exception $e){

            if($conn->inTransaction()){

                $conn->rollBack();

            }

            $popupType =
                "system_error";

            $popupMessage =
                "Something went wrong. Please try again.";

        }

    }

}

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0"

>

<title>Submit Medical Claim</title>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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


.claim-card{

    background:white;

    border-radius:20px;

    padding:30px;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

    margin-bottom:25px;

    max-width:900px;

}


.form-group{

    margin-bottom:26px;

}


.form-group label{

    display:block;

    color:#777;

    font-size:14px;

    margin-bottom:10px;

}


.form-group input,
.form-group textarea{

    width:100%;

    padding:15px 16px;

    border:1px solid #d5d5d5;

    border-radius:10px;

    outline:none;

    font-size:14px;

    color:#333;

    transition:0.2s;

}


.form-group input:focus,
.form-group textarea:focus{

    border-color:#2f6468;

    box-shadow:0 0 0 2px rgba(47,100,104,0.1);

}


.form-group textarea{

    resize:vertical;

    min-height:125px;

}


.student-result{

    display:none;

    margin-top:12px;

    padding:12px 16px;

    border-radius:12px;

}


.student-result.valid{

    display:block;

    background:#edf8f6;

    border:1px solid #c8e6e1;

}


.student-result.invalid{

    display:block;

    background:#fff1f1;

    border:1px solid #e8cccc;

}


.student-result .result-title{

    font-size:15px;

    font-weight:bold;

    margin-bottom:12px;

    color:#2f6468;

}


.student-result.invalid .result-title{

    color:#b55f5f;

    margin-bottom:0;

}


.result-row{

    display:block;

    padding:3px 0 6px 0;

}


.result-row + .result-row{

    padding-top:5px;

}


.result-label{

    display:block;

    color:#2f6468;

    font-size:14px;

    font-weight:bold;

    margin-bottom:2px;

}


.result-value{

    display:block;

    color:#666;

    font-size:14px;

    font-weight:normal;

}


.report-note{

    margin-top:20px;

    padding:16px 18px;

    background:#f2f2f2;

    border-radius:10px;

    color:#777;

    font-size:12px;

    line-height:1.7;

    width:100%;

}


.report-note strong{

    color:#2f6468;

}


.submit-btn{

    width:100%;

    border:none;

    background:#2f6468;

    color:white;

    padding:14px 20px;

    border-radius:30px;

    font-size:14px;

    font-weight:normal;

    cursor:pointer;

    transition:0.2s;

    margin-bottom:20px;

}


.submit-btn:hover{

    background:#234d50;

}


@media(max-width:900px){

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


    .logout-section{

        margin-top:25px;

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

        width:65px;

    }

}


@media(max-width:700px){

    .menu a{

        min-width:150px;

    }

}


@media(max-width:500px){

    .main{

        padding:15px;

    }


    .header{

        margin-bottom:20px;

    }


    .claim-card{

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


    .clinic-icon{

        width:60px;

        height:60px;

    }


    .profile-info h2{

        font-size:16px;

    }


    .uis-logo{

        width:50px;

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


    <a href="clinic_profile.php">

        View Profile

    </a>


    <a href="submit_claim.php" class="active">

        Submit Medical Claim

    </a>


    <a href="report.php">

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

                <?php

                echo htmlspecialchars(

                    $_SESSION['clinic_name']
                    ?? 'Clinic Portal'

                );

                ?>

            </h2>


            <p>

                Clinic ID:

                <?php

                echo htmlspecialchars($clinicID);

                ?>

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

        Submit Medical Claim

    </h1>


    <p>

        Enter the student's information and medical claim details below.

    </p>


</div>


<div class="claim-card">


    <form

        method="POST"

        id="claimForm"

    >


        <div class="form-group">


            <h2
                style="

                    color:#2f6468;

                    font-size:20px;

                    font-weight:bold;

                    margin-bottom:20px;

                "
            >

                Claim Information

            </h2>


            <label for="student_id">

                Student ID

            </label>


            <input

                type="text"

                id="student_id"

                name="student_id"

                placeholder="Enter Student ID"

                autocomplete="off"

                required

            >


            <div

                class="student-result"

                id="studentResult"

            >


                <div

                    class="result-title"

                    id="resultTitle"

                >
                </div>


                <div id="resultContent"></div>


            </div>


        </div>


        <div class="form-group">


            <label for="claim_amount">

                Claim Amount (RM)

            </label>


            <input

                type="number"

                id="claim_amount"

                name="claim_amount"

                placeholder="Enter claim amount"

                min="0.01"

                step="0.01"

                required
                
            >


        </div>


        <div class="form-group">


            <label for="visit_date">

                Visit Date

            </label>


            <input

                type="date"

                id="visit_date"

                name="visit_date"

                required

            >


        </div>


        <div class="form-group">


            <label for="diagnosis">

                Diagnosis Summary

            </label>


            <textarea

                id="diagnosis"

                name="diagnosis"

                placeholder="Enter diagnosis summary"

                required

            >
            </textarea>


        </div>


        <button

            type="submit"

            class="submit-btn"

        >

            Submit Medical Claim

        </button>



        <div class="report-note">

            Please verify the student's ID and available balance before submitting the claim.
            The system will automatically check the student's remaining balance. If the
            balance is sufficient, the claim will be completed and the claim amount will be
            deducted automatically.

        </div>


    </form>


</div>

</div>

<script>


const studentInput =
    document.getElementById("student_id");


const studentResult =
    document.getElementById("studentResult");


const resultTitle =
    document.getElementById("resultTitle");


const resultContent =
    document.getElementById("resultContent");


const claimAmount =
    document.getElementById("claim_amount");


let checkTimer = null;

let studentFound = false;

let currentStudentBalance = 0;

let currentRequest = 0;



function checkStudent(){

    clearTimeout(checkTimer);


    const studentID =
        studentInput.value.trim();


    studentFound = false;

    currentStudentBalance = 0;


    studentResult.className =
        "student-result";


    resultTitle.textContent = "";

    resultContent.innerHTML = "";


    studentInput.classList.remove(
        "valid",
        "invalid"
    );


    if(studentID === "") return;


    const requestID =
        ++currentRequest;


    checkTimer =
        setTimeout(function(){


            fetch(
                "submit_claim.php?check_student="
                + encodeURIComponent(studentID)
            )


            .then(function(response){


                if(!response.ok){

                    throw new Error(
                        "HTTP Error " + response.status
                    );

                }


                return response.json();


            })


            .then(function(data){


                if(requestID !== currentRequest)
                    return;


                if(
                    studentInput.value.trim()
                    !== studentID
                )
                    return;


                if(data.status === "success"){


                    studentFound = true;


                    currentStudentBalance =
                        parseFloat(
                            data.balance || 0
                        );


                    studentInput.classList.remove(
                        "valid",
                        "invalid"
                    );


                    studentResult.className =
                        "student-result valid";


                    resultTitle.textContent = "";


                    resultContent.innerHTML = `

                        <div class="result-row">

                            <span class="result-label">

                                Student Name:

                            </span>

                            <span class="result-value">

                                ${escapeHTML(data.name)}

                            </span>

                        </div>


                        <div class="result-row">

                            <span class="result-label">

                                Current Balance:

                            </span>

                            <span class="result-value">

                                RM${currentStudentBalance.toFixed(2)}

                            </span>

                        </div>

                    `;


                }


                else if(data.status === "not_found"){


                    studentFound = false;

                    currentStudentBalance = 0;


                    studentInput.classList.remove(
                        "valid",
                        "invalid"
                    );


                    studentResult.className =
                        "student-result invalid";


                    resultTitle.textContent =
                        "Student Not Found";


                    resultContent.innerHTML = "";


                }


                else{


                    studentFound = false;

                    currentStudentBalance = 0;


                    studentInput.classList.remove(
                        "valid",
                        "invalid"
                    );


                    studentResult.className =
                        "student-result invalid";


                    resultTitle.textContent =
                        "Student Not Found";


                    resultContent.innerHTML = "";


                }


            })


            .catch(function(error){


                console.error(
                    "Student check error:",
                    error
                );


                studentFound = false;

                currentStudentBalance = 0;


                studentInput.classList.remove(
                    "valid",
                    "invalid"
                );


                studentResult.className =
                    "student-result invalid";


                resultTitle.textContent =
                    "Unable to Check Student";


                resultContent.innerHTML = "";


            });


        }, 300);

}


function escapeHTML(value){

    const div =
        document.createElement("div");


    div.textContent =
        value ?? "";


    return div.innerHTML;

}


studentInput.addEventListener(
    "input",
    checkStudent
);


claimAmount.addEventListener(
    "input",
    function(){


        const amount =
            parseFloat(
                claimAmount.value || 0
            );


        if(
            studentFound &&
            amount > currentStudentBalance
        ){


            claimAmount.style.borderColor =
                "#c96f6f";


        }

        else{


            claimAmount.style.borderColor =
                "";

        }

    }
);


document
.getElementById("claimForm")
.addEventListener(
    "submit",
    function(event){


        const studentID =
            studentInput.value.trim();


        if(
            !studentFound ||
            studentID === ""
        ){


            event.preventDefault();


            Swal.fire({

                icon:'error',

                title:'Student Not Found',

                text:
                    'Please enter a registered Student ID.',

                confirmButtonColor:'#2f6468'

            });


            studentInput.focus();


            return;

        }

    }
);


window.addEventListener(
    "load",
    function(){


        if(
            studentInput.value.trim() !== ""
        ){

            checkStudent();

        }

    }
);

</script>


<?php if($popupType !== ""): ?>

<script>


<?php if($popupType === "student_error"): ?>


Swal.fire({

    icon:'error',

    title:'Student Not Found!',

    text:<?= json_encode($popupMessage) ?>,

    confirmButtonColor:'#2f6468'

});


<?php elseif($popupType === "inactive_student"): ?>


Swal.fire({

    icon:'warning',

    title:'Student Inactive!',

    text:<?= json_encode($popupMessage) ?>,

    confirmButtonColor:'#2f6468'

});


<?php elseif($popupType === "balance_error"): ?>


Swal.fire({

    icon:'warning',

    title:'Insufficient Balance!',

    text:<?= json_encode($popupMessage) ?>,

    confirmButtonColor:'#2f6468'

});


<?php elseif($popupType === "claim_success"): ?>


Swal.fire({

    icon:'success',

    title:'Claim Successful!',

    text:<?= json_encode($popupMessage) ?>,

    confirmButtonColor:'#2f6468'

}).then(function(){

    window.location.href =
        "submit_claim.php";

});


<?php endif; ?>


</script>

<?php endif; ?>

</body>

</html>