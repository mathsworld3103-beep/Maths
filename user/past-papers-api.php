<?php

require_once "auth-check.php";

header("Content-Type: application/json");

try {

    $userId = (int) $_SESSION["user_id"];

    // Get student's grade
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
            "message" => "Student information not found."
        ]);
        exit;
    }

    $student = $result->fetch_assoc();
    $studentGrade = $student["grade"];

    $stmt->close();

    // Filters
    $search = trim($_GET["search"] ?? "");
    $subject = trim($_GET["subject"] ?? "");

    $sql = "
        SELECT
            id,
            title,
            description,
            subject,
            grade,
            paper_type,
            paper_year,
            file_name,
            file_path,
            created_at
        FROM past_papers
        WHERE grade = ?
    ";

    $params = [$studentGrade];
    $types = "s";

    // Search
    if ($search !== "") {

        $sql .= "
            AND (
                title LIKE ?
                OR description LIKE ?
                OR subject LIKE ?
            )
        ";

        $searchValue = "%" . $search . "%";

        $params[] = $searchValue;
        $params[] = $searchValue;
        $params[] = $searchValue;

        $types .= "sss";
    }

    // Subject filter
    if ($subject !== "") {

        $sql .= " AND subject = ?";

        $params[] = $subject;
        $types .= "s";
    }

    $sql .= "
        ORDER BY paper_year DESC, created_at DESC
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param($types, ...$params);

    $stmt->execute();

    $result = $stmt->get_result();

    $papers = [];

    while ($row = $result->fetch_assoc()) {
        $papers[] = $row;
    }

    $stmt->close();

    echo json_encode([
        "success" => true,
        "grade" => $studentGrade,
        "count" => count($papers),
        "papers" => $papers
    ]);

} catch (Throwable $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}

?>