<?php

session_start();

header("Content-Type: application/json");

require_once "../db.php";

// ========================================
// ONLY POST REQUEST
// ========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);

    exit;
}


// ========================================
// GET FORM DATA
// ========================================

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";


// ========================================
// VALIDATION
// ========================================

if ($email === "" || $password === "") {

    echo json_encode([
        "success" => false,
        "message" => "Please enter your email and password."
    ]);

    exit;
}


// ========================================
// FIND USER
// ========================================

$stmt = $conn->prepare("
    SELECT
        id,
        full_name,
        email,
        phone,
        grade,
        role,
        password
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


// ========================================
// USER NOT FOUND
// ========================================

if ($result->num_rows !== 1) {

    $stmt->close();
    $conn->close();

    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);

    exit;
}


$user = $result->fetch_assoc();


// ========================================
// VERIFY PASSWORD
// ========================================

if (!password_verify($password, $user["password"])) {

    $stmt->close();
    $conn->close();

    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);

    exit;
}


// ========================================
// CREATE LOGIN SESSION
// ========================================

session_regenerate_id(true);

$_SESSION["user_id"] = $user["id"];
$_SESSION["full_name"] = $user["full_name"];
$_SESSION["email"] = $user["email"];
$_SESSION["phone"] = $user["phone"];
$_SESSION["grade"] = $user["grade"];
$_SESSION["role"] = $user["role"];
$_SESSION["login_time"] = time();


// ========================================
// ADMIN REDIRECT
// ========================================

if ($user["role"] === "admin") {

    $stmt->close();
    $conn->close();

    echo json_encode([
        "success" => true,
        "message" => "Admin login successful.",
        "role" => "admin",
        "redirect" => "admin/dashboard.php"
    ]);

    exit;
}


// ========================================
// STUDENT REDIRECT
// ========================================

$stmt->close();
$conn->close();

echo json_encode([
    "success" => true,
    "message" => "Login successful.",
    "role" => "student",
    "redirect" => "user/dashboard.php"
]);

exit;

if (isset($_SESSION["login_redirect"])) {

    $redirect = $_SESSION["login_redirect"];

    unset($_SESSION["login_redirect"]);

    header("Location: " . $redirect);
    exit;
}


if ($user["role"] === "admin") {

    header("Location: admin/dashboard.php");
    exit;
}


header("Location: user/dashboard.php");
exit;




?>