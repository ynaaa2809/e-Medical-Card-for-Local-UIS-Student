<?php

session_start();

include("db_connect.php");

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $student_id = trim($_POST['student_id']);
    $full_name = trim($_POST['full_name']);
    $ic_number = trim($_POST['ic_number']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone_number = trim($_POST['phone_number']);
    $program = trim($_POST['program']);
    $status = trim($_POST['status']);

    $check = $conn->prepare("SELECT * FROM students WHERE student_id=:student_id");
    $check->execute([
        ':student_id'=>$student_id
    ]);


    if($check->rowCount()>0){
        $_SESSION['register_status']="student_exist";
        header("Location: register_student.php");
        exit();
    }


    if(!preg_match('/^[0-9]{12}$/',$ic_number)){
        $_SESSION['register_status']="ic_error";
        header("Location: register_student.php");
        exit();
    }


    if(!preg_match('/^[0-9]{10}$/',$phone_number)){
        $_SESSION['register_status']="phone_error";
        header("Location: register_student.php");
        exit();
    }


    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $_SESSION['register_status']="email_error";
        header("Location: register_student.php");
        exit();
    }


    $check=$conn->prepare("SELECT * FROM students WHERE ic_number=:ic");
    $check->execute([
        ':ic'=>$ic_number
    ]);


    if($check->rowCount()>0){
        $_SESSION['register_status']="ic_exist";
        header("Location: register_student.php");
        exit();
    }


    $check=$conn->prepare("SELECT * FROM students WHERE email=:email");
    $check->execute([
        ':email'=>$email
    ]);


    if($check->rowCount()>0){
        $_SESSION['register_status']="email_exist";
        header("Location: register_student.php");
        exit();
    }


    $sql="INSERT INTO students
    (student_id,full_name,ic_number,email,password,phone_number,program,status)
    VALUES
    (:student_id,:full_name,:ic_number,:email,:password,:phone_number,:program,:status)";

    $stmt=$conn->prepare($sql);


    if($stmt->execute([
        ':student_id'=>$student_id,
        ':full_name'=>$full_name,
        ':ic_number'=>$ic_number,
        ':email'=>$email,
        ':password'=>$password,
        ':phone_number'=>$phone_number,
        ':program'=>$program,
        ':status'=>$status
    ])){

        $_SESSION['register_status']="success";

    }else{

        $_SESSION['register_status']="failed";

    }

    header("Location: register_student.php");
    exit();
}

?>