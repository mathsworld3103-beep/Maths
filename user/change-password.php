<?php

require_once "auth-check.php";

$userId = (int) $_SESSION["user_id"];

$successMessage = "";
$errorMessage = "";

// =====================================
// CHANGE PASSWORD
// =====================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $currentPassword = $_POST["current_password"] ?? "";
    $newPassword = $_POST["new_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    // Basic validation
    if (
        $currentPassword === "" ||
        $newPassword === "" ||
        $confirmPassword === ""
    ) {

        $errorMessage = "Please fill in all password fields.";

    } elseif (strlen($newPassword) < 8) {

        $errorMessage = "New password must be at least 8 characters.";

    } elseif ($newPassword !== $confirmPassword) {

        $errorMessage = "New password and confirm password do not match.";

    } else {

        // Get current password
        $stmt = $conn->prepare("
            SELECT password
            FROM users
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows !== 1) {

            $errorMessage = "User account not found.";

        } else {

            $user = $result->fetch_assoc();

            // Verify current password
            if (!password_verify(
                $currentPassword,
                $user["password"]
            )) {

                $errorMessage = "Current password is incorrect.";

            } else {

                // Hash new password
                $hashedPassword = password_hash(
                    $newPassword,
                    PASSWORD_DEFAULT
                );

                // Update password
                $update = $conn->prepare("
                    UPDATE users
                    SET password = ?
                    WHERE id = ?
                ");

                $update->bind_param(
                    "si",
                    $hashedPassword,
                    $userId
                );

                if ($update->execute()) {

                    $update->close();
                    $stmt->close();

                    header("Location: profile.php");
                    exit;

                } else {

                    $errorMessage =
                        "Unable to update password.";
                }

                $update->close();
            }
        }

        $stmt->close();
    }
}

// =====================================
// GET USER DETAILS
// =====================================

$stmt = $conn->prepare("
    SELECT
        full_name,
        email,
        grade
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();

$studentName = $user["full_name"] ?? "Student";
$studentEmail = $user["email"] ?? "";
$studentGrade = $user["grade"] ?? "";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Change Password | MathsWorld</title>

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

        .password-page {
            max-width: 800px;
            margin: 0 auto;
        }

        .password-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.05);
        }

        .password-header {
            margin-bottom: 25px;
        }

        .password-header h2 {
            color: #0b1f3a;
            margin-bottom: 8px;
        }

        .password-header p {
            color: #64748b;
            margin: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1e293b;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        .input-wrapper input {
            width: 100%;
            box-sizing: border-box;
            padding: 13px 45px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
        }

        .input-wrapper input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            left: auto !important;
            cursor: pointer;
        }

        .password-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .save-password {
            border: none;
            background: #0b1f3a;
            color: #ffffff;
            padding: 13px 22px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
        }

        .save-password:hover {
            background: #12345b;
        }

        .cancel-password {
            text-decoration: none;
            background: #f1f5f9;
            color: #334155;
            padding: 13px 22px;
            border-radius: 10px;
            font-weight: 600;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        @media (max-width: 600px) {

            .password-card {
                padding: 20px;
            }

            .password-actions {
                flex-direction: column;
            }

            .save-password,
            .cancel-password {
                text-align: center;
            }

        }

    </style>

</head>

<body>

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


    <!-- MAIN CONTENT -->

    <main class="main-content">

        <header class="top-header">

            <button
                type="button"
                class="menu-button"
                id="menuButton"
            >
                <i class="fa-solid fa-bars"></i>
            </button>

            <div>

                <h1>Change Password</h1>

                <p>
                    Keep your MathsWorld account secure
                </p>

            </div>

        </header>


        <section class="page-content">

            <div class="password-page">

                <div class="password-card">

                    <div class="password-header">

                        <h2>
                            <i class="fa-solid fa-lock"></i>
                            Change Password
                        </h2>

                        <p>
                            Enter your current password and
                            choose a new password.
                        </p>

                    </div>


                    <?php if ($errorMessage !== ""): ?>

                        <div class="alert alert-error">

                            <i class="fa-solid fa-circle-exclamation"></i>

                            <?php
                            echo htmlspecialchars($errorMessage);
                            ?>

                        </div>

                    <?php endif; ?>


                    <form
                        method="POST"
                        action=""
                    >

                        <!-- CURRENT PASSWORD -->

                        <div class="form-group">

                            <label for="current_password">
                                Current Password
                            </label>

                            <div class="input-wrapper">

                                <i class="fa-solid fa-lock"></i>

                                <input
                                    type="password"
                                    id="current_password"
                                    name="current_password"
                                    required
                                >

                                <i
                                    class="fa-solid fa-eye toggle-password"
                                    data-target="current_password"
                                ></i>

                            </div>

                        </div>


                        <!-- NEW PASSWORD -->

                        <div class="form-group">

                            <label for="new_password">
                                New Password
                            </label>

                            <div class="input-wrapper">

                                <i class="fa-solid fa-key"></i>

                                <input
                                    type="password"
                                    id="new_password"
                                    name="new_password"
                                    minlength="8"
                                    required
                                >

                                <i
                                    class="fa-solid fa-eye toggle-password"
                                    data-target="new_password"
                                ></i>

                            </div>

                        </div>


                        <!-- CONFIRM PASSWORD -->

                        <div class="form-group">

                            <label for="confirm_password">
                                Confirm New Password
                            </label>

                            <div class="input-wrapper">

                                <i class="fa-solid fa-key"></i>

                                <input
                                    type="password"
                                    id="confirm_password"
                                    name="confirm_password"
                                    minlength="8"
                                    required
                                >

                                <i
                                    class="fa-solid fa-eye toggle-password"
                                    data-target="confirm_password"
                                ></i>

                            </div>

                        </div>


                        <div class="password-actions">

                            <button
                                type="submit"
                                class="save-password"
                            >
                                <i class="fa-solid fa-shield-halved"></i>
                                Change Password
                            </button>

                            <a
                                href="profile.php"
                                class="cancel-password"
                            >
                                Cancel
                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </section>

    </main>


<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        // Password visibility

        const toggleButtons =
            document.querySelectorAll(".toggle-password");

        toggleButtons.forEach(function (button) {

            button.addEventListener(
                "click",
                function () {

                    const targetId =
                        button.dataset.target;

                    const input =
                        document.getElementById(targetId);

                    if (input.type === "password") {

                        input.type = "text";

                        button.classList.remove(
                            "fa-eye"
                        );

                        button.classList.add(
                            "fa-eye-slash"
                        );

                    } else {

                        input.type = "password";

                        button.classList.remove(
                            "fa-eye-slash"
                        );

                        button.classList.add(
                            "fa-eye"
                        );

                    }

                }
            );

        });


        // Mobile sidebar

        const menuButton =
            document.getElementById("menuButton");

        const sidebar =
            document.getElementById("sidebar");

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