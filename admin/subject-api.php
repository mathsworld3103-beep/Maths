<?php

session_start();

require_once __DIR__ . "/../db.php";

header("Content-Type: application/json");


/*
|--------------------------------------------------------------------------
| Security
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "You are not logged in."
    ]);

    exit;
}


if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {

    echo json_encode([
        "success" => false,
        "message" => "Access denied."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Get Action
|--------------------------------------------------------------------------
*/

$action = $_GET["action"] ?? "";


/*
|--------------------------------------------------------------------------
| GET SUBJECTS
|--------------------------------------------------------------------------
*/

if ($action === "get") {

    $search = trim($_GET["search"] ?? "");

    $level = trim($_GET["level"] ?? "all");

    $status = trim($_GET["status"] ?? "all");


    $sql = "
        SELECT
            id,
            subject_name,
            education_level,
            status,
            teacher,
            description,
            created_at
        FROM subjects
        WHERE 1 = 1
    ";


    $params = [];

    $types = "";


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($search !== "") {

        $sql .= "
            AND (
                subject_name LIKE ?
                OR teacher LIKE ?
                OR description LIKE ?
            )
        ";

        $searchValue = "%" . $search . "%";

        $params[] = $searchValue;
        $params[] = $searchValue;
        $params[] = $searchValue;

        $types .= "sss";
    }


    /*
    |--------------------------------------------------------------------------
    | Education Level Filter
    |--------------------------------------------------------------------------
    */

    if (
        $level !== "" &&
        $level !== "all"
    ) {

        $sql .= " AND education_level = ? ";

        $params[] = $level;

        $types .= "s";
    }


    /*
    |--------------------------------------------------------------------------
    | Status Filter
    |--------------------------------------------------------------------------
    */

    if (
        $status !== "" &&
        $status !== "all"
    ) {

        $sql .= " AND status = ? ";

        $params[] = $status;

        $types .= "s";
    }


    $sql .= "
        ORDER BY created_at DESC
    ";


    $stmt = $conn->prepare($sql);


    if (!empty($params)) {

        $stmt->bind_param(
            $types,
            ...$params
        );

    }


    $stmt->execute();

    $result = $stmt->get_result();


    $subjects = [];


    while ($row = $result->fetch_assoc()) {

        $subjects[] = $row;

    }


    echo json_encode([
        "success" => true,
        "subjects" => $subjects
    ]);


    $stmt->close();

    exit;
}


/*
|--------------------------------------------------------------------------
| STATISTICS
|--------------------------------------------------------------------------
*/

if ($action === "stats") {


    /*
    | Total Subjects
    */

    $result = $conn->query("
        SELECT COUNT(*) AS total
        FROM subjects
    ");

    $total = (int)$result->fetch_assoc()["total"];


    /*
    | Active Subjects
    */

    $result = $conn->query("
        SELECT COUNT(*) AS total
        FROM subjects
        WHERE status = 'active'
    ");

    $active = (int)$result->fetch_assoc()["total"];


    /*
    | A/L Subjects
    */

    $result = $conn->query("
        SELECT COUNT(*) AS total
        FROM subjects
        WHERE education_level = 'al'
    ");

    $alSubjects = (int)$result->fetch_assoc()["total"];


    /*
    | Total Enrollments
    |
    | Currently calculated from students' grade/records
    | when an enrollment table is not available.
    |
    | For now this returns 0.
    */

    $enrollments = 0;


    echo json_encode([
        "success" => true,
        "total" => $total,
        "active" => $active,
        "alSubjects" => $alSubjects,
        "enrollments" => $enrollments
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| VIEW SUBJECT
|--------------------------------------------------------------------------
*/

if ($action === "view") {

    $id = intval($_GET["id"] ?? 0);


    if ($id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid subject ID."
        ]);

        exit;
    }


    $stmt = $conn->prepare("
        SELECT
            id,
            subject_name,
            education_level,
            status,
            teacher,
            description,
            created_at
        FROM subjects
        WHERE id = ?
        LIMIT 1
    ");


    $stmt->bind_param(
        "i",
        $id
    );


    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows === 0) {

        echo json_encode([
            "success" => false,
            "message" => "Subject not found."
        ]);

        exit;
    }


    $subject = $result->fetch_assoc();


    echo json_encode([
        "success" => true,
        "subject" => $subject
    ]);


    $stmt->close();

    exit;
}


/*
|--------------------------------------------------------------------------
| ADD SUBJECT
|--------------------------------------------------------------------------
*/

if ($action === "add") {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );


    $subjectName = trim(
        $data["subject_name"] ?? ""
    );

    $educationLevel = trim(
        $data["education_level"] ?? ""
    );

    $status = trim(
        $data["status"] ?? "active"
    );

    $teacher = trim(
        $data["teacher"] ?? ""
    );

    $description = trim(
        $data["description"] ?? ""
    );


    /*
    | Validation
    */

    if (
        $subjectName === "" ||
        $educationLevel === "" ||
        $teacher === ""
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Please fill all required fields."
        ]);

        exit;
    }


    /*
    | Validate Level
    */

    if (
        !in_array(
            $educationLevel,
            ["school", "al"],
            true
        )
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid education level."
        ]);

        exit;
    }


    /*
    | Validate Status
    */

    if (
        !in_array(
            $status,
            ["active", "inactive"],
            true
        )
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid status."
        ]);

        exit;
    }


    /*
    | Check Duplicate
    */

    $check = $conn->prepare("
        SELECT id
        FROM subjects
        WHERE subject_name = ?
        LIMIT 1
    ");


    $check->bind_param(
        "s",
        $subjectName
    );


    $check->execute();

    $result = $check->get_result();


    if ($result->num_rows > 0) {

        echo json_encode([
            "success" => false,
            "message" => "This subject already exists."
        ]);

        exit;
    }


    /*
    | Insert
    */

    $stmt = $conn->prepare("
        INSERT INTO subjects
        (
            subject_name,
            education_level,
            status,
            teacher,
            description
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ");


    $stmt->bind_param(
        "sssss",
        $subjectName,
        $educationLevel,
        $status,
        $teacher,
        $description
    );


    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Subject added successfully.",
            "id" => $stmt->insert_id
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Unable to add subject."
        ]);

    }


    $stmt->close();

    exit;
}


/*
|--------------------------------------------------------------------------
| UPDATE SUBJECT
|--------------------------------------------------------------------------
*/

if ($action === "update") {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );


    $id = intval(
        $data["id"] ?? 0
    );

    $subjectName = trim(
        $data["subject_name"] ?? ""
    );

    $educationLevel = trim(
        $data["education_level"] ?? ""
    );

    $status = trim(
        $data["status"] ?? "active"
    );

    $teacher = trim(
        $data["teacher"] ?? ""
    );

    $description = trim(
        $data["description"] ?? ""
    );


    if ($id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid subject ID."
        ]);

        exit;
    }


    if (
        $subjectName === "" ||
        $educationLevel === "" ||
        $teacher === ""
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Please fill all required fields."
        ]);

        exit;
    }


    if (
        !in_array(
            $educationLevel,
            ["school", "al"],
            true
        )
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid education level."
        ]);

        exit;
    }


    if (
        !in_array(
            $status,
            ["active", "inactive"],
            true
        )
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid status."
        ]);

        exit;
    }


    /*
    | Check Duplicate Subject
    */

    $check = $conn->prepare("
        SELECT id
        FROM subjects
        WHERE subject_name = ?
        AND id != ?
        LIMIT 1
    ");


    $check->bind_param(
        "si",
        $subjectName,
        $id
    );


    $check->execute();

    $result = $check->get_result();


    if ($result->num_rows > 0) {

        echo json_encode([
            "success" => false,
            "message" => "Another subject already uses this name."
        ]);

        exit;
    }


    /*
    | Update
    */

    $stmt = $conn->prepare("
        UPDATE subjects
        SET
            subject_name = ?,
            education_level = ?,
            status = ?,
            teacher = ?,
            description = ?
        WHERE id = ?
    ");


    $stmt->bind_param(
        "sssssi",
        $subjectName,
        $educationLevel,
        $status,
        $teacher,
        $description,
        $id
    );


    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Subject updated successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Unable to update subject."
        ]);

    }


    $stmt->close();

    exit;
}


/*
|--------------------------------------------------------------------------
| DELETE SUBJECT
|--------------------------------------------------------------------------
*/

if ($action === "delete") {

    $id = intval(
        $_GET["id"] ?? 0
    );


    if ($id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid subject ID."
        ]);

        exit;
    }


    $stmt = $conn->prepare("
        DELETE FROM subjects
        WHERE id = ?
    ");


    $stmt->bind_param(
        "i",
        $id
    );


    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Subject deleted successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Unable to delete subject."
        ]);

    }


    $stmt->close();

    exit;
}


/*
|--------------------------------------------------------------------------
| Invalid Action
|--------------------------------------------------------------------------
*/

echo json_encode([
    "success" => false,
    "message" => "Invalid API action."
]);

?>