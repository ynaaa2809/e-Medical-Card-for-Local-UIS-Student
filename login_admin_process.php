<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db_connect.php";
require_once "auth.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: login_admin.php");
    exit();
}


$adminID = trim(
    $_POST['adminid'] ?? ''
);

$password = $_POST['password'] ?? '';

$remember =
    isset($_POST['remember']) &&
    $_POST['remember'] === '1';


if ($adminID === '' || $password === '') {

    $_SESSION['login_status'] =
        "admin_error";

    header("Location: login_admin.php");
    exit();
}


$sql = "SELECT *
        FROM admins
        WHERE admin_id = :adminID
        LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->execute([
    ':adminID' => $adminID
]);

$admin = $stmt->fetch(
    PDO::FETCH_ASSOC
);


if (!$admin) {

    $_SESSION['login_status'] =
        "admin_error";

    header("Location: login_admin.php");
    exit();
}


if ($password !== $admin['password']) {

    $_SESSION['login_status'] =
        "password_error";

    header("Location: login_admin.php");
    exit();
}


session_regenerate_id(true);


unset(
    $_SESSION['student_id'],
    $_SESSION['student_name'],
    $_SESSION['clinic_id'],
    $_SESSION['clinic_name']
);


$_SESSION['user_type'] =
    'admin';


$_SESSION['user_id'] =
    $admin['admin_id'];


$_SESSION['admin_id'] =
    $admin['admin_id'];


$_SESSION['full_name'] =
    $admin['full_name'];


if ($remember) {

    createRememberToken(
        'admin',
        $admin['admin_id']
    );

}
else {

    clearRememberCookie('admin');
}


$_SESSION['login_status'] =
    "success";


header("Location: login_admin.php");
exit();

?>