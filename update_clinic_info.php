<?php

session_start();

include("db_connect.php");


if(!isset($_SESSION['clinic_id'])){

    header("Location: login_clinic.php");

    exit();

}


$clinicID = $_SESSION['clinic_id'];

$message = "";


$sql = "SELECT
            clinic_id,
            clinic_name,
            address,
            contact_number,
            operating_hours
        FROM clinics
        WHERE clinic_id = ?";

$stmt = $conn->prepare($sql);

$stmt->execute([$clinicID]);

$clinic = $stmt->fetch(PDO::FETCH_ASSOC);


if(!$clinic){

    die("Clinic information not found.");

}


if($_SERVER["REQUEST_METHOD"] == "POST"){

    $contact = trim($_POST['contact_number'] ?? '');

    $hours = trim($_POST['operating_hours'] ?? '');


    if(!preg_match('/^[0-9]{10,}$/', $contact)){

        $_SESSION['clinic_status'] = "phone_error";

        header("Location: clinic_profile.php");

        exit();

    }


    if($hours == ''){

        $_SESSION['clinic_status'] = "hours_error";

        header("Location: clinic_profile.php");

        exit();

    }


    $sql = "UPDATE clinics

            SET
                contact_number = ?,
                operating_hours = ?

            WHERE clinic_id = ?";


    $stmt = $conn->prepare($sql);


    if($stmt->execute([

        $contact,
        $hours,
        $clinicID

    ])){


        $_SESSION['clinic_status'] = "update_success";

        header("Location: clinic_profile.php");

        exit();

    }

    else{

        $_SESSION['clinic_status'] = "update_error";

        header("Location: clinic_profile.php");

        exit();

    }

}

?>


<!DOCTYPE html>

<html>

<head>

<title>Clinic Profile</title>


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


.header-left{

    display:flex;

    align-items:center;

}


.clinic-icon{

    width:55px;

    height:55px;

    margin-right:15px;

}


.header-title h2{

    color:#2f6468;

    font-size:20px;

    margin-bottom:5px;

}


.header-title p{

    color:#777;

    font-size:13px;

}


.uis-logo{

    width:80px;

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

    border-radius:20px;

    padding:30px;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

    max-width:900px;

    margin-bottom:25px;

}


.profile-heading{

    display:flex;

    align-items:center;

    margin-bottom:25px;

    padding-bottom:20px;

    border-bottom:1px solid #eeeeee;

}


.profile-heading-icon{

    width:60px;

    height:60px;

    background:#e5f4ef;

    border-radius:50%;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:30px;

    margin-right:15px;

}


.profile-heading-text h2{

    color:#2f6468;

    font-size:20px;

    margin-bottom:5px;

}


.profile-heading-text p{

    color:#777;

    font-size:13px;

}


.profile-row{

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:16px 0;

    border-bottom:1px solid #eeeeee;

    gap:30px;

}


.profile-row:last-child{

    border-bottom:none;

}


.profile-label{

    color:#777;

    font-size:14px;

    font-weight:bold;

    min-width:180px;

}


.profile-value{

    color:#2f6468;

    font-size:14px;

    font-weight:bold;

    text-align:right;

    line-height:1.5;

}


.readonly-badge{

    display:inline-block;

    background:#f2f2f2;

    color:#777;

    padding:5px 10px;

    border-radius:15px;

    font-size:10px;

    font-weight:bold;

    margin-left:8px;

}


.edit-section{

    margin-top:30px;

    padding-top:25px;

    border-top:1px solid #eeeeee;

}


.edit-section h3{

    color:#2f6468;

    font-size:18px;

    margin-bottom:8px;

}


.edit-section-description{

    color:#777;

    font-size:13px;

    margin-bottom:20px;

}


.form-group{

    margin-bottom:18px;

}


label{

    display:block;

    color:#555;

    font-size:13px;

    font-weight:bold;

    margin-bottom:8px;

}


input{

    width:100%;

    padding:13px 15px;

    border-radius:10px;

    border:1px solid #ddd;

    background:#fafafa;

    color:#333;

    font-size:14px;

    outline:none;

    transition:0.2s;

}


input:focus{

    border-color:#2f6468;

    background:white;

    box-shadow:0 0 0 3px rgba(47,100,104,0.08);

}


.update-btn{

    width:100%;

    margin-top:5px;

    padding:14px;

    border:none;

    border-radius:30px;

    background:#2f6468;

    color:white;

    font-size:14px;

    font-weight:bold;

    cursor:pointer;

    transition:0.2s;

}


.update-btn:hover{

    background:#234d50;

}


.info-note{

    margin-top:18px;

    padding:14px 16px;

    background:#f2f2f2;

    border-radius:10px;

    color:#777;

    font-size:12px;

    line-height:1.5;

}


.info-note strong{

    color:#2f6468;

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


    .profile-card{

        max-width:100%;

    }

}


@media(max-width:600px){

    .main{

        padding:20px;

    }


    .header{

        padding:18px;

    }


    .clinic-icon{

        width:45px;

        height:45px;

    }


    .uis-logo{

        width:65px;

    }


    .page-title h1{

        font-size:23px;

    }


    .profile-card{

        padding:20px;

    }


    .profile-row{

        flex-direction:column;

        align-items:flex-start;

        gap:8px;

    }


    .profile-label{

        min-width:auto;

    }


    .profile-value{

        text-align:left;

    }


    .menu a{

        min-width:100%;

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


        <a href="submit_claim.php">

            Submit Medical Claim

        </a>


        <a href="clinic_profile.php" class="active">

            View Profile

        </a>


        <a href="report.php">

            Report

        </a>


        <a href="logout.php">

            Logout

        </a>


    </div>


    <div class="sidebar-bottom">

        Need help?<br>

        Contact UIS Health Services

    </div>


</div>


<div class="main">


    <div class="header">


        <div class="header-left">


            <img

                src="images/clinic.png"

                class="clinic-icon"

                alt="Clinic"

            >


            <div class="header-title">


                <h2>

                    Clinic Profile

                </h2>


                <p>

                    View and manage your clinic information.

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

            Clinic Profile

        </h1>


        <p>

            View your registered clinic information and update
            your contact details.

        </p>


    </div>


    <div class="profile-card">


        <div class="profile-heading">


            <div class="profile-heading-icon">

                🏥

            </div>


            <div class="profile-heading-text">


                <h2>

                    <?php echo htmlspecialchars($clinic['clinic_name']); ?>

                </h2>


                <p>

                    UIS Panel Clinic

                </p>


            </div>


        </div>


        <div class="profile-row">


            <span class="profile-label">

                Clinic ID

                <span class="readonly-badge">

                    READ ONLY

                </span>

            </span>


            <span class="profile-value">

                <?php echo htmlspecialchars($clinic['clinic_id']); ?>

            </span>


        </div>


        <div class="profile-row">


            <span class="profile-label">

                Clinic Name

                <span class="readonly-badge">

                    READ ONLY

                </span>

            </span>


            <span class="profile-value">

                <?php echo htmlspecialchars($clinic['clinic_name']); ?>

            </span>


        </div>


        <div class="profile-row">


            <span class="profile-label">

                Address

                <span class="readonly-badge">

                    READ ONLY

                </span>

            </span>


            <span class="profile-value">

                <?php echo nl2br(htmlspecialchars($clinic['address'])); ?>

            </span>


        </div>


        <div class="edit-section">


            <h3>

                Contact Information

            </h3>


            <p class="edit-section-description">

                You can update your clinic contact number
                and operating hours below.

            </p>



            <form method="POST">


                <div class="form-group">


                    <label>

                        Contact Number

                    </label>


                    <input

                        type="text"

                        name="contact_number"

                        value="<?php echo htmlspecialchars($clinic['contact_number']); ?>"

                        placeholder="Example: 0312345678"

                        required

                    >


                </div>


                <div class="form-group">


                    <label>

                        Operating Hours

                    </label>


                    <input

                        type="text"

                        name="operating_hours"

                        value="<?php echo htmlspecialchars($clinic['operating_hours']); ?>"

                        placeholder="Example: Monday - Friday, 8:00 AM - 5:00 PM"

                        required

                    >


                </div>


                <button

                    type="submit"

                    class="update-btn"

                >

                    Save Changes

                </button>


                <div class="info-note">

                    <strong>Note:</strong>

                    Clinic ID, clinic name and address are managed
                    by UIS Health Services and cannot be changed
                    by the clinic. Only the contact number and
                    operating hours can be updated.

                </div>


            </form>


        </div>


    </div>


</div>


<?php

if(isset($_SESSION['clinic_status'])){

    $status = $_SESSION['clinic_status'];

    unset($_SESSION['clinic_status']);

    if($status == "phone_error"){

?>

        <div class="popup-overlay">

            <div class="popup">


                <div class="popup-icon error">

                    !

                </div>


                <h3>

                    Invalid Contact Number

                </h3>


                <p>

                    Contact number must contain numbers only
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


    elseif($status == "hours_error"){

?>

        <div class="popup-overlay">

            <div class="popup">


                <div class="popup-icon error">

                    !

                </div>


                <h3>

                    Operating Hours Required

                </h3>


                <p>

                    Please enter your clinic operating hours.

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

                    Your clinic contact information has been
                    updated successfully.

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

                    Unable to update your clinic information.
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


function closePopup(){

    const popup = document.querySelector(".popup-overlay");

    if(popup){

        popup.remove();

    }

}

</script>


</body>

</html>