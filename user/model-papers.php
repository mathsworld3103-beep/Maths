<?php

require_once "auth-check.php";

$studentName = $_SESSION["full_name"] ?? "Student";
$studentGrade = $_SESSION["grade"] ?? "";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Model Papers | MathsWorld</title>


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


/* ==========================================
   CONTENT
========================================== */

.page-content {
    padding: 30px 35px;
    max-width: 1400px;
    margin: auto;
}


/* ==========================================
   WELCOME CARD
========================================== */

.welcome-card {
    background: linear-gradient(
        135deg,
        var(--navy),
        var(--navy-light)
    );
    color: white;
    padding: 28px 30px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 25px;
}

.welcome-label {
    font-size: 11px;
    letter-spacing: 1.5px;
    color: var(--sky);
    font-weight: 700;
}

.welcome-card h2 {
    margin-top: 7px;
    font-size: 25px;
}

.welcome-card p {
    margin-top: 7px;
    color: #cbd5e1;
    font-size: 13px;
}

.welcome-icon {
    width: 70px;
    height: 70px;
    border-radius: 18px;
    background: rgba(56,189,248,0.15);
    color: var(--sky);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
}


/* ==========================================
   FILTERS
========================================== */

.filter-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 15px;
    padding: 18px;
    display: flex;
    gap: 15px;
    align-items: end;
    margin-bottom: 25px;
}

.search-box {
    flex: 1;
    position: relative;
}

.search-box i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
}

.search-box input,
.filter-group select {
    width: 100%;
    height: 45px;
    border: 1px solid var(--border);
    border-radius: 9px;
    outline: none;
    padding: 0 14px;
    background: white;
    color: var(--text);
}

.search-box input {
    padding-left: 40px;
}

.search-box input:focus,
.filter-group select:focus {
    border-color: var(--sky);
}

.filter-group {
    width: 220px;
}

.filter-group label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: var(--muted);
    margin-bottom: 6px;
}

.reset-button {
    height: 45px;
    padding: 0 18px;
    border: none;
    border-radius: 9px;
    background: var(--sky-light);
    color: var(--navy);
    cursor: pointer;
    font-weight: 600;
}

.reset-button:hover {
    background: #bae6fd;
}


/* ==========================================
   RESULT HEADER
========================================== */

.result-header {
    margin-bottom: 15px;
}

.result-header h2 {
    font-size: 18px;
    color: var(--navy);
}

.result-header p {
    font-size: 12px;
    color: var(--muted);
    margin-top: 4px;
}


/* ==========================================
   GRID
========================================== */

.papers-grid {
    display: grid;
    grid-template-columns: repeat(
        auto-fill,
        minmax(280px, 1fr)
    );
    gap: 20px;
}


/* ==========================================
   MODEL PAPER CARD
========================================== */

.paper-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 15px;
    padding: 20px;
    transition: 0.2s;
}

.paper-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(15,23,42,0.08);
}


/* PDF ICON */

.paper-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #fee2e2;
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 15px;
}


/* TITLE */

.paper-card h3 {
    font-size: 16px;
    color: var(--navy);
    line-height: 1.4;
}


/* SUBJECT */

.paper-subject {
    display: block;
    color: var(--sky);
    font-size: 11px;
    font-weight: 700;
    margin-top: 5px;
}


/* DESCRIPTION */

.paper-description {
    color: var(--muted);
    font-size: 12px;
    line-height: 1.6;
    margin-top: 8px;
    min-height: 38px;
}


/* META */

.paper-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
    margin-top: 15px;
}

.paper-meta span {
    background: #f1f5f9;
    color: #475569;
    padding: 5px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 600;
}


/* ACTIONS */

.paper-actions {
    display: flex;
    gap: 8px;
    margin-top: 18px;
}

.paper-actions a {
    flex: 1;
    height: 38px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 700;
}

.view-button {
    background: var(--sky-light);
    color: var(--navy);
}

.view-button:hover {
    background: #bae6fd;
}

.download-button {
    background: var(--navy);
    color: white;
}

.download-button:hover {
    background: var(--navy-light);
}


/* ==========================================
   STATES
========================================== */

.state-box {
    background: white;
    border: 1px solid var(--border);
    border-radius: 15px;
    padding: 50px 20px;
    text-align: center;
    color: var(--muted);
}

.state-box i {
    font-size: 35px;
    margin-bottom: 15px;
    color: var(--sky);
}

.state-box h3 {
    color: var(--navy);
    margin-bottom: 7px;
}

.state-box p {
    font-size: 13px;
}

.error-state i {
    color: var(--danger);
}

.error-state button {
    margin-top: 15px;
    border: none;
    background: var(--navy);
    color: white;
    padding: 10px 18px;
    border-radius: 8px;
    cursor: pointer;
}


/* ==========================================
   LOADING
========================================== */

.spinner {
    width: 35px;
    height: 35px;
    border: 3px solid #e2e8f0;
    border-top-color: var(--sky);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 15px;
}

@keyframes spin {

    to {
        transform: rotate(360deg);
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



    <!-- ==========================================
         MAIN CONTENT
    =========================================== -->

    <main class="main-content">


        <!-- ======================================
             TOP HEADER
        ======================================= -->

        <header class="top-header">
            <div>

                <h1>Model Papers</h1>

                <p>
                    Practice with model examination papers
                </p>

            </div>

            <div class="student-profile">

            <div class="profile-avatar">
                <?php
                echo strtoupper(
                    substr($studentName, 0, 1)
                );
                ?>
            </div>

            <div class="profile-info">

                <strong>
                    <?php
                    echo htmlspecialchars($studentName);
                    ?>
                </strong>

                <span>
                    <?php
                    echo htmlspecialchars($studentGrade);
                    ?>
                </span>

            </div>

            </div>
            <button
                type="button"
                class="menu-button"
                id="menuButton"
            >

                <i class="fa-solid fa-bars"></i>

            </button>

        </header>



        <!-- ======================================
             PAGE CONTENT
        ======================================= -->

        <section class="page-content">


            <!-- ==================================
                 WELCOME CARD
            =================================== -->

            <div class="welcome-card">


                <div>

                    <span class="welcome-label">
                        YOUR MODEL PAPERS
                    </span>


                    <h2>

                        <?php

                        echo htmlspecialchars(
                            $studentGrade
                        );

                        ?>

                        Model Papers

                    </h2>


                    <p>

                        Find model examination papers
                        and improve your preparation.

                    </p>

                </div>


                <div class="welcome-icon">

                    <i class="fa-solid fa-file-circle-check"></i>

                </div>

            </div>



            <!-- ==================================
                 FILTER SECTION
            =================================== -->

            <div class="filter-card">


                <!-- Search -->

                <div class="search-box">

                    <i class="fa-solid fa-magnifying-glass"></i>


                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Search model papers..."
                        autocomplete="off"
                    >

                </div>



                <!-- Subject -->

                <div class="filter-group">

                    <label for="subjectFilter">

                        Subject

                    </label>


                    <select id="subjectFilter">


                        <option value="">
                            All Subjects
                        </option>


                        <option value="Mathematics">
                            Mathematics
                        </option>


                        <option value="Combined Mathematics">
                            Combined Mathematics
                        </option>


                        <option value="Physics">
                            Physics
                        </option>


                        <option value="Chemistry">
                            Chemistry
                        </option>


                        <option value="Biology">
                            Biology
                        </option>


                    </select>

                </div>



                <!-- Reset -->

                <button
                    type="button"
                    id="resetFilters"
                    class="reset-button"
                >

                    <i class="fa-solid fa-rotate-left"></i>

                    Reset

                </button>

            </div>



            <!-- ==================================
                 RESULT HEADER
            =================================== -->

            <div class="result-header">

                <div>

                    <h2>
                        Available Model Papers
                    </h2>


                    <p id="resultText">

                        Loading model papers...

                    </p>

                </div>

            </div>



            <!-- ==================================
                 LOADING
            =================================== -->

            <div
                id="loadingState"
                class="state-box"
            >

                <div class="spinner"></div>


                <p>
                    Loading model papers...
                </p>

            </div>



            <!-- ==================================
                 ERROR
            =================================== -->

            <div
                id="errorState"
                class="state-box error-state"
                style="display:none;"
            >


                <i class="fa-solid fa-circle-exclamation"></i>


                <h3>
                    Something went wrong
                </h3>


                <p id="errorMessage">

                    Unable to load model papers.

                </p>


                <button
                    type="button"
                    id="retryButton"
                >

                    Try Again

                </button>

            </div>



            <!-- ==================================
                 EMPTY
            =================================== -->

            <div
                id="emptyState"
                class="state-box"
                style="display:none;"
            >


                <i class="fa-solid fa-folder-open"></i>


                <h3>
                    No Model Papers Found
                </h3>


                <p>

                    There are currently no model papers
                    matching your search.

                </p>

            </div>



            <!-- ==================================
                 PAPERS GRID
            =================================== -->

            <div
                id="papersGrid"
                class="papers-grid"
            ></div>


        </section>

    </main>

</div>



<!-- JavaScript -->

<script
    src="../js/student-model-papers.js"
></script>


</body>

</html>