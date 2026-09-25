<?php

require_once "auth-check.php";

header("Content-Type: application/json");

try {

    $userId = (int) $_SESSION["user_id"];

    $stmt = $conn->prepare("
        SELECT grade
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $userId);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {

        echo json_encode([
            "success" => false,
            "message" => "Student account not found."
        ]);

        exit;
    }

    $student = $result->fetch_assoc();

    $studentGrade = $student["grade"];


    $search = trim($_GET["search"] ?? "");
    $subject = trim($_GET["subject"] ?? "");


    $sql = "
        SELECT
            id,
            title,
            description,
            subject,
            grade,
            material_type,
            material_year,
            file_name,
            file_path,
            created_at
        FROM materials
        WHERE grade = ?
    ";


    $types = "s";

    $params = [$studentGrade];


    if ($search !== "") {

        $sql .= "
            AND (
                title LIKE ?
                OR description LIKE ?
                OR subject LIKE ?
            )
        ";

        $searchValue = "%" . $search . "%";

        $types .= "sss";

        $params[] = $searchValue;
        $params[] = $searchValue;
        $params[] = $searchValue;
    }


    if ($subject !== "") {

        $sql .= " AND subject = ?";

        $types .= "s";

        $params[] = $subject;
    }


    $sql .= "
        ORDER BY created_at DESC
    ";


    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        $types,
        ...$params
    );

    $stmt->execute();

    $result = $stmt->get_result();


    $materials = [];

    while ($row = $result->fetch_assoc()) {

        $materials[] = $row;
    }


    echo json_encode([

        "success" => true,

        "grade" => $studentGrade,

        "count" => count($materials),

        "materials" => $materials

    ]);

} catch (Throwable $e) {

    echo json_encode([

        "success" => false,

        "message" => "Server error."

    ]);

}

?>