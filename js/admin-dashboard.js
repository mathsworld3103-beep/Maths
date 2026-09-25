/* =========================================================
   MATHSWORLD ADMIN DASHBOARD
   dashboard.js
========================================================= */

document.addEventListener("DOMContentLoaded", () => {

    /* =====================================================
       ELEMENTS
    ===================================================== */

    const adminSidebar = document.getElementById("adminSidebar");
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const sidebarOverlay = document.getElementById("sidebarOverlay");

    const notificationBtn =
        document.getElementById("notificationBtn");

    const notificationPanel =
        document.getElementById("notificationPanel");

    const closeNotifications =
        document.getElementById("closeNotifications");

    const sidebarLogout =
        document.getElementById("sidebarLogout");

    const logoutModal =
        document.getElementById("logoutModal");

    const cancelLogout =
        document.getElementById("cancelLogout");

    const confirmLogout =
        document.getElementById("confirmLogout");

    const toast =
        document.getElementById("toast");

    const closeToast =
        document.getElementById("closeToast");

    const toastTitle =
        document.getElementById("toastTitle");

    const toastMessage =
        document.getElementById("toastMessage");

    const chartPeriod =
        document.getElementById("chartPeriod");

    const headerSearchBtn =
        document.getElementById("headerSearchBtn");


    /* =====================================================
       MOBILE SIDEBAR
    ===================================================== */

    function openSidebar() {

        if (!adminSidebar) return;

        adminSidebar.classList.add("active");

        if (sidebarOverlay) {
            sidebarOverlay.classList.add("active");
        }

        document.body.style.overflow = "hidden";
    }


    function closeSidebar() {

        if (!adminSidebar) return;

        adminSidebar.classList.remove("active");

        if (sidebarOverlay) {
            sidebarOverlay.classList.remove("active");
        }

        document.body.style.overflow = "";
    }


    if (mobileMenuBtn) {

        mobileMenuBtn.addEventListener("click", () => {

            if (adminSidebar.classList.contains("active")) {
                closeSidebar();
            } else {
                openSidebar();
            }

        });

    }


    if (sidebarOverlay) {

        sidebarOverlay.addEventListener(
            "click",
            closeSidebar
        );

    }


    /* =====================================================
       CLOSE MOBILE SIDEBAR WHEN CLICKING NAV LINK
    ===================================================== */

    const navLinks =
        document.querySelectorAll(".nav-link");

    navLinks.forEach(link => {

        link.addEventListener("click", () => {

            if (window.innerWidth <= 850) {
                closeSidebar();
            }

        });

    });


    /* =====================================================
       NOTIFICATION PANEL
    ===================================================== */

    function openNotifications() {

        if (!notificationPanel) return;

        notificationPanel.classList.add("active");
    }


    function closeNotificationPanel() {

        if (!notificationPanel) return;

        notificationPanel.classList.remove("active");
    }


    if (notificationBtn) {

        notificationBtn.addEventListener("click", event => {

            event.stopPropagation();

            if (
                notificationPanel.classList.contains("active")
            ) {
                closeNotificationPanel();
            } else {
                openNotifications();
            }

        });

    }


    if (closeNotifications) {

        closeNotifications.addEventListener(
            "click",
            closeNotificationPanel
        );

    }


    /* =====================================================
       CLOSE NOTIFICATION WHEN CLICKING OUTSIDE
    ===================================================== */

    document.addEventListener("click", event => {

        if (!notificationPanel) return;

        const clickedInsidePanel =
            notificationPanel.contains(event.target);

        const clickedNotificationButton =
            notificationBtn &&
            notificationBtn.contains(event.target);

        if (
            !clickedInsidePanel &&
            !clickedNotificationButton
        ) {
            closeNotificationPanel();
        }

    });


    /* =====================================================
       TOAST FUNCTION
    ===================================================== */

    let toastTimer;


    function showToast(
        title = "Success",
        message = "Action completed successfully."
    ) {

        if (!toast) return;

        clearTimeout(toastTimer);

        if (toastTitle) {
            toastTitle.textContent = title;
        }

        if (toastMessage) {
            toastMessage.textContent = message;
        }

        toast.classList.add("show");

        toastTimer = setTimeout(() => {

            toast.classList.remove("show");

        }, 3500);

    }


    if (closeToast) {

        closeToast.addEventListener("click", () => {

            toast.classList.remove("show");

        });

    }


    /* =====================================================
       LOGOUT MODAL
    ===================================================== */

    function openLogoutModal() {

        if (!logoutModal) return;

        logoutModal.classList.add("active");

        document.body.style.overflow = "hidden";
    }


    function closeLogoutModal() {

        if (!logoutModal) return;

        logoutModal.classList.remove("active");

        document.body.style.overflow = "";
    }


    if (sidebarLogout) {

        sidebarLogout.addEventListener(
            "click",
            openLogoutModal
        );

    }


    if (cancelLogout) {

        cancelLogout.addEventListener(
            "click",
            closeLogoutModal
        );

    }


    /* =====================================================
       CLICK OUTSIDE LOGOUT MODAL
    ===================================================== */

    if (logoutModal) {

        logoutModal.addEventListener("click", event => {

            if (event.target === logoutModal) {
                closeLogoutModal();
            }

        });

    }


    /* =====================================================
       CONFIRM LOGOUT
    ===================================================== */

    if (confirmLogout) {

        confirmLogout.addEventListener("click", () => {

            confirmLogout.disabled = true;

            confirmLogout.textContent = "Logging out...";

            setTimeout(() => {

                /*
                 * FRONTEND DEMO
                 *
                 * Later this will be replaced with:
                 *
                 * localStorage.removeItem("adminToken");
                 * window.location.href = "login.html";
                 */

                showToast(
                    "Logged out",
                    "Logout functionality is ready for backend integration."
                );

                confirmLogout.disabled = false;
                confirmLogout.textContent = "Logout";

                closeLogoutModal();

            }, 800);

        });

    }


    /* =====================================================
       ANIMATED STATISTICS
    ===================================================== */

    const statNumbers =
        document.querySelectorAll(".stat-number");


    function animateCounter(element) {

        const target =
            Number(element.dataset.count || 0);

        const duration = 1300;

        const startTime =
            performance.now();


        function updateCounter(currentTime) {

            const elapsed =
                currentTime - startTime;

            const progress =
                Math.min(elapsed / duration, 1);


            /*
             * Ease-out animation
             */

            const easedProgress =
                1 - Math.pow(1 - progress, 3);


            const currentValue =
                Math.floor(
                    easedProgress * target
                );


            element.textContent =
                currentValue.toLocaleString();


            if (progress < 1) {

                requestAnimationFrame(
                    updateCounter
                );

            } else {

                element.textContent =
                    target.toLocaleString();

            }

        }


        requestAnimationFrame(updateCounter);
    }


    /* =====================================================
       INTERSECTION OBSERVER FOR COUNTERS
    ===================================================== */

    if (statNumbers.length) {

        const counterObserver =
            new IntersectionObserver(
                entries => {

                    entries.forEach(entry => {

                        if (
                            entry.isIntersecting &&
                            !entry.target.dataset.animated
                        ) {

                            entry.target.dataset.animated =
                                "true";

                            animateCounter(
                                entry.target
                            );

                        }

                    });

                },
                {
                    threshold: 0.5
                }
            );


        statNumbers.forEach(number => {

            counterObserver.observe(number);

        });

    }


    /* =====================================================
       CHART PERIOD
    ===================================================== */

    const chartData = {

        "7": {
            line:
                "M0,215 C100,190 120,175 200,185 C280,195 320,130 390,145 C470,160 520,90 590,105 C640,115 670,70 700,75",

            fill:
                "M0,215 C100,190 120,175 200,185 C280,195 320,130 390,145 C470,160 520,90 590,105 C640,115 670,70 700,75 L700,280 L0,280 Z"
        },

        "30": {
            line:
                "M0,220 C70,200 80,180 140,190 C200,200 210,145 280,155 C340,165 360,115 420,125 C480,135 500,80 560,100 C620,120 640,55 700,70",

            fill:
                "M0,220 C70,200 80,180 140,190 C200,200 210,145 280,155 C340,165 360,115 420,125 C480,135 500,80 560,100 C620,120 640,55 700,70 L700,280 L0,280 Z"
        },

        "90": {
            line:
                "M0,230 C65,215 100,205 155,210 C220,215 250,155 320,170 C380,185 420,125 475,140 C540,155 580,75 630,100 C660,112 680,75 700,60",

            fill:
                "M0,230 C65,215 100,205 155,210 C220,215 250,155 320,170 C380,185 420,125 475,140 C540,155 580,75 630,100 C660,112 680,75 700,60 L700,280 L0,280 Z"
        }

    };


    function updateChart(period) {

        const data =
            chartData[period];

        if (!data) return;


        const line =
            document.querySelector(".chart-line");

        const fill =
            document.querySelector(".chart-area-fill");


        if (line) {

            line.setAttribute(
                "d",
                data.line
            );

        }


        if (fill) {

            fill.setAttribute(
                "d",
                data.fill
            );

        }


        showToast(
            "Chart Updated",
            `Showing student registrations for the selected period.`
        );

    }


    if (chartPeriod) {

        chartPeriod.addEventListener(
            "change",
            () => {

                updateChart(
                    chartPeriod.value
                );

            }
        );

    }


    /* =====================================================
       HEADER SEARCH BUTTON
    ===================================================== */

    if (headerSearchBtn) {

        headerSearchBtn.addEventListener(
            "click",
            () => {

                showToast(
                    "Search",
                    "Global search will be connected to the backend later."
                );

            }
        );

    }


    /* =====================================================
       TABLE VIEW BUTTONS
    ===================================================== */

    const tableButtons =
        document.querySelectorAll(".table-action");


    tableButtons.forEach(button => {

        button.addEventListener("click", () => {

            const row =
                button.closest("tr");

            if (!row) return;


            const studentName =
                row.querySelector(
                    ".student-profile strong"
                );


            const name =
                studentName
                    ? studentName.textContent.trim()
                    : "Student";


            showToast(
                "Student Profile",
                `Opening ${name}'s profile.`
            );

        });

    });


    /* =====================================================
       QUICK ACTION FEEDBACK
    ===================================================== */

    const quickActions =
        document.querySelectorAll(".quick-action");


    quickActions.forEach(action => {

        action.addEventListener("click", () => {

            /*
             * The actual page navigation will happen
             * normally through the href.
             *
             * This listener is only used for future
             * analytics/event tracking.
             */

            console.log(
                "Quick action:",
                action.textContent.trim()
            );

        });

    });


    /* =====================================================
       PROFILE CLICK
    ===================================================== */

    const headerProfile =
        document.querySelector(".header-profile");


    if (headerProfile) {

        headerProfile.addEventListener(
            "click",
            () => {

                window.location.href =
                    "settings.html#profile";

            }
        );

    }


    /* =====================================================
       KEYBOARD SHORTCUT
    ===================================================== */

    document.addEventListener(
        "keydown",
        event => {

            /*
             * ESC closes panels/modals.
             */

            if (event.key === "Escape") {

                closeNotificationPanel();
                closeLogoutModal();

                if (window.innerWidth <= 850) {
                    closeSidebar();
                }

            }

        }
    );


    /* =====================================================
       WINDOW RESIZE
    ===================================================== */

    window.addEventListener(
        "resize",
        () => {

            if (window.innerWidth > 850) {

                if (adminSidebar) {
                    adminSidebar.classList.remove(
                        "active"
                    );
                }

                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove(
                        "active"
                    );
                }

                document.body.style.overflow = "";

            }

        }
    );


    /* =====================================================
       CURRENT DATE
    ===================================================== */

    function updateDashboardDate() {

        const dateElements =
            document.querySelectorAll(
                "[data-dashboard-date]"
            );


        if (!dateElements.length) return;


        const now = new Date();


        const formattedDate =
            now.toLocaleDateString(
                "en-LK",
                {
                    weekday: "long",
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                }
            );


        dateElements.forEach(element => {

            element.textContent =
                formattedDate;

        });

    }


    updateDashboardDate();


    /* =====================================================
       NOTIFICATION COUNT
    ===================================================== */

    function updateNotificationCount() {

        const notificationItems =
            document.querySelectorAll(
                ".notification-item"
            );

        const dot =
            document.querySelector(
                ".notification-dot"
            );


        if (
            dot &&
            notificationItems.length === 0
        ) {

            dot.style.display = "none";

        }

    }


    updateNotificationCount();


    /* =====================================================
       INITIAL DASHBOARD MESSAGE
    ===================================================== */

    console.log(
        "%cMathsWorld Admin Dashboard Loaded",
        "font-size:16px;font-weight:bold;"
    );

    console.log(
        "Frontend dashboard is ready for backend integration."
    );

});