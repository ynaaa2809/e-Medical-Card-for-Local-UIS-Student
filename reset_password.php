<?php

session_start();

include("db_connect.php");

$token = isset($_GET['token']) ? trim($_GET['token']) : '';

$type = isset($_GET['type']) ? trim($_GET['type']) : '';


if (empty($token) || empty($type)) {

    die("Invalid password reset link.");

}


$allowedTypes = [
    'student',
    'clinic',
    'admin'
];

if (!in_array($type, $allowedTypes, true)) {

    die("Invalid user type.");

}


$tokenHash = hash('sha256', $token);


$sql = "
    SELECT
        id,
        user_id,
        expires_at
    FROM password_resets
    WHERE user_type = ?
    AND token_hash = ?
    LIMIT 1
";


$stmt = $conn->prepare($sql);

$stmt->execute([
    $type,
    $tokenHash
]);


$reset = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$reset) {

    die("Invalid or expired password reset link.");

}


if (strtotime($reset['expires_at']) < time()) {


    $delete = "
        DELETE FROM password_resets
        WHERE id = ?
    ";

    $stmtDelete = $conn->prepare($delete);

    $stmtDelete->execute([
        $reset['id']
    ]);

    die("This password reset link has expired.");

}


if (isset($_POST['reset_password'])) {

    $newPassword = isset($_POST['new_password'])
        ? trim($_POST['new_password'])
        : '';

    $confirmPassword = isset($_POST['confirm_password'])
        ? trim($_POST['confirm_password'])
        : '';


    if (empty($newPassword) || empty($confirmPassword)) {

        $error = "Please fill in all fields.";

    }

    elseif ($newPassword !== $confirmPassword) {

        $error = "Passwords do not match.";

    }

    elseif (strlen($newPassword) < 8) {

        $error = "Password must be at least 8 characters.";

    }

    else {


        $password = $newPassword;


        if ($type === 'student') {

            $sqlUpdate = "
                UPDATE students
                SET password = ?
                WHERE student_id = ?
            ";

        }


        elseif ($type === 'clinic') {

            $sqlUpdate = "
                UPDATE clinics
                SET password = ?
                WHERE clinic_id = ?
            ";

        }


        elseif ($type === 'admin') {

            $sqlUpdate = "
                UPDATE admins
                SET password = ?
                WHERE admin_id = ?
            ";

        }


        $stmtUpdate = $conn->prepare($sqlUpdate);

        $stmtUpdate->execute([
            $password,
            $reset['user_id']
        ]);


        $delete = "
            DELETE FROM password_resets
            WHERE id = ?
        ";

        $stmtDelete = $conn->prepare($delete);

        $stmtDelete->execute([
            $reset['id']
        ]);


        $_SESSION['password_reset_success'] = true;


        if ($type === 'student') {

            header("Location: login_student.php");

        }

        elseif ($type === 'clinic') {

            header("Location: login_clinic.php");

        }

        elseif ($type === 'admin') {

            header("Location: login_admin.php");

        }

        exit();

    }

}

?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Reset Password</title>


<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}


body {

    background: #5d8d87;

    min-height: 100vh;

    display: flex;

    justify-content: center;

    align-items: center;

    padding: 20px;

}


.container {

    width: 100%;

    max-width: 450px;

    background: white;

    padding: 35px;

    border-radius: 20px;

    box-shadow: 0 8px 25px rgba(0,0,0,0.20);

}


.logo {

    display: flex;

    align-items: center;

    gap: 15px;

    margin-bottom: 20px;

}


.logo img {

    width: 80px;

    height: 80px;

    object-fit: contain;

    flex-shrink: 0;

}


h1 {

    text-align: center;

    color: #2f6468;

    font-size: 24px;

    margin-bottom: 8px;

}


.subtitle {

    text-align: center;

    color: #777;

    font-size: 13px;

    margin-bottom: 25px;

    line-height: 1.5;

}


.form-group {

    margin-bottom: 18px;

}


label {

    display: block;

    color: #555;

    font-size: 13px;

    font-weight: bold;

    margin-bottom: 7px;

}


input {

    width: 100%;

    padding: 13px 15px;

    border: 1px solid #ddd;

    border-radius: 10px;

    font-size: 14px;

    outline: none;

}


input:focus {

    border-color: #2f6468;

}


.btn {

    width: 100%;

    padding: 14px;

    border: none;

    border-radius: 30px;

    background: #2f6468;

    color: white;

    font-size: 14px;

    font-weight: bold;

    cursor: pointer;

}


.btn:hover {

    background: #234d50;

}


.error {

    background: #f8d7da;

    color: #721c24;

    padding: 12px;

    border-radius: 10px;

    margin-bottom: 18px;

    font-size: 13px;

    text-align: center;

}


.info {

    background: #d1ecf1;

    color: #0c5460;

    padding: 12px;

    border-radius: 10px;

    margin-bottom: 18px;

    font-size: 13px;

    text-align: center;

}


.password-note {

    color: #777;

    font-size: 12px;

    margin-top: 5px;

}


</style>

</head>

<body>


<div class="container">


    <div class="logo">

    <img src="images/logoEMCS.png" alt="e-Medical Card Logo">

    <h1>Reset Password</h1>

</div>


    <p class="subtitle">

        Enter your new password below.

    </p>


    <?php if (isset($error)): ?>

        <div class="error">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <form method="POST">


        <div class="form-group">

            <label>New Password</label>

            <input
                type="password"
                name="new_password"
                placeholder="Enter new password"
                required
            >

            <div class="password-note">

                Password must be at least 8 characters.

            </div>

        </div>


        <div class="form-group">

            <label>Confirm New Password</label>

            <input
                type="password"
                name="confirm_password"
                placeholder="Confirm new password"
                required
            >

        </div>


        <button
            type="submit"
            name="reset_password"
            class="btn"
        >

            Reset Password

        </button>


    </form>


</div>


</body>

</html>