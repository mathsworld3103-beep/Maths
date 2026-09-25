<?php

require_once "auth-check.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Material Management | MathsWorld</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

    <link
        rel="stylesheet"
        href="../css/material-management.css"
    >

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

</head>

<body>

<div class="admin-layout">


    <!-- ==========================================
         SIDEBAR
    =========================================== -->

    <aside class="admin-sidebar">

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


            <a
                href="material-management.php"
                class="active"
            >

                <i class="fa-solid fa-file-lines"></i>

                <span>Materials</span>

            </a>


            <a href="past-papers-management.php">

                <i class="fa-solid fa-file-pdf"></i>

                <span>Past Papers</span>

            </a>


            <a href="model-papers-management.php">

                <i class="fa-solid fa-copy"></i>

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

        </nav>


        <div class="sidebar-bottom">

            <a
                href="../logout.php"
                class="logout-link"
            >

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>Logout</span>

            </a>

        </div>

    </aside>



    <!-- ==========================================
         MAIN CONTENT
    =========================================== -->

    <main class="admin-main">


        <!-- HEADER -->

        <header class="page-header">

            <div>

                <span class="page-label">
                    CONTENT MANAGEMENT
                </span>

                <h1>Student Materials</h1>

                <p>
                    Manage notes, lessons and study materials
                    for MathsWorld students.
                </p>

            </div>


            <button
                class="add-material-btn"
                id="openAddMaterial"
            >

                <i class="fa-solid fa-plus"></i>

                Add Material

            </button>

        </header>



        <!-- ======================================
             FILTER BAR
        ======================================= -->

        <section class="filter-card">

            <div class="search-box">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    id="searchMaterial"
                    placeholder="Search materials..."
                >

            </div>


            <select id="filterSubject">

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


            <select id="filterGrade">

                <option value="">
                    All Grades
                </option>

                <option value="Grade 6">Grade 6</option>
                <option value="Grade 7">Grade 7</option>
                <option value="Grade 8">Grade 8</option>
                <option value="Grade 9">Grade 9</option>
                <option value="Grade 10">Grade 10</option>
                <option value="Grade 11">Grade 11</option>
                <option value="A/L">A/L</option>

            </select>


            <button
                id="resetFilters"
                class="reset-btn"
            >

                <i class="fa-solid fa-rotate-left"></i>

                Reset

            </button>

        </section>



        <!-- ======================================
             MATERIAL TABLE
        ======================================= -->

        <section class="table-card">

            <div class="table-header">

                <div>

                    <h2>All Materials</h2>

                    <span id="materialCount">
                        Loading...
                    </span>

                </div>

            </div>


            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Material</th>

                            <th>Subject</th>

                            <th>Grade</th>

                            <th>Type</th>

                            <th>Year</th>

                            <th>Uploaded</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody id="materialTableBody">

                        <tr>

                            <td
                                colspan="8"
                                class="loading"
                            >

                                Loading materials...

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>



<!-- ==========================================
     ADD MATERIAL MODAL
=========================================== -->

<div
    class="modal-overlay"
    id="materialModal"
>

    <div class="material-modal">


        <div class="modal-header">

            <div>

                <h2>Add Student Material</h2>

                <p>
                    Upload a new learning material
                </p>

            </div>


            <button
                type="button"
                class="close-modal"
                id="closeMaterialModal"
            >

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>



        <form
            id="materialForm"
            enctype="multipart/form-data"
        >


            <div class="form-group">

                <label>
                    Material Title
                </label>

                <input
                    type="text"
                    name="title"
                    id="materialTitle"
                    placeholder="Example: Algebra Notes"
                    required
                >

            </div>



            <div class="form-group">

                <label>
                    Description
                </label>

                <textarea
                    name="description"
                    id="materialDescription"
                    placeholder="Enter material description..."
                ></textarea>

            </div>



            <div class="form-row">


                <div class="form-group">

                    <label>
                        Subject
                    </label>

                    <select
                        name="subject"
                        id="materialSubject"
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

                    <label>
                        Grade
                    </label>

                    <select
                        name="grade"
                        id="materialGrade"
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

                        <option value="A/L">
                            A/L
                        </option>

                    </select>

                </div>

            </div>



            <div class="form-row">


                <div class="form-group">

                    <label>
                        Material Type
                    </label>

                    <select
                        name="material_type"
                        id="materialType"
                        required
                    >

                        <option value="Notes">
                            Notes
                        </option>

                        <option value="Lesson">
                            Lesson
                        </option>

                        <option value="Worksheet">
                            Worksheet
                        </option>

                        <option value="Revision">
                            Revision
                        </option>

                        <option value="Formula">
                            Formula
                        </option>

                    </select>

                </div>



                <div class="form-group">

                    <label>
                        Year
                    </label>

                    <input
                        type="number"
                        name="material_year"
                        id="materialYear"
                        min="2000"
                        max="2100"
                        placeholder="2026"
                    >

                </div>

            </div>



            <div class="form-group">

                <label>
                    PDF File
                </label>

                <div class="file-upload">

                    <input
                        type="file"
                        name="material_file"
                        id="materialFile"
                        accept=".pdf"
                        required
                    >

                    <i class="fa-solid fa-cloud-arrow-up"></i>

                    <span>
                        Choose PDF file
                    </span>

                    <small>
                        PDF only · Maximum 10MB
                    </small>

                </div>

            </div>



            <div
                id="materialMessage"
                class="form-message"
            ></div>



            <div class="modal-actions">

                <button
                    type="button"
                    class="cancel-btn"
                    id="cancelMaterial"
                >
                    Cancel
                </button>


                <button
                    type="submit"
                    class="save-btn"
                    id="saveMaterial"
                >

                    <i class="fa-solid fa-floppy-disk"></i>

                    Save Material

                </button>

            </div>

        </form>

    </div>

</div>



<script src="../js/material-management.js"></script>

</body>

</html>