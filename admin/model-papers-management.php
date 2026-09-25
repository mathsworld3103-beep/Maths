<?php

require_once "auth-check.php";

$adminName = $_SESSION["full_name"] ?? "Administrator";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Model Papers Management | MathsWorld</title>

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
        href="../css/model-papers-management.css"
    >

</head>

<body>


<div class="admin-layout">


    <!-- ==========================================
         SIDEBAR
    =========================================== -->

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-brand">

            <div class="logo">
                MW
            </div>

            <div>
                <h2>MathsWorld</h2>
                <span>Admin Panel</span>
            </div>

        </div>


        <div class="admin-profile">

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

                <small>
                    Administrator
                </small>

            </div>

        </div>


        <nav class="sidebar-nav">

            <a href="dashboard.php">

                <i class="fa-solid fa-gauge-high"></i>

                <span>Dashboard</span>

            </a>


            <a href="student-management.php">

                <i class="fa-solid fa-users"></i>

                <span>Students</span>

            </a>


            <a href="material-management.php">

                <i class="fa-solid fa-file-lines"></i>

                <span>Materials</span>

            </a>


            <a href="past-papers-management.php">

                <i class="fa-solid fa-file-pdf"></i>

                <span>Past Papers</span>

            </a>


            <a
                href="model-papers-management.php"
                class="active"
            >

                <i class="fa-solid fa-file-circle-check"></i>

                <span>Model Papers</span>

            </a>

        </nav>


        <div class="sidebar-bottom">

            <a href="../logout.php">

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>Logout</span>

            </a>

        </div>

    </aside>


    <!-- ==========================================
         MAIN CONTENT
    =========================================== -->

    <main class="main-content">


        <!-- Header -->

        <header class="top-header">

            <div class="header-left">

                <button
                    type="button"
                    class="menu-button"
                    id="menuButton"
                >

                    <i class="fa-solid fa-bars"></i>

                </button>

                <div>

                    <h1>Model Papers</h1>

                    <p>
                        Manage student model examination papers
                    </p>

                </div>

            </div>


            <button
                type="button"
                class="add-button"
                id="openModalButton"
            >

                <i class="fa-solid fa-plus"></i>

                Upload Model Paper

            </button>

        </header>


        <!-- ==========================================
             PAGE CONTENT
        =========================================== -->

        <section class="page-content">


            <!-- Statistics -->

            <div class="stats-grid">

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="fa-solid fa-file-circle-check"></i>
                    </div>

                    <div>

                        <span>
                            Total Papers
                        </span>

                        <strong id="totalPapers">
                            0
                        </strong>

                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="fa-solid fa-calculator"></i>
                    </div>

                    <div>

                        <span>
                            Mathematics
                        </span>

                        <strong id="mathPapers">
                            0
                        </strong>

                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="fa-solid fa-flask"></i>
                    </div>

                    <div>

                        <span>
                            Science
                        </span>

                        <strong id="sciencePapers">
                            0
                        </strong>

                    </div>

                </div>

            </div>


            <!-- Filters -->

            <div class="filter-card">

                <div class="search-box">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Search model papers..."
                    >

                </div>


                <div class="filter-item">

                    <label>
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


                <div class="filter-item">

                    <label>
                        Grade
                    </label>

                    <select id="gradeFilter">

                        <option value="">
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

                        <option value="Grade 12">
                            Grade 12
                        </option>

                        <option value="Grade 13">
                            Grade 13
                        </option>

                    </select>

                </div>


                <button
                    type="button"
                    class="reset-button"
                    id="resetFilters"
                >

                    <i class="fa-solid fa-rotate-left"></i>

                    Reset

                </button>

            </div>


            <!-- Message -->

            <div
                id="messageBox"
                class="message-box"
                style="display:none;"
            ></div>


            <!-- Table -->

            <div class="table-card">

                <div class="table-header">

                    <div>

                        <h2>
                            Model Papers
                        </h2>

                        <p id="resultText">
                            Loading...
                        </p>

                    </div>

                </div>


                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    Paper
                                </th>

                                <th>
                                    Subject
                                </th>

                                <th>
                                    Grade
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Year
                                </th>

                                <th>
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody id="papersTableBody">

                            <tr>

                                <td
                                    colspan="6"
                                    class="loading-cell"
                                >

                                    <div class="spinner"></div>

                                    Loading model papers...

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </main>

</div>


<!-- ==========================================
     ADD MODAL
=========================================== -->

<div
    class="modal-overlay"
    id="modalOverlay"
>

    <div class="modal">


        <div class="modal-header">

            <div>

                <h2>
                    Upload Model Paper
                </h2>

                <p>
                    Add a new model paper PDF
                </p>

            </div>


            <button
                type="button"
                class="close-button"
                id="closeModalButton"
            >

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>


        <form
            id="modelPaperForm"
            enctype="multipart/form-data"
        >


            <!-- Title -->

            <div class="form-group">

                <label for="paperTitle">
                    Title *
                </label>

                <input
                    type="text"
                    id="paperTitle"
                    placeholder="Example: Grade 11 Mathematics Model Paper 2026"
                    required
                >

            </div>


            <!-- Description -->

            <div class="form-group">

                <label for="paperDescription">
                    Description
                </label>

                <textarea
                    id="paperDescription"
                    rows="3"
                    placeholder="Enter a short description..."
                ></textarea>

            </div>


            <!-- Row -->

            <div class="form-row">


                <div class="form-group">

                    <label for="paperSubject">
                        Subject *
                    </label>

                    <select
                        id="paperSubject"
                        required
                    >

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

                    <label for="paperGrade">
                        Grade *
                    </label>

                    <select
                        id="paperGrade"
                        required
                    >

                        <option value="">
                            Select Grade
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

                        <option value="Grade 12">
                            Grade 12
                        </option>

                        <option value="Grade 13">
                            Grade 13
                        </option>

                    </select>

                </div>

            </div>


            <!-- Second row -->

            <div class="form-row">


                <div class="form-group">

                    <label for="paperType">
                        Paper Type *
                    </label>

                    <select
                        id="paperType"
                        required
                    >

                        <option value="Model Paper">
                            Model Paper
                        </option>

                        <option value="Term Test">
                            Term Test
                        </option>

                        <option value="Practice Paper">
                            Practice Paper
                        </option>

                        <option value="Revision Paper">
                            Revision Paper
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label for="paperYear">
                        Year *
                    </label>

                    <input
                        type="number"
                        id="paperYear"
                        min="2000"
                        max="2100"
                        value="2026"
                        required
                    >

                </div>

            </div>


            <!-- PDF -->

            <div class="form-group">

                <label for="paperFile">
                    PDF File *
                </label>

                <div class="file-upload">

                    <input
                        type="file"
                        id="paperFile"
                        accept=".pdf,application/pdf"
                        required
                    >

                    <div class="file-label">

                        <i class="fa-solid fa-cloud-arrow-up"></i>

                        <span id="fileName">
                            Choose PDF file
                        </span>

                    </div>

                </div>

                <small>
                    PDF only. Maximum file size: 10 MB.
                </small>

            </div>


            <!-- Form message -->

            <div
                id="formMessage"
                class="form-message"
            ></div>


            <!-- Buttons -->

            <div class="modal-actions">

                <button
                    type="button"
                    class="cancel-button"
                    id="cancelButton"
                >
                    Cancel
                </button>


                <button
                    type="submit"
                    class="save-button"
                    id="saveButton"
                >

                    <i class="fa-solid fa-upload"></i>

                    <span>
                        Upload Paper
                    </span>

                </button>

            </div>

        </form>

    </div>

</div>


<script src="../js/model-papers-management.js"></script>

</body>

</html>