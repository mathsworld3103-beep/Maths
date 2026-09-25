<?php

require_once "auth-check.php";

$userId = (int) $_SESSION["user_id"];

$message = "";
$messageType = "";


// ==========================================
// LOAD USER
// ==========================================

$stmt = $conn->prepare("
    SELECT
        full_name,
        email,
        phone,
        grade
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: ../login.html");
    exit;
}

$user = $result->fetch_assoc();

$stmt->close();


// ==========================================
// UPDATE PROFILE
// ==========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullName = trim($_POST["full_name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $grade = trim($_POST["grade"] ?? "");


    // Validation

    if ($fullName === "") {

        $message = "Please enter your full name.";
        $messageType = "error";

    } elseif ($phone === "") {

        $message = "Please enter your phone number.";
        $messageType = "error";

    } elseif ($grade === "") {

        $message = "Please select your grade.";
        $messageType = "error";

    } else {


        // ==================================
        // UPDATE DATABASE
        // ==================================

        $stmt = $conn->prepare("
            UPDATE users
            SET
                full_name = ?,
                phone = ?,
                grade = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "sssi",
            $fullName,
            $phone,
            $grade,
            $userId
        );


        if ($stmt->execute()) {

    $_SESSION["full_name"] = $fullName;
    $_SESSION["phone"] = $phone;
    $_SESSION["grade"] = $grade;

    header("Location: profile.php");
    exit;


        } else {

            $message =
                "Unable to update your profile.";

            $messageType = "error";

        }

        $stmt->close();

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

    <title>Edit Profile | MathsWorld</title>


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


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="../css/student-profile.css"
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

        .edit-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 15px;
            padding: 28px;
            max-width: 800px;
            margin: auto;
        }

        .edit-header {
            margin-bottom: 25px;
        }

        .edit-header h2 {
            color: var(--navy);
            font-size: 20px;
        }

        .edit-header p {
            color: var(--muted);
            font-size: 12px;
            margin-top: 5px;
        }

        .message {
            padding: 12px 15px;
            border-radius: 9px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .message.success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            color: var(--navy);
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            height: 45px;
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 0 14px 0 40px;
            outline: none;
            color: var(--text);
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--sky);
        }

        .form-group input[readonly] {
            background: #f8fafc;
            color: var(--muted);
            cursor: not-allowed;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .save-button,
        .cancel-button {
            height: 44px;
            padding: 0 20px;
            border-radius: 9px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }

        .save-button {
            border: none;
            background: var(--navy);
            color: white;
        }

        .save-button:hover {
            background: var(--navy-light);
        }

        .cancel-button {
            border: 1px solid var(--border);
            background: white;
            color: var(--navy);
        }

        .cancel-button:hover {
            background: var(--sky-light);
        }

        @media (max-width: 600px) {

            .edit-card {
                padding: 20px;
            }

            .form-actions {
                flex-direction: column;
            }

            .save-button,
            .cancel-button {
                width: 100%;
            }

        }

    </style>

</head>


<body>



    <!-- ==========================================
         SIDEBAR
    =========================================== -->

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



    <!-- ==========================================
         MAIN
    =========================================== -->

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

                <h1>Edit Profile</h1>

                <p>
                    Update your student information
                </p>

            </div>

        </header>



        <!-- CONTENT -->

        <section class="page-content">


            <div class="edit-card">


                <div class="edit-header">

                    <h2>
                        Personal Information
                    </h2>

                    <p>
                        Update the information associated
                        with your MathsWorld account.
                    </p>

                </div>


                <?php if ($message !== ""): ?>

                    <div
                        class="message
                        <?php echo $messageType; ?>"
                    >

                        <?php
                        echo htmlspecialchars($message);
                        ?>

                    </div>

                <?php endif; ?>



                <form
                    method="POST"
                    action=""
                >


                    <!-- FULL NAME -->

                    <div class="form-group">

                        <label for="full_name">

                            Full Name

                        </label>


                        <div class="input-wrapper">

                            <i class="fa-solid fa-user"></i>


                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                value="<?php
                                    echo htmlspecialchars(
                                        $user["full_name"]
                                    );
                                ?>"
                                maxlength="100"
                                required
                            >

                        </div>

                    </div>



                    <!-- EMAIL -->

                    <div class="form-group">

                        <label for="email">

                            Email Address

                        </label>


                        <div class="input-wrapper">

                            <i class="fa-solid fa-envelope"></i>


                            <input
                                type="email"
                                id="email"
                                value="<?php
                                    echo htmlspecialchars(
                                        $user["email"]
                                    );
                                ?>"
                                readonly
                            >

                        </div>

                    </div>



                    <!-- PHONE -->

                    <div class="form-group">

                        <label for="phone">

                            Phone Number

                        </label>


                        <div class="input-wrapper">

                            <i class="fa-solid fa-phone"></i>


                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="<?php
                                    echo htmlspecialchars(
                                        $user["phone"]
                                    );
                                ?>"
                                maxlength="20"
                                required
                            >

                        </div>

                    </div>



                    <!-- GRADE -->

                    <div class="form-group">

                        <label for="grade">

                            Grade

                        </label>


                        <div class="input-wrapper">

                            <i class="fa-solid fa-graduation-cap"></i>


                            <select
                                id="grade"
                                name="grade"
                                required
                            >

                                <option value="">
                                    Select Grade
                                </option>


                                <option
                                    value="Grade 6"
                                    <?php
                                    echo $user["grade"] === "Grade 6"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Grade 6
                                </option>


                                <option
                                    value="Grade 7"
                                    <?php
                                    echo $user["grade"] === "Grade 7"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Grade 7
                                </option>


                                <option
                                    value="Grade 8"
                                    <?php
                                    echo $user["grade"] === "Grade 8"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Grade 8
                                </option>


                                <option
                                    value="Grade 9"
                                    <?php
                                    echo $user["grade"] === "Grade 9"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Grade 9
                                </option>


                                <option
                                    value="Grade 10"
                                    <?php
                                    echo $user["grade"] === "Grade 10"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Grade 10
                                </option>


                                <option
                                    value="Grade 11"
                                    <?php
                                    echo $user["grade"] === "Grade 11"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Grade 11
                                </option>


                                <option
                                    value="Grade 12"
                                    <?php
                                    echo $user["grade"] === "Grade 12"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Grade 12
                                </option>


                                <option
                                    value="Grade 13"
                                    <?php
                                    echo $user["grade"] === "Grade 13"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Grade 13
                                </option>

                            </select>

                        </div>

                    </div>



                    <!-- BUTTONS -->

                    <div class="form-actions">


                        <button
                            type="submit"
                            class="save-button"
                        >

                            <i class="fa-solid fa-floppy-disk"></i>

                            Save Changes

                        </button>


                        <a
                            href="profile.php"
                            class="cancel-button"
                        >

                            <i class="fa-solid fa-arrow-left"></i>

                            Cancel

                        </a>


                    </div>

                </form>

            </div>

        </section>

    </main>





<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

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