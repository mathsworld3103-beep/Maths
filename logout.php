<?php

session_start();

/*
|--------------------------------------------------------------------------
| CLEAR SESSION
|--------------------------------------------------------------------------
*/

$_SESSION = [];

if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();


/*
|--------------------------------------------------------------------------
| GO TO LOGIN PAGE
|--------------------------------------------------------------------------
*/

header("Location: login.html?logout=1");
exit;

?>