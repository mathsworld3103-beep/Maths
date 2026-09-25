<?php

session_start();

require_once "../db.php";

require_once "activity-log-helper.php";


/* ==========================================
   ADMIN CHECK
========================================== */

if (!isset($_SESSION["user_id"])) {

    header("Location: login.html");
    exit;

}

if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {

    header("Location: ../user/dashboard.php");
    exit;

}


/* ==========================================
   POST CHECK
========================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: settings.php");
    exit;

}


$action =
    $_POST["action"] ?? "";


/* ==========================================
   SAVE WEBSITE SETTINGS
========================================== */

if ($action === "save_website") {

    $siteName =
        trim($_POST["site_name"] ?? "");

    $siteEmail =
        trim($_POST["site_email"] ?? "");

    $sitePhone =
        trim($_POST["site_phone"] ?? "");

    $siteDescription =
        trim($_POST["site_description"] ?? "");


    if ($siteName === "") {

        header(
            "Location: settings.php?error=site_name"
        );

        exit;

    }


    $stmt = $conn->prepare("
        UPDATE site_settings

        SET
            site_name = ?,
            site_email = ?,
            site_phone = ?,
            site_description = ?

        WHERE id = 1
    ");


    $stmt->bind_param(
        "ssss",
        $siteName,
        $siteEmail,
        $sitePhone,
        $siteDescription
    );


    $stmt->execute();

    $stmt->close();


    logActivity(
        $conn,
        $_SESSION["user_id"],
        "UPDATE",
        "Settings",
        "Updated website settings"
    );


    header(
        "Location: settings.php?success=website"
    );

    exit;
}


/* ==========================================
   SAVE SYSTEM SETTINGS
========================================== */

if ($action === "save_system") {

    $allowRegistration =
        isset(
            $_POST["allow_registration"]
        )
        ? 1
        : 0;


    $maintenanceMode =
        isset(
            $_POST["maintenance_mode"]
        )
        ? 1
        : 0;


    $stmt = $conn->prepare("
        UPDATE site_settings

        SET
            allow_registration = ?,
            maintenance_mode = ?

        WHERE id = 1
    ");


    $stmt->bind_param(
        "ii",
        $allowRegistration,
        $maintenanceMode
    );


    $stmt->execute();

    $stmt->close();


    logActivity(
        $conn,
        $_SESSION["user_id"],
        "UPDATE",
        "Settings",
        "Updated system settings"
    );


    header(
        "Location: settings.php?success=system"
    );

    exit;
}


header(
    "Location: settings.php"
);

exit;

?>