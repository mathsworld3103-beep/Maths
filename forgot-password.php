<?php

require_once "db.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");

    if ($email === "") {

        $error = "Please enter your email address.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        $stmt = $conn->prepare("
            SELECT id, full_name
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            $userId = (int) $user["id"];

            // Remove old reset tokens
            $delete = $conn->prepare("
                DELETE FROM password_resets
                WHERE user_id = ?
            ");

            $delete->bind_param("i", $userId);
            $delete->execute();
            $delete->close();

            // Generate secure token
            $token = bin2hex(random_bytes(32));

            // Token valid for 30 minutes
            $expiresAt = date(
                "Y-m-d H:i:s",
                time() + (30 * 60)
            );

            $reset = $conn->prepare("
                INSERT INTO password_resets
                (
                    user_id,
                    token,
                    expires_at
                )
                VALUES (?, ?, ?)
            ");

            $reset->bind_param(
                "iss",
                $userId,
                $token,
                $expiresAt
            );

            $reset->execute();

            $reset->close();

            /*
             * Temporary local development link.
             *
             * Later we will send this link through email.
             */

           $resetLink =
            "http://localhost/mathsworld/reset-password.php?token="
            . urlencode($token);

            $message =
                "Password reset link generated.";

        } else {

            /*
             * Don't reveal whether an email exists.
             */
            $message =
                "If an account exists with that email, "
                . "a password reset link will be provided.";
        }

        $stmt->close();
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

    <title>Forgot Password | MathsWorld</title>

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

    <link
        rel="stylesheet"
        href="css/auth.css"
    >

    <style>

        body {
            background: #f5f8fc;
        }

        .forgot-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
        }

        .forgot-card {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            padding: 35px;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 15px 40px rgba(15, 23, 42, 0.08);
        }

        .forgot-logo {
            width: 65px;
            height: 65px;
            margin: 0 auto 20px;
            border-radius: 16px;
            background: #0b1f3a;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 800;
        }

        .forgot-card h1 {
            text-align: center;
            color: #0b1f3a;
            margin-bottom: 8px;
        }

        .forgot-description {
            text-align: center;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1e293b;
            font-weight: 600;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        .input-wrapper input {
            width: 100%;
            box-sizing: border-box;
            padding: 14px 15px 14px 45px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            outline: none;
            font-size: 15px;
        }

        .input-wrapper input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        }

        .submit-button {
            width: 100%;
            border: none;
            background: #0b1f3a;
            color: #ffffff;
            padding: 14px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
        }

        .submit-button:hover {
            background: #12345b;
        }

        .message {
            margin-bottom: 20px;
            padding: 14px;
            border-radius: 10px;
            background: #dcfce7;
            color: #166534;
            line-height: 1.5;
            word-break: break-word;
        }

        .error {
            margin-bottom: 20px;
            padding: 14px;
            border-radius: 10px;
            background: #fee2e2;
            color: #991b1b;
        }

        .back-login {
            display: block;
            text-align: center;
            margin-top: 22px;
            color: #0b1f3a;
            text-decoration: none;
            font-weight: 600;
        }

        .back-login:hover {
            color: #38bdf8;
        }

        .reset-link {
            display: block;
            margin-top: 12px;
            padding: 12px;
            background: #e0f2fe;
            color: #075985;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            word-break: break-all;
        }

        @media (max-width: 600px) {

            .forgot-card {
                padding: 25px 20px;
            }

        }

    </style>

</head>

<body>

<div class="forgot-container">

    <div class="forgot-card">

        <div class="forgot-logo">
            MW
        </div>

        <h1>
            Forgot Password?
        </h1>

        <p class="forgot-description">
            Enter your registered email address and
            we'll help you reset your password.
        </p>

        <?php if ($error !== ""): ?>

            <div class="error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <?php if ($message !== ""): ?>

            <div class="message">

                <i class="fa-solid fa-circle-check"></i>

                <?php echo htmlspecialchars($message); ?>

                <?php if (isset($resetLink)): ?>

                    <a
                        href="<?php echo htmlspecialchars($resetLink); ?>"
                        class="reset-link"
                    >
                        Continue to Reset Password
                    </a>

                <?php endif; ?>

            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <div class="input-wrapper">

                    <i class="fa-solid fa-envelope"></i>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your registered email"
                        autocomplete="email"
                        required
                    >

                </div>

            </div>

            <button
                type="submit"
                class="submit-button"
            >
                <i class="fa-solid fa-paper-plane"></i>
                Generate Reset Link
            </button>

        </form>


        <a
            href="login.html"
            class="back-login"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Back to Login
        </a>

    </div>

</div>

</body>

</html>