<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db_connect.php";


function createRememberToken($userType, $userID)
{
    global $conn;

    $allowedTypes = [
        'student',
        'clinic',
        'admin'
    ];


    if (!in_array($userType, $allowedTypes, true)) {
        return false;
    }

    $cookieName = 'remember_' . $userType;

    $sql = "DELETE FROM remember_tokens
            WHERE user_type = ?
            AND user_id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        $userType,
        $userID
    ]);


    $selector = bin2hex(
        random_bytes(16)
    );


    $token = bin2hex(
        random_bytes(32)
    );


    $tokenHash = hash(
        'sha256',
        $token
    );


    $expires = date(
        'Y-m-d H:i:s',
        time() + (60 * 60 * 24 * 30)
    );


    $sql = "INSERT INTO remember_tokens
            (
                user_type,
                user_id,
                selector,
                token_hash,
                expires_at
            )
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        $userType,
        $userID,
        $selector,
        $tokenHash,
        $expires
    ]);


    $cookieValue =
        $selector . ':' . $token;


    setcookie(
        $cookieName,
        $cookieValue,
        [
            'expires' => time() + (60 * 60 * 24 * 30),
            'path' => '/',
            'secure' => (
                !empty($_SERVER['HTTPS']) &&
                $_SERVER['HTTPS'] !== 'off'
            ),
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );


    return true;
}


function checkRememberMe($expectedUserType)
{
    global $conn;

    $allowedTypes = [
        'student',
        'clinic',
        'admin'
    ];


    if (!in_array($expectedUserType, $allowedTypes, true)) {
        return false;
    }

    $cookieName =
        'remember_' . $expectedUserType;


    if (
        isset($_SESSION['user_type']) &&
        isset($_SESSION['user_id']) &&
        $_SESSION['user_type'] === $expectedUserType
    ) {

        return true;
    }


    if (
        isset($_SESSION['user_type']) &&
        $_SESSION['user_type'] !== $expectedUserType
    ) {

        return false;
    }


    if (!isset($_COOKIE[$cookieName])) {

        return false;
    }


    $parts = explode(
        ':',
        $_COOKIE[$cookieName],
        2
    );


    if (count($parts) !== 2) {

        clearRememberCookie(
            $expectedUserType
        );

        return false;
    }


    $selector = $parts[0];

    $token = $parts[1];


    $sql = "SELECT *
            FROM remember_tokens
            WHERE selector = ?
            AND user_type = ?
            AND expires_at > NOW()
            LIMIT 1";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        $selector,
        $expectedUserType
    ]);


    $remember = $stmt->fetch(
        PDO::FETCH_ASSOC
    );


    if (!$remember) {

        clearRememberCookie(
            $expectedUserType
        );

        return false;
    }


    $tokenHash = hash(
        'sha256',
        $token
    );


    if (
        !hash_equals(
            $remember['token_hash'],
            $tokenHash
        )
    ) {

        clearRememberCookie(
            $expectedUserType
        );

        return false;
    }


    if (
        $remember['user_type'] !==
        $expectedUserType
    ) {

        clearRememberCookie(
            $expectedUserType
        );

        return false;
    }


    session_regenerate_id(true);


    unset(
        $_SESSION['student_id'],
        $_SESSION['student_name'],
        $_SESSION['clinic_id'],
        $_SESSION['clinic_name'],
        $_SESSION['admin_id'],
        $_SESSION['full_name']
    );


    $_SESSION['user_type'] =
        $remember['user_type'];

    $_SESSION['user_id'] =
        $remember['user_id'];


    if (
        $remember['user_type'] ===
        'student'
    ) {

        $_SESSION['student_id'] =
            $remember['user_id'];


        $sql = "SELECT *
                FROM students
                WHERE student_id = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            $remember['user_id']
        ]);


        $student = $stmt->fetch(
            PDO::FETCH_ASSOC
        );


        if (!$student) {

            clearRememberCookie(
                'student'
            );

            unset(
                $_SESSION['user_type'],
                $_SESSION['user_id'],
                $_SESSION['student_id'],
                $_SESSION['student_name']
            );

            return false;
        }


        $_SESSION['student_name'] =
            $student['full_name'];
    }


    elseif (
        $remember['user_type'] ===
        'clinic'
    ) {

        $_SESSION['clinic_id'] =
            $remember['user_id'];


        $sql = "SELECT *
                FROM clinics
                WHERE clinic_id = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            $remember['user_id']
        ]);


        $clinic = $stmt->fetch(
            PDO::FETCH_ASSOC
        );


        if (!$clinic) {

            clearRememberCookie(
                'clinic'
            );

            unset(
                $_SESSION['user_type'],
                $_SESSION['user_id'],
                $_SESSION['clinic_id'],
                $_SESSION['clinic_name']
            );

            return false;
        }


        $_SESSION['clinic_name'] =
            $clinic['clinic_name'];
    }


    elseif (
        $remember['user_type'] ===
        'admin'
    ) {

        $_SESSION['admin_id'] =
            $remember['user_id'];


        $sql = "SELECT *
                FROM admins
                WHERE admin_id = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            $remember['user_id']
        ]);


        $admin = $stmt->fetch(
            PDO::FETCH_ASSOC
        );


        if (!$admin) {

            clearRememberCookie(
                'admin'
            );

            unset(
                $_SESSION['user_type'],
                $_SESSION['user_id'],
                $_SESSION['admin_id'],
                $_SESSION['full_name']
            );

            return false;
        }


        $_SESSION['full_name'] =
            $admin['full_name'];
    }


    return true;
}


function clearRememberCookie($userType = null)
{


    if ($userType !== null) {

        $allowedTypes = [
            'student',
            'clinic',
            'admin'
        ];


        if (
            !in_array(
                $userType,
                $allowedTypes,
                true
            )
        ) {

            return;
        }


        $cookieName =
            'remember_' . $userType;


        setcookie(
            $cookieName,
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => (
                    !empty($_SERVER['HTTPS']) &&
                    $_SERVER['HTTPS'] !== 'off'
                ),
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );


        unset(
            $_COOKIE[$cookieName]
        );


        return;
    }


    $roles = [
        'student',
        'clinic',
        'admin'
    ];


    foreach ($roles as $role) {

        clearRememberCookie($role);
    }
}


function clearCurrentLogin()
{
    global $conn;


    if (
        isset($_SESSION['user_type']) &&
        isset($_SESSION['user_id'])
    ) {

        $sql = "DELETE FROM remember_tokens
                WHERE user_type = ?
                AND user_id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            $_SESSION['user_type'],
            $_SESSION['user_id']
        ]);
    }


    clearRememberCookie();


    unset(
        $_SESSION['user_type'],
        $_SESSION['user_id'],

        $_SESSION['student_id'],
        $_SESSION['student_name'],

        $_SESSION['clinic_id'],
        $_SESSION['clinic_name'],

        $_SESSION['admin_id'],
        $_SESSION['full_name'],

        $_SESSION['login_status']
    );


    session_regenerate_id(true);
}


function clearRoleSessionData()
{
    unset(
        $_SESSION['user_type'],
        $_SESSION['user_id'],

        $_SESSION['student_id'],
        $_SESSION['student_name'],

        $_SESSION['clinic_id'],
        $_SESSION['clinic_name'],

        $_SESSION['admin_id'],
        $_SESSION['full_name']
    );
}


function logoutUser()
{
    global $conn;


    if (
        isset($_SESSION['user_type']) &&
        isset($_SESSION['user_id'])
    ) {

        $sql = "DELETE FROM remember_tokens
                WHERE user_type = ?
                AND user_id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            $_SESSION['user_type'],
            $_SESSION['user_id']
        ]);
    }

    clearRememberCookie();


    $_SESSION = [];


    if (
        ini_get("session.use_cookies")
    ) {

        $params =
            session_get_cookie_params();


        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }


    session_destroy();
}