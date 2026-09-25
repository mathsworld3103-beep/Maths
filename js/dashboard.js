document.addEventListener("DOMContentLoaded", () => {

    // =========================================
    // ELEMENTS
    // =========================================

    const sidebar =
        document.getElementById("dashboardSidebar");

    const overlay =
        document.getElementById("dashboardOverlay");

    const menuToggle =
        document.getElementById("dashboardMenuToggle");

    const logoutBtn =
        document.getElementById("logoutBtn");


    // =========================================
    // CHECK LOGIN SESSION
    // =========================================

    checkSession();


    async function checkSession() {

        try {

            const response = await fetch(
                "backend/check-session.php",
                {
                    method: "GET",
                    credentials: "include"
                }
            );


            if (!response.ok) {

                throw new Error(
                    "Session check failed."
                );

            }


            const data =
                await response.json();


            // User is NOT logged in

            if (!data.loggedIn) {

                window.location.href =
                    "login.html";

                return;
            }


            // User is logged in

            displayUser(data.user);

        } catch (error) {

            console.error(
                "Session Error:",
                error
            );

            window.location.href =
                "login.html";

        }

    }



    // =========================================
    // DISPLAY USER INFORMATION
    // =========================================

    function displayUser(user) {

        const name =
            user.full_name || "Student";

        const grade =
            user.grade || "Student";


        // Sidebar

        const sidebarName =
            document.getElementById(
                "sidebarStudentName"
            );

        const sidebarGrade =
            document.getElementById(
                "sidebarStudentGrade"
            );


        if (sidebarName) {

            sidebarName.textContent =
                name;

        }


        if (sidebarGrade) {

            sidebarGrade.textContent =
                grade;

        }


        // Header

        const headerName =
            document.getElementById(
                "headerStudentName"
            );

        const headerGrade =
            document.getElementById(
                "headerStudentGrade"
            );


        if (headerName) {

            headerName.textContent =
                name;

        }


        if (headerGrade) {

            headerGrade.textContent =
                grade;

        }


        // Welcome

        const welcomeName =
            document.getElementById(
                "welcomeStudentName"
            );


        if (welcomeName) {

            welcomeName.textContent =
                name + "!";

        }

    }



    // =========================================
    // MOBILE SIDEBAR
    // =========================================

    if (menuToggle) {

        menuToggle.addEventListener(
            "click",
            () => {

                sidebar.classList.toggle(
                    "active"
                );

                overlay.classList.toggle(
                    "active"
                );

            }
        );

    }


    // =========================================
    // CLOSE MOBILE SIDEBAR
    // =========================================

    if (overlay) {

        overlay.addEventListener(
            "click",
            closeSidebar
        );

    }


    function closeSidebar() {

        sidebar.classList.remove(
            "active"
        );

        overlay.classList.remove(
            "active"
        );

    }



    // =========================================
    // CLOSE MENU WHEN LINK IS CLICKED
    // =========================================

    const navLinks =
        document.querySelectorAll(
            ".dashboard-nav-link"
        );


    navLinks.forEach(link => {

        link.addEventListener(
            "click",
            () => {

                closeSidebar();

            }
        );

    });



    // =========================================
    // LOGOUT
    // =========================================

    if (logoutBtn) {

        logoutBtn.addEventListener(
            "click",
            async (event) => {

                event.preventDefault();


                const originalHTML =
                    logoutBtn.innerHTML;


                logoutBtn.innerHTML = `
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <span>Logging out...</span>
                `;


                logoutBtn.style.pointerEvents =
                    "none";


                try {

                    const response =
                        await fetch(
                            "backend/logout.php",
                            {
                                method: "POST",
                                credentials: "include"
                            }
                        );


                    const data =
                        await response.json();


                    if (data.success) {

                        window.location.href =
                            "login.html";

                    } else {

                        throw new Error(
                            data.message ||
                            "Logout failed."
                        );

                    }

                } catch (error) {

                    console.error(
                        "Logout Error:",
                        error
                    );


                    logoutBtn.innerHTML =
                        originalHTML;

                    logoutBtn.style.pointerEvents =
                        "auto";

                    alert(
                        "Unable to logout. Please try again."
                    );

                }

            }
        );

    }



    // =========================================
    // DASHBOARD SEARCH
    // =========================================

    const searchInput =
        document.getElementById(
            "dashboardSearch"
        );


    if (searchInput) {

        searchInput.addEventListener(
            "input",
            () => {

                const searchText =
                    searchInput.value
                        .trim()
                        .toLowerCase();


                if (!searchText) {

                    return;

                }


                console.log(
                    "Searching:",
                    searchText
                );

            }
        );

    }



    // =========================================
    // NOTIFICATION BUTTON
    // =========================================

    const notificationBtn =
        document.getElementById(
            "notificationBtn"
        );


    if (notificationBtn) {

        notificationBtn.addEventListener(
            "click",
            () => {

                alert(
                    "You have no new notifications."
                );

            }
        );

    }



    // =========================================
    // PROFILE
    // =========================================

    const profileLink =
        document.getElementById(
            "profileLink"
        );


    if (profileLink) {

        profileLink.addEventListener(
            "click",
            (event) => {

                event.preventDefault();

                alert(
                    "Student profile page will be available soon."
                );

            }
        );

    }



    // =========================================
    // SETTINGS
    // =========================================

    const settingsLink =
        document.getElementById(
            "settingsLink"
        );


    if (settingsLink) {

        settingsLink.addEventListener(
            "click",
            (event) => {

                event.preventDefault();

                alert(
                    "Settings page will be available soon."
                );

            }
        );

    }

});