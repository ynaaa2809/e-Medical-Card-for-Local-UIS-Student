<?php

session_start();

require_once "db_connect.php";


if (!isset($_SESSION['admin_id'])) {

    header("Location: login_admin.php");
    exit();

}


$student_id = '';

if (isset($_POST['student_id'])) {

    $student_id = trim($_POST['student_id']);

}
elseif (isset($_GET['student_id'])) {

    $student_id = trim($_GET['student_id']);

}


if ($student_id === '') {

    header("Location: view_student.php");
    exit();

}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete'])) {

    try {

        $sql = "
            DELETE FROM students
            WHERE student_id = :student_id
        ";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':student_id' => $student_id
        ]);


        if ($stmt->rowCount() > 0) {

            $_SESSION['delete_status'] = "success";

        }
        else {

            $_SESSION['delete_status'] = "failed";

        }


        header("Location: view_student.php");
        exit();


    }
    catch (PDOException $e) {

        $_SESSION['delete_status'] = "error";

        $_SESSION['delete_error'] = $e->getMessage();

        header(
            "Location: delete_student_record.php?student_id=" .
            urlencode($student_id)
        );

        exit();

    }

}


$sql = "
    SELECT *
    FROM students
    WHERE student_id = :student_id
    LIMIT 1
";


$stmt = $conn->prepare($sql);

$stmt->execute([
    ':student_id' => $student_id
]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$student) {

    header("Location: view_student.php");
    exit();

}


$full_name = $student['full_name'] ?? '';

$student_id_value = $student['student_id'] ?? '';

$ic_number = $student['ic_number'] ?? '';

$email = $student['email'] ?? '';

$phone_number = $student['phone_number'] ?? '';

$program = $student['program'] ?? '';

$semester = $student['semester'] ?? '';

$address = $student['address'] ?? '';

$guardian_name = $student['guardian_name'] ?? '';

$guardian_phone = $student['guardian_phone'] ?? '';

$status = $student['status'] ?? '';

$current_balance = $student['current_balance'] ?? 0;

?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Delete Student - EMCS</title>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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

    padding: 30px 20px;

}


.container {

    width: 100%;

    max-width: 750px;

    margin: 0 auto;

    background: white;

    padding: 35px 40px;

    border-radius: 20px;

    box-shadow: 0 8px 25px rgba(0,0,0,0.20);

}


.header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding-bottom: 25px;

    margin-bottom: 25px;

    border-bottom: 1px solid #eeeeee;

}


.header-left {

    display: flex;

    align-items: center;

}


.emcs-logo {

    width: 65px;

    height: 65px;

    object-fit: contain;

    margin-right: 10px;

}


.uis-logo {

    width: 45px;

    height: auto;

    object-fit: contain;

}


.header h1 {

    color: #2f6468;

    font-size: 28px;

    margin-bottom: 4px;

}


.header p {

    color: #777;

    font-size: 13px;

    letter-spacing: 2px;

}


.page-title {

    margin-bottom: 20px;

}


.page-title h2 {

    color: #2f6468;

    font-size: 21px;

    margin-bottom: 6px;

}


.page-title p {

    color: #777;

    font-size: 13px;

}


.warning-box {

    background: #fff3cd;

    border: 1px solid #ffeeba;

    border-left: 5px solid #f0ad4e;

    padding: 17px;

    border-radius: 12px;

    margin-bottom: 22px;

}


.warning-title {

    color: #856404;

    font-weight: bold;

    font-size: 14px;

    margin-bottom: 6px;

}


.warning-text {

    color: #856404;

    font-size: 12px;

    line-height: 1.5;

}


.error-box {

    background: #f8d7da;

    border: 1px solid #f5c6cb;

    border-left: 5px solid #dc3545;

    padding: 15px;

    border-radius: 10px;

    margin-bottom: 20px;

    color: #721c24;

    font-size: 13px;

    word-break: break-word;

}


.student-card {

    background: #f8f8f8;

    border: 1px solid #eeeeee;

    border-radius: 15px;

    padding: 20px;

}


.student-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 18px;

    padding-bottom: 15px;

    border-bottom: 1px solid #e5e5e5;

}


.student-header h3 {

    color: #2f6468;

    font-size: 17px;

}


.student-id {

    background: #2f6468;

    color: white;

    padding: 7px 13px;

    border-radius: 20px;

    font-size: 11px;

    font-weight: bold;

}


.info-grid {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 12px;

}


.info-box {

    background: white;

    padding: 14px;

    border-radius: 10px;

    border: 1px solid #e5e5e5;

}


.info-box.full {

    grid-column: 1 / -1;

}


.info-label {

    color: #999;

    font-size: 10px;

    margin-bottom: 5px;

    font-weight: bold;

}


.info-value {

    color: #444;

    font-size: 13px;

    font-weight: bold;

    word-break: break-word;

    line-height: 1.5;

}


.info-value.empty {

    color: #aaa;

    font-weight: normal;

    font-style: italic;

}


/* =========================
   DELETE AREA
========================= */

.delete-area {

    margin-top: 22px;

    padding-top: 22px;

    border-top: 1px solid #eeeeee;

    text-align: center;

}


.delete-question {

    color: #555;

    font-size: 13px;

    margin-bottom: 15px;

}


.delete-question strong {

    color: #dc3545;

}


.button-area {

    display: flex;

    gap: 12px;

}


.cancel-btn {

    flex: 1;

    padding: 14px;

    text-align: center;

    text-decoration: none;

    background: #7f8c8d;

    color: white;

    border-radius: 30px;

    font-size: 14px;

    font-weight: bold;

}


.cancel-btn:hover {

    background: #666;

}


.delete-form {

    flex: 1;

}


.delete-btn {

    width: 100%;

    padding: 14px;

    border: none;

    border-radius: 30px;

    background: #dc3545;

    color: white;

    font-size: 14px;

    font-weight: bold;

    cursor: pointer;

}


.delete-btn:hover {

    background: #c82333;

}


.footer {

    text-align: center;

    margin-top: 20px;

    color: #999;

    font-size: 11px;

}


@media(max-width:600px) {

    body {

        padding: 20px 15px;

    }


    .container {

        padding: 25px 20px;

    }


    .header h1 {

        font-size: 22px;

    }


    .emcs-logo {

        width: 55px;

        height: 55px;

    }


    .uis-logo {

        width: 40px;

    }


    .student-header {

        flex-direction: column;

        align-items: flex-start;

        gap: 10px;

    }


    .info-grid {

        grid-template-columns: 1fr;

    }


    .info-box.full {

        grid-column: auto;

    }


    .button-area {

        flex-direction: column;

    }


    .cancel-btn,
    .delete-form {

        width: 100%;

    }

}


</style>

</head>

<body>


<div class="container">


    <div class="header">

        <div class="header-left">

            <img
                src="images/logoEMCS.png"
                class="emcs-logo"
                alt="EMCS Logo"
            >

            <div>

                <h1>E-MEDICAL CARD</h1>

                <p>STUDENT MANAGEMENT</p>

            </div>

        </div>


        <img
            src="images/logouis.png"
            class="uis-logo"
            alt="UIS Logo"
        >

    </div>


    <div class="page-title">

        <h2>Delete Student Record</h2>

        <p>
            Review the student information before permanently deleting the account.
        </p>

    </div>


    <?php if (
        isset($_SESSION['delete_status']) &&
        $_SESSION['delete_status'] === "error"
    ): ?>

        <div class="error-box">

            <strong>Unable to delete this student.</strong>

            <br><br>

            <?php

            echo htmlspecialchars(
                $_SESSION['delete_error'] ?? 'Unknown database error.'
            );

            ?>

        </div>

        <?php

        unset(
            $_SESSION['delete_status'],
            $_SESSION['delete_error']
        );

        ?>

    <?php endif; ?>


    <div class="warning-box">

        <div class="warning-title">

            ⚠ Warning: Permanent Action

        </div>

        <div class="warning-text">

            Deleting this student will permanently remove the student
            record from the database. Please make sure you have selected
            the correct student before continuing.

        </div>

    </div>


    <div class="student-card">


        <div class="student-header">

            <h3>

                Student Information

            </h3>


            <div class="student-id">

                <?php echo htmlspecialchars($student_id_value); ?>

            </div>

        </div>


        <div class="info-grid">


            <div class="info-box full">

                <div class="info-label">

                    FULL NAME

                </div>

                <div class="info-value">

                    <?php echo htmlspecialchars($full_name); ?>

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">

                    IC NUMBER

                </div>

                <div class="info-value">

                    <?php echo htmlspecialchars($ic_number); ?>

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">

                    EMAIL

                </div>

                <div class="info-value">

                    <?php echo htmlspecialchars($email); ?>

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">

                    PHONE NUMBER

                </div>

                <div class="info-value <?php echo $phone_number === '' ? 'empty' : ''; ?>">

                    <?php

                    echo $phone_number !== ''
                        ? htmlspecialchars($phone_number)
                        : 'Not provided';

                    ?>

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">

                    PROGRAM

                </div>

                <div class="info-value <?php echo $program === '' ? 'empty' : ''; ?>">

                    <?php

                    echo $program !== ''
                        ? htmlspecialchars($program)
                        : 'Not provided';

                    ?>

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">

                    SEMESTER

                </div>

                <div class="info-value <?php echo $semester === '' || $semester === null ? 'empty' : ''; ?>">

                    <?php

                    echo ($semester !== '' && $semester !== null)
                        ? htmlspecialchars((string)$semester)
                        : 'Not provided';

                    ?>

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">

                    STATUS

                </div>

                <div class="info-value">

                    <?php echo htmlspecialchars($status); ?>

                </div>

            </div>


            <div class="info-box full">

                <div class="info-label">

                    ADDRESS

                </div>

                <div class="info-value <?php echo $address === '' ? 'empty' : ''; ?>">

                    <?php

                    echo $address !== ''
                        ? nl2br(htmlspecialchars($address))
                        : 'Not provided';

                    ?>

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">

                    GUARDIAN NAME

                </div>

                <div class="info-value <?php echo $guardian_name === '' ? 'empty' : ''; ?>">

                    <?php

                    echo $guardian_name !== ''
                        ? htmlspecialchars($guardian_name)
                        : 'Not provided';

                    ?>

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">

                    GUARDIAN PHONE

                </div>

                <div class="info-value <?php echo $guardian_phone === '' ? 'empty' : ''; ?>">

                    <?php

                    echo $guardian_phone !== ''
                        ? htmlspecialchars($guardian_phone)
                        : 'Not provided';

                    ?>

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">

                    CURRENT BALANCE

                </div>

                <div class="info-value">

                    RM <?php echo number_format((float)$current_balance, 2); ?>

                </div>

            </div>


        </div>


        <div class="delete-area">


            <div class="delete-question">

                Are you sure you want to delete

                <strong>

                    <?php echo htmlspecialchars($full_name); ?>

                </strong>

                from the system?

            </div>


            <div class="button-area">


                <a
                    href="view_student.php"
                    class="cancel-btn"
                >

                    ← Cancel

                </a>


                <form
                    method="POST"
                    action="delete_student_record.php"
                    class="delete-form"
                >

                    <input
                        type="hidden"
                        name="student_id"
                        value="<?php echo htmlspecialchars($student_id_value); ?>"
                    >


                    <button
                        type="submit"
                        name="delete"
                        value="1"
                        class="delete-btn"
                        onclick="return confirm('Are you absolutely sure you want to delete this student?');"
                    >

                        Delete Student

                    </button>

                </form>


            </div>


        </div>


    </div>


    <div class="footer">

        e-Medical Card &nbsp; | &nbsp; UIS Local Student System

    </div>


</div>


</body>

</html>