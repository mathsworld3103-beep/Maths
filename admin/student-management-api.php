<?php

session_start();

require_once "../db.php";

header("Content-Type: application/json; charset=UTF-8");


// =====================================================
// CHECK LOGIN
// =====================================================

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);

    exit;
}


// =====================================================
// CHECK ADMIN
// =====================================================

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


// =====================================================
// GET ACTION
// =====================================================

$action = $_GET["action"] ?? $_POST["action"] ?? "";


// =====================================================
// LIST STUDENTS
// =====================================================

if ($action === "list") {

    $sql = "
        SELECT
            id,
            full_name,
            email,
            grade,
            phone,
            status,
            created_at
        FROM users
        WHERE role = 'student'
        ORDER BY id DESC
    ";

    $result = $conn->query($sql);

    $students = [];

    if ($result) {

        while ($row = $result->fetch_assoc()) {

            $students[] = [
                "id" => (int)$row["id"],
                "full_name" => $row["full_name"],
                "email" => $row["email"],
                "grade" => $row["grade"] ?? "",
                "phone" => $row["phone"] ?? "",
                "status" => $row["status"] ?? "Active",
                "created_at" => $row["created_at"]
            ];
        }
    }

    echo json_encode([
        "success" => true,
        "students" => $students
    ]);

    exit;
}


// =====================================================
// GET ONE STUDENT
// =====================================================

if ($action === "get") {

    $id = intval($_GET["id"] ?? 0);

    if ($id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid student ID."
        ]);

        exit;
    }

    $stmt = $conn->prepare("
        SELECT
            id,
            full_name,
            email,
            grade,
            phone,
            status,
            created_at
        FROM users
        WHERE id = ?
        AND role = 'student'
        LIMIT 1
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {

        echo json_encode([
            "success" => false,
            "message" => "Student not found."
        ]);

        exit;
    }

    $student = $result->fetch_assoc();

    echo json_encode([
        "success" => true,
        "student" => $student
    ]);

    exit;
}


// =====================================================
// ADD STUDENT
// =====================================================

if ($action === "add") {

    $name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $grade = trim($_POST["grade"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $status = trim($_POST["status"] ?? "Active");
    $password = $_POST["password"] ?? "";


    // -------------------------
    // Validation
    // -------------------------

    if ($name === "" || $email === "" || $password === "") {

        echo json_encode([
            "success" => false,
            "message" => "Name, email and password are required."
        ]);

        exit;
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        echo json_encode([
            "success" => false,
            "message" => "Please enter a valid email."
        ]);

        exit;
    }


    if (strlen($password) < 6) {

        echo json_encode([
            "success" => false,
            "message" => "Password must contain at least 6 characters."
        ]);

        exit;
    }


    $allowedStatuses = [
        "Active",
        "Pending",
        "Suspended"
    ];

    if (!in_array($status, $allowedStatuses, true)) {
        $status = "Active";
    }


    // -------------------------
    // Check duplicate email
    // -------------------------

    $check = $conn->prepare("
        SELECT id
        FROM users
        WHERE email = ?
        LIMIT 1
    ");

    $check->bind_param("s", $email);
    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows > 0) {

        echo json_encode([
            "success" => false,
            "message" => "This email is already registered."
        ]);

        exit;
    }


    // -------------------------
    // Hash password
    // -------------------------

    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );


    // -------------------------
    // Insert
    // -------------------------

    $stmt = $conn->prepare("
        INSERT INTO users
        (
            full_name,
            email,
            password,
            role,
            grade,
            phone,
            status
        )
        VALUES
        (
            ?,
            ?,
            ?,
            'student',
            ?,
            ?,
            ?
        )
    ");

    $stmt->bind_param(
        "ssssss",
        $name,
        $email,
        $hashedPassword,
        $grade,
        $phone,
        $status
    );


    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Student added successfully.",
            "id" => $stmt->insert_id
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to add student."
        ]);
    }

    exit;
}


// =====================================================
// EDIT STUDENT
// =====================================================

if ($action === "edit") {

    $id = intval($_POST["id"] ?? 0);

    $name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $grade = trim($_POST["grade"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $status = trim($_POST["status"] ?? "Active");
    $password = $_POST["password"] ?? "";


    if ($id <= 0 || $name === "" || $email === "") {

        echo json_encode([
            "success" => false,
            "message" => "Required information is missing."
        ]);

        exit;
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid email address."
        ]);

        exit;
    }


    $allowedStatuses = [
        "Active",
        "Pending",
        "Suspended"
    ];

    if (!in_array($status, $allowedStatuses, true)) {
        $status = "Active";
    }


    // -------------------------
    // Check email
    // -------------------------

    $check = $conn->prepare("
        SELECT id
        FROM users
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

    $result = $check->get_result();

    if ($result->num_rows > 0) {

        echo json_encode([
            "success" => false,
            "message" => "Another account already uses this email."
        ]);

        exit;
    }


    // -------------------------
    // Update with password
    // -------------------------

    if ($password !== "") {

        if (strlen($password) < 6) {

            echo json_encode([
                "success" => false,
                "message" => "Password must contain at least 6 characters."
            ]);

            exit;
        }

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $conn->prepare("
            UPDATE users
            SET
                full_name = ?,
                email = ?,
                grade = ?,
                phone = ?,
                status = ?,
                password = ?
            WHERE id = ?
            AND role = 'student'
        ");

        $stmt->bind_param(
            "ssssssi",
            $name,
            $email,
            $grade,
            $phone,
            $status,
            $hashedPassword,
            $id
        );

    } else {

        // -------------------------
        // Update without password
        // -------------------------

        $stmt = $conn->prepare("
            UPDATE users
            SET
                full_name = ?,
                email = ?,
                grade = ?,
                phone = ?,
                status = ?
            WHERE id = ?
            AND role = 'student'
        ");

        $stmt->bind_param(
            "sssssi",
            $name,
            $email,
            $grade,
            $phone,
            $status,
            $id
        );
    }


    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Student updated successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to update student."
        ]);
    }

    exit;
}


// =====================================================
// DELETE STUDENT
// =====================================================

if ($action === "delete") {

    $id = intval($_POST["id"] ?? 0);

    if ($id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid student ID."
        ]);

        exit;
    }


    $stmt = $conn->prepare("
        DELETE FROM users
        WHERE id = ?
        AND role = 'student'
    ");

    $stmt->bind_param("i", $id);


    if ($stmt->execute()) {

        if ($stmt->affected_rows > 0) {

            echo json_encode([
                "success" => true,
                "message" => "Student deleted successfully."
            ]);

        } else {

            echo json_encode([
                "success" => false,
                "message" => "Student not found."
            ]);
        }

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to delete student."
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