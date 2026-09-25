document.addEventListener("DOMContentLoaded", function () {

    /* ==========================================
       DESCRIPTION CHARACTER COUNTER
    ========================================== */

    const description =
        document.getElementById("site_description");

    const descriptionCount =
        document.getElementById("descriptionCount");


    function updateDescriptionCount() {

        if (!description || !descriptionCount) {
            return;
        }

        descriptionCount.textContent =
            description.value.length;

    }


    if (description) {

        updateDescriptionCount();

        description.addEventListener(
            "input",
            updateDescriptionCount
        );

    }


    /* ==========================================
       MAINTENANCE MODE STATUS
    ========================================== */

    const maintenance =
        document.getElementById("maintenance_mode");

    const systemStatus =
        document.getElementById("systemStatus");

    const systemStatusText =
        document.getElementById("systemStatusText");


    function updateSystemStatus() {

        if (
            !maintenance ||
            !systemStatus ||
            !systemStatusText
        ) {
            return;
        }


        if (maintenance.checked) {

            systemStatus.classList.add(
                "maintenance"
            );

            systemStatusText.textContent =
                "Maintenance mode is currently enabled.";

        } else {

            systemStatus.classList.remove(
                "maintenance"
            );

            systemStatusText.textContent =
                "Website is currently active.";

        }

    }


    if (maintenance) {

        maintenance.addEventListener(
            "change",
            updateSystemStatus
        );

        updateSystemStatus();

    }


    /* ==========================================
       WEBSITE SETTINGS FORM
    ========================================== */

    const websiteForm =
        document.getElementById(
            "websiteSettingsForm"
        );

    const websiteSaveButton =
        document.getElementById(
            "websiteSaveButton"
        );


    if (websiteForm) {

        websiteForm.addEventListener(
            "submit",
            function (event) {

                const siteName =
                    document.getElementById(
                        "site_name"
                    );


                if (
                    !siteName ||
                    siteName.value.trim() === ""
                ) {

                    event.preventDefault();

                    siteName.focus();

                    alert(
                        "Please enter the website name."
                    );

                    return;

                }


                if (websiteSaveButton) {

                    websiteSaveButton.disabled =
                        true;

                    websiteSaveButton.innerHTML = `

                        <i class="fa-solid fa-spinner fa-spin"></i>

                        <span>
                            Saving...
                        </span>

                    `;

                }

            }
        );

    }


    /* ==========================================
       SYSTEM SETTINGS FORM
    ========================================== */

    const systemForm =
        document.getElementById(
            "systemSettingsForm"
        );

    const systemSaveButton =
        document.getElementById(
            "systemSaveButton"
        );


    if (systemForm) {

        systemForm.addEventListener(
            "submit",
            function () {

                if (systemSaveButton) {

                    systemSaveButton.disabled =
                        true;

                    systemSaveButton.innerHTML = `

                        <i class="fa-solid fa-spinner fa-spin"></i>

                        <span>
                            Saving...
                        </span>

                    `;

                }

            }
        );

    }


    /* ==========================================
       AUTO HIDE ALERTS
    ========================================== */

    const alerts =
        document.querySelectorAll(
            ".alert"
        );


    alerts.forEach(function (alert) {

        setTimeout(function () {

            closeAlertElement(alert);

        }, 5000);

    });

});


/* ==========================================
   CLOSE ALERT BUTTON
========================================== */

function closeAlert(button) {

    const alert =
        button.closest(".alert");

    if (!alert) {
        return;
    }

    closeAlertElement(alert);

}


/* ==========================================
   CLOSE ALERT FUNCTION
========================================== */

function closeAlertElement(alert) {

    alert.style.opacity = "0";

    alert.style.transform =
        "translateY(-5px)";

    alert.style.transition =
        "opacity 0.3s ease, transform 0.3s ease";


    setTimeout(function () {

        if (alert) {
            alert.remove();
        }

    }, 300);

}