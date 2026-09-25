<?php

require_once "auth-check.php";

header("Content-Type: application/json");

try {

    // Get logged-in student ID
    $userId = (int) $_SESSION["user_id"];

    // Get student's grade
    $stmt = $conn->prepare("
        SELECT grade
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

    if (!$stmt) {
        throw new Exception(
            "Database prepare error: " . $conn->error
        );
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        $stmt->close();

        echo json_encode([
            "success" => false,
            "message" => "Student account not found."
        ]);

        exit;
    }

    $student = $result->fetch_assoc();

    $stmt->close();

    $studentGrade = $student["grade"];

    // Filters
    $search = trim($_GET["search"] ?? "");
    $subject = trim($_GET["subject"] ?? "");

    // Main query
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
        FROM model_papers
        WHERE grade = ?
    ";

    $params = [];
    $types = "s";

    $params[] = $studentGrade;

    // Search filter
    if ($search !== "") {

        $sql .= "
            AND (
                title LIKE ?
                OR description LIKE ?
                OR subject LIKE ?
                OR paper_type LIKE ?
            )
        ";

        $searchValue = "%" . $search . "%";

        $params[] = $searchValue;
        $params[] = $searchValue;
        $params[] = $searchValue;
        $params[] = $searchValue;

        $types .= "ssss";
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
        throw new Exception(
            "Database prepare error: " . $conn->error
        );
    }

    $stmt->bind_param(
        $types,
        ...$params
    );

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
        "papers" => $papers,
        "count" => count($papers)
    ]);

} catch (Throwable $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}

?>