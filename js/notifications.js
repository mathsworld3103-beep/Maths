document.addEventListener("DOMContentLoaded", function () {

    window.updateNotifications = function () {

        fetch("notification-api.php", {
            method: "GET",
            cache: "no-store"
        })

        .then(function (response) {
            return response.json();
        })

        .then(function (data) {

            if (data.success) {

                const card =
                    button.closest(".message-card");

                if (card) {

                    card.classList.remove("unread");

                }

                button.outerHTML = `
                    <span class="read-label">
                        <i class="fa-solid fa-circle-check"></i>
                        Read
                    </span>
                `;


                /* Update notification count immediately */

                if (typeof updateNotifications === "function") {
                    updateNotifications();
                }

            }

            const count =
                Number(data.unread_count || 0);

            const badge =
                document.getElementById(
                    "messageNotificationBadge"
                );

            const dashboardCount =
                document.getElementById(
                    "unreadMessageCount"
                );


            /* =====================================
               SIDEBAR BADGE
            ===================================== */

            if (badge) {

                if (count > 0) {

                    badge.textContent = count;

                    badge.style.display =
                        "inline-flex";

                } else {

                    badge.style.display =
                        "none";

                }

            }


            /* =====================================
               DASHBOARD COUNT
            ===================================== */

            if (dashboardCount) {

                dashboardCount.textContent =
                    count;

            }

        })

        .catch(function (error) {

            console.error(
                "Notification error:",
                error
            );

        });

    }


    /* Initial check */

    updateNotifications();


    /* Check every 30 seconds */

    setInterval(
        updateNotifications,
        30000
    );

});