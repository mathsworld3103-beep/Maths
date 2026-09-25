<?php

require_once "auth-check.php";

header("Content-Type: application/json");

try {

    $userId = (int) $_SESSION["user_id"];

    $messageId = (int) ($_POST["message_id"] ?? 0);


    // =====================================
    // VALIDATE MESSAGE ID
    // =====================================

    if ($messageId <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid message."
        ]);

        exit;
    }


    // =====================================
    // CHECK MESSAGE BELONGS TO STUDENT
    // =====================================

    $stmt = $conn->prepare("
        SELECT id
        FROM messages
        WHERE id = ?
        AND (
            receiver_id = ?
            OR receiver_id IS NULL
        )
        LIMIT 1
    ");

    $stmt->bind_param(
        "ii",
        $messageId,
        $userId
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {

        $stmt->close();

        echo json_encode([
            "success" => false,
            "message" => "Message not found."
        ]);

        exit;
    }

    $stmt->close();


    // =====================================
    // SAVE READ STATUS FOR THIS STUDENT
    // =====================================

    $stmt = $conn->prepare("
        INSERT INTO message_reads (
            message_id,
            user_id
        )
        VALUES (?, ?)

        ON DUPLICATE KEY UPDATE
            read_at = CURRENT_TIMESTAMP
    ");

    $stmt->bind_param(
        "ii",
        $messageId,
        $userId
    );

    $stmt->execute();

    $stmt->close();


    // =====================================
    // SUCCESS
    // =====================================

    echo json_encode([
        "success" => true,
        "message" => "Message marked as read."
    ]);

} catch (Throwable $e) {

    echo json_encode([
        "success" => false,
        "message" => "Unable to update message."
    ]);
}

?>