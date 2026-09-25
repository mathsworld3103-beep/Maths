

document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("registerForm");
    const message = document.getElementById("registerMessage");

    if (!form) {
        console.error("registerForm not found!");
        return;
    }

    form.addEventListener("submit", async function (event) {

        event.preventDefault();

        const fullName = document.getElementById("fullName").value.trim();
        const email = document.getElementById("registerEmail").value.trim();
        const phone = document.getElementById("phone").value.trim();
        const grade = document.getElementById("grade").value;
        const password = document.getElementById("registerPassword").value;
        const confirmPassword = document.getElementById("confirmPassword").value;
        const terms = document.getElementById("terms").checked;

        message.textContent = "";
        message.className = "auth-message";

        if (fullName === "") {
            showMessage("Please enter your full name.", "error");
            return;
        }

        if (email === "") {
            showMessage("Please enter your email.", "error");
            return;
        }

        if (phone === "") {
            showMessage("Please enter your phone number.", "error");
            return;
        }

        if (grade === "") {
            showMessage("Please select your grade.", "error");
            return;
        }

        if (password.length < 8) {
            showMessage(
                "Password must contain at least 8 characters.",
                "error"
            );
            return;
        }

        if (password !== confirmPassword) {
            showMessage(
                "Passwords do not match.",
                "error"
            );
            return;
        }

        if (!terms) {
            showMessage(
                "Please accept the Terms & Conditions.",
                "error"
            );
            return;
        }

        const button = form.querySelector(".auth-btn");

        button.disabled = true;
        button.textContent = "Creating Account...";

        try {

            const response = await fetch(
                "backend/register.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type": "application/json"
                    },

                    body: JSON.stringify({
                        full_name: fullName,
                        email: email,
                        phone: phone,
                        grade: grade,
                        password: password
                    })
                }
            );

            const responseText = await response.text();

            console.log("PHP Response:", responseText);

            let data;

            try {
                data = JSON.parse(responseText);
            } catch (error) {

                showMessage(
                    "PHP returned an invalid response. Check Console.",
                    "error"
                );

                console.error(
                    "Invalid PHP response:",
                    responseText
                );

                button.disabled = false;
                button.textContent = "Create Account";

                return;
            }


            if (data.success) {

                showMessage(
                    "Account created successfully!",
                    "success"
                );

                form.reset();

                setTimeout(function () {

                    window.location.href = "login.html";

                }, 1500);

            } else {

                showMessage(
                    data.message || "Registration failed.",
                    "error"
                );

                button.disabled = false;
                button.textContent = "Create Account";
            }


        } catch (error) {

            console.error(
                "Registration Error:",
                error
            );

            showMessage(
                "Cannot connect to registration server.",
                "error"
            );

            button.disabled = false;
            button.textContent = "Create Account";
        }

    });


    function showMessage(text, type) {

        message.textContent = text;
        message.className = "auth-message " + type;

    }

});