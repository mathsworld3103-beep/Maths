<?php

session_start();

require_once __DIR__ . "/../db.php";

header("Content-Type: application/json");


// =====================================
// SECURITY
// =====================================

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


// =====================================
// ACTION
// =====================================

$action = $_GET["action"] ?? "";


// =====================================================
// GET TEACHERS
// =====================================================

if ($action === "get") {

    $search =
        trim($_GET["search"] ?? "");

    $subject =
        trim($_GET["subject"] ?? "all");

    $status =
        trim($_GET["status"] ?? "all");


    $sql = "
        SELECT
            id,
            full_name,
            email,
            phone,
            subject,
            qualification,
            status,
            created_at
        FROM teachers
        WHERE 1 = 1
    ";


    $params = [];

    $types = "";


    // =====================================
    // SEARCH
    // =====================================

    if ($search !== "") {

        $sql .= "
            AND (
                full_name LIKE ?
                OR email LIKE ?
                OR phone LIKE ?
            )
        ";

        $searchValue =
            "%" . $search . "%";


        $params[] = $searchValue;
        $params[] = $searchValue;
        $params[] = $searchValue;

        $types .= "sss";
    }


    // =====================================
    // SUBJECT FILTER
    // =====================================

    if (
        $subject !== "" &&
        $subject !== "all"
    ) {

        $sql .= "
            AND subject = ?
        ";

        $params[] = $subject;

        $types .= "s";
    }


    // =====================================
    // STATUS FILTER
    // =====================================

    if (
        $status !== "" &&
        $status !== "all"
    ) {

        $sql .= "
            AND status = ?
        ";

        $params[] = $status;

        $types .= "s";
    }


    $sql .= "
        ORDER BY created_at DESC
    ";


    $stmt =
        $conn->prepare($sql);


    if (!empty($params)) {

        $stmt->bind_param(
            $types,
            ...$params
        );

    }


    $stmt->execute();


    $result =
        $stmt->get_result();


    $teachers = [];


    while (
        $row =
        $result->fetch_assoc()
    ) {

        $teachers[] = $row;

    }


    echo json_encode([

        "success" => true,

        "teachers" => $teachers

    ]);


    $stmt->close();

    exit;
}



// =====================================================
// STATISTICS
// =====================================================

if ($action === "stats") {


    // =====================================
    // TOTAL
    // =====================================

    $result =
        $conn->query("
            SELECT COUNT(*) AS total
            FROM teachers
        ");

    $total =
        (int)$result
            ->fetch_assoc()["total"];


    // =====================================
    // ACTIVE
    // =====================================

    $result =
        $conn->query("
            SELECT COUNT(*) AS total
            FROM teachers
            WHERE status = 'Active'
        ");

    $active =
        (int)$result
            ->fetch_assoc()["total"];


    // =====================================
    // PENDING
    // =====================================

    $result =
        $conn->query("
            SELECT COUNT(*) AS total
            FROM teachers
            WHERE status = 'Pending'
        ");

    $pending =
        (int)$result
            ->fetch_assoc()["total"];


    // =====================================
    // NEW THIS MONTH
    // =====================================

    $result =
        $conn->query("
            SELECT COUNT(*) AS total
            FROM teachers
            WHERE
                MONTH(created_at) =
                MONTH(CURRENT_DATE())

                AND

                YEAR(created_at) =
                YEAR(CURRENT_DATE())
        ");

    $newThisMonth =
        (int)$result
            ->fetch_assoc()["total"];


    echo json_encode([

        "success" => true,

        "total" => $total,

        "active" => $active,

        "pending" => $pending,

        "newThisMonth" => $newThisMonth

    ]);


    exit;
}



// =====================================================
// GET SINGLE TEACHER
// =====================================================

if ($action === "view") {


    $id =
        intval($_GET["id"] ?? 0);


    if ($id <= 0) {

        echo json_encode([

            "success" => false,

            "message" => "Invalid teacher ID."

        ]);

        exit;
    }


    $stmt =
        $conn->prepare("
            SELECT
                id,
                full_name,
                email,
                phone,
                subject,
                qualification,
                status,
                created_at
            FROM teachers
            WHERE id = ?
            LIMIT 1
        ");


    $stmt->bind_param(
        "i",
        $id
    );


    $stmt->execute();


    $result =
        $stmt->get_result();


    if (
        $result->num_rows === 0
    ) {

        echo json_encode([

            "success" => false,

            "message" => "Teacher not found."

        ]);

        exit;
    }


    $teacher =
        $result->fetch_assoc();


    echo json_encode([

        "success" => true,

        "teacher" => $teacher

    ]);


    exit;
}



// =====================================================
// ADD TEACHER
// =====================================================

if ($action === "add") {


    $data =
        json_decode(
            file_get_contents("php://input"),
            true
        );


    $name =
        trim(
            $data["full_name"] ?? ""
        );


    $email =
        trim(
            $data["email"] ?? ""
        );


    $subject =
        trim(
            $data["subject"] ?? ""
        );


    $phone =
        trim(
            $data["phone"] ?? ""
        );


    $qualification =
        trim(
            $data["qualification"] ?? ""
        );


    $status =
        trim(
            $data["status"] ?? "Active"
        );


    // =====================================
    // VALIDATION
    // =====================================

    if (
        $name === "" ||
        $email === "" ||
        $subject === "" ||
        $phone === "" ||
        $qualification === ""
    ) {

        echo json_encode([

            "success" => false,

            "message" =>
                "Please fill all required fields."

        ]);

        exit;
    }


    // =====================================
    // CHECK EMAIL
    // =====================================

    $check =
        $conn->prepare("
            SELECT id
            FROM teachers
            WHERE email = ?
            LIMIT 1
        ");


    $check->bind_param(
        "s",
        $email
    );


    $check->execute();


    $result =
        $check->get_result();


    if (
        $result->num_rows > 0
    ) {

        echo json_encode([

            "success" => false,

            "message" =>
                "This teacher email already exists."

        ]);

        exit;
    }


    // =====================================
    // INSERT
    // =====================================

    $stmt =
        $conn->prepare("
            INSERT INTO teachers
            (
                full_name,
                email,
                phone,
                subject,
                qualification,
                status
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )
        ");


    $stmt->bind_param(
        "ssssss",
        $name,
        $email,
        $phone,
        $subject,
        $qualification,
        $status
    );


    if ($stmt->execute()) {

        echo json_encode([

            "success" => true,

            "message" =>
                "Teacher added successfully.",

            "id" =>
                $stmt->insert_id

        ]);

    } else {

        echo json_encode([

            "success" => false,

            "message" =>
                "Unable to add teacher."

        ]);

    }


    exit;
}



// =====================================================
// UPDATE TEACHER
// =====================================================

if ($action === "update") {


    $data =
        json_decode(
            file_get_contents("php://input"),
            true
        );


    $id =
        intval(
            $data["id"] ?? 0
        );


    $name =
        trim(
            $data["full_name"] ?? ""
        );


    $email =
        trim(
            $data["email"] ?? ""
        );


    $subject =
        trim(
            $data["subject"] ?? ""
        );


    $phone =
        trim(
            $data["phone"] ?? ""
        );


    $qualification =
        trim(
            $data["qualification"] ?? ""
        );


    $status =
        trim(
            $data["status"] ?? "Active"
        );


    if ($id <= 0) {

        echo json_encode([

            "success" => false,

            "message" =>
                "Invalid teacher ID."

        ]);

        exit;
    }


    if (
        $name === "" ||
        $email === "" ||
        $subject === ""
    ) {

        echo json_encode([

            "success" => false,

            "message" =>
                "Please fill all required fields."

        ]);

        exit;
    }


    // =====================================
    // CHECK EMAIL FOR ANOTHER TEACHER
    // =====================================

    $check =
        $conn->prepare("
            SELECT id
            FROM teachers
            WHERE email = ?
            AND id != ?
            LIMIT 1
        ");


    $check->bind_param(
        "si",
        $email,
        $id
    );


    $check->execute();


    $result =
        $check->get_result();


    if (
        $result->num_rows > 0
    ) {

        echo json_encode([

            "success" => false,

            "message" =>
                "Another teacher already uses this email."

        ]);

        exit;
    }


    // =====================================
    // UPDATE
    // =====================================

    $stmt =
        $conn->prepare("
            UPDATE teachers
            SET
                full_name = ?,
                email = ?,
                phone = ?,
                subject = ?,
                qualification = ?,
                status = ?
            WHERE id = ?
        ");


    $stmt->bind_param(
        "ssssssi",
        $name,
        $email,
        $phone,
        $subject,
        $qualification,
        $status,
        $id
    );


    if ($stmt->execute()) {

        echo json_encode([

            "success" => true,

            "message" =>
                "Teacher updated successfully."

        ]);

    } else {

        echo json_encode([

            "success" => false,

            "message" =>
                "Unable to update teacher."

        ]);

    }


    exit;
}



// =====================================================
// DELETE TEACHER
// =====================================================

if ($action === "delete") {


    $id =
        intval(
            $_GET["id"] ?? 0
        );


    if ($id <= 0) {

        echo json_encode([

            "success" => false,

            "message" =>
                "Invalid teacher ID."

        ]);

        exit;
    }


    $stmt =
        $conn->prepare("
            DELETE FROM teachers
            WHERE id = ?
        ");


    $stmt->bind_param(
        "i",
        $id
    );


    if ($stmt->execute()) {

        echo json_encode([

            "success" => true,

            "message" =>
                "Teacher deleted successfully."

        ]);

    } else {

        echo json_encode([

            "success" => false,

            "message" =>
                "Unable to delete teacher."

        ]);

    }


    exit;
}



// =====================================================
// INVALID ACTION
// =====================================================

echo json_encode([

    "success" => false,

    "message" => "Invalid API action."

]);

?>