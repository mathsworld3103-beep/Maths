document.addEventListener("DOMContentLoaded", function () {

    // =====================================================
    // PASSWORD SHOW / HIDE
    // =====================================================

    const passwordToggles =
        document.querySelectorAll(".password-toggle");

    passwordToggles.forEach(function (toggle) {

        toggle.addEventListener("click", function () {

            const targetId =
                toggle.getAttribute("data-target");

            const input =
                document.getElementById(targetId);

            if (!input) {
                return;
            }

            const icon =
                toggle.querySelector("i");

            if (input.type === "password") {

                input.type = "text";

                if (icon) {
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                }

            } else {

                input.type = "password";

                if (icon) {
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                }
            }

        });

    });


    // =====================================================
    // REGISTER
    // =====================================================

    const registerForm =
        document.getElementById("registerForm");

    if (registerForm) {

        registerForm.addEventListener(
            "submit",
            async function (e) {

                e.preventDefault();


                // =========================================
                // GET INPUTS
                // =========================================

                const fullName =
                    document
                        .getElementById("fullName")
                        .value
                        .trim();

                const email =
                    document
                        .getElementById("registerEmail")
                        .value
                        .trim();

                const phone =
                    document
                        .getElementById("phone")
                        .value
                        .trim();

                const grade =
                    document
                        .getElementById("grade")
                        .value;

                const password =
                    document
                        .getElementById("registerPassword")
                        .value;

                const confirmPassword =
                    document
                        .getElementById("confirmPassword")
                        .value;

                const terms =
                    document
                        .getElementById("terms")
                        .checked;

                const message =
                    document.getElementById(
                        "registerMessage"
                    );

                const button =
                    registerForm.querySelector(
                        ".auth-btn"
                    );


                // =========================================
                // TERMS CHECK
                // =========================================

                if (!terms) {

                    message.textContent =
                        "Please agree to the Terms & Conditions and Privacy Policy.";

                    message.className =
                        "auth-message error";

                    return;
                }


                // =========================================
                // PASSWORD MATCH
                // =========================================

                if (password !== confirmPassword) {

                    message.textContent =
                        "Passwords do not match.";

                    message.className =
                        "auth-message error";

                    return;
                }


                // =========================================
                // PASSWORD LENGTH
                // =========================================

                if (password.length < 6) {

                    message.textContent =
                        "Password must be at least 6 characters.";

                    message.className =
                        "auth-message error";

                    return;
                }


                // =========================================
                // DISABLE BUTTON
                // =========================================

                button.disabled = true;

                button.querySelector("span").textContent =
                    "Creating Account...";


                // =========================================
                // FORM DATA
                // =========================================

                const formData = new FormData();

                formData.append(
                    "full_name",
                    fullName
                );

                formData.append(
                    "email",
                    email
                );

                formData.append(
                    "phone",
                    phone
                );

                formData.append(
                    "grade",
                    grade
                );

                formData.append(
                    "password",
                    password
                );

                formData.append(
                    "confirm_password",
                    confirmPassword
                );


                try {

                    // =====================================
                    // SEND REGISTER REQUEST
                    // =====================================

                    const response =
                        await fetch(
                            "auth/register.php",
                            {
                                method: "POST",
                                body: formData
                            }
                        );


                    const data =
                        await response.json();


                    // =====================================
                    // REGISTER SUCCESS
                    // =====================================

                    if (data.success) {

                        message.textContent =
                            data.message ||
                            "Account created successfully!";

                        message.className =
                            "auth-message success";


                        setTimeout(function () {

                            window.location.href =
                                data.redirect ||
                                "login.html";

                        }, 800);


                    } else {

                        message.textContent =
                            data.message ||
                            "Registration failed.";

                        message.className =
                            "auth-message error";


                        button.disabled = false;

                        button.querySelector(
                            "span"
                        ).textContent =
                            "Create Account";
                    }


                } catch (error) {

                    console.error(
                        "Registration error:",
                        error
                    );


                    message.textContent =
                        "Server error. Please try again.";

                    message.className =
                        "auth-message error";


                    button.disabled = false;

                    button.querySelector(
                        "span"
                    ).textContent =
                        "Create Account";
                }

            }
        );

    }


    // =====================================================
    // LOGIN
    // =====================================================

    const loginForm =
        document.getElementById("loginForm");

    if (loginForm) {

        loginForm.addEventListener(
            "submit",
            async function (e) {

                e.preventDefault();


                // =========================================
                // GET LOGIN INPUTS
                // =========================================

                const emailInput =
                    document.getElementById(
                        "loginEmail"
                    );

                const passwordInput =
                    document.getElementById(
                        "loginPassword"
                    );

                const message =
                    document.getElementById(
                        "loginMessage"
                    );

                const button =
                    document.getElementById(
                        "loginButton"
                    );


                if (!emailInput || !passwordInput) {
                    return;
                }


                const email =
                    emailInput.value.trim();

                const password =
                    passwordInput.value;


                // =========================================
                // VALIDATION
                // =========================================

                if (!email || !password) {

                    message.textContent =
                        "Please enter your email and password.";

                    message.className =
                        "auth-message error";

                    return;
                }


                // =========================================
                // DISABLE LOGIN BUTTON
                // =========================================

                if (button) {

                    button.disabled = true;

                    const buttonText =
                        button.querySelector("span");

                    if (buttonText) {

                        buttonText.textContent =
                            "Logging in...";
                    }
                }


                // =========================================
                // CREATE FORM DATA
                // =========================================

                const formData =
                    new FormData();

                formData.append(
                    "email",
                    email
                );

                formData.append(
                    "password",
                    password
                );


                try {

                    // =====================================
                    // SEND LOGIN REQUEST
                    // =====================================

                    const response =
                        await fetch(
                            "auth/login.php",
                            {
                                method: "POST",
                                body: formData
                            }
                        );


                    // =====================================
                    // CHECK HTTP RESPONSE
                    // =====================================

                    if (!response.ok) {

                        throw new Error(
                            "HTTP error: " +
                            response.status
                        );
                    }


                    const data =
                        await response.json();


                    console.log(
                        "Login response:",
                        data
                    );


                    // =====================================
                    // LOGIN SUCCESS
                    // =====================================

                    if (data.success) {

                        message.textContent =
                            data.message ||
                            "Login successful.";

                        message.className =
                            "auth-message success";


                        // =================================
                        // REDIRECT
                        // =================================

                        setTimeout(function () {

                            if (data.redirect) {

                                window.location.href =
                                    data.redirect;

                            } else if (
                                data.role === "admin"
                            ) {

                                window.location.href =
                                    "admin/dashboard.php";

                            } else {

                                window.location.href =
                                    "user/dashboard.php";
                            }

                        }, 500);


                    } else {

                        // =================================
                        // LOGIN FAILED
                        // =================================

                        message.textContent =
                            data.message ||
                            "Invalid email or password.";

                        message.className =
                            "auth-message error";


                        if (button) {

                            button.disabled = false;

                            const buttonText =
                                button.querySelector(
                                    "span"
                                );

                            if (buttonText) {

                                buttonText.textContent =
                                    "Login";
                            }
                        }
                    }


                } catch (error) {

                    console.error(
                        "Login error:",
                        error
                    );


                    message.textContent =
                        "Server error. Please try again.";

                    message.className =
                        "auth-message error";


                    if (button) {

                        button.disabled = false;

                        const buttonText =
                            button.querySelector(
                                "span"
                            );

                        if (buttonText) {

                            buttonText.textContent =
                                "Login";
                        }
                    }

                }

            }
        );

    }


    // =====================================================
    // CLEAR LOGIN FORM
    // =====================================================

    if (loginForm) {

        const emailInput =
            document.getElementById(
                "loginEmail"
            );

        const passwordInput =
            document.getElementById(
                "loginPassword"
            );

        if (emailInput) {
            emailInput.value = "";
        }

        if (passwordInput) {
            passwordInput.value = "";
        }

    }

});


// =========================================================
// CLEAR LOGIN FORM WHEN PAGE IS RESTORED FROM CACHE
// =========================================================

window.addEventListener(
    "pageshow",
    function () {

        const loginForm =
            document.getElementById(
                "loginForm"
            );

        if (!loginForm) {
            return;
        }

        const emailInput =
            document.getElementById(
                "loginEmail"
            );

        const passwordInput =
            document.getElementById(
                "loginPassword"
            );

        if (emailInput) {
            emailInput.value = "";
        }

        if (passwordInput) {
            passwordInput.value = "";
        }

    }
);