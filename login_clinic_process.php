<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db_connect.php";
require_once "auth.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: login_clinic.php");
    exit();
}


$clinicID = trim(
    $_POST['clinicid'] ?? ''
);

$password = $_POST['password'] ?? '';

$remember =
    isset($_POST['remember']) &&
    $_POST['remember'] === '1';


if ($clinicID === '' || $password === '') {

    $_SESSION['login_status'] =
        "clinic_error";

    header("Location: login_clinic.php");
    exit();
}


$sql = "SELECT *
        FROM clinics
        WHERE clinic_id = :clinicID
        LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->execute([
    ':clinicID' => $clinicID
]);

$clinic = $stmt->fetch(
    PDO::FETCH_ASSOC
);


if (!$clinic) {

    $_SESSION['login_status'] =
        "clinic_error";

    header("Location: login_clinic.php");
    exit();
}


if ($password !== $clinic['password']) {

    $_SESSION['login_status'] =
        "password_error";

    header("Location: login_clinic.php");
    exit();
}


session_regenerate_id(true);


unset(
    $_SESSION['student_id'],
    $_SESSION['student_name'],
    $_SESSION['admin_id'],
    $_SESSION['full_name']
);


$_SESSION['user_type'] =
    'clinic';

$_SESSION['user_id'] =
    $clinic['clinic_id'];


$_SESSION['clinic_id'] =
    $clinic['clinic_id'];

$_SESSION['clinic_name'] =
    $clinic['clinic_name'];


if ($remember) {

    createRememberToken(
        'clinic',
        $clinic['clinic_id']
    );

}
else {

    clearRememberCookie('clinic');
}


$_SESSION['login_status'] =
    "success";


header("Location: login_clinic.php");
exit();

?>