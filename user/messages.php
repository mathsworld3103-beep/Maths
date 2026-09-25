<?php

require_once "auth-check.php";


// =====================================
// LOGGED-IN USER
// =====================================

$userId = (int) $_SESSION["user_id"];


// =====================================
// GET STUDENT DETAILS
// =====================================

$stmt = $conn->prepare("
    SELECT
        full_name,
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

    header("Location: ../logout.php");
    exit;
}

$student = $result->fetch_assoc();

$stmt->close();

$studentName = $student["full_name"] ?? "";
$studentGrade = $student["grade"] ?? "";


// =====================================
// GET MESSAGES
// =====================================

$stmt = $conn->prepare("
    SELECT
        m.id,
        m.title,
        m.message,
        m.message_type,
        m.created_at,

        CASE
            WHEN mr.id IS NULL THEN 'unread'
            ELSE 'read'
        END AS user_status

    FROM messages m

    LEFT JOIN message_reads mr
        ON mr.message_id = m.id
        AND mr.user_id = ?

    WHERE
        m.receiver_id = ?
        OR m.receiver_id IS NULL

    ORDER BY m.created_at DESC
");

$stmt->bind_param(
    "ii",
    $userId,
    $userId
);

$stmt->execute();

$result = $stmt->get_result();

$messages = [];

while ($row = $result->fetch_assoc()) {

    $messages[] = $row;

}

$stmt->close();


// =====================================
// GET UNREAD COUNT
// =====================================

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total

    FROM messages m

    LEFT JOIN message_reads mr
        ON mr.message_id = m.id
        AND mr.user_id = ?

    WHERE
        (
            m.receiver_id = ?
            OR m.receiver_id IS NULL
        )

        AND mr.id IS NULL
");

$stmt->bind_param(
    "ii",
    $userId,
    $userId
);

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$unreadCount = (int) ($row["total"] ?? 0);

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Messages | MathsWorld</title>

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
            max-width: 1400px;
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




        .messages-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .messages-header {
            background: linear-gradient(
                135deg,
                #0b1f3a,
                #12345b
            );

            color: #ffffff;
            border-radius: 18px;
            padding: 28px;

            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-bottom: 25px;
        }

        .messages-header h2 {
            margin-bottom: 7px;
        }

        .messages-header p {
            color: #dbeafe;
        }

        .unread-badge {
            min-width: 55px;
            height: 55px;

            border-radius: 50%;

            background: #38bdf8;
            color: #0b1f3a;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 20px;
            font-weight: 800;
        }

        .message-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 15px;

            padding: 22px;
            margin-bottom: 15px;

            transition: 0.2s;
        }

        .message-card:hover {
            transform: translateY(-2px);

            box-shadow:
                0 8px 25px
                rgba(15, 23, 42, 0.07);
        }

        .message-card.unread {
            border-left: 4px solid #38bdf8;
        }

        .message-top {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 10px;
        }

        .message-title {
            color: #0b1f3a;
            font-size: 18px;
            font-weight: 700;
        }

        .message-date {
            color: #64748b;
            font-size: 13px;
            white-space: nowrap;
        }

        .message-body {
            color: #475569;
            line-height: 1.7;
        }

        .message-type {
            display: inline-block;
            margin-top: 15px;

            padding: 5px 10px;

            background: #e0f2fe;
            color: #0369a1;

            border-radius: 20px;

            font-size: 12px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .empty-messages {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 15px;

            padding: 60px 20px;
            text-align: center;
        }

        .empty-messages i {
            font-size: 45px;
            color: #94a3b8;
            margin-bottom: 15px;
        }

        .empty-messages h3 {
            color: #0b1f3a;
            margin-bottom: 8px;
        }

        .empty-messages p {
            color: #64748b;
        }

        .mark-read-button {
            margin-top: 15px;

            border: none;
            border-radius: 8px;

            padding: 9px 14px;

            background: #0b1f3a;
            color: #ffffff;

            font-size: 13px;
            font-weight: 600;

            cursor: pointer;
        }

        .mark-read-button:hover {
            background: #12345b;
        }

        .read-label {
            display: inline-block;

            margin-top: 15px;

            color: #16a34a;

            font-size: 13px;
            font-weight: 600;
        }

        @media (max-width: 600px) {

            .messages-header {
                padding: 20px;
            }

            .message-top {
                flex-direction: column;
                gap: 6px;
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


    <!-- MAIN -->

    <main class="main-content">

        <header class="top-header">

            <div>

                <h1>
                    Messages
                </h1>

                <p>
                    Your announcements and notifications
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

        </header>


        <section class="page-content">

            <div class="messages-container">

                <div class="messages-header">

                    <div>

                        <h2>
                            Your Messages
                        </h2>

                        <p>
                            Stay updated with MathsWorld
                        </p>

                    </div>

                    <div class="unread-badge">

                        <?php
                        echo $unreadCount;
                        ?>

                    </div>

                </div>


                <?php if (count($messages) > 0): ?>

                    <?php foreach ($messages as $message): ?>

                    <div class="message-card
                        <?php echo $message["user_status"] === "unread" ? "unread" : ""; ?>">

                        <div class="message-header">

                            <h3>
                                <?php echo htmlspecialchars($message["title"]); ?>
                            </h3>

                            <span>
                                <?php echo htmlspecialchars($message["message_type"]); ?>
                            </span>

                        </div>

                        <p class="message-date">
                            <?php
                            echo date(
                                "d M Y, h:i A",
                                strtotime($message["created_at"])
                            );
                            ?>
                        </p>

                        <p class="message-body">
                            <?php
                            echo nl2br(
                                htmlspecialchars($message["message"])
                            );
                            ?>
                        </p>


                        <?php if ($message["user_status"] === "unread"): ?>

                            <button
                                type="button"
                                class="mark-read-button"
                                data-message-id="<?php echo $message["id"]; ?>"
                            >
                                <i class="fa-solid fa-check"></i>
                                Mark as Read
                            </button>

                        <?php else: ?>

                            <span class="read-label">
                                <i class="fa-solid fa-circle-check"></i>
                                Read
                            </span>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

                <?php else: ?>

                    <div class="empty-messages">

                        <i class="fa-regular fa-message"></i>

                        <h3>
                            No Messages
                        </h3>

                        <p>
                            You don't have any messages
                            or announcements yet.
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </section>

    </main>

</div>


<script>

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

const markReadButtons =
    document.querySelectorAll(".mark-read-button");

markReadButtons.forEach(function (button) {

    button.addEventListener(
        "click",
        function () {

            const messageId =
                button.dataset.messageId;

            const formData =
                new FormData();

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

                if (data.success) {

                    // Reload page so the unread
                    // count and message status update.

                    window.location.reload();

                } else {

                    alert(
                        data.message ||
                        "Unable to mark message as read."
                    );

                }

            })

            .catch(function () {

                alert(
                    "Something went wrong."
                );

            });

        }
    );

});

</script>

<script src="../js/notifications.js"></script>

</body>

</html>