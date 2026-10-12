<?php

session_start();

include("db_connect.php");


if(!isset($_SESSION['admin_id'])){

    header("Location: login_admin.php");

    exit();

}


if(isset($_GET['student_id'])){

    $studentID = trim($_GET['student_id']);

}
elseif(isset($_POST['student_id'])){

    $studentID = trim($_POST['student_id']);

}
else{

    header("Location: view_student.php");

    exit();

}


$error = "";

$success = "";


if(isset($_POST['update'])){


    $phone = trim($_POST['phone_number'] ?? '');

    $semester = trim($_POST['semester'] ?? '');

    $address = trim($_POST['address'] ?? '');

    $guardianName = trim($_POST['guardian_name'] ?? '');

    $guardianPhone = trim($_POST['guardian_phone'] ?? '');

    $status = trim($_POST['status'] ?? '');

    $balance = trim($_POST['current_balance'] ?? '');

    $password = trim($_POST['password'] ?? '');


    if($phone == "" || $semester == "" || $status == ""){

        $error = "Please fill in all required fields.";

    }


    elseif(!preg_match('/^[0-9]{10,}$/', $phone)){

        $error = "Phone number must contain at least 10 numbers.";

    }


    elseif(!is_numeric($semester) || (int)$semester < 1){

        $error = "Semester must be a valid number.";

    }


    elseif($guardianPhone != "" && !preg_match('/^[0-9]{10,}$/', $guardianPhone)){

        $error = "Guardian phone number must contain at least 10 numbers.";

    }


    elseif($status != "Active" && $status != "Inactive"){

        $error = "Invalid student status.";

    }


    elseif(!is_numeric($balance) || $balance < 0){

        $error = "Current balance must be a valid amount and cannot be negative.";

    }


    if($error == ""){

        try{


            if($password != ""){

                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


                $sql = "

                    UPDATE students

                    SET

                        password = ?,

                        phone_number = ?,

                        semester = ?,

                        address = ?,

                        guardian_name = ?,

                        guardian_phone = ?,

                        status = ?,

                        current_balance = ?

                    WHERE student_id = ?

                ";


                $stmt = $conn->prepare($sql);


                $stmt->execute([

                    $hashedPassword,

                    $phone,

                    $semester,

                    $address,

                    $guardianName,

                    $guardianPhone,

                    $status,

                    $balance,

                    $studentID

                ]);

            }


            else{

                $sql = "

                    UPDATE students

                    SET

                        phone_number = ?,

                        semester = ?,

                        address = ?,

                        guardian_name = ?,

                        guardian_phone = ?,

                        status = ?,

                        current_balance = ?

                    WHERE student_id = ?

                ";


                $stmt = $conn->prepare($sql);


                $stmt->execute([

                    $phone,

                    $semester,

                    $address,

                    $guardianName,

                    $guardianPhone,

                    $status,

                    $balance,

                    $studentID

                ]);

            }


            $success = "Student information updated successfully.";

        }

        catch(PDOException $e){

            $error = "Unable to update student information.";

        }

    }

}


$sqlStudent = "

    SELECT *

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

?>



<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Update Student - EMCS</title>


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

    max-width:850px;

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

    font-size:22px;

    margin-bottom:6px;

}


.page-title p{

    color:#777;

    font-size:13px;

}


.alert{

    padding:14px 16px;

    border-radius:10px;

    margin-bottom:20px;

    font-size:13px;

    line-height:1.5;

}


.alert-error{

    background:#f8d7da;

    color:#721c24;

    border:1px solid #f5c6cb;

}


.alert-success{

    background:#d4edda;

    color:#155724;

    border:1px solid #c3e6cb;

}


.student-summary{

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:15px;

    margin-bottom:25px;

}


.student-info{

    background:#f2f2f2;

    padding:15px;

    border-radius:10px;

    border-left:4px solid #2f6468;

}


.student-info label{

    display:block;

    color:#888;

    font-size:11px;

    font-weight:bold;

    margin-bottom:6px;

    text-transform:uppercase;

}


.student-info strong{

    color:#2f6468;

    font-size:14px;

    word-break:break-word;

}


.form-grid{

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:18px 25px;

}


.form-group{

    display:flex;

    flex-direction:column;

}


.form-group.full-width{

    grid-column:1 / -1;

}


.form-group label{

    display:block;

    margin-bottom:7px;

    color:#444;

    font-size:13px;

    font-weight:bold;

}


.required{

    color:#e74c3c;

}


.form-group input,
.form-group select,
.form-group textarea{

    width:100%;

    padding:12px 14px;

    border-radius:10px;

    border:1px solid #ccc;

    background:#fff;

    color:#444;

    font-size:14px;

    outline:none;

    transition:0.2s;

}


.form-group textarea{

    min-height:90px;

    resize:vertical;

}


.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus{

    border-color:#2f6468;

    box-shadow:0 0 0 3px rgba(47,100,104,0.10);

}


.form-group input[readonly],
.form-group textarea[readonly]{

    background:#eeeeee;

    color:#777;

    cursor:not-allowed;

    border-color:#d5d5d5;

}


.help-text{

    margin-top:5px;

    color:#888;

    font-size:11px;

}


.password-box{

    position:relative;

}


.password-box input{

    padding-right:45px;

}


.password-note{

    margin-top:5px;

    color:#888;

    font-size:11px;

}


.balance-wrapper{

    position:relative;

}


.balance-wrapper span{

    position:absolute;

    left:14px;

    top:50%;

    transform:translateY(-50%);

    color:#777;

    font-size:14px;

}


.balance-wrapper input{

    padding-left:40px;

}


.button-area{

    display:flex;

    gap:12px;

    margin-top:28px;

    padding-top:22px;

    border-top:1px solid #eeeeee;

}


.update-btn{

    flex:1;

    padding:14px;

    border:none;

    border-radius:30px;

    background:#2f6468;

    color:white;

    font-size:15px;

    font-weight:bold;

    cursor:pointer;

    transition:0.2s;

}


.update-btn:hover{

    background:#234d50;

}


.back-btn{

    flex:1;

    padding:14px;

    text-align:center;

    text-decoration:none;

    background:#7f8c8d;

    color:white;

    border-radius:30px;

    font-size:15px;

    font-weight:bold;

    transition:0.2s;

}


.back-btn:hover{

    background:#666;

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


    .form-grid{

        grid-template-columns:1fr;

        gap:16px;

    }


    .form-group.full-width{

        grid-column:auto;

    }


    .student-summary{

        grid-template-columns:1fr;

    }


    .button-area{

        flex-direction:column;

    }


    .update-btn,
    .back-btn{

        width:100%;

    }

}


@media(max-width:450px){

    .header{

        align-items:flex-start;

    }


    .emcs-logo{

        width:65px;

        height:65px;

        margin-right:10px;

    }


    .uis-logo{

        width:45px;

    }


    .header h1{

        font-size:20px;

    }


    .header p{

        font-size:10px;

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

                alt="EMCS Logo"

            >

            <div>

                <h1>

                    E-MEDICAL CARD

                </h1>

                <p>

                    STUDENT MANAGEMENT

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

        <h2>

            Update Student Information

        </h2>

        <p>

            Update the student's account information below.

        </p>

    </div>


    <?php if($error != ""){ ?>

        <div class="alert alert-error">

            <strong>Error:</strong>

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php } ?>


    <?php if($success != ""){ ?>

        <div class="alert alert-success">

            <strong>Success:</strong>

            <?php echo htmlspecialchars($success); ?>

        </div>

    <?php } ?>


    <div class="student-summary">


        <div class="student-info">

            <label>

                Student ID

            </label>

            <strong>

                <?php

                echo htmlspecialchars($student['student_id'] ?? '');

                ?>

            </strong>

        </div>


        <div class="student-info">

            <label>

                IC Number

            </label>

            <strong>

                <?php

                echo htmlspecialchars($student['ic_number'] ?? '');

                ?>

            </strong>

        </div>


    </div>


    <form method="POST">


        <input

            type="hidden"

            name="student_id"

            value="<?php echo htmlspecialchars($student['student_id'] ?? ''); ?>"

        >



        <div class="form-grid">


            <div class="form-group">

                <label>

                    Full Name

                </label>

                <input

                    type="text"

                    value="<?php echo htmlspecialchars($student['full_name'] ?? ''); ?>"

                    readonly

                >

                <span class="help-text">

                    Student name cannot be changed.

                </span>

            </div>


            <div class="form-group">

                <label>

                    Email

                </label>

                <input

                    type="email"

                    value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>"

                    readonly

                >

                <span class="help-text">

                    Student email cannot be changed.

                </span>

            </div>


            <div class="form-group">

                <label>

                    Phone Number

                    <span class="required">*</span>

                </label>

                <input

                    type="text"

                    name="phone_number"

                    value="<?php echo htmlspecialchars($student['phone_number'] ?? ''); ?>"

                    placeholder="Enter phone number"

                    inputmode="numeric"

                    required

                >

                <span class="help-text">

                    Enter numbers only. Minimum 10 digits.

                </span>

            </div>


            <div class="form-group">

                <label>

                    Program

                </label>

                <input

                    type="text"

                    value="<?php echo htmlspecialchars($student['program'] ?? ''); ?>"

                    readonly

                >

                <span class="help-text">

                    Student program cannot be changed.

                </span>

            </div>


            <div class="form-group">

                <label>

                    Semester

                    <span class="required">*</span>

                </label>

                <select

                    name="semester"

                    required

                >

                    <option value="">

                        Select Semester

                    </option>


                    <?php for($i = 1; $i <= 10; $i++){ ?>

                        <option

                            value="<?php echo $i; ?>"

                            <?php

                            if((string)($student['semester'] ?? '') === (string)$i){

                                echo "selected";

                            }

                            ?>

                        >

                            <?php echo $i; ?>

                        </option>

                    <?php } ?>


                </select>

            </div>


            <div class="form-group">

                <label>

                    Status

                    <span class="required">*</span>

                </label>

                <select

                    name="status"

                    required

                >

                    <option

                        value="Active"

                        <?php

                        if(($student['status'] ?? '') == "Active"){

                            echo "selected";

                        }

                        ?>

                    >

                        Active

                    </option>


                    <option

                        value="Inactive"

                        <?php

                        if(($student['status'] ?? '') == "Inactive"){

                            echo "selected";

                        }

                        ?>

                    >

                        Inactive

                    </option>

                </select>

            </div>


            <div class="form-group full-width">

                <label>

                    Address

                </label>

                <textarea

                    name="address"

                    placeholder="Enter student's address"

                ><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>

            </div>


            <div class="form-group">

                <label>

                    Guardian Name

                </label>

                <input

                    type="text"

                    name="guardian_name"

                    value="<?php echo htmlspecialchars($student['guardian_name'] ?? ''); ?>"

                    placeholder="Enter guardian name"

                >

            </div>


            <div class="form-group">

                <label>

                    Guardian Phone

                </label>

                <input

                    type="text"

                    name="guardian_phone"

                    value="<?php echo htmlspecialchars($student['guardian_phone'] ?? ''); ?>"

                    placeholder="Enter guardian phone number"

                    inputmode="numeric"

                >

                <span class="help-text">

                    Enter numbers only. Minimum 10 digits if provided.

                </span>

            </div>


            <div class="form-group">

                <label>

                    Current Balance

                    <span class="required">*</span>

                </label>


                <div class="balance-wrapper">


                    <span>

                        RM

                    </span>


                    <input

                        type="number"

                        name="current_balance"

                        value="<?php echo htmlspecialchars($student['current_balance'] ?? '0.00'); ?>"

                        min="0"

                        step="0.01"

                        placeholder="0.00"

                        required

                    >


                </div>


                <span class="help-text">

                    Enter the student's current medical claim balance.

                </span>

            </div>


            <div class="form-group">

                <label>

                    Password

                </label>


                <div class="password-box">


                    <input

                        type="password"

                        name="password"

                        placeholder="Enter new password"

                        autocomplete="new-password"

                    >


                </div>


                <span class="password-note">

                    Leave blank if the current password should remain unchanged.

                </span>

            </div>


        </div>


        <div class="button-area">


            <a

                href="view_student.php"

                class="back-btn"

            >

                ← Back to Student List

            </a>


            <button

                type="submit"

                name="update"

                class="update-btn"

            >

                Update Student

            </button>


        </div>


    </form>


    <div class="info-box">

        <strong>

            Student Information:

        </strong>

        Student ID, IC Number, Full Name, Email and Program are protected
        and cannot be changed from this page. Phone Number, Semester,
        Address, Guardian Name, Guardian Phone, Status, Current Balance
        and Password can be updated.

    </div>


    <div class="footer">

        e-Medical Card &nbsp; | &nbsp; UIS Local Student System

    </div>


</div>


</body>

</html>