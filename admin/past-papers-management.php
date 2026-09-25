<?php

require_once "auth-check.php";

$adminName = $_SESSION["full_name"] ?? "Administrator";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Past Papers Management | MathsWorld</title>

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

    <link rel="stylesheet"
          href="../css/past-papers-management.css">

</head>

<body>

<div class="admin-layout">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="logo-area">

            <div class="logo-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>

            <div>
                <h2>MathsWorld</h2>
                <span>Admin Panel</span>
            </div>

        </div>


        <nav class="sidebar-menu">

            <a href="dashboard.php">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>

            <a href="student-management.php">
                <i class="fa-solid fa-users"></i>
                <span>Students</span>
            </a>

            <a href="teachers-management.php">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span>Teachers</span>
            </a>

            <a href="subject-management.php">
                <i class="fa-solid fa-book"></i>
                <span>Subjects</span>
            </a>

            <a href="material-management.php">
                <i class="fa-solid fa-file-lines"></i>
                <span>Materials</span>
            </a>

            <a href="past-papers-management.php"
               class="active">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Past Papers</span>
            </a>

            <a href="model-papers-management.php">
                <i class="fa-solid fa-book-open"></i>
                <span>Model Papers</span>
            </a>

            <a href="message-management.php">
                <i class="fa-solid fa-envelope"></i>
                <span>Messages</span>
            </a>

            <a href="activity-log.php">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Activity Log</span>
            </a>

            <a href="settings.php">
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>

        </nav>


        <div class="sidebar-bottom">

            <div class="admin-mini">

                <div class="admin-avatar">

                    <?php
                    echo strtoupper(
                        substr($adminName, 0, 1)
                    );
                    ?>

                </div>

                <div>

                    <strong>
                        <?php
                        echo htmlspecialchars($adminName);
                        ?>
                    </strong>

                    <span>Administrator</span>

                </div>

            </div>


            <a href="../logout.php"
               class="logout-link">

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>Logout</span>

            </a>

        </div>

    </aside>


    <!-- MAIN -->

    <main class="main-content">

        <!-- HEADER -->

        <header class="page-header">

            <div>

                <span class="page-label">
                    CONTENT MANAGEMENT
                </span>

                <h1>
                    Past Papers
                </h1>

                <p>
                    Manage examination past papers for students.
                </p>

            </div>


            <button
                type="button"
                class="add-btn"
                id="openAddModal">

                <i class="fa-solid fa-plus"></i>

                Add Past Paper

            </button>

        </header>


        <!-- FILTERS -->

        <section class="filter-card">

            <div class="search-box">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    id="searchInput"
                    placeholder="Search past papers...">

            </div>


            <div class="filter-select">

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


            <div class="filter-select">

                <select id="gradeFilter">

                    <option value="">
                        All Grades
                    </option>

                    <option value="Grade 6">Grade 6</option>
                    <option value="Grade 7">Grade 7</option>
                    <option value="Grade 8">Grade 8</option>
                    <option value="Grade 9">Grade 9</option>
                    <option value="Grade 10">Grade 10</option>
                    <option value="Grade 11">Grade 11</option>
                    <option value="Grade 12">Grade 12</option>
                    <option value="Grade 13">Grade 13</option>

                </select>

            </div>


            <button
                type="button"
                id="resetFilters"
                class="reset-btn">

                <i class="fa-solid fa-rotate-left"></i>

                Reset

            </button>

        </section>


        <!-- TABLE -->

        <section class="table-card">

            <div class="table-header">

                <div>

                    <h2>
                        Past Papers
                    </h2>

                    <p>
                        All uploaded examination papers
                    </p>

                </div>


                <div class="paper-count">

                    <span id="paperCount">
                        0
                    </span>

                    Papers

                </div>

            </div>


            <div
                id="loadingState"
                class="loading-state">

                <div class="spinner"></div>

                <p>
                    Loading past papers...
                </p>

            </div>


            <div
                id="tableWrapper"
                class="table-wrapper"
                style="display:none;">

                <table>

                    <thead>

                        <tr>

                            <th>Paper</th>

                            <th>Subject</th>

                            <th>Grade</th>

                            <th>Year</th>

                            <th>Type</th>

                            <th>Uploaded</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody id="papersTableBody">
                    </tbody>

                </table>

            </div>


            <div
                id="emptyState"
                class="empty-state"
                style="display:none;">

                <i class="fa-solid fa-folder-open"></i>

                <h3>
                    No Past Papers Found
                </h3>

                <p>
                    Add a past paper or change your filters.
                </p>

            </div>


            <div
                id="errorState"
                class="error-state"
                style="display:none;">

                <i class="fa-solid fa-circle-exclamation"></i>

                <h3>
                    Unable to Load Papers
                </h3>

                <p>
                    Please try again.
                </p>

                <button
                    type="button"
                    class="retry-btn"
                    onclick="loadPapers()">

                    Try Again

                </button>

            </div>

        </section>

    </main>

</div>


<!-- ADD PAPER MODAL -->

<div
    class="modal-overlay"
    id="addModal">

    <div class="modal">

        <div class="modal-header">

            <div>

                <h2>
                    Add Past Paper
                </h2>

                <p>
                    Upload a PDF examination paper.
                </p>

            </div>

            <button
                type="button"
                class="close-btn"
                id="closeAddModal">

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>


        <form
            id="addPaperForm"
            enctype="multipart/form-data">


            <div class="form-group">

                <label>
                    Paper Title
                    <span>*</span>
                </label>

                <input
                    type="text"
                    name="title"
                    placeholder="Example: Grade 11 Mathematics 2024"
                    required>

            </div>


            <div class="form-group">

                <label>
                    Description
                </label>

                <textarea
                    name="description"
                    rows="3"
                    placeholder="Enter a short description"></textarea>

            </div>


            <div class="form-row">

                <div class="form-group">

                    <label>
                        Subject
                        <span>*</span>
                    </label>

                    <select
                        name="subject"
                        required>

                        <option value="">
                            Select Subject
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


                <div class="form-group">

                    <label>
                        Grade
                        <span>*</span>
                    </label>

                    <select
                        name="grade"
                        required>

                        <option value="">
                            Select Grade
                        </option>

                        <option value="Grade 6">Grade 6</option>
                        <option value="Grade 7">Grade 7</option>
                        <option value="Grade 8">Grade 8</option>
                        <option value="Grade 9">Grade 9</option>
                        <option value="Grade 10">Grade 10</option>
                        <option value="Grade 11">Grade 11</option>
                        <option value="Grade 12">Grade 12</option>
                        <option value="Grade 13">Grade 13</option>

                    </select>

                </div>

            </div>


            <div class="form-row">

                <div class="form-group">

                    <label>
                        Paper Type
                    </label>

                    <select name="paper_type">

                        <option value="Past Paper">
                            Past Paper
                        </option>

                        <option value="Term Test">
                            Term Test
                        </option>

                        <option value="School Paper">
                            School Paper
                        </option>

                        <option value="Exam Paper">
                            Exam Paper
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Year
                        <span>*</span>
                    </label>

                    <input
                        type="number"
                        name="paper_year"
                        min="2000"
                        max="2100"
                        placeholder="2026"
                        required>

                </div>

            </div>


            <div class="form-group">

                <label>
                    PDF File
                    <span>*</span>
                </label>

                <div class="file-input">

                    <input
                        type="file"
                        name="paper"
                        id="paperFile"
                        accept=".pdf,application/pdf"
                        required>

                    <i class="fa-solid fa-file-pdf"></i>

                    <span id="fileName">
                        Choose PDF file
                    </span>

                </div>

                <small>
                    PDF only. Maximum 10 MB.
                </small>

            </div>


            <div
                id="formMessage"
                class="form-message">
            </div>


            <div class="modal-actions">

                <button
                    type="button"
                    class="cancel-btn"
                    id="cancelAdd">

                    Cancel

                </button>


                <button
                    type="submit"
                    class="save-btn"
                    id="savePaper">

                    <i class="fa-solid fa-cloud-arrow-up"></i>

                    Upload Paper

                </button>

            </div>

        </form>

    </div>

</div>


<script src="../js/past-papers-management.js"></script>

</body>

</html>