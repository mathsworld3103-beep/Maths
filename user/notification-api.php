<?php

require_once "auth-check.php";

header("Content-Type: application/json");

try {

    $userId = (int) $_SESSION["user_id"];

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

    echo json_encode([
        "success" => true,
        "unread_count" => $unreadCount
    ]);

} catch (Throwable $e) {

    echo json_encode([
        "success" => false,
        "unread_count" => 0
    ]);
}

?>