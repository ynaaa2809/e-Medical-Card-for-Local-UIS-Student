<?php

include("db_connect.php");


if(isset($_GET['student_id'])){


    $studentID = $_GET['student_id'];


    $sql = "SELECT full_name, current_balance
            FROM students
            WHERE student_id = ?";


    $stmt = $conn->prepare($sql);

    $stmt->execute([$studentID]);


    $student = $stmt->fetch(PDO::FETCH_ASSOC);



    if($student){


        echo json_encode([

            "status"=>"success",

            "name"=>$student['full_name'],

            "balance"=>$student['current_balance']

        ]);


    }

    else{


        echo json_encode([

            "status"=>"failed"

        ]);

    }


}

?>