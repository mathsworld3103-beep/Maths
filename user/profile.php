<?php



require_once "auth-check.php";


$userId = (int) $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT
        id,
        full_name,
        email,
        phone,
        grade,
        role,
        created_at
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    session_destroy();

    header("Location: ../login.html");
    exit;
}

$user = $result->fetch_assoc();

$stmt->close();

$fullName = $user["full_name"];
$email = $user["email"];
$phone = $user["phone"];
$grade = $user["grade"];
$role = $user["role"];
$createdAt = $user["created_at"];

$initial = strtoupper(
    substr($fullName, 0, 1)
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

    <title>My Profile | MathsWorld</title>

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
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

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

        .student-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #ffffff;
            padding: 9px 14px;
            border-radius: 12px;
            border: 1px solid #e5eaf0;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            background: #168de2;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
        }

        .profile-info strong {
            font-size: 13px;
        }

        .profile-info span {
            color: #98a2b3;
            font-size: 11px;
            margin-top: 3px;
        }



/* ==========================================
   MAIN
========================================== */

.main-content {
    margin-left: 250px;
    width: calc(100% - 250px);
    padding: 35px;
}


/* ==========================================
   HEADER
========================================== */

.top-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 25px;
    margin-bottom: 28px;
}

.top-header h1 {
    font-size: 24px;
    color: var(--navy);
}

.top-header p {
    font-size: 13px;
    color: var(--muted);
    margin-top: 4px;
}

.menu-button {
    display: none;
    border: none;
    background: var(--sky-light);
    color: var(--navy);
    width: 40px;
    height: 40px;
    border-radius: 9px;
    cursor: pointer;
}


        

.page-content {
    padding: 30px 35px;
    max-width: 1200px;
    margin: auto;
}


/* PROFILE HERO */

.profile-card {
    background: linear-gradient(
        135deg,
        var(--navy),
        var(--navy-light)
    );
    color: white;
    border-radius: 18px;
    padding: 30px;
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 25px;
}

.profile-large-avatar {
    width: 90px;
    height: 90px;
    flex-shrink: 0;
    border-radius: 50%;
    background: var(--sky);
    color: var(--navy);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 34px;
    font-weight: 800;
}

.profile-label {
    color: var(--sky);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.5px;
}

.profile-main-info h2 {
    font-size: 26px;
    margin-top: 5px;
}

.profile-main-info p {
    color: #cbd5e1;
    font-size: 13px;
    margin-top: 5px;
}

.profile-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 14px;
}

.profile-badges span {
    background: rgba(255,255,255,.1);
    padding: 7px 10px;
    border-radius: 7px;
    font-size: 11px;
    color: #e2e8f0;
}

.profile-badges i {
    color: var(--sky);
    margin-right: 4px;
}


/* SECTION */

.section-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 15px;
    padding: 24px;
    margin-bottom: 20px;
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 18px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 20px;
}

.section-header h2 {
    color: var(--navy);
    font-size: 18px;
}

.section-header p {
    color: var(--muted);
    font-size: 12px;
    margin-top: 4px;
}

.section-header > i {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: var(--sky-light);
    color: var(--navy);
    display: flex;
    align-items: center;
    justify-content: center;
}


/* INFORMATION */

.profile-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.info-item {
    padding: 16px;
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: 10px;
}

.info-item span {
    display: block;
    color: var(--muted);
    font-size: 11px;
    margin-bottom: 6px;
}

.info-item strong {
    display: block;
    color: var(--navy);
    font-size: 13px;
    word-break: break-word;
}


/* ACTIONS */

.account-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.action-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 15px;
    border: 1px solid var(--border);
    border-radius: 11px;
    transition: .2s;
}

.action-card:hover {
    border-color: var(--sky);
    background: #f8fcff;
    transform: translateX(2px);
}

.action-icon {
    width: 42px;
    height: 42px;
    border-radius: 9px;
    background: var(--sky-light);
    color: var(--navy);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.action-card h3 {
    font-size: 13px;
    color: var(--navy);
}

.action-card p {
    font-size: 11px;
    color: var(--muted);
    margin-top: 3px;
}

.action-card .arrow {
    margin-left: auto;
    color: #94a3b8;
    font-size: 12px;
}

.logout-card .action-icon {
    background: #fee2e2;
    color: var(--danger);
}

.logout-card h3 {
    color: var(--danger);
}

.success-message {
    display: flex;
    align-items: center;
    gap: 10px;

    margin-bottom: 20px;
    padding: 14px 18px;

    background: #dcfce7;
    color: #166534;

    border: 1px solid #bbf7d0;
    border-radius: 10px;

    font-size: 14px;
    font-weight: 600;
}

.success-message i {
    font-size: 18px;
}


/* RESPONSIVE */

@media (max-width: 900px) {

    .sidebar {
        transform: translateX(-100%);
        transition: .3s;
    }

    .sidebar.show {
        transform: translateX(0);
    }

    .main-content {
        margin-left: 0;
        width: 100%;
    }

    .menu-button {
        display: block;
    }

    .page-content {
        padding: 25px 20px;
    }

}

@media (max-width: 600px) {

    .top-header {
        height: 72px;
        padding: 0 18px;
    }

    .top-header h1 {
        font-size: 20px;
    }

    .top-header p {
        font-size: 11px;
    }

    .page-content {
        padding: 18px 14px;
    }

    .profile-card {
        padding: 22px;
        align-items: flex-start;
    }

    .profile-large-avatar {
        width: 65px;
        height: 65px;
        font-size: 25px;
    }

    .profile-main-info h2 {
        font-size: 20px;
    }

    .profile-main-info p {
        font-size: 11px;
    }

    .profile-grid {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 400px) {

    .profile-card {
        flex-direction: column;
    }

}
    </style>

</head>

<body>


    <!-- SIDEBAR -->

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



    <!-- MAIN -->

    <main class="main-content">


        <!-- HEADER -->

        <header class="top-header">

            <button
                type="button"
                class="menu-button"
                id="menuButton"
            >

                <i class="fa-solid fa-bars"></i>

            </button>


            <div>

                <h1>My Profile</h1>

                <p>
                    Manage your MathsWorld student account
                </p>

            </div>

        </header>



        <!-- CONTENT -->

        <section class="page-content">

        <?php if (
            isset($_GET["updated"]) &&
            $_GET["updated"] === "1"
        ): ?>

            <div class="success-message">
                <i class="fa-solid fa-circle-check"></i>
                Profile updated successfully!
            </div>

        <?php endif; ?>


        <?php if (
            isset($_GET["password_changed"]) &&
            $_GET["password_changed"] === "1"
        ): ?>

            <div class="success-message">
                <i class="fa-solid fa-circle-check"></i>
                Password changed successfully!
            </div>

        <?php endif; ?>


            <!-- PROFILE HERO -->

            <div class="profile-card">

                <div class="profile-large-avatar">

                    <?php
                    echo htmlspecialchars($initial);
                    ?>

                </div>


                <div class="profile-main-info">

                    <span class="profile-label">
                        STUDENT ACCOUNT
                    </span>

                    <h2>
                        <?php
                        echo htmlspecialchars($fullName);
                        ?>
                    </h2>

                    <p>
                        <?php
                        echo htmlspecialchars($email);
                        ?>
                    </p>

                    <div class="profile-badges">

                        <span>
                            <i class="fa-solid fa-graduation-cap"></i>

                            <?php
                            echo htmlspecialchars($grade);
                            ?>
                        </span>

                        <span>
                            <i class="fa-solid fa-user-graduate"></i>

                            Student
                        </span>

                    </div>

                </div>

            </div>



            <!-- INFORMATION -->

            <div class="section-card">

                <div class="section-header">

                    <div>

                        <h2>
                            Personal Information
                        </h2>

                        <p>
                            Your registered account information
                        </p>

                    </div>

                    <i class="fa-solid fa-id-card"></i>

                </div>


                <div class="profile-grid">


                    <div class="info-item">

                        <span>
                            Full Name
                        </span>

                        <strong>
                            <?php
                            echo htmlspecialchars($fullName);
                            ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span>
                            Email Address
                        </span>

                        <strong>
                            <?php
                            echo htmlspecialchars($email);
                            ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span>
                            Phone Number
                        </span>

                        <strong>
                            <?php
                            echo htmlspecialchars($phone);
                            ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span>
                            Grade
                        </span>

                        <strong>
                            <?php
                            echo htmlspecialchars($grade);
                            ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span>
                            Account Type
                        </span>

                        <strong>
                            <?php
                            echo ucfirst(
                                htmlspecialchars($role)
                            );
                            ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span>
                            Member Since
                        </span>

                        <strong>

                            <?php

                            echo date(
                                "d M Y",
                                strtotime($createdAt)
                            );

                            ?>

                        </strong>

                    </div>

                </div>

            </div>



            <!-- ACCOUNT ACTIONS -->

            <div class="section-card">

                <div class="section-header">

                    <div>

                        <h2>
                            Account Settings
                        </h2>

                        <p>
                            Manage your account
                        </p>

                    </div>

                    <i class="fa-solid fa-gear"></i>

                </div>


                <div class="account-actions">


                    <a href="edit-profile.php" class="action-card">
                        <i class="fa-solid fa-user-pen"></i>

                        <div>
                            <h3>Edit Profile</h3>
                            <p>Update your personal information</p>
                        </div>
                    </a>


                    <a href="change-password.php" class="action-card">
                        <i class="fa-solid fa-lock"></i>

                        <div>
                            <h3>Change Password</h3>
                            <p>Update your account password</p>
                        </div>
                    </a>


                    <a href="../logout.php" class="action-card">
                        <i class="fa-solid fa-right-from-bracket"></i>

                        <div>
                            <h3>Logout</h3>
                            <p>Sign out of your account</p>
                        </div>
                    </a>

                </div>

            </div>

        </section>

    </main>

</div>


<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const menuButton =
            document.getElementById("menuButton");

        const sidebar =
            document.querySelector(".sidebar");

        if (menuButton && sidebar) {

            menuButton.addEventListener(
                "click",
                function () {

                    sidebar.classList.toggle("show");

                }
            );

        }

    }
);

</script>

</body>

</html>