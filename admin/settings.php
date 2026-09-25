<?php

require_once "auth-check.php";

$adminName = $_SESSION["full_name"] ?? "Administrator";

/* ==========================================
   ADMIN ACCESS CHECK
========================================== */

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: ../user/dashboard.php");
    exit;
}


/* ==========================================
   GET WEBSITE SETTINGS
========================================== */

$settings = [
    "site_name" => "MathsWorld",
    "site_email" => "",
    "site_phone" => "",
    "site_description" => "",
    "maintenance_mode" => 0,
    "allow_registration" => 1
];

$stmt = $conn->prepare("
    SELECT
        site_name,
        site_email,
        site_phone,
        site_description,
        maintenance_mode,
        allow_registration
    FROM site_settings
    WHERE id = 1
    LIMIT 1
");

if ($stmt) {

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $settings = $result->fetch_assoc();
    }

    $stmt->close();
}


/* ==========================================
   SUCCESS / ERROR MESSAGE
========================================== */

$successMessage = "";
$errorMessage = "";

if (isset($_GET["success"])) {

    if ($_GET["success"] === "website") {

        $successMessage =
            "Website settings saved successfully.";

    }

    if ($_GET["success"] === "system") {

        $successMessage =
            "System settings saved successfully.";

    }
}

if (isset($_GET["error"])) {

    if ($_GET["error"] === "site_name") {

        $errorMessage =
            "Website name is required.";

    }
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

    <title>
        Settings | MathsWorld Admin
    </title>


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
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    <!-- Font Awesome -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- Settings CSS -->


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

<link
    rel="stylesheet"
    href="css/settings.css"
>

<link rel="stylesheet" href="../css/style.css">

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

        <a
            href="activity-log.php"
            class="sidebar-link"
        >

            <i class="fa-solid fa-clock-rotate-left"></i>

            <span>
                Activity Log
            </span>

        </a>


        <a
            href="settings.php"
            class="sidebar-link"
        >

            <i class="fa-solid fa-gear"></i>

            <span>
                Settings
            </span>

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


<div class="settings-page">


    <!-- =====================================
         PAGE HEADER
    ====================================== -->

    <div class="page-header">

        <div class="page-header-left">

            <span class="page-label">
                ADMINISTRATION
            </span>

            <h1>
                Settings
            </h1>

            <p>
                Manage your MathsWorld website and system settings.
            </p>

        </div>


        <div class="page-header-icon">

            <i class="fa-solid fa-gear"></i>

        </div>

    </div>


    <!-- =====================================
         ALERTS
    ====================================== -->

    <?php if ($successMessage !== ""): ?>

        <div class="alert alert-success">

            <i class="fa-solid fa-circle-check"></i>

            <span>
                <?php
                echo htmlspecialchars(
                    $successMessage
                );
                ?>
            </span>

            <button
                type="button"
                class="alert-close"
                onclick="closeAlert(this)"
            >

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>

    <?php endif; ?>


    <?php if ($errorMessage !== ""): ?>

        <div class="alert alert-error">

            <i class="fa-solid fa-circle-exclamation"></i>

            <span>
                <?php
                echo htmlspecialchars(
                    $errorMessage
                );
                ?>
            </span>

            <button
                type="button"
                class="alert-close"
                onclick="closeAlert(this)"
            >

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>

    <?php endif; ?>


    <!-- =====================================
         SETTINGS GRID
    ====================================== -->

    <div class="settings-grid">


        <!-- =================================
             WEBSITE SETTINGS
        ================================== -->

        <section class="settings-card">


            <div class="settings-card-header">

                <div class="settings-card-icon">

                    <i class="fa-solid fa-globe"></i>

                </div>

                <div>

                    <h2>
                        Website Settings
                    </h2>

                    <p>
                        Manage your website information.
                    </p>

                </div>

            </div>


            <form
                action="settings-api.php"
                method="POST"
                id="websiteSettingsForm"
            >

                <input
                    type="hidden"
                    name="action"
                    value="save_website"
                >


                <!-- Website Name -->

                <div class="form-group">

                    <label for="site_name">

                        Website Name

                        <span>*</span>

                    </label>

                    <div class="input-wrapper">

                        <i class="fa-solid fa-building"></i>

                        <input
                            type="text"
                            id="site_name"
                            name="site_name"
                            value="<?php
                                echo htmlspecialchars(
                                    $settings["site_name"]
                                );
                            ?>"
                            placeholder="Enter website name"
                            maxlength="150"
                            required
                        >

                    </div>

                </div>


                <!-- Email -->

                <div class="form-group">

                    <label for="site_email">
                        Website Email
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-solid fa-envelope"></i>

                        <input
                            type="email"
                            id="site_email"
                            name="site_email"
                            value="<?php
                                echo htmlspecialchars(
                                    $settings["site_email"]
                                );
                            ?>"
                            placeholder="info@example.com"
                            maxlength="150"
                        >

                    </div>

                </div>


                <!-- Phone -->

                <div class="form-group">

                    <label for="site_phone">
                        Phone Number
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-solid fa-phone"></i>

                        <input
                            type="text"
                            id="site_phone"
                            name="site_phone"
                            value="<?php
                                echo htmlspecialchars(
                                    $settings["site_phone"]
                                );
                            ?>"
                            placeholder="0771055842"
                            maxlength="30"
                        >

                    </div>

                </div>


                <!-- Description -->

                <div class="form-group">

                    <label for="site_description">
                        Website Description
                    </label>

                    <textarea
                        id="site_description"
                        name="site_description"
                        rows="5"
                        maxlength="500"
                        placeholder="Enter a short description..."
                    ><?php
                        echo htmlspecialchars(
                            $settings["site_description"]
                        );
                    ?></textarea>


                    <div class="character-count">

                        <span id="descriptionCount">
                            0
                        </span>

                        / 500

                    </div>

                </div>


                <!-- Save -->

                <button
                    type="submit"
                    class="save-button"
                    id="websiteSaveButton"
                >

                    <i class="fa-solid fa-floppy-disk"></i>

                    <span>
                        Save Website Settings
                    </span>

                </button>

            </form>

        </section>


        <!-- =================================
             SYSTEM SETTINGS
        ================================== -->

        <section class="settings-card">


            <div class="settings-card-header">

                <div class="settings-card-icon system-icon">

                    <i class="fa-solid fa-sliders"></i>

                </div>

                <div>

                    <h2>
                        System Settings
                    </h2>

                    <p>
                        Control website behaviour.
                    </p>

                </div>

            </div>


            <form
                action="settings-api.php"
                method="POST"
                id="systemSettingsForm"
            >

                <input
                    type="hidden"
                    name="action"
                    value="save_system"
                >


                <!-- Allow Registration -->

                <div class="setting-option">

                    <div class="setting-option-info">

                        <div class="setting-option-icon">

                            <i class="fa-solid fa-user-plus"></i>

                        </div>

                        <div>

                            <h3>
                                Allow Registration
                            </h3>

                            <p>
                                Allow new students to create accounts.
                            </p>

                        </div>

                    </div>


                    <label class="switch">

                        <input
                            type="checkbox"
                            id="allow_registration"
                            name="allow_registration"
                            value="1"

                            <?php

                            echo $settings[
                                "allow_registration"
                            ]

                                ? "checked"

                                : "";

                            ?>

                        >

                        <span class="slider"></span>

                    </label>

                </div>


                <!-- Maintenance -->

                <div class="setting-option">

                    <div class="setting-option-info">

                        <div class="setting-option-icon warning-icon">

                            <i class="fa-solid fa-screwdriver-wrench"></i>

                        </div>

                        <div>

                            <h3>
                                Maintenance Mode
                            </h3>

                            <p>
                                Temporarily disable student access.
                            </p>

                        </div>

                    </div>


                    <label class="switch">

                        <input
                            type="checkbox"
                            id="maintenance_mode"
                            name="maintenance_mode"
                            value="1"

                            <?php

                            echo $settings[
                                "maintenance_mode"
                            ]

                                ? "checked"

                                : "";

                            ?>

                        >

                        <span class="slider"></span>

                    </label>

                </div>


                <!-- Status -->

                <div
                    class="system-status"
                    id="systemStatus"
                >

                    <div class="status-icon">

                        <i class="fa-solid fa-circle-check"></i>

                    </div>

                    <div>

                        <strong>
                            System Status
                        </strong>

                        <span id="systemStatusText">
                            Website is currently active.
                        </span>

                    </div>

                </div>


                <!-- Save -->

                <button
                    type="submit"
                    class="save-button"
                    id="systemSaveButton"
                >

                    <i class="fa-solid fa-check"></i>

                    <span>
                        Save System Settings
                    </span>

                </button>

            </form>

        </section>


        <!-- =================================
             QUICK INFORMATION
        ================================== -->

        <section class="settings-card settings-info-card">

            <div class="settings-card-header">

                <div class="settings-card-icon">

                    <i class="fa-solid fa-circle-info"></i>

                </div>

                <div>

                    <h2>
                        Settings Information
                    </h2>

                    <p>
                        Important information about these settings.
                    </p>

                </div>

            </div>


            <div class="info-list">


                <div class="info-item">

                    <i class="fa-solid fa-user-check"></i>

                    <div>

                        <strong>
                            Student Registration
                        </strong>

                        <p>
                            When disabled, new students cannot create accounts.
                        </p>

                    </div>

                </div>


                <div class="info-item">

                    <i class="fa-solid fa-screwdriver-wrench"></i>

                    <div>

                        <strong>
                            Maintenance Mode
                        </strong>

                        <p>
                            Use this when performing important website maintenance.
                        </p>

                    </div>

                </div>


                <div class="info-item">

                    <i class="fa-solid fa-clock-rotate-left"></i>

                    <div>

                        <strong>
                            Activity Log
                        </strong>

                        <p>
                            Settings changes are recorded in the admin activity log.
                        </p>

                    </div>

                </div>

            </div>

        </section>


    </div>


</div>


<!-- JavaScript -->

<script src="../js/settings.js"></script>

</body>

</html>