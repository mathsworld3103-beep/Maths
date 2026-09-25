<?php

session_start();

$redirect = $_GET["redirect"] ?? "index.html";

/*
|--------------------------------------------------------------------------
| Allowed pages
|--------------------------------------------------------------------------
*/

$allowedPages = [
    "material.html",
    "subject.html",
    "past-papers.html",
    "model-papers.html",
    "about.html",
    "contact.html"
];

if (!in_array($redirect, $allowedPages, true)) {
    $redirect = "index.html";
}


/*
|--------------------------------------------------------------------------
| Already logged in
|--------------------------------------------------------------------------
*/

if (isset($_SESSION["user_id"])) {

    header("Location: " . $redirect);
    exit;
}


/*
|--------------------------------------------------------------------------
| Save requested page
|--------------------------------------------------------------------------
*/

$_SESSION["login_redirect"] = $redirect;


/*
|--------------------------------------------------------------------------
| Go to login
|--------------------------------------------------------------------------
*/

header("Location: login.html");
exit;

$_SESSION["user_id"] = $user["id"];
$_SESSION["full_name"] = $user["full_name"];
$_SESSION["email"] = $user["email"];
$_SESSION["phone"] = $user["phone"];
$_SESSION["grade"] = $user["grade"];
$_SESSION["role"] = $user["role"];
$_SESSION["login_time"] = time();


/*
|--------------------------------------------------------------------------
| Redirect after login
|--------------------------------------------------------------------------
*/

if (isset($_SESSION["login_redirect"])) {

    $redirect = $_SESSION["login_redirect"];

    unset($_SESSION["login_redirect"]);

    header("Location: " . $redirect);
    exit;
}


/*
|--------------------------------------------------------------------------
| Normal login
|--------------------------------------------------------------------------
*/

if ($user["role"] === "admin") {

    header("Location: admin/dashboard.php");
    exit;

}

header("Location: user/dashboard.php");
exit;
?>