<?php

session_start();

include("db_connect.php");


if(!isset($_SESSION['admin_id'])){

    header("Location: login_admin.php");

    exit();

}


$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : "";


if($search != ""){

    $stmt = $conn->prepare("

        SELECT *

        FROM students

        WHERE

            student_id LIKE ?

            OR full_name LIKE ?

            OR ic_number LIKE ?

            OR email LIKE ?

            OR phone_number LIKE ?

            OR program LIKE ?

            OR semester LIKE ?

            OR address LIKE ?

            OR guardian_name LIKE ?

            OR guardian_phone LIKE ?

        ORDER BY student_id ASC

    ");

    $searchTerm = "%" . $search . "%";

    $stmt->execute([

        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm

    ]);

}else{

    $stmt = $conn->prepare("

        SELECT *

        FROM students

        ORDER BY student_id ASC

    ");

    $stmt->execute();

}


$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student List - EMCS</title>


<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


<style>


*{

    margin:0;

    padding:0;

    box-sizing:border-box;

    font-family:Arial, Helvetica, sans-serif;

}


body{

    background:#5d8d87;

    min-height:100vh;

    padding:30px 20px;

}


.container{

    width:100%;

    max-width:1400px;

    margin:0 auto;

    background:white;

    padding:35px 40px;

    border-radius:20px;

    box-shadow:0 8px 25px rgba(0,0,0,0.20);

}


.header{

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding-bottom:25px;

    margin-bottom:25px;

    border-bottom:1px solid #eeeeee;

}


.header-left{

    display:flex;

    align-items:center;

}


.emcs-logo{

    width:75px;

    height:75px;

    object-fit:contain;

    margin-right:15px;

}


.uis-logo{

    width:55px;

    height:auto;

    object-fit:contain;

}


.header h1{

    color:#2f6468;

    font-size:28px;

    margin-bottom:4px;

}


.header p{

    color:#777;

    font-size:13px;

    letter-spacing:2px;

}


.page-title{

    margin-bottom:20px;

}


.page-title h2{

    color:#2f6468;

    font-size:22px;

    margin-bottom:6px;

}


.page-title p{

    color:#777;

    font-size:13px;

}


.search-box{

    background:#f2f2f2;

    padding:18px 20px;

    border-radius:12px;

    margin-bottom:20px;

}


.search-form{

    display:flex;

    gap:10px;

    align-items:center;

}


.search-input-wrapper{

    flex:1;

    position:relative;

}


.search-input-wrapper i{

    position:absolute;

    left:15px;

    top:50%;

    transform:translateY(-50%);

    color:#999;

}


.search-input{

    width:100%;

    padding:13px 15px 13px 42px;

    border:1px solid #ccc;

    border-radius:30px;

    background:white;

    color:#444;

    font-size:14px;

    outline:none;

}


.search-input:focus{

    border-color:#2f6468;

    box-shadow:0 0 0 3px rgba(47,100,104,0.10);

}


.search-btn{

    padding:13px 25px;

    border:none;

    border-radius:30px;

    background:#2f6468;

    color:white;

    font-size:14px;

    font-weight:bold;

    cursor:pointer;

}


.search-btn:hover{

    background:#234d50;

}


.clear-btn{

    padding:13px 22px;

    border-radius:30px;

    background:#7f8c8d;

    color:white;

    text-decoration:none;

    font-size:14px;

    font-weight:bold;

}


.clear-btn:hover{

    background:#666;

}


.search-result{

    margin-top:12px;

    color:#777;

    font-size:12px;

}


.search-result strong{

    color:#2f6468;

}


.student-summary{

    display:flex;

    justify-content:space-between;

    align-items:center;

    background:#f2f2f2;

    padding:15px 18px;

    border-radius:10px;

    margin-bottom:20px;

}


.summary-left{

    color:#555;

    font-size:13px;

}


.summary-left strong{

    color:#2f6468;

}


.summary-count{

    background:#2f6468;

    color:white;

    padding:8px 15px;

    border-radius:20px;

    font-size:12px;

    font-weight:bold;

}


.table-container{

    width:100%;

    overflow-x:auto;

    border-radius:10px;

    border:1px solid #eeeeee;

}


table{

    width:100%;

    border-collapse:collapse;

    min-width:1700px;

}


th{

    background:#2f6468;

    color:white;

    padding:14px 12px;

    text-align:center;

    font-size:13px;

    white-space:nowrap;

}


td{

    padding:13px 12px;

    border:1px solid #e0e0e0;

    text-align:center;

    color:#444;

    font-size:13px;

    background:white;

}


tr:nth-child(even) td{

    background:#f8f8f8;

}


tr:hover td{

    background:#f0f7f6;

}


.status{

    display:inline-block;

    padding:6px 12px;

    border-radius:20px;

    font-size:11px;

    font-weight:bold;

}


.status-active{

    background:#d4edda;

    color:#155724;

}


.status-inactive{

    background:#f8d7da;

    color:#721c24;

}


.action{

    text-align:center;

    white-space:nowrap;

}


.action a{

    display:inline-flex;

    align-items:center;

    justify-content:center;

    width:38px;

    height:38px;

    margin:0 3px;

    border-radius:8px;

    text-decoration:none;

    transition:0.2s;

}


.history-btn{

    background:#17a2b8;

    color:white;

}


.history-btn:hover{

    background:#138496;

}


.update-btn{

    background:#ffc107;

    color:#222;

}


.update-btn:hover{

    background:#e0a800;

}


.delete-btn{

    background:#dc3545;

    color:white;

}


.delete-btn:hover{

    background:#c82333;

}


.no-record{

    text-align:center;

    padding:50px 20px;

    background:#f8f8f8;

    border-radius:10px;

    border:1px solid #eeeeee;

}


.no-record-icon{

    width:55px;

    height:55px;

    margin:0 auto 15px;

    border-radius:50%;

    background:#e8eeee;

    color:#7f8c8d;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:22px;

}


.no-record h3{

    color:#2f6468;

    font-size:18px;

    margin-bottom:7px;

}


.no-record p{

    color:#777;

    font-size:13px;

}


.button-area{

    display:flex;

    justify-content:flex-start;

    margin-top:25px;

    padding-top:22px;

    border-top:1px solid #eeeeee;

}


.back-btn{

    display:inline-flex;

    align-items:center;

    justify-content:center;

    gap:8px;

    padding:13px 24px;

    background:#7f8c8d;

    color:white;

    text-decoration:none;

    border-radius:30px;

    font-size:14px;

    font-weight:bold;

}


.back-btn:hover{

    background:#666;

}


.info-box{

    margin-top:20px;

    padding:14px 16px;

    background:#f2f2f2;

    border-radius:10px;

    color:#777;

    font-size:12px;

    line-height:1.5;

}


.info-box strong{

    color:#2f6468;

}


.footer{

    text-align:center;

    margin-top:20px;

    color:#999;

    font-size:11px;

}


@media(max-width:800px){

    body{

        padding:20px 15px;

    }


    .container{

        padding:25px 20px;

    }


    .header h1{

        font-size:23px;

    }


    .student-summary{

        flex-direction:column;

        align-items:flex-start;

        gap:10px;

    }


    .summary-count{

        align-self:flex-start;

    }


    .page-title h2{

        font-size:20px;

    }


    .search-form{

        flex-direction:column;

        align-items:stretch;

    }


    .search-btn,
    .clear-btn{

        width:100%;

        text-align:center;

    }

}


@media(max-width:450px){

    .header{

        align-items:flex-start;

    }


    .emcs-logo{

        width:65px;

        height:65px;

        margin-right:10px;

    }


    .uis-logo{

        width:45px;

    }


    .header h1{

        font-size:20px;

    }


    .header p{

        font-size:10px;

    }


    .page-title h2{

        font-size:18px;

    }


    .back-btn{

        width:100%;

    }

}

</style>

</head>


<body>


<div class="container">


    <div class="header">

        <div class="header-left">

            <img src="images/logoEMCS.png" class="emcs-logo">

            <div>

                <h1>
                    E-MEDICAL CARD
                </h1>

                <p>
                    STUDENT MANAGEMENT
                </p>

            </div>

        </div>

        <img src="images/logouis.png" class="uis-logo">

    </div>


    <div class="page-title">

        <h2>
            Student List
        </h2>

        <p>
            View and manage registered student accounts in the system.
        </p>

    </div>


    <div class="search-box">

        <form method="GET" class="search-form">

            <div class="search-input-wrapper">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input

                    type="text"

                    name="search"

                    class="search-input"

                    placeholder="Search by Student ID, Name, IC Number, Email, Phone, Program, Semester or Guardian..."

                    value="<?php echo htmlspecialchars($search); ?>"

                >

            </div>


            <button

                type="submit"

                class="search-btn"

            >

                <i class="fa-solid fa-search"></i>

                Search

            </button>


            <?php if($search != ""){ ?>

                <a

                    href="view_student.php"

                    class="clear-btn"

                >

                    <i class="fa-solid fa-xmark"></i>

                    Clear

                </a>

            <?php } ?>


        </form>


        <?php if($search != ""){ ?>

            <div class="search-result">

                Search results for:

                <strong>

                    "<?php echo htmlspecialchars($search); ?>"

                </strong>

                —

                <?php echo count($students); ?>

                result<?php echo count($students) != 1 ? 's' : ''; ?> found.

            </div>

        <?php } ?>


    </div>


    <div class="student-summary">

        <div class="summary-left">

            <strong>
                Student Records:
            </strong>

            View student information, medical claim history,
            update account details or delete student records.

        </div>


        <div class="summary-count">

            <?php echo count($students); ?>

            Student<?php echo count($students) != 1 ? 's' : ''; ?>

        </div>

    </div>


    <?php if(count($students) > 0){ ?>

    <div class="table-container">

        <table>

            <tr>

                <th>
                    Student ID
                </th>

                <th>
                    Full Name
                </th>

                <th>
                    IC Number
                </th>

                <th>
                    Email
                </th>

                <th>
                    Phone Number
                </th>

                <th>
                    Program
                </th>

                <th>
                    Semester
                </th>

                <th>
                    Address
                </th>

                <th>
                    Guardian Name
                </th>

                <th>
                    Guardian Phone
                </th>

                <th>
                    Status
                </th>

                <th>
                    Balance
                </th>

                <th>
                    Action
                </th>

            </tr>



            <?php foreach($students as $row){ ?>

            <tr>


                <td>

                    <?php

                    echo htmlspecialchars(
                        $row['student_id']
                    );

                    ?>

                </td>


                <td>

                    <?php

                    echo htmlspecialchars(
                        $row['full_name']
                    );

                    ?>

                </td>


                <td>

                    <?php

                    echo htmlspecialchars(
                        $row['ic_number']
                    );

                    ?>

                </td>


                <td>

                    <?php

                    echo htmlspecialchars(
                        $row['email']
                    );

                    ?>

                </td>


                <td>

                    <?php

                    echo htmlspecialchars(
                        $row['phone_number']
                    );

                    ?>

                </td>


                <td>

                    <?php

                    echo htmlspecialchars(
                        $row['program']
                    );

                    ?>

                </td>


                <td>

                    <?php

                    echo htmlspecialchars(
                        $row['semester'] ?? ''
                    );

                    ?>

                </td>


                <td>

                    <?php

                    echo htmlspecialchars(
                        $row['address'] ?? ''
                    );

                    ?>

                </td>


                <td>

                    <?php

                    echo htmlspecialchars(
                        $row['guardian_name'] ?? ''
                    );

                    ?>

                </td>


                <td>

                    <?php

                    echo htmlspecialchars(
                        $row['guardian_phone'] ?? ''
                    );

                    ?>

                </td>


                <td>

                    <?php

                    $studentStatus = strtolower(
                        trim($row['status'])
                    );


                    if($studentStatus == "active"){

                        $statusClass = "status-active";

                    }

                    elseif($studentStatus == "inactive"){

                        $statusClass = "status-inactive";

                    }

                    else{

                        $statusClass = "";

                    }

                    ?>


                    <span class="status <?php echo $statusClass; ?>">

                        <?php

                        echo htmlspecialchars(
                            $row['status']
                        );

                        ?>

                    </span>

                </td>


                <td>

                    RM

                    <?php

                    echo number_format(

                        (float)$row['current_balance'],

                        2

                    );

                    ?>

                </td>


                <td class="action">


                    <a

                        class="history-btn"

                        href="admin_claimhistory.php?student_id=<?php echo urlencode($row['student_id']); ?>"

                        title="Medical Claim History"

                    >

                        <i class="fa-solid fa-file-medical"></i>

                    </a>


                    <a

                        class="update-btn"

                        href="update_student_info.php?student_id=<?php echo urlencode($row['student_id']); ?>"

                        title="Update Student"

                    >

                        <i class="fa-solid fa-pen"></i>

                    </a>


                    <a

                        class="delete-btn"

                        href="delete_student_record.php?student_id=<?php echo urlencode($row['student_id']); ?>"

                        onclick="return confirm('Are you sure you want to delete this student?');"

                        title="Delete Student"

                    >

                        <i class="fa-solid fa-trash"></i>

                    </a>


                </td>


            </tr>


            <?php } ?>


        </table>


    </div>


    <?php }else{ ?>


    <div class="no-record">


        <div class="no-record-icon">

            <i class="fa-solid fa-user-graduate"></i>

        </div>


        <h3>

            No Student Records Found

        </h3>


        <p>

            <?php if($search != ""){ ?>

                No students matched your search for

                "<strong><?php echo htmlspecialchars($search); ?></strong>".

            <?php }else{ ?>

                There are currently no registered students in the system.

            <?php } ?>


        </p>


    </div>


    <?php } ?>


    <div class="button-area">


        <a

            href="dashboard_admin.php"

            class="back-btn"

        >

            <i class="fa-solid fa-arrow-left"></i>

            Back To Dashboard

        </a>


    </div>


    <div class="info-box">


        <strong>

            Student Management Information:

        </strong>


        Use the search box to find students by Student ID,
        name, IC number, email, phone number, program, semester,
        address, guardian name or guardian phone number.
        For example, searching for <strong>MS39</strong>
        will display students registered under the MS39 program.


    </div>


    <div class="footer">

        e-Medical Card &nbsp; | &nbsp; UIS Local Student System

    </div>


</div>


</body>

</html>