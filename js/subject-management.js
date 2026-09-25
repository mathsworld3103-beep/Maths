document.addEventListener("DOMContentLoaded", () => {

    /* ================= ELEMENTS ================= */

    const sidebar = document.getElementById("sidebar");
    const menuBtn = document.getElementById("menuBtn");
    const closeSidebar = document.getElementById("closeSidebar");
    const sidebarOverlay =
        document.getElementById("sidebarOverlay");

    const searchInput =
        document.getElementById("subjectSearch");

    const levelFilter =
        document.getElementById("levelFilter");

    const statusFilter =
        document.getElementById("statusFilter");

    const subjectCards =
        document.querySelectorAll(".subject-card");

    const noResults =
        document.getElementById("noResults");

    const addSubjectBtn =
        document.getElementById("addSubjectBtn");

    const subjectModal =
        document.getElementById("subjectModal");

    const closeModal =
        document.getElementById("closeModal");

    const cancelModal =
        document.getElementById("cancelModal");

    const subjectForm =
        document.getElementById("subjectForm");

    const toast =
        document.getElementById("toast");

    const toastText =
        document.getElementById("toastText");


    /* ================= SIDEBAR ================= */

    function openSidebar() {

        sidebar.classList.add("open");
        sidebarOverlay.classList.add("show");

        document.body.style.overflow = "hidden";
    }


    function closeMenu() {

        sidebar.classList.remove("open");
        sidebarOverlay.classList.remove("show");

        document.body.style.overflow = "";
    }


    menuBtn.addEventListener("click", openSidebar);

    closeSidebar.addEventListener("click", closeMenu);

    sidebarOverlay.addEventListener("click", closeMenu);


    /* ================= TOAST ================= */

    function showToast(message) {

        toastText.textContent = message;

        toast.classList.add("show");

        setTimeout(() => {
            toast.classList.remove("show");
        }, 2500);
    }


    /* ================= FILTER ================= */

    function filterSubjects() {

        const search =
            searchInput.value
                .trim()
                .toLowerCase();

        const level =
            levelFilter.value;

        const status =
            statusFilter.value;

        let visible = 0;


        subjectCards.forEach(card => {

            const name =
                card.dataset.name.toLowerCase();

            const cardLevel =
                card.dataset.level;

            const cardStatus =
                card.dataset.status;


            const searchMatch =
                name.includes(search);

            const levelMatch =
                level === "all" ||
                cardLevel === level;

            const statusMatch =
                status === "all" ||
                cardStatus === status;


            if (
                searchMatch &&
                levelMatch &&
                statusMatch
            ) {

                card.style.display = "";

                visible++;

            } else {

                card.style.display = "none";

            }

        });


        noResults.classList.toggle(
            "show",
            visible === 0
        );
    }


    searchInput.addEventListener(
        "input",
        filterSubjects
    );

    levelFilter.addEventListener(
        "change",
        filterSubjects
    );

    statusFilter.addEventListener(
        "change",
        filterSubjects
    );


    /* ================= MODAL ================= */

    function openModal() {

        subjectModal.classList.add("show");

        document.body.style.overflow = "hidden";
    }


    function closeSubjectModal() {

        subjectModal.classList.remove("show");

        document.body.style.overflow = "";
    }


    addSubjectBtn.addEventListener(
        "click",
        openModal
    );

    closeModal.addEventListener(
        "click",
        closeSubjectModal
    );

    cancelModal.addEventListener(
        "click",
        closeSubjectModal
    );


    subjectModal.addEventListener(
        "click",
        event => {

            if (event.target === subjectModal) {
                closeSubjectModal();
            }

        }
    );


    /* ================= ADD SUBJECT ================= */

    subjectForm.addEventListener(
        "submit",
        event => {

            event.preventDefault();

            const name =
                document.getElementById("subjectName").value;

            const level =
                document.getElementById("subjectLevel").value;


            showToast(
                `${name} added successfully`
            );


            subjectForm.reset();

            closeSubjectModal();

        }
    );


    /* ================= VIEW ================= */

    document
        .querySelectorAll(".view-subject")
        .forEach(button => {

            button.addEventListener(
                "click",
                () => {

                    const card =
                        button.closest(".subject-card");

                    const name =
                        card.dataset.name;

                    showToast(
                        `Viewing ${name}`
                    );

                }
            );

        });


    /* ================= EDIT ================= */

    document
        .querySelectorAll(".edit-subject")
        .forEach(button => {

            button.addEventListener(
                "click",
                () => {

                    const card =
                        button.closest(".subject-card");

                    const name =
                        card.dataset.name;

                    showToast(
                        `Editing ${name}`
                    );

                }
            );

        });


    /* ================= DELETE ================= */

    document
        .querySelectorAll(".delete-subject")
        .forEach(button => {

            button.addEventListener(
                "click",
                () => {

                    const card =
                        button.closest(".subject-card");

                    const name =
                        card.dataset.name;


                    const confirmed =
                        confirm(
                            `Are you sure you want to delete ${name}?`
                        );


                    if (confirmed) {

                        card.remove();

                        showToast(
                            `${name} deleted successfully`
                        );

                        filterSubjects();

                    }

                }
            );

        });


    /* ================= NOTIFICATION ================= */

    const notification =
        document.querySelector(".notification-btn");

    notification.addEventListener(
        "click",
        () => {

            showToast(
                "You have 4 new notifications"
            );

        }
    );


    /* ================= RESPONSIVE ================= */

    window.addEventListener(
        "resize",
        () => {

            if (window.innerWidth > 850) {
                closeMenu();
            }

        }
    );

});