<?php

session_start();

header("Content-Type: application/json");

require_once "../db.php";


// =====================================================
// CHECK REQUEST
// =====================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);

    exit;
}


// =====================================================
// GET DATA
// =====================================================

$full_name = trim($_POST["full_name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$grade = trim($_POST["grade"] ?? "");

$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";


// =====================================================
// REQUIRED FIELD CHECK
// =====================================================

if (
    $full_name === "" ||
    $email === "" ||
    $phone === "" ||
    $grade === "" ||
    $password === "" ||
    $confirm_password === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Please fill in all fields."
    ]);

    exit;
}


// =====================================================
// EMAIL VALIDATION
// =====================================================

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid email address."
    ]);

    exit;
}


// =====================================================
// PASSWORD MATCH
// =====================================================

if ($password !== $confirm_password) {

    echo json_encode([
        "success" => false,
        "message" => "Passwords do not match."
    ]);

    exit;
}


// =====================================================
// PASSWORD LENGTH
// =====================================================

if (strlen($password) < 6) {

    echo json_encode([
        "success" => false,
        "message" => "Password must contain at least 6 characters."
    ]);

    exit;
}


// =====================================================
// CHECK EXISTING EMAIL
// =====================================================

$stmt = $conn->prepare("
    SELECT id
    FROM users
    WHERE email = ?
    LIMIT 1
");

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Database error."
    ]);

    exit;
}

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $stmt->close();

    echo json_encode([
        "success" => false,
        "message" => "This email is already registered."
    ]);

    exit;
}

$stmt->close();


// =====================================================
// HASH PASSWORD
// =====================================================

$hashed_password = password_hash(
    $password,
    PASSWORD_DEFAULT
);


// =====================================================
// STUDENT ROLE
// =====================================================

$role = "student";


// =====================================================
// INSERT USER
// =====================================================

$stmt = $conn->prepare("
    INSERT INTO users
    (
        full_name,
        email,
        phone,
        grade,
        role,
        password
    )
    VALUES
    (?, ?, ?, ?, ?, ?)
");

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Unable to create account."
    ]);

    exit;
}


$stmt->bind_param(
    "ssssss",
    $full_name,
    $email,
    $phone,
    $grade,
    $role,
    $hashed_password
);


// =====================================================
// SAVE ACCOUNT
// =====================================================

if ($stmt->execute()) {

    $stmt->close();

    echo json_encode([
        "success" => true,
        "message" => "Account created successfully.",
        "redirect" => "/mathsworld/login.html?registered=1"
    ]);

    exit;
}


// =====================================================
// DATABASE ERROR
// =====================================================

$error = $stmt->error;

$stmt->close();

echo json_encode([
    "success" => false,
    "message" => "Registration failed: " . $error
]);

exit;

?>