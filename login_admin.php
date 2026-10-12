<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db_connect.php";
require_once "auth.php";


if (
    isset($_SESSION['user_type']) &&
    $_SESSION['user_type'] !== 'admin'
) {

    clearCurrentLogin();
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

<title>Admin Login | e-Medical Card</title>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<style>


*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}


body{

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    min-height:100vh;

    background:#eef4f3;

    color:#333;

}


.login-page{

    min-height:100vh;

    display:flex;

    justify-content:center;

    align-items:center;

    padding:35px;

}


.login-wrapper{

    width:100%;

    max-width:1050px;

    min-height:620px;

    background:white;

    border-radius:25px;

    overflow:hidden;

    display:grid;

    grid-template-columns:45% 55%;

    box-shadow:
        0 20px 60px rgba(35,77,80,0.18);

}


.brand-panel{

    position:relative;

    background:#5d8d87;

    color:white;

    padding:55px;

    display:flex;

    flex-direction:column;

    justify-content:space-between;

    overflow:hidden;

}


.brand-panel::before{

    content:"";

    position:absolute;

    width:320px;

    height:320px;

    border-radius:50%;

    background:rgba(255,255,255,0.06);

    top:-130px;

    right:-130px;

}


.brand-panel::after{

    content:"";

    position:absolute;

    width:270px;

    height:270px;

    border-radius:50%;

    background:rgba(255,255,255,0.05);

    bottom:-110px;

    left:-110px;

}


.brand-header{

    position:relative;

    z-index:2;

    display:flex;

    align-items:center;

    gap:15px;

}


.brand-header img{

    width:80px;

    height:80px;

    object-fit:contain;

    background:white;

    padding:8px;

    border-radius:15px;

}


.brand-header-text h2{

    font-size:20px;

    font-weight:bold;

    margin-bottom:4px;

}


.brand-header-text p{

    font-size:11px;

    letter-spacing:1.5px;

    opacity:0.85;

}


.brand-content{

    position:relative;

    z-index:2;

    max-width:390px;

}


.portal-label{

    display:inline-block;

    padding:6px 13px;

    border-radius:20px;

    background:rgba(255,255,255,0.14);

    font-size:10px;

    font-weight:bold;

    letter-spacing:1.2px;

    margin-bottom:18px;

}


.brand-content h1{

    font-size:42px;

    line-height:1.1;

    margin-bottom:20px;

}


.brand-content h1 span{

    color:#d9f1ee;

}


.brand-content p{

    font-size:14px;

    line-height:1.8;

    color:rgba(255,255,255,0.88);

}


.features{

    margin-top:30px;

    display:flex;

    flex-direction:column;

    gap:14px;

}


.feature{

    display:flex;

    align-items:center;

    gap:12px;

    font-size:13px;

    color:rgba(255,255,255,0.95);

}


.feature-icon{

    width:30px;

    height:30px;

    min-width:30px;

    border-radius:50%;

    background:rgba(255,255,255,0.15);

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:13px;

}


.brand-footer{

    position:relative;

    z-index:2;

    font-size:11px;

    color:rgba(255,255,255,0.75);

    line-height:1.6;

}


.login-panel{

    padding:65px 75px;

    display:flex;

    align-items:center;

    justify-content:center;

}


.login-content{

    width:100%;

    max-width:410px;

}


.login-header{

    margin-bottom:35px;

}


.login-header .small-title{

    color:#5d8d87;

    font-size:12px;

    font-weight:bold;

    letter-spacing:2px;

    text-transform:uppercase;

    margin-bottom:10px;

}


.login-header h2{

    color:#234d50;

    font-size:30px;

    margin-bottom:9px;

}


.login-header p{

    color:#888;

    font-size:13px;

    line-height:1.6;

}


.admin-notice{

    display:flex;

    align-items:center;

    gap:10px;

    background:#f0f7f6;

    border-left:4px solid #2f6468;

    padding:12px 14px;

    border-radius:8px;

    margin-bottom:25px;

}


.admin-notice-icon{

    width:27px;

    height:27px;

    border-radius:50%;

    background:#2f6468;

    color:white;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:12px;

    font-weight:bold;

}


.admin-notice-text{

    color:#667;

    font-size:11px;

    line-height:1.4;

}


.form-group{

    margin-bottom:22px;

}


.form-group label{

    display:block;

    color:#444;

    font-size:13px;

    font-weight:bold;

    margin-bottom:9px;

}


.input-container{

    position:relative;

}


.input-container input{

    width:100%;

    height:52px;

    padding:0 16px;

    border:1px solid #dce4e3;

    border-radius:10px;

    background:#f8faf9;

    color:#333;

    font-size:14px;

    outline:none;

    transition:
        border-color 0.2s,
        background 0.2s,
        box-shadow 0.2s;

}


.input-container input:hover{

    border-color:#b8ccca;

}


.input-container input:focus{

    background:white;

    border-color:#5d8d87;

    box-shadow:
        0 0 0 4px rgba(93,141,135,0.10);

}


.input-container input::placeholder{

    color:#aaa;

}


.password-input{

    padding-right:65px !important;

}


.show-password{

    position:absolute;

    right:14px;

    top:50%;

    transform:translateY(-50%);

    border:none;

    background:none;

    color:#5d8d87;

    font-size:11px;

    font-weight:bold;

    cursor:pointer;

    padding:5px;

}


.show-password:hover{

    color:#234d50;

}


.login-options{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-top:-5px;

    margin-bottom:28px;

}


.remember{

    display:flex;

    align-items:center;

    gap:7px;

    color:#777;

    font-size:12px;

    cursor:pointer;

}


.remember input{

    width:14px;

    height:14px;

    accent-color:#2f6468;

}


.forgot{

    color:#2f6468;

    font-size:12px;

    font-weight:bold;

    text-decoration:none;

}


.forgot:hover{

    text-decoration:underline;

}


.login-button{

    width:100%;

    height:53px;

    border:none;

    border-radius:10px;

    background:#2f6468;

    color:white;

    font-size:14px;

    font-weight:bold;

    letter-spacing:0.5px;

    cursor:pointer;

    transition:
        background 0.2s,
        transform 0.2s,
        box-shadow 0.2s;

    box-shadow:
        0 7px 15px rgba(47,100,104,0.18);

}


.login-button:hover{

    background:#234d50;

    transform:translateY(-1px);

    box-shadow:
        0 9px 20px rgba(47,100,104,0.25);

}


.login-button:active{

    transform:translateY(0);

}



.back-home{

    display:block;

    text-align:center;

    margin-top:16px;

    color:#2f6468;

    text-decoration:none;

    font-size:12px;

    font-weight:bold;

}


.back-home:hover{

    color:#234d50;

    text-decoration:underline;

}


.login-footer{

    text-align:center;

    margin-top:30px;

    padding-top:20px;

    border-top:1px solid #eeeeee;

    color:#999;

    font-size:11px;

    line-height:1.7;

}


.login-footer strong{

    color:#5d8d87;

}


.security-note{

    display:flex;

    align-items:center;

    justify-content:center;

    gap:6px;

    margin-top:15px;

    color:#aaa;

    font-size:10px;

}


@media(max-width:850px){

    .login-page{

        padding:20px;

    }


    .login-wrapper{

        grid-template-columns:1fr;

        max-width:500px;

        min-height:auto;

    }


    .brand-panel{

        padding:35px;

        min-height:300px;

    }


    .brand-content{

        margin-top:40px;

    }


    .brand-content h1{

        font-size:32px;

    }


    .features{

        display:none;

    }


    .brand-footer{

        margin-top:35px;

    }


    .login-panel{

        padding:45px 40px;

    }

}


@media(max-width:500px){

    .login-page{

        padding:0;

        align-items:stretch;

    }


    .login-wrapper{

        min-height:100vh;

        border-radius:0;

        box-shadow:none;

    }


    .brand-panel{

        min-height:245px;

        padding:25px;

    }


    .brand-header img{

        width:60px;

        height:60px;

    }


    .brand-header-text h2{

        font-size:17px;

    }


    .brand-content{

        margin-top:25px;

    }


    .brand-content h1{

        font-size:28px;

        margin-bottom:10px;

    }


    .brand-content p{

        font-size:12px;

        line-height:1.6;

    }


    .brand-footer{

        display:none;

    }


    .login-panel{

        padding:35px 25px;

        align-items:flex-start;

    }


    .login-header{

        margin-bottom:25px;

    }


    .login-header h2{

        font-size:26px;

    }


    .login-options{

        align-items:flex-start;

        gap:10px;

    }

}


</style>

</head>


<body>


<div class="login-page">


    <div class="login-wrapper">


        <div class="brand-panel">


            <div class="brand-header">


                <img

                    src="images/logoEMCS.png"

                    alt="UIS Logo"

                >


                <div class="brand-header-text">

                    <h2>
                        e-Medical Card
                    </h2>

                    <p>
                        UIS ADMIN PORTAL
                    </p>

                </div>


            </div>


            <div class="brand-content">


                <div class="portal-label">

                    ADMINISTRATION

                </div>


                <h1>

                    Manage the
                    <span>system.</span>

                </h1>


                <p>

                    Access the UIS e-Medical Card
                    administration portal to manage students,
                    clinics, claims, and system information
                    securely.

                </p>


                <div class="features">


                    <div class="feature">

                        <div class="feature-icon">
                            ✓
                        </div>

                        <span>
                            Manage student records
                        </span>

                    </div>


                    <div class="feature">

                        <div class="feature-icon">
                            ✓
                        </div>

                        <span>
                            Manage clinic information
                        </span>

                    </div>


                    <div class="feature">

                        <div class="feature-icon">
                            ✓
                        </div>

                        <span>
                            Monitor medical claims
                        </span>

                    </div>


                </div>


            </div>


            <div class="brand-footer">

                UIS e-Medical Card System<br>

                Administration Portal

            </div>


        </div>


        <div class="login-panel">


            <div class="login-content">


                <div class="login-header">


                    <div class="small-title">

                        Administration Portal

                    </div>


                    <h2>

                        Welcome back

                    </h2>


                    <p>

                        Sign in with your Admin ID and
                        password to access the system.

                    </p>


                </div>


                <div class="admin-notice">


                    <div class="admin-notice-icon">

                        !

                    </div>


                    <div class="admin-notice-text">

                        This portal is restricted to
                        authorized UIS administrators.

                    </div>


                </div>


<form

    action="login_admin_process.php"

    method="POST"

    autocomplete="off"

>

        <div class="form-group">

            <label for="adminid">
                Admin ID
            </label>

            <div class="input-container">

            <input

                type="text"

                id="adminid"

                name="adminid"

                placeholder="Enter your Admin ID"

                autocomplete="section-admin username"

                autocapitalize="none"

                spellcheck="false"

                required

            >

        </div>

    </div>


    <div class="form-group">

        <label for="password">
            Password
        </label>

        <div class="input-container">

            <input

                type="password"

                id="password"

                name="password"

                class="password-input"

                placeholder="Enter your password"

                autocomplete="section-admin current-password"

                required

            >

            <button

                type="button"

                class="show-password"

                id="passwordToggle"

                onclick="togglePassword()"

            >
                SHOW

            </button>

        </div>

    </div>


    <div class="login-options">
        
        <a

            href="forgot_admin.php"

            class="forgot"

        >
            Forgot Password?

        </a>

    </div>


        <button

            type="submit"

            class="login-button"

        >

            SIGN IN

        </button>


        <a
            href="index.php"
            class="back-home"
        >
            Back to Home
        </a>

</form>


    <div class="login-footer">


        <strong>

            UIS e-Medical Card System

        </strong>


        <br>


            Authorized administrators only.


    </div>



                <div class="security-note">

                    🔒 Secure Administration Portal

                </div>


            </div>


        </div>


    </div>

</div>


<script>

function togglePassword(){

    const password =
        document.getElementById("password");

    const toggle =
        document.getElementById("passwordToggle");


    if(password.type === "password"){

        password.type = "text";

        toggle.textContent = "HIDE";

    }
    else{

        password.type = "password";

        toggle.textContent = "SHOW";

    }

}

</script>



<?php


if(isset($_SESSION['login_status'])){


    if($_SESSION['login_status'] == "success"){

?>


<script>

Swal.fire({

    icon:'success',

    title:'Login Successful!',

    text:'Welcome to the Administration Portal.',

    confirmButtonText:'Continue',

    confirmButtonColor:'#2f6468',

    allowOutsideClick:false,

    allowEscapeKey:false

}).then((result)=>{

    if(result.isConfirmed){

        window.location.href="dashboard_admin.php";

    }

});

</script>


<?php

    }


    elseif($_SESSION['login_status'] == "password_error"){

?>


<script>

Swal.fire({

    icon:'error',

    title:'Incorrect Password',

    text:'Please enter the correct password.',

    confirmButtonText:'Try Again',

    confirmButtonColor:'#2f6468'

});

</script>


<?php

    }


    elseif($_SESSION['login_status'] == "admin_error"){

?>


<script>

Swal.fire({

    icon:'warning',

    title:'Admin ID Not Found',

    text:'Please check your Admin ID and try again.',

    confirmButtonText:'OK',

    confirmButtonColor:'#2f6468'

});

</script>


<?php

    }


    unset($_SESSION['login_status']);

}

?>


</body>

</h