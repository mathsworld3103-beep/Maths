<?php

session_start();

require_once "../db.php";

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
   GET ACTIVITY LOGS
========================================== */

$stmt = $conn->prepare("
    SELECT
        a.id,
        a.action,
        a.module,
        a.description,
        a.ip_address,
        a.created_at,

        u.full_name,
        u.email

    FROM activity_logs a

    LEFT JOIN users u
        ON u.id = a.user_id

    ORDER BY a.created_at DESC
");

$stmt->execute();

$result =
    $stmt->get_result();

$activities = [];

while ($row = $result->fetch_assoc()) {

    $activities[] = $row;

}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>activity-log</title>

    <link
        rel="stylesheet"
        href="../css/activity-log.css"
    >

    
</head>
<body>
    <div class="page-header">

    <div>

        <span class="page-label">
            SYSTEM
        </span>

        <h1>
            Activity Log
        </h1>

        <p>
            Monitor administrative activities and system actions.
        </p>

    </div>

</div>


<div class="activity-filters">

    <div class="activity-search">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input
            type="text"
            id="activitySearch"
            placeholder="Search activity..."
        >

    </div>


    <select id="activityActionFilter">

        <option value="all">
            All Actions
        </option>

        <option value="LOGIN">
            Login
        </option>

        <option value="CREATE">
            Create
        </option>

        <option value="UPDATE">
            Update
        </option>

        <option value="DELETE">
            Delete
        </option>

        <option value="LOGOUT">
            Logout
        </option>

    </select>


    <select id="activityModuleFilter">

        <option value="all">
            All Modules
        </option>

        <option value="Authentication">
            Authentication
        </option>

        <option value="Messages">
            Messages
        </option>

        <option value="Materials">
            Materials
        </option>

        <option value="Past Papers">
            Past Papers
        </option>

        <option value="Model Papers">
            Model Papers
        </option>

        <option value="Students">
            Students
        </option>

        <option value="Settings">
            Settings
        </option>

    </select>

</div>


<div class="activity-table-container">

    <table class="activity-table">

        <thead>

            <tr>

                <th>Admin</th>

                <th>Action</th>

                <th>Module</th>

                <th>Description</th>

                <th>IP Address</th>

                <th>Date & Time</th>

            </tr>

        </thead>


        <tbody id="activityTableBody">

            <?php if (count($activities) > 0): ?>

                <?php foreach ($activities as $activity): ?>

                    <tr
                        class="activity-row"

                        data-search="<?php
                            echo htmlspecialchars(
                                strtolower(
                                    $activity["full_name"]
                                    . " "
                                    . $activity["description"]
                                )
                            );
                        ?>"

                        data-action="<?php
                            echo htmlspecialchars(
                                $activity["action"]
                            );
                        ?>"

                        data-module="<?php
                            echo htmlspecialchars(
                                $activity["module"]
                            );
                        ?>"
                    >

                        <td>

                            <div class="activity-admin">

                                <div class="activity-avatar">

                                    <?php
                                    echo strtoupper(
                                        substr(
                                            $activity["full_name"] ?? "A",
                                            0,
                                            1
                                        )
                                    );
                                    ?>

                                </div>

                                <div>

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $activity["full_name"]
                                            ?? "Unknown"
                                        );
                                        ?>
                                    </strong>

                                    <span>
                                        <?php
                                        echo htmlspecialchars(
                                            $activity["email"]
                                            ?? ""
                                        );
                                        ?>
                                    </span>

                                </div>

                            </div>

                        </td>


                        <td>

                            <span class="
                                activity-action
                                <?php
                                echo strtolower(
                                    $activity["action"]
                                );
                                ?>
                            ">

                                <?php
                                echo htmlspecialchars(
                                    $activity["action"]
                                );
                                ?>

                            </span>

                        </td>


                        <td>

                            <span class="activity-module">

                                <?php
                                echo htmlspecialchars(
                                    $activity["module"]
                                );
                                ?>

                            </span>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $activity["description"]
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $activity["ip_address"]
                                ?? "-"
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo date(
                                "d M Y, h:i A",
                                strtotime(
                                    $activity["created_at"]
                                )
                            );
                            ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td
                        colspan="6"
                        class="activity-empty"
                    >

                        <i class="fa-solid fa-clock-rotate-left"></i>

                        <h3>
                            No Activity Found
                        </h3>

                        <p>
                            Admin activities will appear here.
                        </p>

                    </td>

                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</div>

<script src="../js/activity-log.js"></script>

</body>
</html>