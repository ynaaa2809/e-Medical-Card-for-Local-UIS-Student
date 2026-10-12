<?php

session_start();

include("db_connect.php");


if(isset($_POST['forgot_password'])){

    $clinicID = trim($_POST['clinic_id']);
    $contactNumber = trim($_POST['contact_number']);


    $sql = "
        SELECT clinic_id
        FROM clinics
        WHERE clinic_id = ?
        AND contact_number = ?
        LIMIT 1
    ";


    $stmt = $conn->prepare($sql);

    $stmt->execute([
        $clinicID,
        $contactNumber
    ]);

    $clinic = $stmt->fetch(PDO::FETCH_ASSOC);


    if(!$clinic){

        $_SESSION['reset_status'] = "clinic_error";

        header("Location: forgot_clinic.php");

        exit();

    }


    $token = bin2hex(random_bytes(32));

    $tokenHash = hash('sha256', $token);

    $expiresAt = date(
        "Y-m-d H:i:s",
        strtotime("+15 minutes")
    );


    $delete = "
        DELETE FROM password_resets
        WHERE user_type = 'clinic'
        AND user_id = ?
    ";

    $stmtDelete = $conn->prepare($delete);

    $stmtDelete->execute([
        $clinicID
    ]);


    $insert = "
        INSERT INTO password_resets
        (
            user_type,
            user_id,
            token_hash,
            expires_at
        )
        VALUES
        (
            'clinic',
            ?,
            ?,
            ?
        )
    ";


    $stmtInsert = $conn->prepare($insert);

    $stmtInsert->execute([
        $clinicID,
        $tokenHash,
        $expiresAt
    ]);


    header(
        "Location: reset_password.php?token=" .
        urlencode($token) .
        "&type=clinic"
    );

    exit();

}

?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Forgot Password - Clinic</title>

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
    padding:20px;
}


.container{
    width:100%;
    max-width:450px;
    background:white;
    padding:35px;
    border-radius:20px;
    box-shadow:0 8px 25px rgba(0,0,0,0.20);
}


.logo{
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:20px;
}


.logo img{
    width:80px;
    height:80px;
    object-fit:contain;
    flex-shrink:0;
}


h1{
    color:#2f6468;
    font-size:24px;
    margin:0;
}


.subtitle{
    text-align:center;
    color:#777;
    font-size:13px;
    margin-bottom:25px;
    line-height:1.5;
}


.form-group{
    margin-bottom:18px;
}


label{
    display:block;
    color:#555;
    font-size:13px;
    font-weight:bold;
    margin-bottom:7px;
}


input{
    width:100%;
    padding:13px 15px;
    border:1px solid #ddd;
    border-radius:10px;
    font-size:14px;
    outline:none;
}


input:focus{
    border-color:#2f6468;
}


.btn{
    width:100%;
    padding:14px;
    border:none;
    border-radius:30px;
    background:#2f6468;
    color:white;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
}


.btn:hover{
    background:#234d50;
}


.back{
    text-align:center;
    margin-top:20px;
}


.back a{
    color:#2f6468;
    text-decoration:none;
    font-size:12px;
    font-weight:bold;
}


.back a:hover{
    color:#234d50;
    text-decoration:underline;
}


.error{
    background:#f8d7da;
    color:#721c24;
    padding:12px;
    border-radius:10px;
    margin-bottom:18px;
    font-size:13px;
    text-align:center;
}


</style>

</head>

<body>


<div class="container">


   <div class="logo">

    <img src="images/logoEMCS.png" alt="e-Medical Card Logo">

    <h1>Forgot Password?</h1>

</div>


    <p class="subtitle">

        Enter your Clinic ID and contact number
        to reset your password.

    </p>


    <?php

    if(
        isset($_SESSION['reset_status']) &&
        $_SESSION['reset_status'] === "clinic_error"
    ):

    ?>

        <div class="error">

            Clinic ID and contact number do not match
            our records.

        </div>

    <?php

        unset($_SESSION['reset_status']);

    endif;

    ?>


    <form method="POST">


        <div class="form-group">

            <label>

                Clinic ID

            </label>

            <input
                type="text"
                name="clinic_id"
                placeholder="Enter Clinic ID"
                required
            >

        </div>


        <div class="form-group">

            <label>

                Contact Number

            </label>

            <input
                type="text"
                name="contact_number"
                placeholder="Enter registered contact number"
                required
            >

        </div>


        <button
            type="submit"
            name="forgot_password"
            class="btn"
        >

            Reset Password

        </button>


    </form>


    <div class="back">

        <a href="login_clinic.php">

            Back to Clinic Login

        </a>

    </div>


</div>


</body>

</html>