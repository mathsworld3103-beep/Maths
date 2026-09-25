<?php

function logActivity(
    $conn,
    $userId,
    $action,
    $module,
    $description
) {

    $ipAddress =
        $_SERVER["REMOTE_ADDR"] ?? null;

    $userAgent =
        $_SERVER["HTTP_USER_AGENT"] ?? null;


    $stmt = $conn->prepare("
        INSERT INTO activity_logs (
            user_id,
            action,
            module,
            description,
            ip_address,
            user_agent
        )

        VALUES (?, ?, ?, ?, ?, ?)
    ");


    if (!$stmt) {
        return false;
    }


    $stmt->bind_param(
        "isssss",
        $userId,
        $action,
        $module,
        $description,
        $ipAddress,
        $userAgent
    );


    $success =
        $stmt->execute();


    $stmt->close();


    return $success;
}

?>