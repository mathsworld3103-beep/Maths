<?php

require_once "activity-log-helper.php";

header("Content-Type: application/json");

try {

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {

        echo json_encode([
            "success" => false,
            "message" => "Invalid request method."
        ]);

        exit;
    }

    $action = $_POST["action"] ?? "";

    $messageId = isset($_POST["message_id"])
        ? (int) $_POST["message_id"]
        : 0;

    // =====================================
    // VALIDATE MESSAGE ID
    // =====================================

    if ($messageId <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid message ID."
        ]);

        exit;
    }


    // =====================================
    // VIEW MESSAGE
    // =====================================

    if ($action === "view") {

        $stmt = $conn->prepare("
            SELECT
                m.id,
                m.title,
                m.message,
                m.message_type,
                m.created_at,
                m.receiver_id,

                u.full_name AS receiver_name

            FROM messages m

            LEFT JOIN users u
                ON u.id = m.receiver_id

            WHERE m.id = ?

            LIMIT 1
        ");

        if (!$stmt) {

            throw new Exception(
                "Database error: " . $conn->error
            );
        }

        $stmt->bind_param(
            "i",
            $messageId
        );

        $stmt->execute();

        $result = $stmt->get_result();


        if ($result->num_rows === 0) {

            $stmt->close();

            echo json_encode([
                "success" => false,
                "message" => "Message not found.",
                "debug_id" => $messageId
            ]);

            exit;
        }


        $message = $result->fetch_assoc();

        $stmt->close();


        // =====================================
        // RECIPIENT COUNT
        // =====================================

        if ($message["receiver_id"] === null) {

            // Message sent to all students

            $stmt = $conn->prepare("
                SELECT COUNT(*) AS total
                FROM users
                WHERE role = 'student'
            ");

            $stmt->execute();

            $result = $stmt->get_result();

            $row = $result->fetch_assoc();

            $recipientCount =
                (int) ($row["total"] ?? 0);

            $stmt->close();

        } else {

            // Message sent to one student

            $recipientCount = 1;

        }


        // =====================================
        // READ COUNT
        // =====================================

        $stmt = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM message_reads
            WHERE message_id = ?
        ");

        $stmt->bind_param(
            "i",
            $messageId
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        $readCount =
            (int) ($row["total"] ?? 0);

        $stmt->close();


        // =====================================
        // UNREAD COUNT
        // =====================================

        $unreadCount = max(
            0,
            $recipientCount - $readCount
        );


        // =====================================
        // READ PERCENTAGE
        // =====================================

        $readPercentage = 0;

        if ($recipientCount > 0) {

            $readPercentage = round(
                (
                    $readCount /
                    $recipientCount
                ) * 100
            );

        }


        // =====================================
        // RECIPIENT NAME
        // =====================================

        if ($message["receiver_id"] === null) {

            $recipient = "All Students";

        } else {

            $recipient =
                $message["receiver_name"]
                ?: "Student";

        }


        // =====================================
        // RESPONSE
        // =====================================

        echo json_encode([
            "success" => true,

            "message" => [
                "id" =>
                    (int) $message["id"],

                "title" =>
                    $message["title"],

                "body" =>
                    $message["message"],

                "type" =>
                    $message["message_type"],

                "created_at" =>
                    $message["created_at"],

                "recipient" =>
                    $recipient,

                "recipient_count" =>
                    $recipientCount,

                "read_count" =>
                    $readCount,

                "unread_count" =>
                    $unreadCount,

                "read_percentage" =>
                    $readPercentage
            ]
        ]);

        exit;
    }


    // =====================================
    // DELETE MESSAGE
    // =====================================

    if ($action === "delete") {

        $stmt = $conn->prepare("
            DELETE FROM messages
            WHERE id = ?
        ");

        if (!$stmt) {

            throw new Exception(
                "Database error: " . $conn->error
            );
        }

        $stmt->bind_param(
            "i",
            $messageId
        );

        $stmt->execute();

        $deleted =
            $stmt->affected_rows;

        $stmt->close();


        if ($deleted === 0) {

            echo json_encode([
                "success" => false,
                "message" => "Message not found."
            ]);

            exit;
        }


        echo json_encode([
            "success" => true,
            "message" =>
                "Message deleted successfully."
        ]);

        exit;
    }


    // =====================================
    // INVALID ACTION
    // =====================================

    echo json_encode([
        "success" => false,
        "message" => "Invalid action."
    ]);

} catch (Throwable $e) {

    echo json_encode([
        "success" => false,
        "message" =>
            "Server error: " . $e->getMessage()
    ]);

}


/* ==========================================
   EDIT MESSAGE
========================================== */

if ($action === "edit") {

    $title = trim($_POST["title"] ?? "");

    $message = trim($_POST["message"] ?? "");

    $messageType =
        trim($_POST["message_type"] ?? "");


    if ($messageId <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid message ID."
        ]);

        exit;
    }


    if ($title === "") {

        echo json_encode([
            "success" => false,
            "message" => "Message title is required."
        ]);

        exit;
    }


    if ($message === "") {

        echo json_encode([
            "success" => false,
            "message" => "Message body is required."
        ]);

        exit;
    }


    $allowedTypes = [
        "announcement",
        "notice",
        "exam",
        "class",
        "general"
    ];


    if (!in_array(
        $messageType,
        $allowedTypes,
        true
    )) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid message type."
        ]);

        exit;
    }


    $stmt = $conn->prepare("
        UPDATE messages

        SET
            title = ?,
            message = ?,
            message_type = ?

        WHERE id = ?
    ");


    if (!$stmt) {

        throw new Exception(
            "Prepare failed: " .
            $conn->error
        );
    }


    $stmt->bind_param(
        "sssi",
        $title,
        $message,
        $messageType,
        $messageId
    );


    if (!$stmt->execute()) {

        throw new Exception(
            "Update failed: " .
            $stmt->error
        );
    }


    $stmt->close();


    echo json_encode([
        "success" => true,
        "message" =>
            "Message updated successfully."
    ]);

    exit;

}

?>