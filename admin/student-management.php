<?php

session_start();

require_once "../db.php";


// =====================================
// CHECK LOGIN
// =====================================

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


// =====================================
// CHECK ADMIN
// =====================================

if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {

    header("Location: ../user/dashboard.php");
    exit;

}


// =====================================
// ADMIN INFORMATION
// =====================================

$adminName = $_SESSION["user_name"] ?? "Administrator";

$firstLetter = strtoupper(
    substr($adminName, 0, 1)
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Students | MathsWorld Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="../css/student-management.css"
    >

</head>


<body>





    <!-- ================= SIDEBAR ================= -->

    <aside class="admin-sidebar" id="adminSidebar">


        <div class="sidebar-logo">

            <div class="logo-icon">
                <i class="fa-solid fa-calculator"></i>
            </div>

            <div>

                <h2>MathsWorld</h2>

                <span>Admin Panel</span>

            </div>

        </div>


        <nav class="sidebar-nav">


            <p class="nav-title">
                MAIN MENU
            </p>


            <a
                href="dashboard.php"
                class="nav-item"
            >

                <i class="fa-solid fa-gauge-high"></i>

                <span>Dashboard</span>

            </a>


            <a
                href="student-management.php"
                class="nav-item active"
            >

                <i class="fa-solid fa-user-graduate"></i>

                <span>Students</span>

            </a>


            <a
                href="teachers-management.php"
                class="nav-item"
            >

                <i class="fa-solid fa-chalkboard-user"></i>

                <span>Teachers</span>

            </a>


            <a
                href="subject-management.php"
                class="nav-item"
            >

                <i class="fa-solid fa-book"></i>

                <span>Subjects</span>

            </a>


            <a
                href="material-management.php"
                class="nav-item"
            >

                <i class="fa-solid fa-file-lines"></i>

                <span>Materials</span>

            </a>


            <a
                href="past-papers-management.php"
                class="nav-item"
            >

                <i class="fa-solid fa-file-pdf"></i>

                <span>Past Papers</span>

            </a>


            <a
                href="model-papers-management.php"
                class="nav-item"
            >

                <i class="fa-solid fa-copy"></i>

                <span>Model Papers</span>

            </a>


            <p class="nav-title">
                COMMUNICATION
            </p>


            <a
                href="message-management.php"
                class="nav-item"
            >

                <i class="fa-solid fa-envelope"></i>

                <span>Messages</span>

                <span class="nav-badge">
                    5
                </span>

            </a>


            <a
                href="activity-log.php"
                class="nav-item"
            >

                <i class="fa-solid fa-clock-rotate-left"></i>

                <span>Activity Log</span>

            </a>


            <p class="nav-title">
                SYSTEM
            </p>


            <a
                href="settings.php"
                class="nav-item"
            >

                <i class="fa-solid fa-gear"></i>

                <span>Settings</span>

            </a>


        </nav>


        <div class="sidebar-bottom">


            <div class="admin-mini-profile">

                <div class="mini-avatar">
                    <?php echo htmlspecialchars($firstLetter); ?>
                </div>


                <div>

                    <strong>
                        <?php echo htmlspecialchars($adminName); ?>
                    </strong>

                    <small>
                        Super Admin
                    </small>

                </div>

            </div>


            <button
                class="sidebar-logout"
                id="logoutBtn"
            >

                <i class="fa-solid fa-right-from-bracket"></i>

                Logout

            </button>


        </div>


    </aside>



    <!-- ================= MAIN ================= -->

    <main class="main-content">


        <!-- HEADER -->

        <header class="top-header">


            <div class="header-left">


                <button
                    class="mobile-menu-btn"
                    id="mobileMenuBtn"
                >

                    <i class="fa-solid fa-bars"></i>

                </button>


                <div class="page-heading">

                    <h1>
                        Students
                    </h1>

                    <p>
                        Manage MathsWorld students
                    </p>

                </div>


            </div>



            <div class="header-right">


                <button
                    class="header-icon-btn"
                    id="notificationBtn"
                >

                    <i class="fa-regular fa-bell"></i>

                    <span class="notification-dot"></span>

                </button>


                <div
                    class="header-profile"
                    id="profileBtn"
                >


                    <div class="profile-avatar">

                        <?php echo htmlspecialchars($firstLetter); ?>

                    </div>


                    <div class="profile-info">

                        <strong>
                            <?php echo htmlspecialchars($adminName); ?>
                        </strong>

                        <span>
                            Super Admin
                        </span>

                    </div>


                    <i class="fa-solid fa-chevron-down profile-arrow"></i>


                </div>


            </div>


        </header>



        <!-- ================= CONTENT ================= -->

        <section class="page-content">


            <div class="page-top">


                <div>

                    <h2>
                        Student Management
                    </h2>

                    <p>
                        View, add, edit and manage student accounts.
                    </p>

                </div>


                <button
                    type="button"
                    id="addStudentBtn"
                    class="add-student-btn"
                >
                    <i class="fas fa-plus"></i>
                    Add Student
                </button>


            </div>



            <!-- ================= STATS ================= -->

            <div class="stats-grid">


                <div class="stat-card">

                    <div class="stat-icon blue">

                        <i class="fa-solid fa-user-graduate"></i>

                    </div>


                    <div class="stat-info">

                        <span>
                            Total Students
                        </span>

                        <h3 id="totalStudents">
                            0
                        </h3>

                    </div>

                </div>



                <div class="stat-card">

                    <div class="stat-icon green">

                        <i class="fa-solid fa-user-check"></i>

                    </div>


                    <div class="stat-info">

                        <span>
                            Active Students
                        </span>

                        <h3 id="activeStudents">
                            0
                        </h3>

                    </div>

                </div>



                <div class="stat-card">

                    <div class="stat-icon orange">

                        <i class="fa-solid fa-user-clock"></i>

                    </div>


                    <div class="stat-info">

                        <span>
                            Pending
                        </span>

                        <h3 id="pendingStudents">
                            0
                        </h3>

                    </div>

                </div>



                <div class="stat-card">

                    <div class="stat-icon purple">

                        <i class="fa-solid fa-user-plus"></i>

                    </div>


                    <div class="stat-info">

                        <span>
                            New This Month
                        </span>

                        <h3 id="newStudents">
                            0
                        </h3>

                    </div>

                </div>


            </div>



            <!-- ================= TABLE ================= -->

            <div class="content-card">


                <div class="student-toolbar">


                    <div class="search-box">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input
                            type="text"
                            id="studentSearch"
                            placeholder="Search by name, email or phone..."
                        >

                    </div>


                    <div class="filter-group">


                        <select id="gradeFilter">

                            <option value="all">
                                All Grades
                            </option>

                            <option value="Grade 6">
                                Grade 6
                            </option>

                            <option value="Grade 7">
                                Grade 7
                            </option>

                            <option value="Grade 8">
                                Grade 8
                            </option>

                            <option value="Grade 9">
                                Grade 9
                            </option>

                            <option value="Grade 10">
                                Grade 10
                            </option>

                            <option value="Grade 11">
                                Grade 11
                            </option>

                            <option value="A/L">
                                A/L
                            </option>

                        </select>


                        <select id="statusFilter">

                            <option value="all">
                                All Status
                            </option>

                            <option value="Active">
                                Active
                            </option>

                            <option value="Pending">
                                Pending
                            </option>

                            <option value="Suspended">
                                Suspended
                            </option>

                        </select>


                    </div>


                </div>



                <div class="table-info">


                    <span id="resultCount">
                        Showing 0 students
                    </span>


                    <button
                        class="clear-filter-btn"
                        id="clearFilters"
                    >

                        <i class="fa-solid fa-filter-circle-xmark"></i>

                        Clear Filters

                    </button>


                </div>



                <div class="table-wrapper">


                    <table class="students-table">


                        <thead>

                            <tr>

                                <th>
                                    <input
                                        type="checkbox"
                                        id="selectAll"
                                    >
                                </th>

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
                                    Phone
                                </th>

                                <th>
                                    Registered
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody id="studentsTableBody">

                        </tbody>


                    </table>


                    <div
                        class="empty-state"
                        id="emptyState"
                    >

                        <div class="empty-icon">

                            <i class="fa-solid fa-user-graduate"></i>

                        </div>


                        <h3>
                            No Students Found
                        </h3>


                        <p>
                            Try changing your search or filter options.
                        </p>


                    </div>


                </div>



                <div class="pagination-container">

                    <span id="paginationInfo">
                        Page 1
                    </span>

                    <div
                        class="pagination"
                        id="pagination"
                    ></div>

                </div>


            </div>


        </section>


    </main>


</div>



<!-- ================= OVERLAY ================= -->

<div
    class="sidebar-overlay"
    id="sidebarOverlay"
></div>



<!-- ================= VIEW MODAL ================= -->

<div
    class="modal-overlay"
    id="viewStudentModal"
>


    <div class="modal">


        <div class="modal-header">


            <div>

                <h3>
                    Student Profile
                </h3>

                <p>
                    Student account information
                </p>

            </div>


            <button
                class="modal-close"
                data-close="viewStudentModal"
            >

                <i class="fa-solid fa-xmark"></i>

            </button>


        </div>



        <div class="student-profile">


            <div
                class="large-avatar"
                id="viewAvatar"
            >
                A
            </div>


            <h2 id="viewName">
                Student Name
            </h2>


            <span
                class="profile-status"
                id="viewStatus"
            >
                Active
            </span>


        </div>



        <div class="profile-details">


            <div class="detail-item">

                <span>
                    Email
                </span>

                <strong id="viewEmail">
                    -
                </strong>

            </div>


            <div class="detail-item">

                <span>
                    Grade
                </span>

                <strong id="viewGrade">
                    -
                </strong>

            </div>


            <div class="detail-item">

                <span>
                    Phone
                </span>

                <strong id="viewPhone">
                    -
                </strong>

            </div>


            <div class="detail-item">

                <span>
                    Registered
                </span>

                <strong id="viewRegistered">
                    -
                </strong>

            </div>


        </div>



        <div class="modal-footer">


            <button
                class="secondary-btn"
                data-close="viewStudentModal"
            >
                Close
            </button>


            <button
                class="primary-btn"
                id="viewEditBtn"
            >

                <i class="fa-solid fa-pen"></i>

                Edit Student

            </button>


        </div>


    </div>


</div>



<!-- ================= ADD / EDIT ================= -->

<div
    class="modal-overlay"
    id="studentFormModal"
>


    <div class="modal form-modal">


        <div class="modal-header">


            <div>

                <h3 id="formModalTitle">
                    Add Student
                </h3>

                <p>
                    Create a MathsWorld student account
                </p>

            </div>


            <button
                class="modal-close"
                data-close="studentFormModal"
            >

                <i class="fa-solid fa-xmark"></i>

            </button>


        </div>



        <form id="studentForm">


            <input
                type="hidden"
                id="studentId"
            >


            <div class="form-grid">


                <div class="form-group">

                    <label for="studentName">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="studentName"
                        placeholder="Enter full name"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="studentEmail">
                        Email
                    </label>

                    <input
                        type="email"
                        id="studentEmail"
                        placeholder="student@example.com"
                        required
                    >

                </div>


                <div class="form-group">
                    <label for="studentPassword">Password</label>

                    <input
                        type="password"
                        id="studentPassword"
                        name="password"
                        placeholder="Enter password"
                        minlength="6"
                    >

                    <small>
                        Leave blank when editing to keep the current password.
                    </small>
                </div>


                <div class="form-group">

                    <label for="studentPhone">
                        Phone Number
                    </label>

                    <input
                        type="tel"
                        id="studentPhone"
                        placeholder="0771234567"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="studentStatus">
                        Status
                    </label>

                    <select id="studentStatus">

                        <option value="Active">
                            Active
                        </option>

                        <option value="Pending">
                            Pending
                        </option>

                        <option value="Suspended">
                            Suspended
                        </option>

                    </select>

                </div>


            </div>


            <div class="modal-footer">


                <button
                    type="button"
                    class="secondary-btn"
                    data-close="studentFormModal"
                >
                    Cancel
                </button>


                <<button type="submit" class="btn btn-primary">
                    Save Student
                </button>


            </div>


        </form>


    </div>


</div>



<!-- ================= DELETE ================= -->

<div
    class="modal-overlay"
    id="deleteModal"
>


    <div class="modal small-modal">


        <div class="delete-icon">

            <i class="fa-solid fa-trash"></i>

        </div>


        <h3>
            Delete Student?
        </h3>


        <p>

            Are you sure you want to delete

            <strong id="deleteStudentName">
                this student
            </strong>?

            This action cannot be undone.

        </p>


        <div class="modal-footer">


            <button
                class="secondary-btn"
                data-close="deleteModal"
            >
                Cancel
            </button>


            <button
                class="danger-btn"
                id="confirmDelete"
            >

                <i class="fa-solid fa-trash"></i>

                Delete

            </button>


        </div>


    </div>


</div>



<!-- ================= LOGOUT ================= -->

<div
    class="modal-overlay"
    id="logoutModal"
>


    <div class="modal small-modal">


        <div class="logout-icon">

            <i class="fa-solid fa-right-from-bracket"></i>

        </div>


        <h3>
            Logout?
        </h3>


        <p>
            Are you sure you want to logout from the admin panel?
        </p>


        <div class="modal-footer">


            <button
                class="secondary-btn"
                data-close="logoutModal"
            >
                Cancel
            </button>


            <button
                class="danger-btn"
                id="confirmLogout"
            >
                Logout
            </button>


        </div>


    </div>


</div>



<!-- ================= TOAST ================= -->

<div
    class="toast"
    id="toast"
>


    <div class="toast-icon">

        <i class="fa-solid fa-check"></i>

    </div>


    <div>

        <strong id="toastTitle">
            Success
        </strong>

        <p id="toastMessage">
            Operation completed.
        </p>

    </div>


    <button id="closeToast">

        <i class="fa-solid fa-xmark"></i>

    </button>


</div>



<script src="../js/student-management.js"></script>

</body>

</html>