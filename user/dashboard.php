<?php

require_once "auth-check.php";


/*
|--------------------------------------------------------------------------
| GET LOGGED-IN STUDENT
|--------------------------------------------------------------------------
*/

$user_id = (int) $_SESSION["user_id"];


$stmt = $conn->prepare("
    SELECT
        id,
        full_name,
        email,
        phone,
        grade,
        role
    FROM users
    WHERE id = ?
    LIMIT 1
");


$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| CHECK USER EXISTS
|--------------------------------------------------------------------------
*/

if ($result->num_rows !== 1) {

    $_SESSION = [];

    session_destroy();

    header("Location: ../login.html");
    exit;

}


$user = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| CHECK ROLE
|--------------------------------------------------------------------------
*/

if ($user["role"] !== "student") {

    if ($user["role"] === "admin") {

        header("Location: ../admin/dashboard.php");
        exit;

    }

    $_SESSION = [];

    session_destroy();

    header("Location: ../login.html");
    exit;

}


/*
|--------------------------------------------------------------------------
| STUDENT INFORMATION
|--------------------------------------------------------------------------
*/

$student_id = $user["id"];

$student_name = $user["full_name"];

$student_email = $user["email"];

$student_phone = $user["phone"];

$student_grade = $user["grade"];

$student_role = $user["role"];


/*
|--------------------------------------------------------------------------
| COUNT MATERIALS
|--------------------------------------------------------------------------
*/

$materials_count = 0;


$check_materials = $conn->query(
    "SHOW TABLES LIKE 'materials'"
);


if (
    $check_materials &&
    $check_materials->num_rows > 0
) {

    $result = $conn->query(
        "SELECT COUNT(*) AS total FROM materials"
    );

    if ($result) {

        $row = $result->fetch_assoc();

        $materials_count = (int) $row["total"];

    }

}


/*
|--------------------------------------------------------------------------
| COUNT PAST PAPERS
|--------------------------------------------------------------------------
*/

$past_papers_count = 0;


$check_papers = $conn->query(
    "SHOW TABLES LIKE 'past_papers'"
);


if (
    $check_papers &&
    $check_papers->num_rows > 0
) {

    $result = $conn->query(
        "SELECT COUNT(*) AS total FROM past_papers"
    );

    if ($result) {

        $row = $result->fetch_assoc();

        $past_papers_count = (int) $row["total"];

    }

}


/*
|--------------------------------------------------------------------------
| COUNT MODEL PAPERS
|--------------------------------------------------------------------------
*/

$model_papers_count = 0;


$check_models = $conn->query(
    "SHOW TABLES LIKE 'model_papers'"
);


if (
    $check_models &&
    $check_models->num_rows > 0
) {

    $result = $conn->query(
        "SELECT COUNT(*) AS total FROM model_papers"
    );

    if ($result) {

        $row = $result->fetch_assoc();

        $model_papers_count = (int) $row["total"];

    }

}

/* ==========================================
   UNREAD MESSAGE COUNT
========================================== */

$userId = (int) $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM messages m

    LEFT JOIN message_reads mr
        ON mr.message_id = m.id
        AND mr.user_id = ?

    WHERE
        (
            m.receiver_id = ?
            OR m.receiver_id IS NULL
        )
        AND mr.id IS NULL
");

$stmt->bind_param(
    "ii",
    $userId,
    $userId
);

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$unreadMessages = (int) ($row["total"] ?? 0);

$stmt->close();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Student Dashboard | MathsWorld
    </title>

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


    <style>

        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --navy: #0b1f3a;
    --navy-light: #12345b;
    --sky: #38bdf8;
    --sky-light: #e0f2fe;
    --white: #ffffff;
    --text: #1e293b;
    --muted: #64748b;
    --border: #e2e8f0;
    --background: #f5f8fc;
    --danger: #dc2626;
}

body {
    font-family: "Inter", sans-serif;
    background: var(--background);
    color: var(--text);
}

button,
input,
select {
    font-family: inherit;
}

a {
    text-decoration: none;
    color: inherit;
}


        /* =========================================
           SIDEBAR
        ========================================= */

        .sidebar {

            position: fixed;

            left: 0;
            top: 0;

            width: 250px;

            height: 100vh;

            background: #071b3a;

            color: white;

            padding: 25px 18px;

            z-index: 1000;

        }


        .logo {

            text-align: center;

            margin-bottom: 40px;

        }


        .logo h2 {

            font-size: 26px;

            color: white;

        }


        .logo span {

            color: #36a9e1;

        }


        .menu {

            list-style: none;

        }


        .menu li {

            margin-bottom: 8px;

        }


        .menu a {

            display: block;

            padding: 13px 15px;

            color: #dce8f5;

            text-decoration: none;

            border-radius: 8px;

            transition: 0.3s;

        }


        .menu a:hover,
        .menu a.active {

            background: #168de2;

            color: white;

        }


        .logout {

            position: absolute;

            bottom: 25px;

            left: 18px;

            right: 18px;

        }


        .logout a {

            display: block;

            text-align: center;

            padding: 12px;

            background: #e53935;

            color: white;

            text-decoration: none;

            border-radius: 8px;

        }



        /* =========================================
           MAIN
        ========================================= */

        .main {

            margin-left: 250px;

            padding: 30px;

        }


        /* =========================================
           TOP BAR
        ========================================= */

        .topbar {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 30px;

        }


        .topbar h1 {

            font-size: 28px;

            color: #071b3a;

        }


        .profile {

            background: white;

            padding: 10px 16px;

            border-radius: 10px;

            box-shadow:
                0 3px 15px rgba(0, 0, 0, 0.06);

        }


        .profile strong {

            color: #168de2;

        }


        /* =========================================
           WELCOME
        ========================================= */

        .welcome {

            background:
                linear-gradient(
                    135deg,
                    #071b3a,
                    #168de2
                );

            color: white;

            padding: 30px;

            border-radius: 16px;

            margin-bottom: 30px;

        }


        .welcome h2 {

            font-size: 27px;

            margin-bottom: 10px;

        }


        .welcome p {

            color: #e5f3ff;

            line-height: 1.6;

        }


        .grade {

            display: inline-block;

            margin-top: 15px;

            padding: 8px 14px;

            background: rgba(255,255,255,0.15);

            border-radius: 20px;

        }


        /* =========================================
           CARDS
        ========================================= */

        .cards {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;

            margin-bottom: 30px;

        }


        .card {

            background: white;

            padding: 25px;

            border-radius: 14px;

            box-shadow:
                0 4px 18px rgba(0, 0, 0, 0.06);

        }


        .card-icon {

            width: 45px;

            height: 45px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #e7f4fd;

            color: #168de2;

            border-radius: 10px;

            font-size: 22px;

            margin-bottom: 15px;

        }


        .card h3 {

            color: #667085;

            font-size: 15px;

            margin-bottom: 8px;

        }


        .card .number {

            font-size: 30px;

            font-weight: 700;

            color: #071b3a;

        }


        /* =========================================
           CONTENT GRID
        ========================================= */

        .content-grid {

            display: grid;

            grid-template-columns:
                2fr 1fr;

            gap: 20px;

        }


        .panel {

            background: white;

            padding: 25px;

            border-radius: 14px;

            box-shadow:
                0 4px 18px rgba(0, 0, 0, 0.06);

        }


        .panel h2 {

            font-size: 20px;

            color: #071b3a;

            margin-bottom: 20px;

        }


        .quick-links {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 15px;

        }


        .quick-link {

            padding: 18px;

            border: 1px solid #e4eaf0;

            border-radius: 10px;

            text-decoration: none;

            color: #071b3a;

            transition: 0.3s;

        }


        .quick-link:hover {

            border-color: #168de2;

            transform: translateY(-2px);

        }


        .quick-link strong {

            display: block;

            margin-bottom: 6px;

        }


        .quick-link small {

            color: #667085;

        }


        /* =========================================
           PROFILE INFO
        ========================================= */

        .info-row {

            display: flex;

            justify-content: space-between;

            padding: 13px 0;

            border-bottom: 1px solid #edf0f3;

        }


        .info-row:last-child {

            border-bottom: none;

        }


        .info-label {

            color: #667085;

        }


        .info-value {

            font-weight: 600;

            color: #071b3a;

        }

        /* ==========================================
        MESSAGE NOTIFICATION BADGE
        ========================================== */

        .notification-badge {

            margin-left: auto;

            min-width: 22px;

            height: 22px;

            padding: 0 7px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            border-radius: 20px;

            background: #38bdf8;

            color: #0b1f3a;

            font-size: 11px;

            font-weight: 800;

            line-height: 1;

        }


        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 1000px) {

            .cards {

                grid-template-columns:
                    repeat(2, 1fr);

            }

            .content-grid {

                grid-template-columns: 1fr;

            }

        }


        @media (max-width: 700px) {

            .sidebar {

                position: relative;

                width: 100%;

                height: auto;

            }

            .logout {

                position: static;

                margin-top: 20px;

            }

            .main {

                margin-left: 0;

                padding: 20px;

            }

            .cards {

                grid-template-columns: 1fr;

            }

            .topbar {

                flex-direction: column;

                align-items: flex-start;

                gap: 15px;

            }

            .quick-links {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>


<body>


<!-- =========================================
     SIDEBAR
========================================= -->

<aside class="sidebar">

    <div class="logo">

        <h2>
            Maths<span>World</span>
        </h2>

    </div>


    <ul class="menu">

        <li>
            <a href="dashboard.php">
                <i class="fa-solid fa-gauge"></i>
                <span>Dashboard</span>
            </a>

            <a href="materials.php">
                <i class="fa-solid fa-file-lines"></i>
                <span>My Materials</span>
            </a>

            <a href="past-papers.php">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Past Papers</span>
            </a>

            <a href="model-papers.php">
                <i class="fa-solid fa-book-open"></i>
                <span>Model Papers</span>
            </a>

            <a href="messages.php" class="sidebar-link">

                <i class="fa-solid fa-envelope"></i>

                <span>
                    Messages
                </span>

            </a>

       <a href="profile.php">

            <i class="fa-solid fa-user"></i>

            <span>My Profile</span>

        </a>

    </ul>


    <div class="logout">

        <a href="../logout.php">
            Logout
        </a>

    </div>

</aside>


<!-- =========================================
     MAIN CONTENT
========================================= -->

<main class="main">


    <div class="topbar">

        <h1>
            Student Dashboard
        </h1>

        <div class="profile">

            Welcome,
            <strong>
                <?= htmlspecialchars($student_name) ?>
            </strong>

        </div>

    </div>


    <!-- WELCOME -->

    <section class="welcome">

        <h2>
            Welcome, <?= htmlspecialchars($student_name) ?> 👋
        </h2>

        <p>
            Welcome to your MathsWorld student dashboard.
            Access your learning materials, past papers,
            and model papers from here.
        </p>


        <?php if (!empty($student_grade)): ?>

            <div class="grade">

                Grade:
                <?= htmlspecialchars($student_grade) ?>

            </div>

        <?php endif; ?>

    </section>


    <!-- STATISTICS -->

    <section class="cards">


        <div class="card">

            <div class="card-icon">
                📚
            </div>

            <h3>
                Materials
            </h3>

            <div class="number">
                <?= $materials_count ?>
            </div>

        </div>


        <div class="card">

            <div class="card-icon">
                📄
            </div>

            <h3>
                Past Papers
            </h3>

            <div class="number">
                <?= $past_papers_count ?>
            </div>

        </div>


        <div class="card">

            <div class="card-icon">
                📝
            </div>

            <h3>
                Model Papers
            </h3>

            <div class="number">
                <?= $model_papers_count ?>
            </div>

        </div>

        <a
            href="messages.php"
            class="dashboard-stat-card message-stat-card"
        >

            <div class="stat-icon">
                <i class="fa-solid fa-envelope"></i>
            </div>

            <div class="stat-content">

                <span>
                    Unread Messages
                </span>

                <strong id="unreadMessageCount">
                    <?php echo $unreadMessages; ?>
                </strong>

            </div>

        </a>


    </section>


    <!-- CONTENT -->

    <section class="content-grid">


        <!-- QUICK ACCESS -->

        <div class="panel">

            <h2>
                Quick Access
            </h2>


            <div class="quick-links">


                <a
                    href="materials.php"
                    class="quick-link"
                >

                    <strong>
                        📚 Study Materials
                    </strong>

                    <small>
                        View notes and learning resources
                    </small>

                </a>


                <a
                    href="past-papers.php"
                    class="quick-link"
                >

                    <strong>
                        📄 Past Papers
                    </strong>

                    <small>
                        Practice with previous papers
                    </small>

                </a>


                <a
                    href="model-papers.php"
                    class="quick-link"
                >

                    <strong>
                        📝 Model Papers
                    </strong>

                    <small>
                        Test your knowledge
                    </small>

                </a>


                <a
                    href="profile.php"
                    class="quick-link"
                >

                    <strong>
                        👤 My Profile
                    </strong>

                    <small>
                        View your account information
                    </small>

                </a>


            </div>

        </div>


        <!-- ACCOUNT INFO -->

        <div class="panel">

            <h2>
                My Account
            </h2>


            <div class="info-row">

                <span class="info-label">
                    Name
                </span>

                <span class="info-value">
                    <?= htmlspecialchars($student_name) ?>
                </span>

            </div>


            <div class="info-row">

                <span class="info-label">
                    Email
                </span>

                <span class="info-value">
                    <?= htmlspecialchars($student_email) ?>
                </span>

            </div>


            <div class="info-row">

                <span class="info-label">
                    Grade
                </span>

                <span class="info-value">

                    <?= !empty($student_grade)
                        ? htmlspecialchars($student_grade)
                        : "Not set"
                    ?>

                </span>

            </div>


            <div class="info-row">

                <span class="info-label">
                    Account
                </span>

                <span class="info-value">
                    Student
                </span>

            </div>


        </div>


    </section>


</main>

<script src="../js/notifications.js"></script>

</body>

</html>