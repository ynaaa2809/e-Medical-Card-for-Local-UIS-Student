<?php 
 
session_start(); 
 
include("db_connect.php"); 

 
if(isset($_POST['forgot_password'])){ 
 
    $studentID = trim($_POST['student_id']); 
    $icNumber = trim($_POST['ic_number']); 

 
    $sql = " 
        SELECT student_id 
        FROM students 
        WHERE student_id = ? 
        AND ic_number = ? 
        LIMIT 1 
    ";

 
    $stmt = $conn->prepare($sql); 
 
    $stmt->execute([ 
        $studentID, 
        $icNumber 
    ]);

 
    $student = $stmt->fetch(PDO::FETCH_ASSOC); 
 
 
    if(!$student){ 
 
        $_SESSION['reset_status'] = "student_error"; 
 
        header("Location: forgot_student.php"); 
 
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
        WHERE user_type = 'student' 
        AND user_id = ? 
    "; 
 
    $stmtDelete = $conn->prepare($delete); 
 
    $stmtDelete->execute([ 
        $studentID 
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
            'student', 
            ?, 
            ?, 
            ? 
        ) 
    "; 
 
    $stmtInsert = $conn->prepare($insert); 
 
    $stmtInsert->execute([ 
        $studentID, 
        $tokenHash, 
        $expiresAt 
    ]); 

 
    header( 
        "Location: reset_password.php?token=" . 
        urlencode($token) . 
        "&type=student" 
    ); 
 
    exit(); 
 
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
 
    <title>Forgot Password - Student</title> 
 
 
    <style> 

 
        *{ 
 
            margin:0; 
 
            padding:0; 
 
            box-sizing:border-box; 
 
            font-family: 
                Arial, 
                Helvetica, 
                sans-serif; 
 
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
 
            box-shadow: 
                0 8px 25px rgba(0,0,0,0.20); 
 
        } 

 
        .logos{ 
 
            display:flex; 
 
            align-items:center; 
 
            gap:15px; 
 
            margin-bottom:20px; 
 
        }

 
        .logos img{ 
 
            width:80px; 
 
            height:80px; 
 
            object-fit:contain; 
 
            flex-shrink:0; 
 
        }

 
        .header-title{ 
 
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
 
            transition:0.2s; 
 
        } 
 
 
        input:hover{ 
 
            border-color:#b8ccca; 
 
        } 
 
 
        input:focus{ 
 
            border-color:#2f6468; 
 
            background:white; 
 
            box-shadow: 
                0 0 0 3px rgba(47,100,104,0.08); 
 
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
 
            transition:0.2s; 
 
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

 
        @media(max-width:500px){ 
 
            body{ 
 
                padding:15px; 
 
            } 
 
 
            .container{ 
 
                padding:30px 25px; 
 
            } 
 
 
            .logos img{ 
 
                width:70px; 
 
                height:70px; 
 
            } 
 
 
            .header-title{ 
 
                font-size:22px; 
 
            } 
 
        } 

 
</style> 
 
</head> 
 
 
<body> 
 
 
<div class="container"> 

 
    <div class="logos"> 

        <img 

            src="images/logoEMCS.png" 

            alt="e-Medical Card Logo" 

        > 
 
        
        <h1 class="header-title"> 
 
            Forgot Password ? 
 
        </h1> 
 
    </div> 

 
    <p class="subtitle"> 
 
        Enter your Student ID and IC Number 
        to reset your password. 
 
    </p> 

 
    <?php 
 
    if( 
        isset($_SESSION['reset_status']) && 
        $_SESSION['reset_status'] === "student_error" 
    ): 
 
    ?> 
 
        <div class="error"> 
 
            Student ID and IC Number do not match 
            our records. 
 
        </div> 
 
    <?php 
 
        unset($_SESSION['reset_status']); 
 
    endif; 
 
    ?> 
 
 
    <form method="POST"> 
 
 
        <div class="form-group"> 
 
            <label for="student_id"> 
 
                Student ID 
 
            </label> 
 
 
            <input 

                type="text" 

                id="student_id" 

                name="student_id" 

                placeholder="Enter Student ID" 

                required 

            > 
 
        </div> 
 
 
        <div class="form-group"> 
 
            <label for="ic_number"> 
 
                IC Number 
 
            </label> 
 
 
            <input 

                type="text" 

                id="ic_number" 

                name="ic_number" 

                placeholder="Enter IC Number" 

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
 
        <a href="login_student.php"> 
 
            Back to Student Login 
 
        </a> 
 
    </div> 
 
 
</div> 
 
 
</body> 
 
</html>