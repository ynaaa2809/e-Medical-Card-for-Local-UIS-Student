<?php

session_start();


if(!isset($_SESSION['student_id'])){

    header("Location: login_student.php");
    exit();

}


include("db_connect.php");


$studentID = $_SESSION['student_id'];


if(isset($_POST['update'])){


    $phone = trim($_POST['phone_number']);


    $updateSQL = "UPDATE students 
                  SET phone_number = ?
                  WHERE student_id = ?";


    $updateStmt = $conn->prepare($updateSQL);


    $updateStmt->execute([

        $phone,
        $studentID

    ]);


    header("Location: student_profile.php");

    exit();

}


$sql = "SELECT * FROM students WHERE student_id = ?";


$stmt = $conn->prepare($sql);


$stmt->execute([$studentID]);


$student = $stmt->fetch(PDO::FETCH_ASSOC);


if(!$student){

    echo "Student information not found.";

    exit();

}

?>


<!DOCTYPE html>

<html>

<head>

<title>Update Student Information</title>


<style>


*{

    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;

}


body{

    background:#5d8d87;

}


.profile-box{

    width:550px;

    margin:50px auto;

    background:white;

    padding:35px;

    border-radius:20px;

    box-shadow:0 5px 15px rgba(0,0,0,0.2);

}


h1{

    text-align:center;

    color:#2f6468;

    margin-bottom:30px;

}


.profile-image{

    text-align:center;

    margin-bottom:25px;

}


.profile-image img{

    width:100px;

    height:100px;

    border-radius:50%;

}


.detail{

    background:#f2f2f2;

    padding:15px;

    margin:15px 0;

    border-radius:10px;

    color:#555;

}


.detail b{

    color:#2f6468;

}


input{

    width:100%;

    padding:12px;

    margin-top:10px;

    border-radius:10px;

    border:2px solid #6bb6c2;

    font-size:16px;

}


button{

    width:100%;

    margin-top:20px;

    padding:14px;

    border:none;

    border-radius:30px;

    background:#2f6468;

    color:white;

    font-size:16px;

    cursor:pointer;

}


button:hover{

    background:#234d50;

}


.back-btn{

    display:block;

    text-align:center;

    margin-top:15px;

    padding:14px;

    background:#777;

    color:white;

    text-decoration:none;

    border-radius:30px;

}

</style>


</head>

<body>


<div class="profile-box">


    <h1>

        Update Student Information

    </h1>


    <div class="profile-image">

        <img src="images/student.png">

    </div>


    <div class="detail">

        <b>Student ID:</b>

        <br>

        <?php echo htmlspecialchars($student['student_id']); ?>

    </div>


    <div class="detail">

        <b>Full Name:</b>

        <br>

        <?php echo htmlspecialchars($student['full_name']); ?>

    </div>


    <div class="detail">

        <b>IC Number:</b>

        <br>

        <?php echo htmlspecialchars($student['ic_number']); ?>

    </div>


    <div class="detail">

        <b>Email:</b>

        <br>

        <?php echo htmlspecialchars($student['email']); ?>

    </div>


<form method="POST">


    <div class="detail">

        <b>Phone Number:</b>


`       <input 

            type="text"

            name="phone_number"

            value="<?php echo htmlspecialchars($student['phone_number']); ?>"

            required>


    </div>


    <div class="detail">

        <b>Program:</b>

        <br>

        <?php echo htmlspecialchars($student['program']); ?>

    </div>


    <div class="detail">

        <b>Status:</b>

        <br>

        <?php echo htmlspecialchars($student['status']); ?>

    </div>


    <button type="submit" name="update">

        Save Phone Number

    </button>


</form>


<a href="student_profile.php" class="back-btn">

Cancel

</a>


</div>

</body>

</html>