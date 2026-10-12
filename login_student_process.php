<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db_connect.php";
require_once "auth.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: login_student.php");
    exit();
}


$studentID = trim(
    $_POST['studentid'] ?? ''
);

$password = $_POST['password'] ?? '';

$remember =
    isset($_POST['remember']) &&
    $_POST['remember'] === '1';


if ($studentID === '' || $password === '') {

    $_SESSION['login_status'] = "student_error";

    header("Location: login_student.php");
    exit();
}


$sql = "
    SELECT *
    FROM students
    WHERE student_id = :studentID
    LIMIT 1
";

$stmt = $conn->prepare($sql);

$stmt->execute([
    ':studentID' => $studentID
]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$student) {

    $_SESSION['login_status'] = "student_error";

    header("Location: login_student.php");
    exit();
}


if ($password !== $student['password']) {

    $_SESSION['login_status'] = "password_error";

    header("Location: login_student.php");
    exit();
}


session_regenerate_id(true);


unset(
    $_SESSION['clinic_id'],
    $_SESSION['clinic_name'],
    $_SESSION['admin_id'],
    $_SESSION['full_name']
);


$_SESSION['user_type'] = 'student';

$_SESSION['user_id'] = $student['student_id'];


$_SESSION['student_id'] = $student['student_id'];

$_SESSION['student_name'] = $student['full_name'];


if ($remember) {

    createRememberToken(
        'student',
        $student['student_id']
    );

} else {

    clearRememberCookie('student');
}


$_SESSION['login_status'] = "success";


header("Location: login_student.php");
exit();

?>