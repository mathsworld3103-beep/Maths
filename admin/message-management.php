<?php

require_once "auth-check.php";

$message = "";
$error = "";

// =====================================
// SEND MESSAGE
// =====================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $messageText = trim($_POST["message"] ?? "");
    $messageType = trim($_POST["message_type"] ?? "announcement");
    $receiverType = $_POST["receiver_type"] ?? "all";
    $receiverId = (int) ($_POST["receiver_id"] ?? 0);

    if ($title === "" || $messageText === "") {

        $error = "Please fill in the title and message.";

    } else {

        // =====================================
        // SEND TO ALL STUDENTS
        // =====================================

        if ($receiverType === "all") {

            $stmt = $conn->prepare("
                INSERT INTO messages
                (
                    sender_id,
                    receiver_id,
                    title,
                    message,
                    message_type
                )
                VALUES (?, NULL, ?, ?, ?)
            ");

            $senderId = (int) $_SESSION["user_id"];

            $stmt->bind_param(
                "isss",
                $senderId,
                $title,
                $messageText,
                $messageType
            );

            if ($stmt->execute()) {

                $message = "Announcement sent successfully.";

            } else {

                $error = "Unable to send announcement.";
            }

            $stmt->close();

        }

        // =====================================
        // SEND TO ONE STUDENT
        // =====================================

        elseif ($receiverType === "student") {

            if ($receiverId <= 0) {

                $error = "Please select a student.";

            } else {

                $stmt = $conn->prepare("
                    INSERT INTO messages
                    (
                        sender_id,
                        receiver_id,
                        title,
                        message,
                        message_type
                    )
                    VALUES (?, ?, ?, ?, ?)
                ");

                $senderId = (int) $_SESSION["user_id"];

                $stmt->bind_param(
                    "iisss",
                    $senderId,
                    $receiverId,
                    $title,
                    $messageText,
                    $messageType
                );

                if ($stmt->execute()) {

                    $message = "Message sent successfully.";

                } else {

                    $error = "Unable to send message.";
                }

                $stmt->close();
            }
        }
    }
}


// =====================================
// GET STUDENTS
// =====================================

$students = [];

$stmt = $conn->prepare("
    SELECT
        id,
        full_name,
        email,
        grade
    FROM users
    WHERE role = 'student'
    ORDER BY full_name ASC
");

$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $students[] = $row;

}

$stmt->close();


// =====================================
// GET SENT MESSAGES
// =====================================

$messages = [];

$stmt = $conn->prepare("
    SELECT
        m.id,
        m.title,
        m.message,
        m.message_type,
        m.status,
        m.created_at,
        u.full_name AS receiver_name
    FROM messages m
    LEFT JOIN users u
        ON m.receiver_id = u.id
    ORDER BY m.created_at DESC
");

$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $messages[] = $row;

}

$stmt->close();

$stmt = $conn->prepare("
    SELECT
        m.id,
        m.title,
        m.message,
        m.message_type,
        m.created_at,
        m.receiver_id,

        u.full_name AS receiver_name,

        (
            SELECT COUNT(*)
            FROM message_reads mr
            WHERE mr.message_id = m.id
        ) AS read_count,

        (
            SELECT COUNT(*)
            FROM users s
            WHERE s.role = 'student'
        ) AS total_students

                FROM messages m

                LEFT JOIN users u
                    ON m.receiver_id = u.id

                ORDER BY m.created_at DESC
            ");

            $stmt->execute();

            $result = $stmt->get_result();

            $sentMessages = [];

            while ($row = $result->fetch_assoc()) {

                $sentMessages[] = $row;

            }

            $stmt->close();


            $totalMessages = 0;
$totalStudents = 0;
$totalReads = 0;
$totalRecipients = 0;
$totalUnread = 0;
$readRate = 0;


/* ==========================================
   TOTAL MESSAGES
========================================== */

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM messages
");

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$totalMessages =
    (int) ($row["total"] ?? 0);

$stmt->close();


/* ==========================================
   TOTAL STUDENTS
========================================== */

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM users
    WHERE role = 'student'
");

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$totalStudents =
    (int) ($row["total"] ?? 0);

$stmt->close();


/* ==========================================
   TOTAL READS
========================================== */

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM message_reads
");

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$totalReads =
    (int) ($row["total"] ?? 0);

$stmt->close();


/* ==========================================
   TOTAL RECIPIENTS
========================================== */

$stmt = $conn->prepare("
    SELECT
        COALESCE(
            SUM(
                CASE
                    WHEN receiver_id IS NULL
                    THEN ?
                    ELSE 1
                END
            ),
            0
        ) AS total
    FROM messages
");

$stmt->bind_param(
    "i",
    $totalStudents
);

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$totalRecipients =
    (int) ($row["total"] ?? 0);

$stmt->close();


/* ==========================================
   TOTAL UNREAD
========================================== */

$totalUnread = max(
    0,
    $totalRecipients - $totalReads
);


/* ==========================================
   READ RATE
========================================== */

if ($totalRecipients > 0) {

    $readRate = round(
        (
            $totalReads /
            $totalRecipients
        ) * 100
    );

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

    <title>Message Management | MathsWorld</title>

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
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            background: #f5f8fc;
            color: #1e293b;
        }

        .dashboard-layout {
            min-height: 100vh;
        }

        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;

            width: 260px;
            height: 100vh;

            background: #0b1f3a;
            color: #ffffff;

            display: flex;
            flex-direction: column;

            z-index: 1000;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 12px;

            padding: 22px;
        }

        .logo {
            width: 45px;
            height: 45px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #38bdf8;
            color: #0b1f3a;

            border-radius: 12px;

            font-weight: 800;
        }

        .brand h2 {
            margin: 0;
            font-size: 19px;
        }

        .brand p {
            margin: 4px 0 0;
            font-size: 12px;
            color: #bae6fd;
        }

        .sidebar-nav {
            padding: 15px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;

            padding: 13px 15px;
            margin-bottom: 5px;

            color: #cbd5e1;
            text-decoration: none;

            border-radius: 9px;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: #12345b;
            color: #ffffff;
        }

        .sidebar-nav i {
            width: 20px;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding: 15px;
        }

        .sidebar-bottom a {
            display: flex;
            gap: 12px;
            align-items: center;

            padding: 13px 15px;

            color: #cbd5e1;
            text-decoration: none;
        }

        /* =========================
           MAIN
        ========================= */

        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }

        .top-header {
            height: 82px;

            display: flex;
            align-items: center;

            padding: 0 30px;

            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }

        .top-header h1 {
            margin: 0;
            color: #0b1f3a;
            font-size: 25px;
        }

        .top-header p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 14px;
        }

        .menu-button {
            display: none;
        }

        .page-content {
            padding: 30px;
        }

        /* =========================
           FORM
        ========================= */

        .content-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 10px;

            color: #0b1f3a;
            margin-bottom: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            color: #334155;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;

            border: 1px solid #cbd5e1;
            border-radius: 9px;

            font-family: inherit;
            font-size: 14px;

            outline: none;
        }

        textarea {
            min-height: 140px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #38bdf8;

            box-shadow:
                0 0 0 3px
                rgba(56, 189, 248, 0.15);
        }

        .send-button {
            border: none;

            padding: 13px 22px;

            border-radius: 9px;

            background: #0b1f3a;
            color: #ffffff;

            font-weight: 700;

            cursor: pointer;
        }

        .send-button:hover {
            background: #12345b;
        }

        .alert {
            padding: 13px 16px;
            border-radius: 9px;
            margin-bottom: 20px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        /* =========================
           TABLE
        ========================= */

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
        }

        td {
            font-size: 14px;
        }

        .badge {
            display: inline-block;

            padding: 5px 9px;

            border-radius: 20px;

            font-size: 11px;
            font-weight: 700;
        }

        .badge-unread {
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge-read {
            background: #f1f5f9;
            color: #64748b;
        }

        .badge-all {
            background: #dcfce7;
            color: #166534;
        }

        .message-preview {
            max-width: 350px;
            color: #64748b;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .read-stat,
        .unread-stat {
            display: inline-block;
            margin: 2px;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .read-stat {
            background: #dcfce7;
            color: #166534;
        }

        .unread-stat {
            background: #fee2e2;
            color: #991b1b;
        }

        .delete-message-button {
            border: none;
            padding: 8px 12px;
            border-radius: 7px;
            background: #dc2626;
            color: #ffffff;
            cursor: pointer;
            font-weight: 600;
        }

        .delete-message-button:hover {
            background: #b91c1c;
        }

        .message-statistics {
            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 20px;

            margin-bottom: 25px;
        }

        .message-stat-card {
            display: flex;

            align-items: center;

            gap: 15px;

            padding: 20px;

            background: #ffffff;

            border: 1px solid #e2e8f0;

            border-radius: 14px;

            box-shadow:
                0 5px 20px rgba(
                    15,
                    23,
                    42,
                    0.05
                );
        }

        .stat-icon {
            width: 48px;
            height: 48px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 12px;

            background: #e0f2fe;

            color: #0b1f3a;

            font-size: 20px;
        }

        .stat-label {
            display: block;

            color: #64748b;

            font-size: 13px;

            margin-bottom: 4px;
        }

        .message-stat-card strong {
            display: block;

            color: #0b1f3a;

            font-size: 25px;
        }

        .message-filters {
    display: grid;

    grid-template-columns:
        2fr 1fr 1fr auto;

    gap: 15px;

    align-items: end;

    margin-bottom: 20px;

    padding: 20px;

    background: #ffffff;

    border: 1px solid #e2e8f0;

    border-radius: 14px;
    }

    .filter-group {
        display: flex;

        flex-direction: column;

        gap: 7px;
    }

    .filter-group label {
        font-size: 13px;

        font-weight: 700;

        color: #334155;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;

        padding: 11px 13px;

        border: 1px solid #cbd5e1;

        border-radius: 8px;

        background: #ffffff;

        color: #1e293b;

        outline: none;

        font-size: 14px;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        border-color: #38bdf8;

        box-shadow:
            0 0 0 3px
            rgba(56, 189, 248, 0.15);
    }

    .clear-filter-button {
        border: none;

        border-radius: 8px;

        padding: 11px 16px;

        background: #0b1f3a;

        color: #ffffff;

        font-weight: 700;

        cursor: pointer;

        white-space: nowrap;
    }

    .clear-filter-button:hover {
        background: #12345b;
    }

    .message-pagination {
    display: flex;

    justify-content: center;

    align-items: center;

    gap: 7px;

    margin-top: 20px;
    }

    .message-pagination button {
        width: 36px;

        height: 36px;

        border: 1px solid #e2e8f0;

        border-radius: 8px;

        background: #ffffff;

        color: #0b1f3a;

        font-size: 13px;

        font-weight: 700;

        cursor: pointer;
    }

    .message-pagination button:hover {
        background: #e0f2fe;
    }

    .message-pagination button.active {
        background: #0b1f3a;

        color: #ffffff;

        border-color: #0b1f3a;
    }

    .message-pagination button:disabled {
        opacity: 0.4;

        cursor: not-allowed;
    }

    .message-modal {
    position: fixed;

    inset: 0;

    display: none;

    align-items: center;

    justify-content: center;

    padding: 20px;

    background: rgba(
        15,
        23,
        42,
        0.55
    );

    z-index: 9999;
}

.message-modal.show {
    display: flex;
}

.message-modal-content {
    width: 100%;

    max-width: 650px;

    max-height: 90vh;

    overflow-y: auto;

    background: #ffffff;

    border-radius: 16px;

    box-shadow:
        0 25px 60px
        rgba(
            15,
            23,
            42,
            0.25
        );
}

.message-modal-header {
    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 20px 24px;

    border-bottom: 1px solid #e2e8f0;
}

.message-modal-header h2 {
    margin: 0;

    color: #0b1f3a;

    font-size: 20px;
}

.close-message-modal {
    width: 36px;

    height: 36px;

    border: none;

    border-radius: 8px;

    background: #f1f5f9;

    color: #334155;

    cursor: pointer;

    font-size: 16px;
}

.close-message-modal:hover {
    background: #e2e8f0;
}

.message-modal-body {
    padding: 24px;
}

.message-detail h3 {
    margin: 0 0 15px;

    color: #0b1f3a;

    font-size: 22px;
}

.detail-meta {
    display: flex;

    flex-wrap: wrap;

    gap: 10px;

    margin-bottom: 20px;
}

.detail-meta span {
    padding: 7px 10px;

    background: #f1f5f9;

    border-radius: 7px;

    color: #475569;

    font-size: 12px;
}

.detail-message {
    padding: 18px;

    background: #f8fafc;

    border-radius: 10px;

    color: #334155;

    line-height: 1.7;

    margin-bottom: 20px;
}

.message-read-stats {
    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 12px;
}

.read-box,
.unread-box,
.percentage-box {
    padding: 18px;

    text-align: center;

    border-radius: 10px;
}

.read-box {
    background: #dcfce7;

    color: #166534;
}

.unread-box {
    background: #fee2e2;

    color: #991b1b;
}

.percentage-box {
    background: #e0f2fe;

    color: #075985;
}

.read-box strong,
.unread-box strong,
.percentage-box strong {
    display: block;

    font-size: 24px;
}

.read-box span,
.unread-box span,
.percentage-box span {
    font-size: 12px;

    font-weight: 600;
}

.view-message-button {
    border: none;

    padding: 8px 12px;

    border-radius: 7px;

    background: #e0f2fe;

    color: #075985;

    cursor: pointer;

    font-weight: 600;

    margin-right: 5px;
}

.view-message-button:hover {
    background: #bae6fd;
}

.modal-loading {
    text-align: center;

    padding: 30px;

    color: #64748b;
}

.modal-error {
    padding: 20px;

    text-align: center;

    color: #dc2626;
}

/* ==========================================
   EDIT MESSAGE FORM
========================================== */

.form-group {

    margin-bottom: 20px;

}


.form-group label {

    display: block;

    margin-bottom: 7px;

    color: #334155;

    font-size: 13px;

    font-weight: 700;

}


.form-group input,
.form-group select,
.form-group textarea {

    width: 100%;

    padding: 12px 14px;

    border: 1px solid #dbe3ec;

    border-radius: 10px;

    background: #ffffff;

    color: #0f172a;

    font-family: inherit;

    font-size: 14px;

    outline: none;

    box-sizing: border-box;

    transition: 0.2s;

}


.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {

    border-color: #38bdf8;

    box-shadow:
        0 0 0 3px
        rgba(56, 189, 248, 0.12);

}


.form-group textarea {

    resize: vertical;

    min-height: 150px;

}


.edit-message-actions {

    display: flex;

    justify-content: flex-end;

    gap: 10px;

    padding-top: 5px;

}


.cancel-button,
.save-message-button {

    padding: 11px 18px;

    border: none;

    border-radius: 9px;

    cursor: pointer;

    font-family: inherit;

    font-size: 13px;

    font-weight: 700;

}


.cancel-button {

    background: #f1f5f9;

    color: #475569;

}


.save-message-button {

    background: #0b1f3a;

    color: #ffffff;

}


.save-message-button:hover {

    background: #16365d;

}


.edit-message-button {

    border: none;

    padding: 7px 10px;

    border-radius: 7px;

    background: #eff6ff;

    color: #2563eb;

    cursor: pointer;

    font-weight: 700;

}


.edit-message-button:hover {

    background: #dbeafe;

}

.delete-modal-content {

    max-width: 450px;

    padding: 30px;

    text-align: center;
}

.delete-modal-icon {

    width: 65px;
    height: 65px;

    margin: 0 auto 18px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #fee2e2;

    color: #dc2626;

    font-size: 25px;
}

.delete-modal-content h2 {

    margin: 0 0 10px;

    color: #0b1f3a;

    font-size: 22px;
}

.delete-modal-content p {

    margin: 0 0 25px;

    color: #64748b;

    line-height: 1.6;

    font-size: 14px;
}

.delete-confirm-button {

    padding: 11px 18px;

    border: none;

    border-radius: 9px;

    background: #dc2626;

    color: white;

    cursor: pointer;

    font-family: inherit;

    font-size: 13px;

    font-weight: 700;
}

.delete-confirm-button:hover {

    background: #b91c1c;
}

.delete-message-button {

    border: none;

    padding: 7px 10px;

    border-radius: 7px;

    background: #fef2f2;

    color: #dc2626;

    cursor: pointer;

    font-weight: 700;
}

.delete-message-button:hover {

    background: #fee2e2;
}


@media (max-width: 600px) {

   

}

        @media (max-width: 900px) {

            .sidebar {
                transform: translateX(-100%);
                transition: 0.3s;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-button {
                display: block;

                margin-right: 15px;

                border: none;
                background: none;

                font-size: 20px;
                cursor: pointer;
            }

            .message-statistics {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .message-filters {
            grid-template-columns: 1fr 1fr;
        }

        .clear-filter-button {
            width: 100%;
        }

        }

        @media (max-width: 700px) {

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .page-content {
                padding: 20px;
            }

            .message-statistics {
                grid-template-columns: 1fr;
            }

             .message-filters {
                grid-template-columns: 1fr;
            }

             .message-read-stats {
        grid-template-columns: 1fr;
    }

    .message-modal {
        padding: 10px;
    }

    .message-modal-body {
        padding: 18px;
    }

        }

         .edit-message-actions {

        flex-direction: column;

    }

    .cancel-button,
    .save-message-button {

        width: 100%;

    }

    </style>

</head>

<body>

<div class="dashboard-layout">

    <!-- SIDEBAR -->

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-header">

            <div class="logo">
                MW
            </div>

            <div class="brand">

                <h2>
                    MathsWorld
                </h2>

                <p>
                    Admin Portal
                </p>

            </div>

        </div>


        <nav class="sidebar-nav">

            <a href="dashboard.php">
                <i class="fa-solid fa-gauge-high"></i>
                Dashboard
            </a>

            <a href="material-management.php">
                <i class="fa-solid fa-file-lines"></i>
                Materials
            </a>

            <a href="past-papers-management.php">
                <i class="fa-solid fa-file-pdf"></i>
                Past Papers
            </a>

            <a href="model-papers-management.php">
                <i class="fa-solid fa-book-open"></i>
                Model Papers
            </a>

            <a href="message-management.php" class="active">
                <i class="fa-solid fa-message"></i>
                Messages
            </a>

        </nav>


        <div class="sidebar-bottom">

            <a href="../logout.php">
                <i class="fa-solid fa-right-from-bracket"></i>
                Logout
            </a>

        </div>

    </aside>


    <!-- MAIN -->

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

                <h1>
                    Message Management
                </h1>

                <p>
                    Send announcements and student messages
                </p>

            </div>

        </header>


        <section class="page-content">


            <!-- ALERTS -->

            <?php if ($message !== ""): ?>

                <div class="alert success">

                    <i class="fa-solid fa-circle-check"></i>

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </div>

            <?php endif; ?>


            <?php if ($error !== ""): ?>

                <div class="alert error">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <?php
                    echo htmlspecialchars($error);
                    ?>

                </div>

            <?php endif; ?>

            <div class="message-statistics">

                <div class="message-stat-card">

                    <div class="stat-icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>

                    <div>
                        <span class="stat-label">
                            Total Messages
                        </span>

                        <strong>
                            <?php echo $totalMessages; ?>
                        </strong>
                    </div>

                </div>


                <div class="message-stat-card">

                    <div class="stat-icon">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>

                    <div>
                        <span class="stat-label">
                            Students
                        </span>

                        <strong>
                            <?php echo $totalStudents; ?>
                        </strong>
                    </div>

                </div>


                <div class="message-stat-card">

                    <div class="stat-icon">
                        <i class="fa-solid fa-envelope-open"></i>
                    </div>

                    <div>
                        <span class="stat-label">
                            Read
                        </span>

                        <strong>
                            <?php echo $totalReads; ?>
                        </strong>
                    </div>

                </div>


                <div class="message-stat-card">

                    <div class="stat-icon">
                        <i class="fa-solid fa-envelope-circle-exclamation"></i>
                    </div>

                    <div>
                        <span class="stat-label">
                            Unread
                        </span>

                        <strong>
                            <?php echo $totalUnread; ?>
                        </strong>
                    </div>

                </div>

            </div>


            <!-- SEND MESSAGE -->

            <div class="content-card">

                <h2 class="card-title">

                    <i class="fa-solid fa-paper-plane"></i>

                    Send New Message

                </h2>


                <form method="POST">

                    <div class="form-grid">

                        <div class="form-group">

                            <label for="message_type">
                                Message Type
                            </label>

                            <select
                                name="message_type"
                                id="message_type"
                            >

                                <option value="announcement">
                                    Announcement
                                </option>

                                <option value="notice">
                                    Notice
                                </option>

                                <option value="exam">
                                    Exam
                                </option>

                                <option value="class">
                                    Class Update
                                </option>

                                <option value="general">
                                    General
                                </option>

                            </select>

                        </div>


                        <div class="form-group">

                            <label for="receiver_type">
                                Send To
                            </label>

                            <select
                                name="receiver_type"
                                id="receiver_type"
                            >

                                <option value="all">
                                    All Students
                                </option>

                                <option value="student">
                                    Specific Student
                                </option>

                            </select>

                        </div>


                        <div
                            class="form-group"
                            id="studentGroup"
                            style="display:none;"
                        >

                            <label for="receiver_id">
                                Select Student
                            </label>

                            <select
                                name="receiver_id"
                                id="receiver_id"
                            >

                                <option value="">
                                    Select Student
                                </option>

                                <?php foreach ($students as $student): ?>

                                    <option
                                        value="<?php echo $student["id"]; ?>"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $student["full_name"]
                                        );
                                        ?>

                                        —
                                        <?php
                                        echo htmlspecialchars(
                                            $student["grade"]
                                        );
                                        ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="form-group full">

                            <label for="title">
                                Message Title
                            </label>

                            <input
                                type="text"
                                name="title"
                                id="title"
                                placeholder="Enter message title"
                                maxlength="255"
                                required
                            >

                        </div>


                        <div class="form-group full">

                            <label for="message">
                                Message
                            </label>

                            <textarea
                                name="message"
                                id="message"
                                placeholder="Write your announcement or message..."
                                required
                            ></textarea>

                        </div>

                    </div>


                    <button
                        type="submit"
                        class="send-button"
                    >

                        <i class="fa-solid fa-paper-plane"></i>

                        Send Message

                    </button>



                </form>

            </div>

            <div class="message-filters">

                <div class="filter-group">

                    <label for="messageSearch">
                        Search Messages
                    </label>

                    <input
                        type="text"
                        id="messageSearch"
                        placeholder="Search title or message..."
                    >

                </div>


                <div class="filter-group">

                    <label for="messageTypeFilter">
                        Message Type
                    </label>

                    <select id="messageTypeFilter">

                        <option value="">
                            All Types
                        </option>

                        <option value="announcement">
                            Announcement
                        </option>

                        <option value="notice">
                            Notice
                        </option>

                        <option value="exam">
                            Exam
                        </option>

                        <option value="class">
                            Class
                        </option>

                        <option value="general">
                            General
                        </option>

                    </select>

                </div>


                <div class="filter-group">

                    <label for="recipientFilter">
                        Recipient
                    </label>

                    <select id="recipientFilter">

                        <option value="">
                            All Recipients
                        </option>

                        <option value="all">
                            All Students
                        </option>

                        <option value="individual">
                            Individual Student
                        </option>

                    </select>

                </div>


                <button
                    type="button"
                    id="clearMessageFilters"
                    class="clear-filter-button"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                    Clear
                </button>

            </div>


            <!-- MESSAGE HISTORY -->

            <div class="content-card">

                <h2 class="card-title">

                    <i class="fa-solid fa-clock-rotate-left"></i>

                    Message History

                </h2>


                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    Title
                                </th>

                                <th>
                                    Recipient
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Date
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if (count($sentMessages) > 0): ?>

                            <?php foreach ($sentMessages as $message): ?>

                            <?php

                            $readCount =
                                (int) $message["read_count"];

                            $totalStudents =
                                (int) $message["total_students"];


                            if ($message["receiver_id"] === null) {

                                $recipient = "All Students";

                                $recipientCount =
                                    $totalStudents;

                            } else {

                                $recipient =
                                    $message["receiver_name"]
                                    ?: "Student";

                                $recipientCount = 1;

                            }

                            $unreadCount =
                                max(
                                    0,
                                    $recipientCount - $readCount
                                );

                            ?>

                            <tr class="message-history-row"

                            data-title="<?php
                                echo htmlspecialchars(
                                    strtolower($message["title"])
                                );
                            ?>"

                            data-message="<?php
                                echo htmlspecialchars(
                                    strtolower($message["message"])
                                );
                            ?>"

                            data-type="<?php
                                echo htmlspecialchars(
                                    strtolower($message["message_type"])
                                );
                            ?>"

                            data-recipient="<?php
                                echo $message["receiver_id"] === null
                                    ? "all"
                                    : "individual";
                            ?>"
                        >

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $message["title"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $message["message_type"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $recipient
                                    );
                                    ?>
                                </td>

                                <td>

                                    <span class="read-stat">
                                        <?php echo $readCount; ?> Read
                                    </span>

                                    <?php if ($unreadCount > 0): ?>

                                        <span class="unread-stat">
                                            <?php echo $unreadCount; ?> Unread
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>
                                    <?php
                                    echo date(
                                        "d M Y, h:i A",
                                        strtotime(
                                            $message["created_at"]
                                        )
                                    );
                                    ?>
                                </td>

                                <td>
                                    

                                    <button
                                        type="button"
                                        class="view-message-button"
                                        data-id="<?php echo $message["id"]; ?>"
                                    >
                                        <i class="fa-solid fa-eye"></i>
                                        View
                                    </button>

                                    <button
                                        type="button"
                                        class="delete-message-button"
                                        data-id="<?php echo (int) $message["id"]; ?>"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                        Delete
                                    </button>

                                    <button
                                        type="button"
                                        class="edit-message-button"
                                        data-id="<?php echo (int) $message["id"]; ?>"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                        Edit
                                    </button>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="6"
                                    style="text-align:center;"
                                >
                                    No messages sent yet.
                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                    <div class="message-pagination" id="messagePagination"></div>

                </div>

            </div>

        </section>

    </main>

</div>

<!-- ==========================================
     MESSAGE DETAILS MODAL
========================================== -->

<div class="message-modal" id="messageModal">

    <div class="message-modal-content">

        <div class="message-modal-header">

            <div>
                <span class="modal-label">
                    MESSAGE DETAILS
                </span>

                <h2>
                    <i class="fa-solid fa-envelope-open-text"></i>
                    Message Details
                </h2>
            </div>

            <button
                type="button"
                id="closeMessageModal"
                class="close-message-modal"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>


        <div
            id="messageModalBody"
            class="message-modal-body"
        >

            <div class="modal-loading">

                <i class="fa-solid fa-spinner fa-spin"></i>

                <p>Loading message...</p>

            </div>

        </div>

    </div>

</div>

<!-- ==========================================
     EDIT MESSAGE MODAL
========================================== -->

<div
    class="message-modal"
    id="editMessageModal"
>

    <div class="message-modal-content">

        <div class="message-modal-header">

            <div>

                <span class="modal-label">
                    MESSAGE MANAGEMENT
                </span>

                <h2>
                    <i class="fa-solid fa-pen"></i>
                    Edit Message
                </h2>

            </div>

            <button
                type="button"
                id="closeEditMessageModal"
                class="close-message-modal"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>


        <form
            id="editMessageForm"
            class="message-modal-body"
        >

            <input
                type="hidden"
                id="editMessageId"
                name="message_id"
            >


            <div class="form-group">

                <label for="editMessageTitle">
                    Message Title
                </label>

                <input
                    type="text"
                    id="editMessageTitle"
                    name="title"
                    maxlength="255"
                    required
                >

            </div>


            <div class="form-group">

                <label for="editMessageType">
                    Message Type
                </label>

                <select
                    id="editMessageType"
                    name="message_type"
                    required
                >

                    <option value="announcement">
                        Announcement
                    </option>

                    <option value="notice">
                        Notice
                    </option>

                    <option value="exam">
                        Exam
                    </option>

                    <option value="class">
                        Class
                    </option>

                    <option value="general">
                        General
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label for="editMessageBody">
                    Message
                </label>

                <textarea
                    id="editMessageBody"
                    name="message"
                    rows="7"
                    required
                ></textarea>

            </div>


            <div class="edit-message-actions">

                <button
                    type="button"
                    id="cancelEditMessage"
                    class="cancel-button"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="save-message-button"
                >
                    <i class="fa-solid fa-floppy-disk"></i>
                    Save Changes
                </button>

            </div>

        </form>

    </div>

</div>

<div class="message-modal" id="deleteMessageModal">

    <div class="message-modal-content delete-modal-content">

        <div class="delete-modal-icon">
            <i class="fa-solid fa-trash"></i>
        </div>

        <h2>Delete Message?</h2>

        <p>
            Are you sure you want to delete this message?
            This action cannot be undone.
        </p>

        <input
            type="hidden"
            id="deleteMessageId"
        >

        <div class="edit-message-actions">

            <button
                type="button"
                id="cancelDeleteMessage"
                class="cancel-button"
            >
                Cancel
            </button>

            <button
                type="button"
                id="confirmDeleteMessage"
                class="delete-confirm-button"
            >
                <i class="fa-solid fa-trash"></i>
                Delete Message
            </button>

        </div>

    </div>

</div>


<script>

document
    .querySelectorAll(".delete-message-button")
    .forEach(function(button) {

        button.addEventListener(
            "click",
            function() {

                const messageId =
                    button.dataset.id;

                const confirmed =
                    confirm(
                        "Are you sure you want to delete this message?"
                    );

                if (!confirmed) {
                    return;
                }

                const formData =
                    new FormData();

                formData.append(
                    "action",
                    "delete"
                );

                formData.append(
                    "message_id",
                    messageId
                );

                fetch("message-api.php", {

                    method: "POST",

                    body: formData

                })
                .then(function(response) {

                    return response.json();

                })
                .then(function(data) {

                    if (data.success) {

                        window.location.reload();

                    } else {

                        alert(
                            data.message ||
                            "Unable to delete message."
                        );

                    }

                })
                .catch(function() {

                    alert(
                        "Something went wrong."
                    );

                });

            }
        );

    });

    const messageSearch =
    document.getElementById("messageSearch");

const messageTypeFilter =
    document.getElementById("messageTypeFilter");

const recipientFilter =
    document.getElementById("recipientFilter");

const clearMessageFilters =
    document.getElementById("clearMessageFilters");

const messageRows =
    Array.from(
        document.querySelectorAll(
            ".message-history-row"
        )
    );

const pagination =
    document.getElementById(
        "messagePagination"
    );


// =====================================
// PAGINATION SETTINGS
// =====================================

const rowsPerPage = 10;

let currentPage = 1;

let filteredRows = messageRows;


// =====================================
// FILTER MESSAGES
// =====================================

function filterMessages() {

    const searchValue =
        messageSearch.value
            .trim()
            .toLowerCase();

    const typeValue =
        messageTypeFilter.value
            .toLowerCase();

    const recipientValue =
        recipientFilter.value
            .toLowerCase();


    filteredRows = messageRows.filter(
        function(row) {

            const title =
                row.dataset.title || "";

            const message =
                row.dataset.message || "";

            const type =
                row.dataset.type || "";

            const recipient =
                row.dataset.recipient || "";


            const matchesSearch =
                searchValue === "" ||
                title.includes(searchValue) ||
                message.includes(searchValue);


            const matchesType =
                typeValue === "" ||
                type === typeValue;


            const matchesRecipient =
                recipientValue === "" ||
                recipient === recipientValue;


            return (
                matchesSearch &&
                matchesType &&
                matchesRecipient
            );

        }
    );


    currentPage = 1;

    renderMessages();

}


// =====================================
// RENDER MESSAGES
// =====================================

function renderMessages() {

    messageRows.forEach(
        function(row) {

            row.style.display = "none";

        }
    );


    const start =
        (currentPage - 1) *
        rowsPerPage;

    const end =
        start + rowsPerPage;


    const pageRows =
        filteredRows.slice(
            start,
            end
        );


    pageRows.forEach(
        function(row) {

            row.style.display = "";

        }
    );


    renderPagination();

}


// =====================================
// PAGINATION
// =====================================

function renderPagination() {

    pagination.innerHTML = "";


    const totalPages =
        Math.ceil(
            filteredRows.length /
            rowsPerPage
        );


    if (totalPages <= 1) {
        return;
    }


    // Previous button

    const previous =
        document.createElement("button");

    previous.innerHTML =
        '<i class="fa-solid fa-chevron-left"></i>';

    previous.disabled =
        currentPage === 1;

    previous.addEventListener(
        "click",
        function() {

            if (currentPage > 1) {

                currentPage--;

                renderMessages();

            }

        }
    );

    pagination.appendChild(previous);


    // Page numbers

    for (
        let page = 1;
        page <= totalPages;
        page++
    ) {

        const button =
            document.createElement("button");

        button.textContent = page;


        if (page === currentPage) {

            button.classList.add("active");

        }


        button.addEventListener(
            "click",
            function() {

                currentPage = page;

                renderMessages();

            }
        );


        pagination.appendChild(button);

        }


        // Next button

        const next =
            document.createElement("button");

        next.innerHTML =
            '<i class="fa-solid fa-chevron-right"></i>';

        next.disabled =
            currentPage === totalPages;

        next.addEventListener(
            "click",
            function() {

                if (
                    currentPage <
                    totalPages
                ) {

                    currentPage++;

                    renderMessages();

                }

            }
        );

        pagination.appendChild(next);

    }


    // =====================================
    // EVENTS
    // =====================================

    messageSearch.addEventListener(
        "input",
        filterMessages
    );

    messageTypeFilter.addEventListener(
        "change",
        filterMessages
    );

    recipientFilter.addEventListener(
        "change",
        filterMessages
    );


    // =====================================
    // CLEAR FILTERS
    // =====================================

    clearMessageFilters.addEventListener(
        "click",
        function() {

            messageSearch.value = "";

            messageTypeFilter.value = "";

            recipientFilter.value = "";

            filterMessages();

        }
    );


    // =====================================
    // INITIAL LOAD
    // =====================================

    filterMessages();

    const viewButtons =
    document.querySelectorAll(
        ".view-message-button"
    );

const messageModal =
    document.getElementById(
        "messageModal"
    );

const messageModalBody =
    document.getElementById(
        "messageModalBody"
    );

const closeMessageModal =
    document.getElementById(
        "closeMessageModal"
    );


viewButtons.forEach(
    function(button) {

        button.addEventListener(
            "click",
            function() {

                const messageId =
                    button.dataset.id;


                messageModal.classList.add(
                    "show"
                );


                messageModalBody.innerHTML = `
                    <div class="modal-loading">
                        Loading...
                    </div>
                `;


                const formData =
                    new FormData();


                formData.append(
                    "action",
                    "view"
                );

                formData.append(
                    "message_id",
                    messageId
                );


                fetch("message-api.php", {

                    method: "POST",

                    body: formData

                })

                .then(function(response) {

                    return response.json();

                })

                .then(function(data) {

                    if (!data.success) {

                        messageModalBody.innerHTML = `
                            <div class="modal-error">
                                ${data.message}
                            </div>
                        `;

                        return;

                    }


                    const message =
                        data.message;


                    messageModalBody.innerHTML = `

                        <div class="message-detail">

                            <h3>
                                ${escapeHtml(
                                    message.title
                                )}
                            </h3>


                            <div class="detail-meta">

                                <span>
                                    <strong>
                                        Type:
                                    </strong>

                                    ${escapeHtml(
                                        message.type
                                    )}
                                </span>


                                <span>
                                    <strong>
                                        Recipient:
                                    </strong>

                                    ${escapeHtml(
                                        message.recipient
                                    )}
                                </span>


                                <span>
                                    <strong>
                                        Sent:
                                    </strong>

                                    ${formatDate(
                                        message.created_at
                                    )}
                                </span>

                            </div>


                            <div class="detail-message">

                                ${escapeHtml(
                                    message.body
                                ).replace(
                                    /\n/g,
                                    "<br>"
                                )}

                            </div>


                            <div class="message-read-stats">

                                <div class="read-box">

                                    <strong>
                                        ${message.read_count}
                                    </strong>

                                    <span>
                                        Read
                                    </span>

                                </div>


                                <div class="unread-box">

                                    <strong>
                                        ${message.unread_count}
                                    </strong>

                                    <span>
                                        Unread
                                    </span>

                                </div>


                                <div class="percentage-box">

                                    <strong>
                                        ${message.read_percentage}%
                                    </strong>

                                    <span>
                                        Read Rate
                                    </span>

                                </div>

                            </div>

                        </div>

                    `;

                })

                .catch(function() {

                    messageModalBody.innerHTML = `
                        <div class="modal-error">
                            Something went wrong.
                        </div>
                    `;

                });

            }
        );

    }
);


// =====================================
// CLOSE MODAL
// =====================================

closeMessageModal.addEventListener(
    "click",
    function() {

        messageModal.classList.remove(
            "show"
        );

    }
);


messageModal.addEventListener(
    "click",
    function(event) {

        if (
            event.target ===
            messageModal
        ) {

            messageModal.classList.remove(
                "show"
            );

        }

    }
);


// =====================================
// HELPERS
// =====================================

function escapeHtml(value) {

    const div =
        document.createElement("div");

    div.textContent =
        value ?? "";

    return div.innerHTML;

}


function formatDate(value) {

    const date =
        new Date(
            value.replace(" ", "T")
        );

    if (
        Number.isNaN(
            date.getTime()
        )
    ) {

        return value;

    }

    return date.toLocaleString();

}

/* ==========================================
   EDIT MESSAGE
========================================== */

const editMessageModal =
    document.getElementById(
        "editMessageModal"
    );

const editMessageForm =
    document.getElementById(
        "editMessageForm"
    );

const closeEditMessageModal =
    document.getElementById(
        "closeEditMessageModal"
    );

const cancelEditMessage =
    document.getElementById(
        "cancelEditMessage"
    );


document.addEventListener(
    "click",
    function (event) {

        const button =
            event.target.closest(
                ".edit-message-button"
            );

        if (!button) {
            return;
        }

        const messageId =
            button.dataset.id;

        if (!messageId) {

            alert("Invalid message ID.");

            return;
        }

        const formData =
            new FormData();

        formData.append(
            "action",
            "view"
        );

        formData.append(
            "message_id",
            messageId
        );


        fetch("message-api.php", {

            method: "POST",

            body: formData

        })

        .then(function (response) {

            return response.json();

        })

        .then(function (data) {

            if (!data.success) {

                alert(data.message);

                return;

            }

            const message =
                data.message;


            document.getElementById(
                "editMessageId"
            ).value =
                message.id;


            document.getElementById(
                "editMessageTitle"
            ).value =
                message.title;


            document.getElementById(
                "editMessageType"
            ).value =
                message.type;


            document.getElementById(
                "editMessageBody"
            ).value =
                message.body;


            editMessageModal.classList.add(
                "show"
            );

        })

        .catch(function (error) {

            console.error(error);

            alert(
                "Unable to load message."
            );

        });

    }
);

editMessageForm.addEventListener(
    "submit",
    function (event) {

        event.preventDefault();

        const messageId =
            document.getElementById(
                "editMessageId"
            ).value;

        const title =
            document.getElementById(
                "editMessageTitle"
            ).value.trim();

        const message =
            document.getElementById(
                "editMessageBody"
            ).value.trim();

        const messageType =
            document.getElementById(
                "editMessageType"
            ).value;


        if (!messageId) {

            alert("Invalid message ID.");

            return;
        }


        if (!title || !message) {

            alert(
                "Title and message are required."
            );

            return;
        }


        const formData =
            new FormData();

        formData.append(
            "action",
            "edit"
        );

        formData.append(
            "message_id",
            messageId
        );

        formData.append(
            "title",
            title
        );

        formData.append(
            "message",
            message
        );

        formData.append(
            "message_type",
            messageType
        );


        fetch("message-api.php", {
    method: "POST",
    body: formData
})
.then(async function (response) {

    const text = await response.text();

    console.log("HTTP Status:", response.status);
    console.log("Raw API Response:", text);

    try {

        return JSON.parse(text);

    } catch (error) {

        throw new Error(
            "PHP returned invalid JSON:\n\n" + text
        );

    }

})
.then(function (data) {

    console.log("Parsed API Response:", data);

    if (!data.success) {

        alert(
            "Update failed:\n\n" +
            data.message
        );

        return;
    }

    alert(
        "Message updated successfully."
    );

    editMessageModal.classList.remove("show");

    location.reload();

})
.catch(function (error) {

    console.error(
        "EDIT MESSAGE ERROR:",
        error
    );

    alert(
        "Unable to update message.\n\n" +
        error.message
    );

});

    }
);

function closeEditModal() {

    editMessageModal.classList.remove(
        "show"
    );

}


if (closeEditMessageModal) {

    closeEditMessageModal.addEventListener(
        "click",
        closeEditModal
    );

}


if (cancelEditMessage) {

    cancelEditMessage.addEventListener(
        "click",
        closeEditModal
    );

}


if (editMessageModal) {

    editMessageModal.addEventListener(
        "click",
        function (event) {

            if (
                event.target ===
                editMessageModal
            ) {

                closeEditModal();

            }

        }
    );

}


/* ==========================================
   DELETE MESSAGE
========================================== */

const deleteMessageModal =
    document.getElementById("deleteMessageModal");

const deleteMessageId =
    document.getElementById("deleteMessageId");

const cancelDeleteMessage =
    document.getElementById("cancelDeleteMessage");

const confirmDeleteMessage =
    document.getElementById("confirmDeleteMessage");


document.addEventListener("click", function (event) {

    const button =
        event.target.closest(
            ".delete-message-button"
        );

    if (!button) {
        return;
    }

    const id = button.dataset.id;

    if (!id) {

        alert("Invalid message ID.");

        return;
    }

    deleteMessageId.value = id;

    deleteMessageModal.classList.add("show");

});

function closeDeleteModal() {

    deleteMessageModal.classList.remove("show");

    deleteMessageId.value = "";

}


cancelDeleteMessage.addEventListener(
    "click",
    closeDeleteModal
);


deleteMessageModal.addEventListener(
    "click",
    function (event) {

        if (event.target === deleteMessageModal) {

            closeDeleteModal();

        }

    }
);

confirmDeleteMessage.addEventListener(
    "click",
    function () {

        const id =
            deleteMessageId.value;

        if (!id) {

            alert("Invalid message ID.");

            return;
        }


        const formData =
            new FormData();

        formData.append(
            "action",
            "delete"
        );

        formData.append(
            "message_id",
            id
        );


        confirmDeleteMessage.disabled = true;

        confirmDeleteMessage.innerHTML = `
            <i class="fa-solid fa-spinner fa-spin"></i>
            Deleting...
        `;


        fetch("message-api.php", {

            method: "POST",

            body: formData

        })

        .then(function (response) {

            return response.json();

        })

        .then(function (data) {

            if (!data.success) {

                alert(data.message);

                return;
            }


            closeDeleteModal();

            alert(
                "Message deleted successfully."
            );

            location.reload();

        })

        .catch(function (error) {

            console.error(error);

            alert(
                "Unable to delete message."
            );

        })

        .finally(function () {

            confirmDeleteMessage.disabled =
                false;

            confirmDeleteMessage.innerHTML = `
                <i class="fa-solid fa-trash"></i>
                Delete Message
            `;

        });

    }
);



</script>

</body>

</html>