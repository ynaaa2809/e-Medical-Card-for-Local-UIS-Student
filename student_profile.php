<?php

session_start();


if(!isset($_SESSION['student_id'])){

    header("Location: login_student.php");

    exit();

}


include("db_connect.php");


$studentID = $_SESSION['student_id'];


if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_phone'])){

    $phone = trim($_POST['phone_number'] ?? '');


    if(!preg_match('/^[0-9]{10,}$/', $phone)){

        $_SESSION['phone_status'] = "phone_error";

        header("Location: student_profile.php");

        exit();

    }


    $sql = "UPDATE students
            SET phone_number = ?
            WHERE student_id = ?";


    $stmt = $conn->prepare($sql);


    if($stmt->execute([

        $phone,
        $studentID

    ])){

        $_SESSION['phone_status'] = "update_success";

        header("Location: student_profile.php");

        exit();

    }

    else{

        $_SESSION['phone_status'] = "update_error";

        header("Location: student_profile.php");

        exit();

    }

}


$sql = "SELECT *
        FROM students
        WHERE student_id = ?";


$stmt = $conn->prepare($sql);


$stmt->execute([

    $studentID

]);


$student = $stmt->fetch(PDO::FETCH_ASSOC);


if(!$student){

    die("Student information not found.");

}


$studentIDValue = $student['student_id'] ?? '';

$studentName = $student['full_name'] ?? '';

$icNumber = $student['ic_number'] ?? '';

$email = $student['email'] ?? '';

$phoneNumber = $student['phone_number'] ?? '';

$program = $student['program'] ?? '';

$semester = $student['semester'] ?? '';

$address = $student['address'] ?? '';

$guardianName = $student['guardian_name'] ?? '';

$guardianPhone = $student['guardian_phone'] ?? '';

$statusValue = $student['status'] ?? '';

?>


<!DOCTYPE html>

<html>

<head>

<title>Student Profile</title>


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


.profile-card{

    background:white;

    border-radius:15px;

    padding:30px;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

    margin-bottom:20px;

}


.profile-card-header{

    display:flex;

    align-items:center;

    margin-bottom:25px;

    padding-bottom:20px;

    border-bottom:1px solid #f0f0f0;

}


.profile-card-header h2{

    color:#2f6468;

    font-size:20px;

    margin-bottom:5px;

}


.profile-card-header p{

    color:#777;

    font-size:13px;

}


.info-row{

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:17px 0;

    border-bottom:1px solid #f2f2f2;

    gap:20px;

}


.info-row:last-child{

    border-bottom:none;

}


.info-label{

    color:#777;

    font-size:13px;

    min-width:150px;

}


.info-value{

    color:#2f6468;

    font-size:14px;

    font-weight:bold;

    text-align:right;

    flex:1;

    word-break:break-word;

    line-height:1.5;

}


.phone-display{

    display:flex;

    align-items:center;

    justify-content:flex-end;

    gap:10px;

}


.edit-btn{

    border:none;

    background:#e8f1f0;

    color:#2f6468;

    width:32px;

    height:32px;

    border-radius:50%;

    cursor:pointer;

    font-size:16px;

    display:flex;

    align-items:center;

    justify-content:center;

    transition:0.2s;

}


.edit-btn:hover{

    background:#2f6468;

    color:white;

}


.phone-edit{

    display:none;

    align-items:center;

    justify-content:flex-end;

    gap:8px;

    flex-wrap:wrap;

}


.phone-input{

    width:220px;

    padding:10px 12px;

    border:1px solid #ccc;

    border-radius:8px;

    font-size:14px;

    color:#333;

    outline:none;

}


.phone-input:focus{

    border-color:#2f6468;

    box-shadow:0 0 0 2px rgba(47,100,104,0.1);

}


.save-btn{

    border:none;

    background:#2f6468;

    color:white;

    padding:10px 15px;

    border-radius:8px;

    cursor:pointer;

    font-size:13px;

    font-weight:bold;

}


.save-btn:hover{

    background:#234d50;

}


.cancel-btn{

    border:none;

    background:#eee;

    color:#555;

    padding:10px 15px;

    border-radius:8px;

    cursor:pointer;

    font-size:13px;

}


.cancel-btn:hover{

    background:#ddd;

}


.status-badge{

    display:inline-block;

    background:#e8f1f0;

    color:#2f6468;

    padding:7px 15px;

    border-radius:20px;

    font-size:12px;

    font-weight:bold;

}


.popup-overlay{

    position:fixed;

    top:0;

    left:0;

    width:100%;

    height:100%;

    background:rgba(0,0,0,0.45);

    display:flex;

    justify-content:center;

    align-items:center;

    z-index:9999;

}


.popup{

    background:white;

    width:380px;

    max-width:90%;

    padding:30px;

    border-radius:15px;

    text-align:center;

    box-shadow:0 5px 20px rgba(0,0,0,0.20);

}


.popup-icon{

    width:55px;

    height:55px;

    margin:0 auto 15px;

    border-radius:50%;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:28px;

    font-weight:bold;

}


.popup-icon.success{

    background:#e5f4ef;

    color:#2f6468;

}


.popup-icon.error{

    background:#fbe8e8;

    color:#a04444;

}


.popup h3{

    color:#333;

    font-size:19px;

    margin-bottom:10px;

}


.popup p{

    color:#777;

    font-size:14px;

    line-height:1.5;

    margin-bottom:20px;

}


.popup button{

    border:none;

    padding:11px 30px;

    border-radius:25px;

    background:#2f6468;

    color:white;

    font-size:14px;

    font-weight:bold;

    cursor:pointer;

}


.popup button:hover{

    background:#234d50;

}


.popup button.error-btn{

    background:#dc3545;

}


.popup button.error-btn:hover{

    background:#b02a37;

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

    .header{

        padding:18px;

    }


    .uis-logo{

        width:50px;

    }


    .profile-card{

        padding:20px;

    }


    .info-row{

        align-items:flex-start;

        flex-direction:column;

        gap:8px;

    }


    .info-value{

        text-align:left;

        width:100%;

    }


    .phone-display{

        justify-content:flex-start;

        width:100%;

    }


    .phone-edit{

        justify-content:flex-start;

        width:100%;

    }


    .phone-input{

        width:100%;

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


        <a href="student_profile.php" class="active">

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

                    <?php echo htmlspecialchars($studentName); ?>

                </h2>


                <p>

                    Student ID:

                    <?php echo htmlspecialchars($studentIDValue); ?>

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

            Student Profile

        </h1>


        <p>

            View and manage your personal information.

        </p>


    </div>


    <div class="profile-card">


        <div class="profile-card-header">


            <div>


                <h2>

                    <?php echo htmlspecialchars($studentName); ?>

                </h2>


                <p>

                    Student ID:

                    <?php echo htmlspecialchars($studentIDValue); ?>

                </p>


            </div>


        </div>


        <div class="info-row">


            <span class="info-label">

                Student ID

            </span>


            <span class="info-value">

                <?php echo htmlspecialchars($studentIDValue); ?>

            </span>


        </div>


        <div class="info-row">


            <span class="info-label">

                Full Name

            </span>


            <span class="info-value">

                <?php echo htmlspecialchars($studentName); ?>

            </span>


        </div>


        <div class="info-row">


            <span class="info-label">

                IC Number

            </span>


            <span class="info-value">

                <?php echo htmlspecialchars($icNumber); ?>

            </span>


        </div>


        <div class="info-row">


            <span class="info-label">

                Email

            </span>


            <span class="info-value">

                <?php echo htmlspecialchars($email); ?>

            </span>


        </div>


        <div class="info-row">


            <span class="info-label">

                Phone Number

            </span>


            <div class="info-value">


                <div

                    class="phone-display"

                    id="phoneDisplay"

                >


                    <span>

                        <?php

                        echo $phoneNumber !== ''
                            ? htmlspecialchars($phoneNumber)
                            : 'Not provided';

                        ?>

                    </span>


                    <button

                        type="button"

                        class="edit-btn"

                        onclick="editPhone()"

                        title="Edit phone number"

                    >

                        ✎

                    </button>


                </div>


                <form

                    method="POST"

                    class="phone-edit"

                    id="phoneEdit"

                >


                    <input

                        type="text"

                        name="phone_number"

                        class="phone-input"

                        value="<?php echo htmlspecialchars($phoneNumber); ?>"

                        placeholder="Enter phone number"

                        required

                    >


                    <button

                        type="submit"

                        name="update_phone"

                        class="save-btn"

                    >

                        Save

                    </button>


                    <button

                        type="button"

                        class="cancel-btn"

                        onclick="cancelPhone()"

                    >

                        Cancel

                    </button>


                </form>


            </div>


        </div>


        <div class="info-row">


            <span class="info-label">

                Program

            </span>


            <span class="info-value">

                <?php

                echo $program !== ''
                    ? htmlspecialchars($program)
                    : 'Not provided';

                ?>

            </span>


        </div>


        <div class="info-row">


            <span class="info-label">

                Semester

            </span>


            <span class="info-value">

                <?php

                echo ($semester !== '' && $semester !== null)
                    ? htmlspecialchars((string)$semester)
                    : 'Not provided';

                ?>

            </span>


        </div>


        <div class="info-row">


            <span class="info-label">

                Address

            </span>


            <span class="info-value">

                <?php

                echo $address !== ''
                    ? nl2br(htmlspecialchars($address))
                    : 'Not provided';

                ?>

            </span>


        </div>


        <div class="info-row">


            <span class="info-label">

                Guardian Name

            </span>


            <span class="info-value">

                <?php

                echo $guardianName !== ''
                    ? htmlspecialchars($guardianName)
                    : 'Not provided';

                ?>

            </span>


        </div>


        <div class="info-row">


            <span class="info-label">

                Guardian Phone

            </span>


            <span class="info-value">

                <?php

                echo $guardianPhone !== ''
                    ? htmlspecialchars($guardianPhone)
                    : 'Not provided';

                ?>

            </span>


        </div>


        <div class="info-row">


            <span class="info-label">

                Status

            </span>


            <span class="info-value">


                <span class="status-badge">

                    <?php echo htmlspecialchars($statusValue); ?>

                </span>


            </span>


        </div>


    </div>


</div>


<?php

if(isset($_SESSION['phone_status'])){

    $status = $_SESSION['phone_status'];

    unset($_SESSION['phone_status']);


    if($status == "phone_error"){

?>

        <div class="popup-overlay">

            <div class="popup">


                <div class="popup-icon error">

                    !

                </div>


                <h3>

                    Invalid Phone Number

                </h3>


                <p>

                    Phone number must contain numbers only

                    and must have at least 10 digits.

                </p>


                <button

                    class="error-btn"

                    onclick="closePopup()"

                >

                    OK

                </button>


            </div>

        </div>

<?php

    }


    elseif($status == "update_success"){

?>

        <div class="popup-overlay">

            <div class="popup">


                <div class="popup-icon success">

                    ✓

                </div>


                <h3>

                    Update Successful

                </h3>


                <p>

                    Your phone number has been updated successfully.

                </p>


                <button

                    onclick="closePopup()"

                >

                    OK

                </button>


            </div>

        </div>

<?php

    }


    elseif($status == "update_error"){

?>

        <div class="popup-overlay">

            <div class="popup">


                <div class="popup-icon error">

                    !

                </div>


                <h3>

                    Update Failed

                </h3>


                <p>

                    Unable to update your phone number.

                    Please try again.

                </p>


                <button

                    class="error-btn"

                    onclick="closePopup()"

                >

                    OK

                </button>


            </div>

        </div>

<?php

    }

}

?>


<script>


function editPhone(){

    document.getElementById("phoneDisplay").style.display = "none";

    document.getElementById("phoneEdit").style.display = "flex";

}


function cancelPhone(){

    document.getElementById("phoneEdit").style.display = "none";

    document.getElementById("phoneDisplay").style.display = "flex";

}


function closePopup(){

    const popup = document.querySelector(".popup-overlay");


    if(popup){

        popup.remove();

    }

}

</script>


</body>

</html>