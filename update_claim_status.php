<?php

session_start();

include("db_connect.php");


if(!isset($_SESSION['admin_id'])){

    header("Location: login_admin.php");

    exit();

}


if(!isset($_GET['claim_id']) || empty($_GET['claim_id'])){

    header("Location: view_student.php");

    exit();

}


$claimID = trim($_GET['claim_id']);


$action = $_GET['action'] ?? '';


if($action !== 'approve' && $action !== 'reject'){

    header("Location: view_student.php");

    exit();

}


$sqlClaim = "

SELECT
    claim_id,
    student_id,
    claim_amount,
    claim_status

FROM medical_claims

WHERE claim_id = ?

";


$stmtClaim = $conn->prepare($sqlClaim);

$stmtClaim->execute([$claimID]);

$claim = $stmtClaim->fetch(PDO::FETCH_ASSOC);


if(!$claim){

    header("Location: view_student.php");

    exit();

}


$sqlStudent = "

SELECT
    student_id,
    current_balance

FROM students

WHERE student_id = ?

";


$stmtStudent = $conn->prepare($sqlStudent);

$stmtStudent->execute([$claim['student_id']]);

$student = $stmtStudent->fetch(PDO::FETCH_ASSOC);


if(!$student){

    header("Location: view_student.php");

    exit();

}


if($claim['claim_status'] !== 'Pending'){

    header(

        "Location: admin_claimhistory.php?student_id=" .
        urlencode($claim['student_id']) .
        "&error=already_processed"

    );

    exit();

}


if($action === 'approve'){

    $claimAmount = (float)$claim['claim_amount'];

    $currentBalance = (float)$student['current_balance'];


    if($claimAmount <= 0){

        header(

            "Location: admin_claimhistory.php?student_id=" .
            urlencode($claim['student_id']) .
            "&error=invalid_amount"

        );

        exit();

    }


    if($claimAmount > $currentBalance){

        header(

            "Location: admin_claimhistory.php?student_id=" .
            urlencode($claim['student_id']) .
            "&error=insufficient_balance"

        );

        exit();

    }


    $newBalance = $currentBalance - $claimAmount;


    try{


        $conn->beginTransaction();


        $sqlUpdateClaim = "

        UPDATE medical_claims

        SET claim_status = 'Approved'

        WHERE claim_id = ?

        AND claim_status = 'Pending'

        ";


        $stmtUpdateClaim = $conn->prepare($sqlUpdateClaim);

        $stmtUpdateClaim->execute([$claimID]);


        if($stmtUpdateClaim->rowCount() !== 1){

            throw new Exception("Claim was not updated.");

        }


        $sqlUpdateBalance = "

        UPDATE students

        SET current_balance = ?

        WHERE student_id = ?

        ";


        $stmtUpdateBalance = $conn->prepare($sqlUpdateBalance);

        $stmtUpdateBalance->execute([

            number_format($newBalance, 2, '.', ''),

            $claim['student_id']

        ]);


        $conn->commit();


        header(

            "Location: admin_claimhistory.php?student_id=" .
            urlencode($claim['student_id']) .
            "&success=approved"

        );

        exit();

    }

    catch(Exception $e){


        if($conn->inTransaction()){

            $conn->rollBack();

        }


        header(

            "Location: admin_claimhistory.php?student_id=" .
            urlencode($claim['student_id']) .
            "&error=update_failed"

        );

        exit();

    }

}


if($action === 'reject'){

    try{


        $conn->beginTransaction();


        $sqlRejectClaim = "

        UPDATE medical_claims

        SET claim_status = 'Rejected'

        WHERE claim_id = ?

        AND claim_status = 'Pending'

        ";


        $stmtRejectClaim = $conn->prepare($sqlRejectClaim);

        $stmtRejectClaim->execute([$claimID]);


        if($stmtRejectClaim->rowCount() !== 1){

            throw new Exception("Claim was not updated.");

        }


        $conn->commit();


        header(

            "Location: admin_claimhistory.php?student_id=" .
            urlencode($claim['student_id']) .
            "&success=rejected"

        );

        exit();

    }

    catch(Exception $e){


        if($conn->inTransaction()){

            $conn->rollBack();

        }


        header(

            "Location: admin_claimhistory.php?student_id=" .
            urlencode($claim['student_id']) .
            "&error=update_failed"

        );

        exit();

    }

}

?>