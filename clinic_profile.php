<?php

session_start();


if(!isset($_SESSION['clinic_id'])){

    header("Location: login_clinic.php");

    exit();

}


include("db_connect.php");


$clinicID = $_SESSION['clinic_id'];


if($_SERVER['REQUEST_METHOD'] === 'POST'){


    if(isset($_POST['update_name'])){

        $clinicName = trim($_POST['clinic_name']);


        if($clinicName === ""){

            $_SESSION['clinic_profile_status'] = "name_error";

            header("Location: clinic_profile.php");

            exit();

        }


        $sql = "UPDATE clinics
                SET clinic_name = ?
                WHERE clinic_id = ?";


        $stmt = $conn->prepare($sql);


        if($stmt->execute([

            $clinicName,
            $clinicID

        ])){

            $_SESSION['clinic_profile_status'] = "name_success";

            header("Location: clinic_profile.php");

            exit();

        }

        else{

            $_SESSION['clinic_profile_status'] = "name_update_error";

            header("Location: clinic_profile.php");

            exit();

        }

    }


    if(isset($_POST['update_address'])){

        $address = trim($_POST['address']);


        if($address === ""){

            $_SESSION['clinic_profile_status'] = "address_error";

            header("Location: clinic_profile.php");

            exit();

        }


        $sql = "UPDATE clinics
                SET address = ?
                WHERE clinic_id = ?";


        $stmt = $conn->prepare($sql);


        if($stmt->execute([

            $address,
            $clinicID

        ])){

            $_SESSION['clinic_profile_status'] = "address_success";

            header("Location: clinic_profile.php");

            exit();

        }

        else{

            $_SESSION['clinic_profile_status'] = "address_update_error";

            header("Location: clinic_profile.php");

            exit();

        }

    }


    if(isset($_POST['update_contact'])){

        $contact = trim($_POST['contact_number']);


        if(!preg_match('/^[0-9]{10,}$/', $contact)){

            $_SESSION['clinic_profile_status'] = "contact_error";

            header("Location: clinic_profile.php");

            exit();

        }


        $sql = "UPDATE clinics
                SET contact_number = ?
                WHERE clinic_id = ?";


        $stmt = $conn->prepare($sql);


        if($stmt->execute([

            $contact,
            $clinicID

        ])){

            $_SESSION['clinic_profile_status'] = "contact_success";

            header("Location: clinic_profile.php");

            exit();

        }

        else{

            $_SESSION['clinic_profile_status'] = "contact_update_error";

            header("Location: clinic_profile.php");

            exit();

        }

    }


    if(isset($_POST['update_email'])){

        $email = trim($_POST['email']);


        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){

            $_SESSION['clinic_profile_status'] = "email_error";

            header("Location: clinic_profile.php");

            exit();

        }


        $sql = "UPDATE clinics
                SET email = ?
                WHERE clinic_id = ?";


        $stmt = $conn->prepare($sql);


        if($stmt->execute([

            $email,
            $clinicID

        ])){

            $_SESSION['clinic_profile_status'] = "email_success";

            header("Location: clinic_profile.php");

            exit();

        }

        else{

            $_SESSION['clinic_profile_status'] = "email_update_error";

            header("Location: clinic_profile.php");

            exit();

        }

    }


    if(isset($_POST['update_hours'])){

        $hours = trim($_POST['operating_hours']);


        if($hours === ""){

            $_SESSION['clinic_profile_status'] = "hours_error";

            header("Location: clinic_profile.php");

            exit();

        }


        $sql = "UPDATE clinics
                SET operating_hours = ?
                WHERE clinic_id = ?";


        $stmt = $conn->prepare($sql);


        if($stmt->execute([

            $hours,
            $clinicID

        ])){

            $_SESSION['clinic_profile_status'] = "hours_success";

            header("Location: clinic_profile.php");

            exit();

        }

        else{

            $_SESSION['clinic_profile_status'] = "hours_update_error";

            header("Location: clinic_profile.php");

            exit();

        }

    }

}


$sql = "SELECT *
        FROM clinics
        WHERE clinic_id = ?";


$stmt = $conn->prepare($sql);


$stmt->execute([

    $clinicID

]);


$clinic = $stmt->fetch(PDO::FETCH_ASSOC);


if(!$clinic){

    die("Clinic information not found.");

}


$clinicName = $clinic['clinic_name'];

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


.profile-card{

    background:white;

    border-radius:20px;

    padding:30px;

    box-shadow:0 3px 10px rgba(0,0,0,0.08);

    margin-bottom:25px;

}


.profile-card-header{

    display:flex;

    align-items:center;

    margin-bottom:25px;

    padding-bottom:20px;

    border-bottom:1px solid #eeeeee;

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

    border-bottom:1px solid #eeeeee;

    gap:20px;

}


.info-row:last-child{

    border-bottom:none;

}


.info-label{

    color:#777;

    font-size:14px;

    min-width:150px;

}


.info-value{

    color:#2f6468;

    font-size:14px;

    font-weight:bold;

    text-align:right;

    flex:1;

}


.editable-display{

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


.edit-form{

    display:none;

    align-items:center;

    justify-content:flex-end;

    gap:8px;

    flex-wrap:wrap;

}


.edit-input{

    width:260px;

    padding:10px 12px;

    border:1px solid #ccc;

    border-radius:8px;

    font-size:14px;

    color:#333;

    outline:none;

}


.edit-input:focus{

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

    background:#eeeeee;

    color:#555;

    padding:10px 15px;

    border-radius:8px;

    cursor:pointer;

    font-size:13px;

}


.cancel-btn:hover{

    background:#dddddd;

}


.readonly-value{

    color:#2f6468;

    font-size:14px;

    font-weight:bold;

    text-align:right;

    flex:1;

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


    .profile-card{

        padding:25px;

    }


    .info-row{

        flex-direction:column;

        align-items:flex-start;

        gap:5px;

    }


    .info-value{

        text-align:left;

        width:100%;

    }


    .readonly-value{

        text-align:left;

        width:100%;

    }


    .editable-display{

        justify-content:flex-start;

        width:100%;

    }


    .edit-form{

        justify-content:flex-start;

        width:100%;

    }


    .edit-input{

        width:100%;

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


        <a href="clinic_profile.php" class="active">

            View Profile

        </a>


        <a href="submit_claim.php">

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

                    <?php echo htmlspecialchars($clinicName); ?>

                </h2>


                <p>

                    Clinic ID:

                    <?php echo htmlspecialchars($clinicID); ?>

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

            View and manage your clinic information.

        </p>


    </div>


    <div class="profile-card">


        <div class="profile-card-header">


            <div>


                <h2>

                    <?php echo htmlspecialchars($clinic['clinic_name']); ?>

                </h2>


                <p>

                    Clinic ID:

                    <?php echo htmlspecialchars($clinic['clinic_id']); ?>

                </p>


            </div>


        </div>


        <div class="info-row">


            <span class="info-label">

                Clinic ID

            </span>


            <span class="readonly-value">

                <?php echo htmlspecialchars($clinic['clinic_id']); ?>

            </span>


        </div>


        <div class="info-row">


            <span class="info-label">

                Clinic Name

            </span>


            <div class="info-value">


                <div

                    class="editable-display"

                    id="nameDisplay"

                >


                    <span>

                        <?php echo htmlspecialchars($clinic['clinic_name']); ?>

                    </span>


                    <button

                        type="button"

                        class="edit-btn"

                        onclick="editName()"

                        title="Edit clinic name"

                    >

                        ✎

                    </button>


                </div>


                <form

                    method="POST"

                    class="edit-form"

                    id="nameEdit"

                >


                    <input

                        type="text"

                        name="clinic_name"

                        class="edit-input"

                        value="<?php echo htmlspecialchars($clinic['clinic_name']); ?>"

                        placeholder="Enter clinic name"

                        required

                    >


                    <button

                        type="submit"

                        name="update_name"

                        class="save-btn"

                    >

                        Save

                    </button>


                    <button

                        type="button"

                        class="cancel-btn"

                        onclick="cancelName()"

                    >

                        Cancel

                    </button>


                </form>


            </div>


        </div>


        <div class="info-row">


            <span class="info-label">

                Address

            </span>


            <div class="info-value">


                <div

                    class="editable-display"

                    id="addressDisplay"

                >


                    <span>

                        <?php echo htmlspecialchars($clinic['address'] ?? ''); ?>

                    </span>


                    <button

                        type="button"

                        class="edit-btn"

                        onclick="editAddress()"

                        title="Edit address"

                    >

                        ✎

                    </button>


                </div>


                <form

                    method="POST"

                    class="edit-form"

                    id="addressEdit"

                >


                    <input

                        type="text"

                        name="address"

                        class="edit-input"

                        value="<?php echo htmlspecialchars($clinic['address'] ?? ''); ?>"

                        placeholder="Enter clinic address"

                        required

                    >


                    <button

                        type="submit"

                        name="update_address"

                        class="save-btn"

                    >

                        Save

                    </button>


                    <button

                        type="button"

                        class="cancel-btn"

                        onclick="cancelAddress()"

                    >

                        Cancel

                    </button>


                </form>


            </div>


        </div>


        <div class="info-row">


            <span class="info-label">

                Contact Number

            </span>


            <div class="info-value">


                <div

                    class="editable-display"

                    id="contactDisplay"

                >


                    <span>

                        <?php echo htmlspecialchars($clinic['contact_number']); ?>

                    </span>


                    <button

                        type="button"

                        class="edit-btn"

                        onclick="editContact()"

                        title="Edit contact number"

                    >

                        ✎

                    </button>


                </div>


                <form

                    method="POST"

                    class="edit-form"

                    id="contactEdit"

                >


                    <input

                        type="text"

                        name="contact_number"

                        class="edit-input"

                        value="<?php echo htmlspecialchars($clinic['contact_number']); ?>"

                        placeholder="Enter contact number"

                        required

                    >


                    <button

                        type="submit"

                        name="update_contact"

                        class="save-btn"

                    >

                        Save

                    </button>


                    <button

                        type="button"

                        class="cancel-btn"

                        onclick="cancelContact()"

                    >

                        Cancel

                    </button>


                </form>


            </div>


        </div>


        <div class="info-row">


            <span class="info-label">

                Email

            </span>


            <div class="info-value">


                <div

                    class="editable-display"

                    id="emailDisplay"

                >


                    <span>

                        <?php

                        echo htmlspecialchars(
                            $clinic['email'] ?? ''
                        );

                        ?>

                    </span>


                    <button

                        type="button"

                        class="edit-btn"

                        onclick="editEmail()"

                        title="Edit email"

                    >

                        ✎

                    </button>


                </div>


                <form

                    method="POST"

                    class="edit-form"

                    id="emailEdit"

                >


                    <input

                        type="email"

                        name="email"

                        class="edit-input"

                        value="<?php echo htmlspecialchars($clinic['email'] ?? ''); ?>"

                        placeholder="Enter email address"

                        required

                    >


                    <button

                        type="submit"

                        name="update_email"

                        class="save-btn"

                    >

                        Save

                    </button>


                    <button

                        type="button"

                        class="cancel-btn"

                        onclick="cancelEmail()"

                    >

                        Cancel

                    </button>


                </form>


            </div>


        </div>


        <div class="info-row">


            <span class="info-label">

                Operating Hours

            </span>


            <div class="info-value">


                <div

                    class="editable-display"

                    id="hoursDisplay"

                >


                    <span>

                        <?php

                        echo htmlspecialchars(
                            $clinic['operating_hours']
                        );

                        ?>

                    </span>


                    <button

                        type="button"

                        class="edit-btn"

                        onclick="editHours()"

                        title="Edit operating hours"

                    >

                        ✎

                    </button>


                </div>


                <form

                    method="POST"

                    class="edit-form"

                    id="hoursEdit"

                >


                    <input

                        type="text"

                        name="operating_hours"

                        class="edit-input"

                        value="<?php echo htmlspecialchars($clinic['operating_hours']); ?>"

                        placeholder="Example: Monday - Friday, 8:00 AM - 5:00 PM"

                        required

                    >


                    <button

                        type="submit"

                        name="update_hours"

                        class="save-btn"

                    >

                        Save

                    </button>


                    <button

                        type="button"

                        class="cancel-btn"

                        onclick="cancelHours()"

                    >

                        Cancel

                    </button>


                </form>


            </div>


        </div>


    </div>


</div>


<?php

if(isset($_SESSION['clinic_profile_status'])){

    $status = $_SESSION['clinic_profile_status'];

    unset($_SESSION['clinic_profile_status']);


    if($status == "name_error"){

?>

        <div class="popup-overlay">

            <div class="popup">

                <div class="popup-icon error">

                    !

                </div>


                <h3>

                    Invalid Clinic Name

                </h3>


                <p>

                    Please enter the clinic name.

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


    elseif($status == "name_success"){

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

                    Your clinic name has been
                    updated successfully.

                </p>


                <button onclick="closePopup()">

                    OK

                </button>


            </div>

        </div>

<?php

    }


    elseif($status == "name_update_error"){

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

                    Unable to update the clinic name.
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


    elseif($status == "address_error"){

?>

        <div class="popup-overlay">

            <div class="popup">

                <div class="popup-icon error">

                    !

                </div>


                <h3>

                    Invalid Address

                </h3>


                <p>

                    Please enter the clinic address.

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


    elseif($status == "address_success"){

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

                    Your clinic address has been
                    updated successfully.

                </p>


                <button onclick="closePopup()">

                    OK

                </button>


            </div>

        </div>

<?php

    }


    elseif($status == "address_update_error"){

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

                    Unable to update the clinic address.
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


    elseif($status == "contact_error"){

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


    elseif($status == "contact_success"){

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

                    Your clinic contact number has been
                    updated successfully.

                </p>


                <button onclick="closePopup()">

                    OK

                </button>


            </div>

        </div>

<?php

    }


    elseif($status == "contact_update_error"){

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

                    Unable to update the contact number.
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


    elseif($status == "email_error"){

?>

        <div class="popup-overlay">

            <div class="popup">

                <div class="popup-icon error">

                    !

                </div>


                <h3>

                    Invalid Email

                </h3>


                <p>

                    Please enter a valid email address.

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


    elseif($status == "email_success"){

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

                    Your clinic email has been
                    updated successfully.

                </p>


                <button onclick="closePopup()">

                    OK

                </button>


            </div>

        </div>

<?php

    }


    elseif($status == "email_update_error"){

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

                    Unable to update the clinic email.
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


    elseif($status == "hours_error"){

?>

        <div class="popup-overlay">

            <div class="popup">

                <div class="popup-icon error">

                    !

                </div>


                <h3>

                    Invalid Operating Hours

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


    elseif($status == "hours_success"){

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

                    Your clinic operating hours have been
                    updated successfully.

                </p>


                <button onclick="closePopup()">

                    OK

                </button>


            </div>

        </div>

<?php

    }


    elseif($status == "hours_update_error"){

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

                    Unable to update the operating hours.
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


function editName(){

    document.getElementById("nameDisplay").style.display = "none";

    document.getElementById("nameEdit").style.display = "flex";

}


function cancelName(){

    document.getElementById("nameEdit").style.display = "none";

    document.getElementById("nameDisplay").style.display = "flex";

}


function editAddress(){

    document.getElementById("addressDisplay").style.display = "none";

    document.getElementById("addressEdit").style.display = "flex";

}


function cancelAddress(){

    document.getElementById("addressEdit").style.display = "none";

    document.getElementById("addressDisplay").style.display = "flex";

}


function editContact(){

    document.getElementById("contactDisplay").style.display = "none";

    document.getElementById("contactEdit").style.display = "flex";

}


function cancelContact(){

    document.getElementById("contactEdit").style.display = "none";

    document.getElementById("contactDisplay").style.display = "flex";

}


function editEmail(){

    document.getElementById("emailDisplay").style.display = "none";

    document.getElementById("emailEdit").style.display = "flex";

}


function cancelEmail(){

    document.getElementById("emailEdit").style.display = "none";

    document.getElementById("emailDisplay").style.display = "flex";

}


function editHours(){

    document.getElementById("hoursDisplay").style.display = "none";

    document.getElementById("hoursEdit").style.display = "flex";

}


function cancelHours(){

    document.getElementById("hoursEdit").style.display = "none";

    document.getElementById("hoursDisplay").style.display = "flex";

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