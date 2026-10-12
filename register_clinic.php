<?php

session_start();

include("db_connect.php");


if(!isset($_SESSION['admin_id'])){

    header("Location: login_admin.php");

    exit();

}


$clinic_id = "";
$clinic_name = "";
$address = "";
$contact_number = "";
$email = "";
$operating_hours = "";


if($_SERVER["REQUEST_METHOD"] == "POST"){

    $clinic_id = trim($_POST['clinic_id'] ?? "");

    $clinic_name = trim($_POST['clinic_name'] ?? "");

    $address = trim($_POST['address'] ?? "");

    $contact_number = trim($_POST['contact_number'] ?? "");

    $email = trim($_POST['email'] ?? "");

    $operating_hours = trim($_POST['operating_hours'] ?? "");

    $password = trim($_POST['password'] ?? "");


    if(
        $clinic_id == "" ||
        $clinic_name == "" ||
        $address == "" ||
        $contact_number == "" ||
        $email == "" ||
        $operating_hours == "" ||
        $password == ""
    ){

        $_SESSION['clinic_status'] = "required_error";

        $_SESSION['clinic_form'] = [

            "clinic_id" => $clinic_id,

            "clinic_name" => $clinic_name,

            "address" => $address,

            "contact_number" => $contact_number,

            "email" => $email,

            "operating_hours" => $operating_hours

        ];

        header("Location: register_clinic.php");

        exit();

    }


    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){

        $_SESSION['clinic_status'] = "email_error";

        $_SESSION['clinic_form'] = [

            "clinic_id" => $clinic_id,

            "clinic_name" => $clinic_name,

            "address" => $address,

            "contact_number" => $contact_number,

            "email" => $email,

            "operating_hours" => $operating_hours

        ];

        header("Location: register_clinic.php");

        exit();

    }


    if(!preg_match('/^[0-9]{10,}$/', $contact_number)){

        $_SESSION['clinic_status'] = "phone_error";

        $_SESSION['clinic_form'] = [

            "clinic_id" => $clinic_id,

            "clinic_name" => $clinic_name,

            "address" => $address,

            "contact_number" => $contact_number,

            "email" => $email,

            "operating_hours" => $operating_hours

        ];

        header("Location: register_clinic.php");

        exit();

    }


    if(strlen($password) < 6){

        $_SESSION['clinic_status'] = "password_error";

        $_SESSION['clinic_form'] = [

            "clinic_id" => $clinic_id,

            "clinic_name" => $clinic_name,

            "address" => $address,

            "contact_number" => $contact_number,

            "email" => $email,

            "operating_hours" => $operating_hours

        ];

        header("Location: register_clinic.php");

        exit();

    }


    try{


        $check = $conn->prepare("

            SELECT clinic_id

            FROM clinics

            WHERE clinic_id = ?

        ");


        $check->execute([

            $clinic_id

        ]);


        if($check->fetch()){

            $_SESSION['clinic_status'] = "clinic_exist";

            $_SESSION['clinic_form'] = [

                "clinic_id" => $clinic_id,

                "clinic_name" => $clinic_name,

                "address" => $address,

                "contact_number" => $contact_number,

                "email" => $email,

                "operating_hours" => $operating_hours

            ];

            header("Location: register_clinic.php");

            exit();

        }


        $sql = "

            INSERT INTO clinics

            (

                clinic_id,

                clinic_name,

                address,

                contact_number,

                email,

                operating_hours,

                password

            )

            VALUES

            (

                ?, ?, ?, ?, ?, ?, ?

            )

        ";


        $stmt = $conn->prepare($sql);


        $stmt->execute([

            $clinic_id,

            $clinic_name,

            $address,

            $contact_number,

            $email,

            $operating_hours,

            $password

        ]);


        $_SESSION['clinic_status'] = "success";


        unset($_SESSION['clinic_form']);


        header("Location: register_clinic.php");

        exit();


    }


    catch(PDOException $e){

        $_SESSION['clinic_status'] = "failed";


        $_SESSION['clinic_form'] = [

            "clinic_id" => $clinic_id,

            "clinic_name" => $clinic_name,

            "address" => $address,

            "contact_number" => $contact_number,

            "email" => $email,

            "operating_hours" => $operating_hours

        ];


        header("Location: register_clinic.php");

        exit();

    }

}


if(isset($_SESSION['clinic_form'])){

    $form = $_SESSION['clinic_form'];


    $clinic_id = $form['clinic_id'] ?? "";

    $clinic_name = $form['clinic_name'] ?? "";

    $address = $form['address'] ?? "";

    $contact_number = $form['contact_number'] ?? "";

    $email = $form['email'] ?? "";

    $operating_hours = $form['operating_hours'] ?? "";

}

?>


<!DOCTYPE html>

<html>

<head>


<meta charset="UTF-8">


<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>Clinic Registration - EMCS</title>


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

    height:100px;

    resize:vertical;

    min-height:100px;

}


.form-group input:focus,
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
                CLINIC REGISTRATION
            </p>

        </div>

    </div>

    <img src="images/logouis.png" class="uis-logo">

</div>


    <div class="form-title">


        <h2>

            Register New Clinic

        </h2>


        <p>

            Enter the clinic's information to create a new clinic account.

        </p>


    </div>


    <form method="POST">


        <div class="form-grid">


            <div class="form-group">


                <label>

                    Clinic ID <span class="required">*</span>

                </label>


                <input

                    type="text"

                    name="clinic_id"

                    placeholder="Enter Clinic ID"

                    value="<?php echo htmlspecialchars($clinic_id); ?>"

                    required

                >


            </div>


            <div class="form-group">


                <label>

                    Clinic Name <span class="required">*</span>

                </label>


                <input

                    type="text"

                    name="clinic_name"

                    placeholder="Enter Clinic Name"

                    value="<?php echo htmlspecialchars($clinic_name); ?>"

                    required

                >


            </div>


            <div class="form-group">


                <label>

                    Contact Number <span class="required">*</span>

                </label>


                <input

                    type="text"

                    name="contact_number"

                    placeholder="Enter contact number"

                    value="<?php echo htmlspecialchars($contact_number); ?>"

                    inputmode="numeric"

                    required

                >


                <span class="help-text">

                    Enter at least 10 digits.

                </span>


            </div>


            <div class="form-group">


                <label>

                    Email <span class="required">*</span>

                </label>


                <input

                    type="email"

                    name="email"

                    placeholder="Enter clinic email"

                    value="<?php echo htmlspecialchars($email); ?>"

                    required

                >


            </div>


            <div class="form-group">


                <label>

                    Operating Hours <span class="required">*</span>

                </label>


                <input

                    type="text"

                    name="operating_hours"

                    placeholder="Example: 8AM - 5PM"

                    value="<?php echo htmlspecialchars($operating_hours); ?>"

                    required

                >


            </div>


            <div class="form-group full-width">


                <label>

                    Clinic Address <span class="required">*</span>

                </label>


                <textarea

                    name="address"

                    placeholder="Enter Clinic Address"

                    required

                ><?php echo htmlspecialchars($address); ?></textarea>


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


                <span class="help-text">

                    Password must contain at least 6 characters.

                </span>


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

                Register Clinic

            </button>


        </div>


    </form>


    <div class="info-box">


        <strong>

            Registration Information:

        </strong>


        Please make sure the Clinic ID, clinic name, address,
        contact number, email, operating hours and password are correct
        before registering the clinic account.


    </div>


    <div class="footer">


        e-Medical Card &nbsp; | &nbsp; UIS Local Student System


    </div>


</div>



<?php

if(isset($_SESSION['clinic_status'])){


    $status = $_SESSION['clinic_status'];


    if($status == "success"){

?>


<script>

Swal.fire({

    icon:"success",

    title:"Clinic Registered!",

    text:"Clinic account has been created successfully.",

    confirmButtonColor:"#2f6468",

    allowOutsideClick:false

}).then(()=>{

    window.location.href="dashboard_admin.php";

});

</script>


<?php

    }


    /* =========================
       REQUIRED FIELD ERROR
    ========================= */

    elseif($status == "required_error"){

?>


<script>

Swal.fire({

    icon:"error",

    title:"Required Fields Missing!",

    text:"Please fill in all required fields.",

    confirmButtonColor:"#dc3545"

});

</script>


<?php

    }

    elseif($status == "clinic_exist"){

?>


<script>

Swal.fire({

    icon:"error",

    title:"Clinic ID Exists!",

    text:"This Clinic ID has already been registered.",

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

    text:"Please enter a valid email address.",

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

    title:"Invalid Contact Number!",

    text:"Contact number must contain at least 10 numbers.",

    confirmButtonColor:"#dc3545"

});

</script>


<?php

    }

    elseif($status == "password_error"){

?>


<script>

Swal.fire({

    icon:"error",

    title:"Invalid Password!",

    text:"Password must contain at least 6 characters.",

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

    text:"Something went wrong while registering the clinic.",

    confirmButtonColor:"#dc3545"

});

</script>


<?php

    }

    unset($_SESSION['clinic_status']);

    unset($_SESSION['clinic_form']);

}

?>


</body>

</html>