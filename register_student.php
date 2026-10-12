<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db_connect.php";


if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $student_id     = trim($_POST['student_id'] ?? '');

    $full_name      = trim($_POST['full_name'] ?? '');

    $ic_number      = trim($_POST['ic_number'] ?? '');

    $email          = trim($_POST['email'] ?? '');

    $password       = $_POST['password'] ?? '';

    $phone_number   = trim($_POST['phone_number'] ?? '');

    $program        = trim($_POST['program'] ?? '');

    $semester       = isset($_POST['semester']) && $_POST['semester'] !== ''
        ? (int)$_POST['semester']
        : null;

    $address        = trim($_POST['address'] ?? '');

    $guardian_name  = trim($_POST['guardian_name'] ?? '');

    $guardian_phone = trim($_POST['guardian_phone'] ?? '');

    $status         = $_POST['status'] ?? 'Active';

    $current_balance = isset($_POST['current_balance']) && $_POST['current_balance'] !== ''
        ? (float)$_POST['current_balance']
        : 120.00;


    if (
        $student_id === '' ||
        $full_name === '' ||
        $ic_number === '' ||
        $email === '' ||
        $password === '' ||
        $phone_number === '' ||
        $program === ''
    ) {

        $_SESSION['register_status'] = "failed";

        header("Location: register_student.php");
        exit();
    }


    if (!preg_match('/^[0-9]{12}$/', $ic_number)) {

        $_SESSION['register_status'] = "ic_error";

        header("Location: register_student.php");
        exit();
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $_SESSION['register_status'] = "email_error";

        header("Location: register_student.php");
        exit();
    }


    if (!preg_match('/^[0-9]{10,}$/', $phone_number)) {

        $_SESSION['register_status'] = "phone_error";

        header("Location: register_student.php");
        exit();
    }


    if (
        $guardian_phone !== '' &&
        !preg_match('/^[0-9]{10,}$/', $guardian_phone)
    ) {

        $_SESSION['register_status'] = "guardian_phone_error";

        header("Location: register_student.php");
        exit();
    }


    if ($semester !== null && ($semester < 1 || $semester > 10)) {

        $_SESSION['register_status'] = "semester_error";

        header("Location: register_student.php");
        exit();
    }


    if ($current_balance < 0) {

        $_SESSION['register_status'] = "balance_error";

        header("Location: register_student.php");
        exit();
    }


    try {


        $stmt = $conn->prepare("
            SELECT student_id
            FROM students
            WHERE student_id = ?
            LIMIT 1
        ");

        $stmt->execute([$student_id]);

        if ($stmt->fetch()) {

            $_SESSION['register_status'] = "student_exist";

            header("Location: register_student.php");
            exit();
        }


        $stmt = $conn->prepare("
            SELECT ic_number
            FROM students
            WHERE ic_number = ?
            LIMIT 1
        ");

        $stmt->execute([$ic_number]);

        if ($stmt->fetch()) {

            $_SESSION['register_status'] = "ic_exist";

            header("Location: register_student.php");
            exit();
        }


        $stmt = $conn->prepare("
            SELECT email
            FROM students
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        if ($stmt->fetch()) {

            $_SESSION['register_status'] = "email_exist";

            header("Location: register_student.php");
            exit();
        }


        $student_password = $password;


        $stmt = $conn->prepare("
            INSERT INTO students
            (
                student_id,
                full_name,
                ic_number,
                email,
                password,
                phone_number,
                program,
                semester,
                address,
                guardian_name,
                guardian_phone,
                status,
                current_balance
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )
        ");


        $stmt->execute([
            $student_id,
            $full_name,
            $ic_number,
            $email,
            $student_password,
            $phone_number,
            $program,
            $semester,
            $address !== '' ? $address : null,
            $guardian_name !== '' ? $guardian_name : null,
            $guardian_phone !== '' ? $guardian_phone : null,
            $status,
            $current_balance
        ]);


        $_SESSION['register_status'] = "success";

        header("Location: register_student.php");
        exit();


    } catch (PDOException $e) {


        die("Registration failed: " . $e->getMessage());
    }
}


?>

<!DOCTYPE html>

<html>

<head>

<title>Student Registration - EMCS</title>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    display:flex;

    justify-content:center;

    align-items:center;

    padding:30px 20px;

}


.register-box{

    width:850px;

    max-width:100%;

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


.form-title{

    margin-bottom:25px;

}


.form-title h2{

    color:#2f6468;

    font-size:20px;

    margin-bottom:6px;

}


.form-title p{

    color:#777;

    font-size:13px;

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


.form-group input::placeholder,
.form-group textarea::placeholder{

    color:#aaa;

}


.help-text{

    margin-top:5px;

    color:#888;

    font-size:11px;

}


.full-width{

    grid-column:1 / -1;

}


.button-area{

    display:flex;

    gap:12px;

    margin-top:28px;

    padding-top:22px;

    border-top:1px solid #eeeeee;

}


.register-btn{

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


.register-btn:hover{

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


    .register-box{

        padding:25px 20px;

    }


    .header h1{

        font-size:23px;

    }


    .form-grid{

        grid-template-columns:1fr;

        gap:16px;

    }


    .full-width{

        grid-column:auto;

    }


    .button-area{

        flex-direction:column;

    }


    .register-btn,
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

<div class="register-box">


<div class="header">

    <div class="header-left">

        <img src="images/logoEMCS.png" class="emcs-logo">

        <div>

            <h1>
                E-MEDICAL CARD
            </h1>

            <p>
                STUDENT REGISTRATION
            </p>

        </div>

    </div>

    <img src="images/logouis.png" class="uis-logo">

</div>


<div class="form-title">

    <h2>
        Register New Student
    </h2>

    <p>
        Enter the student's information to create a new account.
    </p>

</div>


<form method="POST" action="register_student.php">

    <div class="form-grid">


        <div class="form-group">

            <label>
                Student ID <span class="required">*</span>
            </label>

            <input

                type="text"

                name="student_id"

                placeholder="Enter Student ID"

                maxlength="15"

                required

            >

        </div>


        <div class="form-group">

            <label>
                Full Name <span class="required">*</span>
            </label>

            <input

                type="text"

                name="full_name"

                placeholder="Enter Full Name"

                maxlength="100"

                required

            >

        </div>


        <div class="form-group">

            <label>
                IC Number <span class="required">*</span>
            </label>

            <input

                type="text"

                name="ic_number"

                placeholder="12 digit IC number"

                maxlength="12"

                inputmode="numeric"

                pattern="[0-9]{12}"

                required

            >

            <span class="help-text">
                Enter exactly 12 digits.
            </span>

        </div>


        <div class="form-group">

            <label>
                Email <span class="required">*</span>
            </label>

            <input

                type="email"

                name="email"

                placeholder="example@gmail.com"

                maxlength="100"

                required

            >

        </div>


        <div class="form-group">

            <label>
                Password <span class="required">*</span>
            </label>

            <input

                type="password"

                name="password"

                placeholder="Enter Password"

                required

            >

        </div>


        <div class="form-group">

            <label>
                Phone Number <span class="required">*</span>
            </label>

            <input

                type="text"

                name="phone_number"

                placeholder="Enter phone number"

                inputmode="numeric"

                pattern="[0-9]{10,}"

                required

            >

            <span class="help-text">
                Enter at least 10 digits.
            </span>

        </div>


        <div class="form-group">

            <label>
                Program <span class="required">*</span>
            </label>

            <select name="program" required>

                <option value="">
                    Select Program
                </option>

                <option value="IS11">
                    IS11 - Diploma Pengajian Bahasa Al-Quran
                </option>

                <option value="IS13">
                    IS13 - Diploma Akidah dan Pemikiran Islam
                </option>

                <option value="IS14">
                    IS14 - Diploma Al-Quran dan As-Sunnah
                </option>

                <option value="IS15">
                    IS15 - Diploma Dakwah
                </option>

                <option value="IS16">
                    IS16 - Diploma Tahfiz Al-Quran dan Al-Qiraat
                </option>

                <option value="IS12">
                    IS12 - Diploma Pengajian Syariah
                </option>

                <option value="IS18">
                    IS18 - Diploma Syariah dan Perundangan Islam
                </option>

                <option value="MS31">
                    MS31 - Diploma Perakaunan
                </option>

                <option value="MS32">
                    MS32 - Diploma Pengurusan Perniagaan
                </option>

                <option value="MS33">
                    MS33 - Diploma Perbankan Islam
                </option>

                <option value="MS34">
                    MS34 - Diploma Pengurusan Sumber Manusia
                </option>

                <option value="MS36">
                    MS36 - Diploma Multimedia
                </option>

                <option value="MS39">
                    MS39 - Diploma Sains Komputer
                </option>

                <option value="ES52">
                    ES52 - Diploma Perguruan (TESL)
                </option>

                <option value="ES53">
                    ES53 - Diploma Perguruan (Pendidikan Islam)
                </option>

            </select>

        </div>


        <div class="form-group">

            <label>
                Semester
            </label>

            <select name="semester">

                <option value="">
                    Select Semester
                </option>

                <option value="1">
                    1
                </option>

                <option value="2">
                    2
                </option>

                <option value="3">
                    3
                </option>

                <option value="4">
                    4
                </option>

                <option value="5">
                    5
                </option>

                <option value="6">
                    6
                </option>

                <option value="7">
                    7
                </option>

                <option value="8">
                    8
                </option>

                <option value="9">
                    9
                </option>

                <option value="10">
                    10
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

                maxlength="255"
            >
            </textarea>

        </div>


        <div class="form-group">

            <label>
                Guardian Name
            </label>

            <input

                type="text"

                name="guardian_name"

                placeholder="Enter guardian name"

                maxlength="100"

            >

        </div>


        <div class="form-group">

            <label>
                Guardian Phone
            </label>

            <input

                type="text"

                name="guardian_phone"

                placeholder="Enter guardian phone number"

                maxlength="20"

                inputmode="numeric"

                pattern="[0-9]{10,}"

            >

            <span class="help-text">
                Enter at least 10 digits if provided.
            </span>

        </div>


        <div class="form-group">

            <label>
                Current Balance
            </label>

            <input

                type="number"

                name="current_balance"

                placeholder="120.00"

                value="120.00"

                min="0"

                step="0.01"

            >

            <span class="help-text">
                Default balance is RM120.00.
            </span>

        </div>


        <div class="form-group">

            <label>
                Status <span class="required">*</span>
            </label>

            <select name="status" required>

                <option value="Active">
                    Active
                </option>

                <option value="Inactive">
                    Inactive
                </option>

            </select>

        </div>


    </div>


    <div class="button-area">

        <a

            href="dashboard_admin.php"

            class="back-btn"

        >

            Back To Dashboard

        </a>

        <button

            type="submit"

            class="register-btn"

        >

            Register Student

        </button>

    </div>

</form>


<div class="info-box">

    <strong>
        Registration Information:
    </strong>

    Please make sure the Student ID, IC number, email,
    phone number and other student information are correct
    before registering the account.

</div>


<div class="footer">

    e-Medical Card &nbsp; | &nbsp; UIS Local Student System

</div>

</div>


<?php

if(isset($_SESSION['register_status'])){

    $status = $_SESSION['register_status'];


    if($status == "success"){

?>


<script>

Swal.fire({

    icon:"success",

    title:"Registration Successful!",

    text:"Student account has been created.",

    confirmButtonColor:"#2f6468",

    allowOutsideClick:false

}).then(() => {

    window.location.href = "dashboard_admin.php";

});

</script>


<?php

    }

    elseif($status == "student_exist"){

?>


<script>

Swal.fire({

    icon:"error",

    title:"Student ID Exists!",

    text:"This Student ID has already been registered.",

    confirmButtonColor:"#dc3545"

});

</script>


<?php

    }

    elseif($status == "ic_exist"){

?>


<script>

Swal.fire({

    icon:"error",

    title:"IC Number Exists!",

    text:"This IC Number has already been registered.",

    confirmButtonColor:"#dc3545"

});

</script>


<?php

    }

    elseif($status == "email_exist"){

?>


<script>

Swal.fire({

    icon:"error",

    title:"Email Exists!",

    text:"This email has already been registered.",

    confirmButtonColor:"#dc3545"

});

</script>


<?php

    }

    elseif($status == "ic_error"){

?>


<script>

Swal.fire({

    icon:"error",

    title:"Invalid IC Number!",

    text:"IC number must contain exactly 12 digits.",

    confirmButtonColor:"#dc3545"

});

</script>


<?php

    }

    elseif($status == "email_error"){

?>


<script>

Swal.fire({

    icon:"error",

    title:"Invalid Email!",

    text:"Please enter a valid email.",

    confirmButtonColor:"#dc3545"

});

</script>


<?php

    }

    elseif($status == "phone_error"){

?>


<script>

Swal.fire({

    icon:"error",

    title:"Invalid Phone Number!",

    text:"Phone number must contain at least 10 numbers.",

    confirmButtonColor:"#dc3545"

});

</script>


<?php

    }

    elseif($status == "guardian_phone_error"){

?>


<script>

Swal.fire({

    icon:"error",

    title:"Invalid Guardian Phone!",

    text:"Guardian phone number must contain at least 10 numbers.",

    confirmButtonColor:"#dc3545"

});

</script>


<?php

    }

    elseif($status == "semester_error"){

?>


<script>

Swal.fire({

    icon:"error",

    title:"Invalid Semester!",

    text:"Semester must be between 1 and 10.",

    confirmButtonColor:"#dc3545"

});

</script>


<?php

    }

    elseif($status == "balance_error"){

?>


<script>

Swal.fire({

    icon:"error",

    title:"Invalid Balance!",

    text:"Current balance cannot be negative.",

    confirmButtonColor:"#dc3545"

});

</script>


<?php

    }

    elseif($status == "failed"){

?>


<script>

Swal.fire({

    icon:"error",

    title:"Registration Failed!",

    text:"Something went wrong while registering the student.",

    confirmButtonColor:"#dc3545"

});

</script>


<?php

    }

    unset($_SESSION['register_status']);

}

?>

</body>

</html>