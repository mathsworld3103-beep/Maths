require_once "activity-log-helper.php";

logActivity(
    $conn,
    $_SESSION["user_id"],
    "LOGIN",
    "Authentication",
    "Admin logged into the dashboard"
);