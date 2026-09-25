<?php

require_once "auth-check.php";

// =========================================
// ADMIN INFORMATION
// =========================================

$adminId = (int) $_SESSION["user_id"];

$adminName = $_SESSION["full_name"] ?? "Administrator";


// =========================================
// GET ADMIN INFORMATION FROM USERS TABLE
// =========================================

$stmt = $conn->prepare("
    SELECT
        id,
        full_name,
        email,
        phone,
        role,
        grade
    FROM users
    WHERE id = ?
    AND role = 'admin'
    LIMIT 1
");

$stmt->bind_param("i", $adminId);

$stmt->execute();

$result = $stmt->get_result();

$admin = $result->fetch_assoc();

$stmt->close();


// =========================================
// ADMIN NOT FOUND
// =========================================

if (!$admin) {

    $_SESSION = [];

    session_destroy();

    header("Location: ../login.html");

    exit;
}


// =========================================
// ADMIN DATA
// =========================================

$adminName = $admin["full_name"];

$adminEmail = $admin["email"];

$adminPhone = $admin["phone"];

$adminGrade = $admin["grade"];


// =========================================
// TOTAL STUDENTS
// =========================================

$studentCount = 0;

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM users
    WHERE role = 'student'
");

$stmt->execute();

$result = $stmt->get_result();

if ($result) {

    $row = $result->fetch_assoc();

    $studentCount = (int)$row["total"];

}

$stmt->close();


// =========================================
// TOTAL TEACHERS
// =========================================

$teacherCount = 0;

$tableCheck = $conn->query("
    SHOW TABLES LIKE 'teachers'
");

if ($tableCheck && $tableCheck->num_rows > 0) {

    $result = $conn->query("
        SELECT COUNT(*) AS total
        FROM teachers
        WHERE status = 'active'
    ");

    if ($result) {

        $row = $result->fetch_assoc();

        $teacherCount = (int)$row["total"];

    }

}


// =========================================
// TOTAL MATERIALS
// =========================================

$materialCount = 0;

$tableCheck = $conn->query("
    SHOW TABLES LIKE 'materials'
");

if ($tableCheck && $tableCheck->num_rows > 0) {

    $result = $conn->query("
        SELECT COUNT(*) AS total
        FROM materials
    ");

    if ($result) {

        $row = $result->fetch_assoc();

        $materialCount = (int)$row["total"];

    }

}


// =========================================
// TOTAL PAST PAPERS
// =========================================

$pastPaperCount = 0;

$tableCheck = $conn->query("
    SHOW TABLES LIKE 'past_papers'
");

if ($tableCheck && $tableCheck->num_rows > 0) {

    $result = $conn->query("
        SELECT COUNT(*) AS total
        FROM past_papers
    ");

    if ($result) {

        $row = $result->fetch_assoc();

        $pastPaperCount = (int)$row["total"];

    }

}


// =========================================
// TOTAL MODEL PAPERS
// =========================================

$modelPaperCount = 0;

$tableCheck = $conn->query("
    SHOW TABLES LIKE 'model_papers'
");

if ($tableCheck && $tableCheck->num_rows > 0) {

    $result = $conn->query("
        SELECT COUNT(*) AS total
        FROM model_papers
    ");

    if ($result) {

        $row = $result->fetch_assoc();

        $modelPaperCount = (int)$row["total"];

    }

}


$totalPapers = $pastPaperCount + $modelPaperCount;


// =========================================
// UNREAD MESSAGES
// =========================================

$unreadMessages = 0;

$tableCheck = $conn->query("
    SHOW TABLES LIKE 'messages'
");

if ($tableCheck && $tableCheck->num_rows > 0) {

    $result = $conn->query("
        SELECT COUNT(*) AS total
        FROM messages
        WHERE status = 'unread'
    ");

    if ($result) {

        $row = $result->fetch_assoc();

        $unreadMessages = (int)$row["total"];

    }

}


// =========================================
// RECENT STUDENTS
// =========================================

$recentStudents = [];

$stmt = $conn->prepare("
    SELECT
        id,
        full_name,
        email,
        grade,
        created_at
    FROM users
    WHERE role = 'student'
    ORDER BY created_at DESC
    LIMIT 5
");

$stmt->execute();

$result = $stmt->get_result();

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $recentStudents[] = $row;

    }

}

$stmt->close();


// =========================================
// RECENT ACTIVITIES
// =========================================

$activities = [];

$tableCheck = $conn->query("
    SHOW TABLES LIKE 'activity_logs'
");

if ($tableCheck && $tableCheck->num_rows > 0) {

    $stmt = $conn->prepare("
        SELECT
            activity_logs.*,
            users.full_name
        FROM activity_logs

        LEFT JOIN users
            ON activity_logs.user_id = users.id

        ORDER BY activity_logs.created_at DESC

        LIMIT 5
    ");

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result) {

        while ($row = $result->fetch_assoc()) {

            $activities[] = $row;

        }

    }

    $stmt->close();

}


// =========================================
// STUDENT REGISTRATIONS
// LAST 30 DAYS
// =========================================

$chartLabels = [];

$chartData = [];


// One query instead of 30 separate queries
$registrationData = [];

$stmt = $conn->prepare("
    SELECT
        DATE(created_at) AS registration_date,
        COUNT(*) AS total
    FROM users
    WHERE role = 'student'
    AND created_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
    GROUP BY DATE(created_at)
");

$stmt->execute();

$result = $stmt->get_result();

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $registrationData[$row["registration_date"]] =
            (int)$row["total"];

    }

}

$stmt->close();


for ($i = 29; $i >= 0; $i--) {

    $timestamp = strtotime("-" . $i . " days");

    $date = date("Y-m-d", $timestamp);

    $chartLabels[] = date("d M", $timestamp);

    $chartData[] =
        $registrationData[$date] ?? 0;

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>MathsWorld Admin Dashboard</title>


    <!-- Google Font -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <!-- Font Awesome -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- Dashboard CSS -->

    <link
        rel="stylesheet"
        href="../css/admin-dashboard.css"
    >


</head>


<body>


<!-- =========================================
     ADMIN SIDEBAR
========================================= -->

<aside
    class="admin-sidebar"
    id="adminSidebar"
>

    <div class="sidebar-logo">

        <div class="logo-icon">

            <i class="fa-solid fa-calculator"></i>

        </div>


        <div class="logo-text">

            <h2>
                MATHS<span>WORLD</span>
            </h2>

            <p>
                ADMIN PANEL
            </p>

        </div>

    </div>


    <nav class="sidebar-nav">


        <div class="nav-title">
            MAIN MENU
        </div>


        <a href="dashboard.php" class="nav-link active">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Dashboard</span>
        </a>

        <a href="student-management.php" class="nav-link">
            <i class="fa-solid fa-user-graduate"></i>
            <span>Students</span>
        </a>

        <a href="teachers-management.php" class="nav-link">
            <i class="fa-solid fa-chalkboard-user"></i>
            <span>Teachers</span>
        </a>

        <a href="subject-management.php" class="nav-link">
            <i class="fa-solid fa-book-open"></i>
            <span>Subjects</span>
        </a>

        <a href="material-management.php">
            <i class="fa-solid fa-file-lines"></i>
            <span>Materials</span>
        </a>

        <a href="past-papers-management.php" class="nav-link">
            <i class="fa-solid fa-file-lines"></i>
            <span>Past Papers</span>
        </a>

        <a href="model-papers-management.php" class="nav-link">
            <i class="fa-solid fa-file-circle-check"></i>
            <span>Model Papers</span>
        </a>

        <a href="message-management.php" class="nav-link">
            <i class="fa-solid fa-envelope"></i>
            <span>Messages</span>

            <?php if ($unreadMessages > 0): ?>
                <span class="nav-badge">
                    <?= $unreadMessages ?>
                </span>
            <?php endif; ?>
        </a>

        <a href="activity-log.php" class="nav-link">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>Activity Log</span>
        </a>

        <a href="settings.php" class="nav-link">
            <i class="fa-solid fa-gear"></i>
            <span>Settings</span>
        </a>

    </nav>


    <!-- Sidebar Bottom -->

    <div class="sidebar-bottom">

        <div class="admin-mini-profile">

            <div class="mini-avatar">

                <span>
                    <?= strtoupper(substr($adminName, 0, 2)) ?>
                </span>

            </div>


            <div class="mini-info">

                <strong>
                    <?= htmlspecialchars($adminName) ?>
                </strong>

                <small>
                    Administrator
                </small>

            </div>

        </div>


       <a
        href="../logout.php"
        class="sidebar-logout"
        id="sidebarLogout"
        title="Logout"
    >
        <i class="fa-solid fa-right-from-bracket"></i>
    </a>

    </div>

</aside>


<!-- =========================================
     MAIN
========================================= -->

<main class="admin-main">


    <!-- HEADER -->

    <header class="admin-header">

        <div class="header-left">

            <button
                class="mobile-menu-btn"
                id="mobileMenuBtn"
            >

                <i class="fa-solid fa-bars"></i>

            </button>


            <div>

                <h1>
                    Dashboard
                </h1>

                <p>
                    Welcome back,
                    <?= htmlspecialchars($adminName) ?>
                    👋
                </p>

            </div>

        </div>


        <div class="header-right">

            <button
                class="header-icon-btn"
                id="headerSearchBtn"
            >

                <i class="fa-solid fa-magnifying-glass"></i>

            </button>


            <button
                class="header-icon-btn notification-btn"
                id="notificationBtn"
            >

                <i class="fa-regular fa-bell"></i>

                <?php if ($unreadMessages > 0): ?>

                    <span class="notification-dot"></span>

                <?php endif; ?>

            </button>


            <div class="header-profile">

                <div class="header-avatar">

                    <?= strtoupper(substr($adminName, 0, 2)) ?>

                </div>


                <div class="header-profile-info">

                    <strong>
                        <?= htmlspecialchars($adminName) ?>
                    </strong>

                    <span>
                        Administrator
                    </span>

                </div>


                <i
                    class="fa-solid fa-chevron-down profile-arrow"
                ></i>

            </div>

        </div>

    </header>


    <!-- =====================================
         CONTENT
    ====================================== -->

    <section class="dashboard-content">


        <!-- Welcome -->

        <div class="welcome-banner">

            <div class="welcome-content">

                <span class="welcome-label">
                    MATHSWORLD ADMINISTRATION
                </span>


                <h2>
                    Manage your education platform
                </h2>


                <p>
                    Monitor students, teachers, learning materials,
                    papers and website activity from one place.
                </p>


                <a
                    href="settings.php"
                    class="welcome-btn"
                >

                    <i class="fa-solid fa-gear"></i>

                    Website Settings

                </a>

            </div>


            <div class="welcome-illustration">

                <div class="illustration-circle circle-one"></div>

                <div class="illustration-circle circle-two"></div>

                <i class="fa-solid fa-graduation-cap"></i>

            </div>

        </div>


        <!-- =====================================
             STATISTICS
        ====================================== -->

        <div class="stats-grid">


            <!-- STUDENTS -->

            <div class="stat-card">

                <div class="stat-card-top">

                    <div class="stat-icon students-icon">

                        <i class="fa-solid fa-user-graduate"></i>

                    </div>

                </div>


                <div class="stat-number">
                    <?= number_format($studentCount) ?>
                </div>


                <div class="stat-title">
                    Total Students
                </div>


                <div class="stat-footer">

                    <span>
                        Registered students
                    </span>

                </div>

            </div>


            <!-- TEACHERS -->

            <div class="stat-card">

                <div class="stat-card-top">

                    <div class="stat-icon teachers-icon">

                        <i class="fa-solid fa-chalkboard-user"></i>

                    </div>

                </div>


                <div class="stat-number">
                    <?= number_format($teacherCount) ?>
                </div>


                <div class="stat-title">
                    Total Teachers
                </div>


                <div class="stat-footer">

                    <span>
                        Active teaching staff
                    </span>

                </div>

            </div>


            <!-- MATERIALS -->

            <div class="stat-card">

                <div class="stat-card-top">

                    <div class="stat-icon materials-icon">

                        <i class="fa-solid fa-folder-open"></i>

                    </div>

                </div>


                <div class="stat-number">
                    <?= number_format($materialCount) ?>
                </div>


                <div class="stat-title">
                    Learning Materials
                </div>


                <div class="stat-footer">

                    <span>
                        Available resources
                    </span>

                </div>

            </div>


            <!-- PAPERS -->

            <div class="stat-card">

                <div class="stat-card-top">

                    <div class="stat-icon papers-icon">

                        <i class="fa-solid fa-file-lines"></i>

                    </div>

                </div>


                <div class="stat-number">
                    <?= number_format($totalPapers) ?>
                </div>


                <div class="stat-title">
                    Papers
                </div>


                <div class="stat-footer">

                    <span>
                        Past & model papers
                    </span>

                </div>

            </div>

        </div>


        <!-- =====================================
             QUICK ACTIONS
        ====================================== -->

        <section class="section-block">

            <div class="section-heading">

                <div>

                    <h2>
                        Quick Actions
                    </h2>

                    <p>
                        Frequently used administration tools
                    </p>

                </div>

            </div>


            <div class="quick-actions">


                <a
                    href="student-management.php?action=add"
                    class="quick-action"
                >

                    <div class="quick-action-icon">

                        <i class="fa-solid fa-user-plus"></i>

                    </div>


                    <div>

                        <strong>
                            Add Student
                        </strong>

                        <span>
                            Create a new student account
                        </span>

                    </div>


                    <i class="fa-solid fa-arrow-right"></i>

                </a>


                <a
                    href="teachers-management.php?action=add"
                    class="quick-action"
                >

                    <div class="quick-action-icon">

                        <i class="fa-solid fa-person-chalkboard"></i>

                    </div>


                    <div>

                        <strong>
                            Add Teacher
                        </strong>

                        <span>
                            Add a new teacher
                        </span>

                    </div>


                    <i class="fa-solid fa-arrow-right"></i>

                </a>


                <a
                    href="material-management.php?action=add"
                    class="quick-action"
                >

                    <div class="quick-action-icon">

                        <i class="fa-solid fa-cloud-arrow-up"></i>

                    </div>


                    <div>

                        <strong>
                            Upload Material
                        </strong>

                        <span>
                            Add learning resources
                        </span>

                    </div>


                    <i class="fa-solid fa-arrow-right"></i>

                </a>


                <a
                    href="subject-management.php?action=add"
                    class="quick-action"
                >

                    <div class="quick-action-icon">

                        <i class="fa-solid fa-book-medical"></i>

                    </div>


                    <div>

                        <strong>
                            Add Subject
                        </strong>

                        <span>
                            Create a new subject
                        </span>

                    </div>


                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>

        </section>


        <!-- =====================================
             RECENT STUDENTS
        ====================================== -->

        <section class="dashboard-card students-card">

            <div class="card-header">

                <div>

                    <h3>
                        Recent Students
                    </h3>

                    <p>
                        Latest student registrations
                    </p>

                </div>


                <a href="student-management.php">

                    View All Students

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>


            <div class="table-container">

                <table class="students-table">

                    <thead>

                        <tr>

                            <th>
                                Student
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Grade
                            </th>

                            <th>
                                Registered
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if (empty($recentStudents)): ?>

                        <tr>

                            <td colspan="6">

                                No students registered yet.

                            </td>

                        </tr>

                    <?php else: ?>


                        <?php foreach ($recentStudents as $student): ?>

                            <tr>

                                <td>

                                    <div class="student-profile">

                                        <div class="student-avatar">

                                            <?= strtoupper(
                                                substr(
                                                    $student["full_name"],
                                                    0,
                                                    2
                                                )
                                            ) ?>

                                        </div>


                                        <div>

                                            <strong>

                                                <?= htmlspecialchars(
                                                    $student["full_name"]
                                                ) ?>

                                            </strong>


                                            <span>

                                                Student
                                                #<?= $student["id"] ?>

                                            </span>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $student["email"]
                                    ) ?>

                                </td>


                                <td>

                                    <span class="grade-badge">

                                        <?= htmlspecialchars(
                                            $student["grade"] ?? "N/A"
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <?= date(
                                        "d M Y, h:i A",
                                        strtotime(
                                            $student["created_at"]
                                        )
                                    ) ?>

                                </td>


                                <td>

                                    <span class="status active">
                                        Active
                                    </span>

                                </td>


                                <td>

                                    <button
                                        class="table-action"
                                        title="View student"
                                        onclick="viewStudent(<?= $student['id'] ?>)"
                                    >

                                        <i class="fa-regular fa-eye"></i>

                                    </button>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>


        <!-- =====================================
             RECENT ACTIVITY
        ====================================== -->

        <section class="dashboard-card activity-card">

            <div class="card-header">

                <div>

                    <h3>
                        Recent Activity
                    </h3>

                    <p>
                        Latest admin activities
                    </p>

                </div>


                <a href="activity-log.php">
                    View All
                </a>

            </div>


            <div class="activity-list">


            <?php if (empty($activities)): ?>

                <div class="activity-item">

                    <div class="activity-info">

                        <strong>
                            No activity yet
                        </strong>

                        <span>
                            Admin activity will appear here.
                        </span>

                    </div>

                </div>

            <?php else: ?>


                <?php foreach ($activities as $activity): ?>

                    <div class="activity-item">

                        <div class="activity-icon login">

                            <i class="fa-solid fa-clock"></i>

                        </div>


                        <div class="activity-info">

                            <strong>

                                <?= htmlspecialchars(
                                    $activity["action"]
                                ) ?>

                            </strong>


                            <span>

                                <?= htmlspecialchars(
                                    $activity["description"]
                                ) ?>

                            </span>


                            <small>

                                <?= date(
                                    "d M Y, h:i A",
                                    strtotime(
                                        $activity["created_at"]
                                    )
                                ) ?>

                            </small>

                        </div>

                    </div>

                <?php endforeach; ?>


            <?php endif; ?>

            </div>

        </section>


    </section>

</main>


<!-- =========================================
     MOBILE OVERLAY
========================================= -->

<div
    class="sidebar-overlay"
    id="sidebarOverlay"
></div>


<!-- =========================================
     LOGOUT MODAL
========================================= -->

<div
    class="modal-overlay"
    id="logoutModal"
>

    <div class="logout-modal">

        <div class="logout-icon">

            <i class="fa-solid fa-right-from-bracket"></i>

        </div>


        <h3>
            Logout?
        </h3>


        <p>
            Are you sure you want to logout from
            the MathsWorld admin panel?
        </p>


        <div class="logout-actions">

            <button
                class="cancel-btn"
                id="cancelLogout"
            >
                Cancel
            </button>


            <button
                class="confirm-logout-btn"
                id="confirmLogout"
            >
                Logout
            </button>

        </div>

    </div>

</div>


<!-- =========================================
     JAVASCRIPT
========================================= -->

<script>

const chartLabels =
    <?= json_encode($chartLabels) ?>;

const chartData =
    <?= json_encode($chartData) ?>;


function viewStudent(id) {

    window.location.href =
        "student-management.php?id=" + id;

}

</script>


<script src="../js/admin-dashboard.js"></script>

</body>

</html>