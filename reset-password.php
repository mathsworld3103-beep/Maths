<?php

require_once "db.php";

$token = trim($_GET["token"] ?? "");

$errorMessage = "";
$successMessage = "";
$validToken = false;
$userId = 0;

// =====================================
// CHECK TOKEN
// =====================================

if ($token === "") {

    $errorMessage = "Invalid password reset link.";

} else {

    $stmt = $conn->prepare("
        SELECT
            id,
            user_id,
            expires_at
        FROM password_resets
        WHERE token = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $token);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {

        $errorMessage = "This password reset link is invalid or has already been used.";

    } else {

        $reset = $result->fetch_assoc();

        $userId = (int) $reset["user_id"];

        if (strtotime($reset["expires_at"]) < time()) {

            $errorMessage = "This password reset link has expired.";

        } else {

            $validToken = true;
        }
    }

    $stmt->close();
}


// =====================================
// RESET PASSWORD
// =====================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    $validToken
) {

    $newPassword = $_POST["new_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if (
        $newPassword === "" ||
        $confirmPassword === ""
    ) {

        $errorMessage =
            "Please fill in both password fields.";

    } elseif (strlen($newPassword) < 8) {

        $errorMessage =
            "Password must be at least 8 characters.";

    } elseif ($newPassword !== $confirmPassword) {

        $errorMessage =
            "Passwords do not match.";

    } else {

        // Hash password
        $hashedPassword = password_hash(
            $newPassword,
            PASSWORD_DEFAULT
        );

        // Update password
        $stmt = $conn->prepare("
            UPDATE users
            SET password = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "si",
            $hashedPassword,
            $userId
        );

        if ($stmt->execute()) {

            $stmt->close();

            // Delete used token
            $delete = $conn->prepare("
                DELETE FROM password_resets
                WHERE token = ?
            ");

            $delete->bind_param("s", $token);
            $delete->execute();
            $delete->close();

            $validToken = false;

            $successMessage =
                "Your password has been reset successfully.";

        } else {

            $errorMessage =
                "Unable to reset your password.";

            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reset Password | MathsWorld</title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Inter", sans-serif;
            background: #f5f8fc;
            color: #1e293b;
        }

        .reset-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 25px;
        }

        .reset-card {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 35px;
            box-shadow:
                0 15px 40px rgba(15, 23, 42, 0.08);
        }

        .logo {
            width: 65px;
            height: 65px;
            margin: 0 auto 20px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 16px;

            background: #0b1f3a;
            color: #ffffff;

            font-size: 22px;
            font-weight: 800;
        }

        h1 {
            text-align: center;
            color: #0b1f3a;
            margin-bottom: 8px;
        }

        .description {
            text-align: center;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper > i:first-child {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 45px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            outline: none;
            font-size: 15px;
        }

        .input-wrapper input:focus {
            border-color: #38bdf8;
            box-shadow:
                0 0 0 3px rgba(56, 189, 248, 0.15);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            left: auto !important;
            cursor: pointer;
        }

        .reset-button {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 14px;

            background: #0b1f3a;
            color: #ffffff;

            font-size: 15px;
            font-weight: 700;

            cursor: pointer;
        }

        .reset-button:hover {
            background: #12345b;
        }

        .alert {
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .login-link {
            display: block;
            margin-top: 22px;
            text-align: center;
            color: #0b1f3a;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link:hover {
            color: #38bdf8;
        }

        @media (max-width: 600px) {

            .reset-card {
                padding: 25px 20px;
            }

        }

    </style>

</head>

<body>

<div class="reset-container">

    <div class="reset-card">

        <div class="logo">
            MW
        </div>

        <h1>
            Reset Password
        </h1>

        <p class="description">
            Create a new password for your
            MathsWorld account.
        </p>


        <?php if ($errorMessage !== ""): ?>

            <div class="alert alert-error">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?php
                echo htmlspecialchars($errorMessage);
                ?>

            </div>

        <?php endif; ?>


        <?php if ($successMessage !== ""): ?>

            <div class="alert alert-success">

                <i class="fa-solid fa-circle-check"></i>

                <?php
                echo htmlspecialchars($successMessage);
                ?>

            </div>

            <a
                href="login.html"
                class="login-link"
            >
                <i class="fa-solid fa-right-to-bracket"></i>
                Go to Login
            </a>

        <?php endif; ?>


        <?php if ($validToken): ?>

            <form method="POST">

                <!-- NEW PASSWORD -->

                <div class="form-group">

                    <label for="new_password">
                        New Password
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-solid fa-lock"></i>

                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            minlength="8"
                            required
                        >

                        <i
                            class="fa-solid fa-eye toggle-password"
                            data-target="new_password"
                        ></i>

                    </div>

                </div>


                <!-- CONFIRM PASSWORD -->

                <div class="form-group">

                    <label for="confirm_password">
                        Confirm New Password
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-solid fa-lock"></i>

                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            minlength="8"
                            required
                        >

                        <i
                            class="fa-solid fa-eye toggle-password"
                            data-target="confirm_password"
                        ></i>

                    </div>

                </div>


                <button
                    type="submit"
                    class="reset-button"
                >
                    <i class="fa-solid fa-key"></i>
                    Reset Password
                </button>

            </form>

        <?php endif; ?>


        <?php if (
            !$validToken &&
            $successMessage === "" &&
            $errorMessage !== ""
        ): ?>

            <a
                href="forgot-password.php"
                class="login-link"
            >
                <i class="fa-solid fa-rotate-left"></i>
                Request a New Reset Link
            </a>

        <?php endif; ?>

    </div>

</div>


<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const buttons =
            document.querySelectorAll(".toggle-password");

        buttons.forEach(function (button) {

            button.addEventListener(
                "click",
                function () {

                    const target =
                        document.getElementById(
                            button.dataset.target
                        );

                    if (target.type === "password") {

                        target.type = "text";

                        button.classList.remove(
                            "fa-eye"
                        );

                        button.classList.add(
                            "fa-eye-slash"
                        );

                    } else {

                        target.type = "password";

                        button.classList.remove(
                            "fa-eye-slash"
                        );

                        button.classList.add(
                            "fa-eye"
                        );

                    }

                }
            );

        });

    }
);

</script>

</body>

</html>