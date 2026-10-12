<?php

session_start();

?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>E-Medical Card System - UIS</title>


<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
>


<style>


*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}


html{
    scroll-behavior:smooth;
}


body{
    background:#5d8d87;
    min-height:100vh;
    color:#333;
}


.navbar{
    width:100%;
    background:white;
    padding:16px 6%;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 3px 15px rgba(0,0,0,0.10);
    position:relative;
    z-index:10;
}


.logo-area{
    display:flex;
    align-items:center;
    text-decoration:none;
}


.logo-area img{
    width:60px;
    height:auto;
    margin-right:12px;
}


.navbar-right{
    display:flex;
    align-items:center;
    gap:28px;
}


.uis-logo-area{
    display:flex;
    align-items:center;
    justify-content:center;
    padding-left:20px;
    border-left:1px solid #eeeeee;
}


.uis-logo{
    width:45px;
    height:auto;
    display:block;
}


.logo-text h2{
    color:#2f6468;
    font-size:19px;
    margin-bottom:2px;
}


.logo-text p{
    color:#888;
    font-size:10px;
    letter-spacing:2px;
}


.nav-links{
    display:flex;
    align-items:center;
    gap:10px;
}


.nav-links a{
    text-decoration:none;
    color:#555;
    font-size:13px;
    font-weight:bold;
    padding:10px 15px;
    border-radius:20px;
    transition:0.2s;
}


.nav-links a:hover{
    background:#f0f6f5;
    color:#2f6468;
}


.nav-login{
    background:#2f6468;
    color:white !important;
}


.nav-login:hover{
    background:#234d50 !important;
}


.hero{
    min-height:560px;
    padding:70px 7%;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:50px;
    position:relative;
    overflow:hidden;
}


.hero::before{
    content:"";
    position:absolute;
    width:450px;
    height:450px;
    border-radius:50%;
    background:rgba(255,255,255,0.07);
    right:-120px;
    top:-100px;
}


.hero::after{
    content:"";
    position:absolute;
    width:300px;
    height:300px;
    border-radius:50%;
    background:rgba(255,255,255,0.05);
    left:-120px;
    bottom:-120px;
}


.hero-content{
    width:55%;
    position:relative;
    z-index:2;
}


.badge{
    display:inline-flex;
    align-items:center;
    gap:8px;
    background:rgba(255,255,255,0.18);
    color:white;
    padding:9px 16px;
    border-radius:30px;
    font-size:12px;
    font-weight:bold;
    margin-bottom:20px;
    border:1px solid rgba(255,255,255,0.25);
}


.badge i{
    color:#d4edda;
}


.hero h1{
    color:white;
    font-size:48px;
    line-height:1.12;
    margin-bottom:20px;
}


.hero h1 span{
    color:#d9f1ef;
}


.hero-description{
    color:rgba(255,255,255,0.88);
    font-size:15px;
    line-height:1.7;
    max-width:600px;
    margin-bottom:30px;
}


.hero-buttons{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
}


.primary-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:9px;
    padding:14px 25px;
    background:white;
    color:#2f6468;
    text-decoration:none;
    border-radius:30px;
    font-size:14px;
    font-weight:bold;
    transition:0.2s;
    box-shadow:0 5px 15px rgba(0,0,0,0.12);
}


.primary-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(0,0,0,0.18);
}


.hero-card{
    width:380px;
    position:relative;
    z-index:2;
}


.medical-card{
    background:white;
    border-radius:25px;
    padding:25px;
    box-shadow:0 15px 40px rgba(0,0,0,0.20);
    transform:rotate(2deg);
}


.medical-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding-bottom:18px;
    border-bottom:1px solid #eeeeee;
}


.medical-logo{
    display:flex;
    align-items:center;
}


.medical-logo i{
    width:45px;
    height:45px;
    border-radius:12px;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#e6f2f1;
    color:#2f6468;
    font-size:20px;
    margin-right:10px;
}


.medical-logo strong{
    color:#2f6468;
    font-size:14px;
}


.medical-logo small{
    display:block;
    color:#999;
    font-size:9px;
    margin-top:3px;
}


.card-status{
    background:#d4edda;
    color:#155724;
    padding:6px 10px;
    border-radius:20px;
    font-size:9px;
    font-weight:bold;
}


.medical-body{
    padding:22px 0 10px;
}


.card-title{
    color:#888;
    font-size:10px;
    text-transform:uppercase;
    letter-spacing:1px;
    margin-bottom:5px;
}


.card-name{
    color:#2f6468;
    font-size:22px;
    font-weight:bold;
    margin-bottom:18px;
}


.card-details{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}


.card-detail{
    background:#f6f8f8;
    padding:12px;
    border-radius:10px;
}


.card-detail label{
    display:block;
    color:#999;
    font-size:9px;
    margin-bottom:5px;
}


.card-detail strong{
    color:#444;
    font-size:12px;
}


.medical-footer{
    margin-top:18px;
    padding-top:15px;
    border-top:1px solid #eeeeee;
    display:flex;
    justify-content:space-between;
    align-items:center;
}


.medical-footer span{
    color:#999;
    font-size:9px;
}


.qr{
    width:42px;
    height:42px;
    background:
        linear-gradient(
            90deg,
            #333 10%,
            transparent 10%,
            transparent 20%,
            #333 20%,
            #333 30%,
            transparent 30%
        ),
        linear-gradient(
            #333 10%,
            transparent 10%,
            transparent 20%,
            #333 20%,
            #333 30%,
            transparent 30%
        );
    background-size:15px 15px;
    border:3px solid white;
    outline:1px solid #ddd;
}


.login-section{
    background:white;
    padding:70px 7%;
}


.section-heading{
    text-align:center;
    margin-bottom:40px;
}


.section-heading .small-title{
    color:#2f6468;
    font-size:11px;
    font-weight:bold;
    letter-spacing:2px;
    text-transform:uppercase;
    margin-bottom:8px;
}


.section-heading h2{
    color:#2f6468;
    font-size:30px;
    margin-bottom:10px;
}


.section-heading p{
    color:#777;
    font-size:13px;
    max-width:550px;
    margin:auto;
    line-height:1.6;
}


.login-cards{
    max-width:1100px;
    margin:auto;
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
}


.login-card{
    border:1px solid #eeeeee;
    border-radius:18px;
    padding:28px;
    text-align:center;
    transition:0.25s;
    background:white;
}


.login-card:hover{
    transform:translateY(-6px);
    box-shadow:0 12px 30px rgba(0,0,0,0.10);
    border-color:#d7e7e5;
}


.login-icon{
    width:65px;
    height:65px;
    border-radius:18px;
    background:#e9f3f2;
    color:#2f6468;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:0 auto 18px;
    font-size:26px;
}


.login-card h3{
    color:#2f6468;
    font-size:19px;
    margin-bottom:8px;
}


.login-card p{
    color:#888;
    font-size:12px;
    line-height:1.6;
    min-height:58px;
}


.login-btn{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:8px;
    width:100%;
    padding:12px;
    margin-top:20px;
    border-radius:25px;
    background:#2f6468;
    color:white;
    text-decoration:none;
    font-size:13px;
    font-weight:bold;
    transition:0.2s;
}


.login-btn:hover{
    background:#234d50;
}


.info-section{
    padding:65px 7%;
    background:#f4f7f7;
}


.info-box{
    max-width:1000px;
    margin:auto;
    background:#2f6468;
    border-radius:22px;
    padding:40px 45px;
    display:flex;
    align-items:center;
    gap:25px;
    box-shadow:0 10px 30px rgba(47,100,104,0.18);
}


.info-icon{
    min-width:65px;
    height:65px;
    border-radius:18px;
    background:rgba(255,255,255,0.12);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
}


.info-content h2{
    color:white;
    font-size:25px;
    margin-bottom:8px;
}


.info-content p{
    color:rgba(255,255,255,0.80);
    font-size:13px;
    line-height:1.6;
}


.features-section{
    background:white;
    padding:70px 7%;
}


.features{
    max-width:1050px;
    margin:auto;
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:25px;
}


.feature{
    text-align:center;
    padding:20px;
}


.feature i{
    color:#2f6468;
    font-size:28px;
    margin-bottom:15px;
}


.feature h3{
    color:#2f6468;
    font-size:16px;
    margin-bottom:8px;
}


.feature p{
    color:#888;
    font-size:12px;
    line-height:1.6;
}


.footer{
    background:#234d50;
    padding:35px 7%;
    text-align:center;
    color:rgba(255,255,255,0.75);
    font-size:11px;
    line-height:1.7;
    scroll-margin-top:20px;
}


.footer strong{
    color:white;
}


.footer-title{
    color:white;
    font-size:13px;
    font-weight:bold;
    margin-bottom:8px;
}


.footer-address{
    color:rgba(255,255,255,0.80);
    font-size:11px;
    line-height:1.7;
    margin-bottom:8px;
}


.footer-phone{
    color:white;
    font-size:11px;
    font-weight:bold;
    margin-bottom:18px;
}


.footer-divider{
    width:100%;
    max-width:600px;
    height:1px;
    background:rgba(255,255,255,0.15);
    margin:0 auto 18px;
}


@media(max-width:900px){

    .hero{
        padding:60px 6%;
    }

    .hero-content{
        width:100%;
    }

    .hero-card{
        display:none;
    }

    .login-cards{
        grid-template-columns:1fr;
        max-width:450px;
    }

    .features{
        grid-template-columns:1fr;
        max-width:450px;
    }

    .info-box{
        flex-direction:column;
        align-items:flex-start;
    }

}


@media(max-width:650px){

    .navbar{
        padding:14px 5%;
    }

    .navbar-right{
        gap:12px;
    }

    .nav-links a{
        display:none;
    }

    .nav-links .nav-login{
        display:block;
    }

    .uis-logo-area{
        padding-left:12px;
    }

    .uis-logo{
        width:38px;
    }

    .hero{
        min-height:520px;
        padding:55px 7%;
    }

    .hero h1{
        font-size:37px;
    }

    .hero-description{
        font-size:14px;
    }

    .hero-buttons{
        flex-direction:column;
        align-items:stretch;
    }

    .primary-btn{
        width:100%;
    }

    .login-section,
    .features-section{
        padding:55px 7%;
    }

    .section-heading h2{
        font-size:25px;
    }

    .info-section{
        padding:45px 6%;
    }

    .info-box{
        padding:30px 25px;
    }

    .info-content h2{
        font-size:22px;
    }

    .footer{
        padding:30px 7%;
    }

}


@media(max-width:400px){

    .logo-text h2{
        font-size:16px;
    }

    .logo-text p{
        font-size:8px;
    }

    .logo-area img{
        width:42px;
    }

    .hero h1{
        font-size:32px;
    }

}


</style>

</head>

<body>


<nav class="navbar">

    <a href="index.php" class="logo-area">

        <img

            src="images/logoEMCS.png"

            alt="EMCS Logo"

        >

        <div class="logo-text">

            <h2>
                E-MEDICAL CARD
            </h2>

            <p>
                UIS STUDENT SYSTEM
            </p>

        </div>

    </a>


    <div class="navbar-right">

        <div class="nav-links">

            <a href="#login">
                Login
            </a>

            <a href="#features">
                Features
            </a>

            <a href="#contact">
                Contact Us
            </a>

            <a href="#information" class="nav-login">
                Access System
            </a>

        </div>


        <div class="uis-logo-area">

            <img

                src="images/logouis.png"

                class="uis-logo"

                alt="UIS Logo"

            >

        </div>

    </div>

</nav>


<section class="hero">

    <div class="hero-content">

        <div class="badge">

            <i class="fa-solid fa-shield-heart"></i>

            SECURE DIGITAL MEDICAL SYSTEM

        </div>


        <h1>

            Your Health Information,

            <span>
                Made Simple.
            </span>

        </h1>


        <p class="hero-description">

            E-Medical Card System provides a convenient way
            for students, clinics and administrators to manage
            medical information and claims through one centralized
            digital platform.

        </p>


        <div class="hero-buttons">

            <a

                href="#login"

                class="primary-btn"

            >

                <i class="fa-solid fa-right-to-bracket"></i>

                Login to System

            </a>

        </div>

    </div>



    <div class="hero-card">

        <div class="medical-card">

            <div class="medical-top">

                <div class="medical-logo">

                    <i class="fa-solid fa-heart-pulse"></i>

                    <div>

                        <strong>
                            E-MEDICAL CARD
                        </strong>

                        <small>
                            UIS STUDENT SYSTEM
                        </small>

                    </div>

                </div>


                <div class="card-status">
                    ACTIVE
                </div>

            </div>


            <div class="medical-body">

                <div class="card-title">
                    Student Medical Card
                </div>

                <div class="card-name">
                    Student Account
                </div>


                <div class="card-details">

                    <div class="card-detail">

                        <label>
                            STUDENT ID
                        </label>

                        <strong>
                            UIS-STUDENT
                        </strong>

                    </div>


                    <div class="card-detail">

                        <label>
                            PROGRAM
                        </label>

                        <strong>
                            UNIVERSITY
                        </strong>

                    </div>


                    <div class="card-detail">

                        <label>
                            STATUS
                        </label>

                        <strong>
                            Active
                        </strong>

                    </div>


                    <div class="card-detail">

                        <label>
                            MEDICAL CLAIM
                        </label>

                        <strong>
                            Available
                        </strong>

                    </div>

                </div>

            </div>


            <div class="medical-footer">

                <span>
                    DIGITAL MEDICAL CARD
                </span>

                <div class="qr"></div>

            </div>

        </div>

    </div>

</section>


<section class="login-section" id="login">

    <div class="section-heading">

        <div class="small-title">
            SYSTEM ACCESS
        </div>

        <h2>
            Choose Your Login
        </h2>

        <p>

            Select the account type that matches your role
            in the E-Medical Card System.

        </p>

    </div>


    <div class="login-cards">


        <div class="login-card">

            <div class="login-icon">

                <i class="fa-solid fa-user-graduate"></i>

            </div>

            <h3>
                Student
            </h3>

            <p>

                Access your medical card, view claim information
                and manage your student account.

            </p>

            <a
                href="login_student.php"
                class="login-btn"
            >

                Student Login

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>



        <div class="login-card">

            <div class="login-icon">

                <i class="fa-solid fa-hospital"></i>

            </div>

            <h3>
                Clinic
            </h3>

            <p>

                Manage student visits, medical claims and
                clinic-related information.

            </p>

            <a
                href="login_clinic.php"
                class="login-btn"
            >

                Clinic Login

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>



        <div class="login-card">

            <div class="login-icon">

                <i class="fa-solid fa-user-shield"></i>

            </div>

            <h3>
                Administrator
            </h3>

            <p>

                Manage students, clinics, claims and the
                overall E-Medical Card system.

            </p>

            <a
                href="login_admin.php"
                class="login-btn"
            >

                Admin Login

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>


    </div>

</section>


<section class="info-section" id="information">

    <div class="info-box">

        <div class="info-icon">

            <i class="fa-solid fa-circle-info"></i>

        </div>


        <div class="info-content">

            <h2>
                Need an Account?
            </h2>

            <p>

                Student and clinic accounts are managed by the
                system administrator. Please contact the
                administrator if you require a new account or
                need assistance accessing the system.

            </p>

        </div>

    </div>

</section>


<section class="features-section" id="features">

    <div class="section-heading">

        <div class="small-title">
            SYSTEM FEATURES
        </div>

        <h2>
            One System, Three Roles
        </h2>

        <p>

            E-Medical Card connects students, clinics and
            administrators in one organized platform.

        </p>

    </div>


    <div class="features">

        <div class="feature">

            <i class="fa-solid fa-id-card"></i>

            <h3>
                Digital Medical Card
            </h3>

            <p>

                Students can access their medical card
                information through the system.

            </p>

        </div>


        <div class="feature">

            <i class="fa-solid fa-file-medical"></i>

            <h3>
                Medical Claims
            </h3>

            <p>

                Medical claims can be submitted, managed
                and reviewed through the system.

            </p>

        </div>


        <div class="feature">

            <i class="fa-solid fa-lock"></i>

            <h3>
                Secure Access
            </h3>

            <p>

                Separate login access is provided for students,
                clinics and administrators.

            </p>

        </div>

    </div>

</section>


<footer class="footer" id="contact">

    <div class="footer-title">

        BAHAGIAN HAL EHWAL PELAJAR

    </div>


    <div class="footer-address">

        UNIVERSITI ISLAM SELANGOR (UIS)<br>

        BANDAR SERI PUTRA<br>

        43000 KAJANG<br>

        SELANGOR DARUL EHSAN

    </div>


    <div class="footer-phone">

        Tel. No.: 03-8911 7000 ext. 1118, 1117, 1434, 3337

    </div>


    <div class="footer-divider"></div>


    <strong>
        E-Medical Card System
    </strong>


    &nbsp; | &nbsp;


    UIS Local Student System


    <br>


    © 2026 All Rights Reserved.

</footer>


</body>

</html>