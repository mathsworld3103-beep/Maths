/* =====================================================
   MATHSWORLD ADMIN - STUDENTS JS
===================================================== */

document.addEventListener("DOMContentLoaded", () => {

    /* =================================================
       DEMO STUDENT DATA
    ================================================= */

    let students = [
        {
            id: 1,
            name: "Kavindu Perera",
            email: "kavindu@gmail.com",
            grade: "Grade 11",
            phone: "0771234567",
            registered: "2026-09-02",
            status: "Active"
        },
        {
            id: 2,
            name: "Sathira Fernando",
            email: "sathira@gmail.com",
            grade: "Grade 10",
            phone: "0772345678",
            registered: "2026-09-04",
            status: "Active"
        },
        {
            id: 3,
            name: "Hiruni Silva",
            email: "hiruni@gmail.com",
            grade: "Grade 9",
            phone: "0773456789",
            registered: "2026-09-05",
            status: "Pending"
        },
        {
            id: 4,
            name: "Mohamed Ahamed",
            email: "ahamed@gmail.com",
            grade: "A/L",
            phone: "0774567890",
            registered: "2026-08-22",
            status: "Active"
        },
        {
            id: 5,
            name: "Nethmi Jayasinghe",
            email: "nethmi@gmail.com",
            grade: "Grade 8",
            phone: "0775678901",
            registered: "2026-08-18",
            status: "Active"
        },
        {
            id: 6,
            name: "Tharindu Kumara",
            email: "tharindu@gmail.com",
            grade: "Grade 7",
            phone: "0776789012",
            registered: "2026-08-10",
            status: "Suspended"
        },
        {
            id: 7,
            name: "Piumi Senanayake",
            email: "piumi@gmail.com",
            grade: "Grade 11",
            phone: "0777890123",
            registered: "2026-09-08",
            status: "Active"
        },
        {
            id: 8,
            name: "Dulshan Bandara",
            email: "dulshan@gmail.com",
            grade: "A/L",
            phone: "0778901234",
            registered: "2026-09-09",
            status: "Pending"
        },
        {
            id: 9,
            name: "Isuru Madushan",
            email: "isuru@gmail.com",
            grade: "Grade 6",
            phone: "0779012345",
            registered: "2026-08-05",
            status: "Active"
        },
        {
            id: 10,
            name: "Hansani Fernando",
            email: "hansani@gmail.com",
            grade: "Grade 10",
            phone: "0770123456",
            registered: "2026-09-11",
            status: "Active"
        },
        {
            id: 11,
            name: "Ravindu Silva",
            email: "ravindu@gmail.com",
            grade: "Grade 8",
            phone: "0781234567",
            registered: "2026-07-20",
            status: "Active"
        },
        {
            id: 12,
            name: "Ayesha Nadeesha",
            email: "ayesha@gmail.com",
            grade: "Grade 9",
            phone: "0782345678",
            registered: "2026-09-13",
            status: "Pending"
        }
    ];


    /* =================================================
       DOM ELEMENTS
    ================================================= */

    const sidebar = document.getElementById("adminSidebar");
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const sidebarOverlay = document.getElementById("sidebarOverlay");

    const studentSearch = document.getElementById("studentSearch");
    const gradeFilter = document.getElementById("gradeFilter");
    const statusFilter = document.getElementById("statusFilter");
    const clearFilters = document.getElementById("clearFilters");

    const tableBody = document.getElementById("studentsTableBody");
    const emptyState = document.getElementById("emptyState");

    const resultCount = document.getElementById("resultCount");
    const paginationInfo = document.getElementById("paginationInfo");
    const pagination = document.getElementById("pagination");

    const selectAll = document.getElementById("selectAll");

    const totalStudents = document.getElementById("totalStudents");
    const activeStudents = document.getElementById("activeStudents");
    const pendingStudents = document.getElementById("pendingStudents");
    const newStudents = document.getElementById("newStudents");

    const addStudentBtn = document.getElementById("addStudentBtn");

    const viewStudentModal = document.getElementById("viewStudentModal");
    const studentFormModal = document.getElementById("studentFormModal");
    const deleteModal = document.getElementById("deleteModal");
    const logoutModal = document.getElementById("logoutModal");

    const studentForm = document.getElementById("studentForm");

    const formModalTitle = document.getElementById("formModalTitle");

    const studentId = document.getElementById("studentId");
    const studentName = document.getElementById("studentName");
    const studentEmail = document.getElementById("studentEmail");
    const studentGrade = document.getElementById("studentGrade");
    const studentPhone = document.getElementById("studentPhone");
    const studentStatus = document.getElementById("studentStatus");

    const viewAvatar = document.getElementById("viewAvatar");
    const viewName = document.getElementById("viewName");
    const viewEmail = document.getElementById("viewEmail");
    const viewGrade = document.getElementById("viewGrade");
    const viewPhone = document.getElementById("viewPhone");
    const viewRegistered = document.getElementById("viewRegistered");
    const viewStatus = document.getElementById("viewStatus");

    const viewEditBtn = document.getElementById("viewEditBtn");

    const deleteStudentName = document.getElementById("deleteStudentName");
    const confirmDelete = document.getElementById("confirmDelete");

    const logoutBtn = document.getElementById("logoutBtn");
    const confirmLogout = document.getElementById("confirmLogout");

    const notificationBtn = document.getElementById("notificationBtn");
    const profileBtn = document.getElementById("profileBtn");

    const toast = document.getElementById("toast");
    const toastTitle = document.getElementById("toastTitle");
    const toastMessage = document.getElementById("toastMessage");
    const closeToast = document.getElementById("closeToast");


    /* =================================================
       PAGINATION
    ================================================= */

    const rowsPerPage = 7;

    let currentPage = 1;

    let currentFilteredStudents = [];

    let selectedStudentId = null;


    /* =================================================
       MOBILE SIDEBAR
    ================================================= */

    function openSidebar() {

        sidebar.classList.add("open");

        sidebarOverlay.classList.add("show");

    }


    function closeSidebar() {

        sidebar.classList.remove("open");

        sidebarOverlay.classList.remove("show");

    }


    mobileMenuBtn?.addEventListener("click", openSidebar);

    sidebarOverlay?.addEventListener("click", closeSidebar);


    /* =================================================
       MODAL FUNCTIONS
    ================================================= */

    function openModal(modal) {

        modal.classList.add("show");

        document.body.style.overflow = "hidden";

    }


    function closeModal(modal) {

        modal.classList.remove("show");

        document.body.style.overflow = "";

    }


    document.querySelectorAll("[data-close]").forEach(button => {

        button.addEventListener("click", () => {

            const modalId = button.dataset.close;

            const modal = document.getElementById(modalId);

            if (modal) {
                closeModal(modal);
            }

        });

    });


    document.querySelectorAll(".modal-overlay").forEach(overlay => {

        overlay.addEventListener("click", event => {

            if (event.target === overlay) {

                closeModal(overlay);

            }

        });

    });


    /* =================================================
       INITIAL RENDER
    ================================================= */

    renderStudents();

    updateStats();


    /* =================================================
       FILTER STUDENTS
    ================================================= */

    function getFilteredStudents() {

        const searchValue =
            studentSearch.value
                .trim()
                .toLowerCase();

        const selectedGrade =
            gradeFilter.value;

        const selectedStatus =
            statusFilter.value;


        return students.filter(student => {

            const matchesSearch =
                !searchValue ||
                student.name.toLowerCase().includes(searchValue) ||
                student.email.toLowerCase().includes(searchValue) ||
                student.phone.includes(searchValue);

            const matchesGrade =
                selectedGrade === "all" ||
                student.grade === selectedGrade;

            const matchesStatus =
                selectedStatus === "all" ||
                student.status === selectedStatus;

            return (
                matchesSearch &&
                matchesGrade &&
                matchesStatus
            );

        });

    }


    /* =================================================
       RENDER STUDENTS
    ================================================= */

    function renderStudents() {

        currentFilteredStudents =
            getFilteredStudents();

        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    currentFilteredStudents.length /
                    rowsPerPage
                )
            );


        if (currentPage > totalPages) {
            currentPage = totalPages;
        }


        const start =
            (currentPage - 1) *
            rowsPerPage;

        const end =
            start + rowsPerPage;


        const pageStudents =
            currentFilteredStudents.slice(
                start,
                end
            );


        tableBody.innerHTML = "";


        if (pageStudents.length === 0) {

            emptyState.style.display = "block";

            resultCount.textContent =
                "Showing 0 students";

        } else {

            emptyState.style.display = "none";

            pageStudents.forEach(student => {

                tableBody.appendChild(
                    createStudentRow(student)
                );

            });

            const first =
                start + 1;

            const last =
                Math.min(
                    end,
                    currentFilteredStudents.length
                );

            resultCount.textContent =
                `Showing ${first}-${last} of ${currentFilteredStudents.length} students`;

        }


        paginationInfo.textContent =
            `Page ${currentPage} of ${totalPages}`;

        renderPagination(totalPages);

        selectAll.checked = false;

    }


    /* =================================================
       CREATE TABLE ROW
    ================================================= */

    function createStudentRow(student) {

        const row =
            document.createElement("tr");


        const initials =
            getInitials(student.name);


        const statusClass =
            student.status
                .toLowerCase();


        row.innerHTML = `

            <td>
                <input
                    type="checkbox"
                    class="student-checkbox"
                    data-id="${student.id}"
                >
            </td>

            <td>

                <div class="student-cell">

                    <div class="student-avatar">
                        ${initials}
                    </div>

                    <div>
                        <div class="student-name">
                            ${student.name}
                        </div>

                        <div class="student-sub">
                            ID #MW${String(student.id).padStart(4, "0")}
                        </div>
                    </div>

                </div>

            </td>

            <td class="email-cell">
                ${student.email}
            </td>

            <td>
                <span class="grade-badge">
                    ${student.grade}
                </span>
            </td>

            <td>
                ${student.phone}
            </td>

            <td>
                ${formatDate(student.registered)}
            </td>

            <td>

                <span class="status-badge status-${statusClass}">
                    ${student.status}
                </span>

            </td>

            <td>

                <div class="action-buttons">

                    <button
                        class="table-action"
                        title="View"
                        data-action="view"
                        data-id="${student.id}"
                    >
                        <i class="fa-regular fa-eye"></i>
                    </button>

                    <button
                        class="table-action"
                        title="Edit"
                        data-action="edit"
                        data-id="${student.id}"
                    >
                        <i class="fa-solid fa-pen"></i>
                    </button>

                    <button
                        class="table-action delete"
                        title="Delete"
                        data-action="delete"
                        data-id="${student.id}"
                    >
                        <i class="fa-solid fa-trash"></i>
                    </button>

                </div>

            </td>
        `;


        return row;

    }


    /* =================================================
       TABLE ACTIONS
    ================================================= */

    tableBody.addEventListener("click", event => {

        const button =
            event.target.closest("[data-action]");

        if (!button) return;


        const id =
            Number(button.dataset.id);

        const action =
            button.dataset.action;


        if (action === "view") {

            openViewStudent(id);

        }

        if (action === "edit") {

            openEditStudent(id);

        }

        if (action === "delete") {

            openDeleteStudent(id);

        }

    });


    /* =================================================
       VIEW STUDENT
    ================================================= */

    function openViewStudent(id) {

        const student =
            students.find(
                item => item.id === id
            );

        if (!student) return;


        selectedStudentId = id;


        viewAvatar.textContent =
            getInitials(student.name);

        viewName.textContent =
            student.name;

        viewEmail.textContent =
            student.email;

        viewGrade.textContent =
            student.grade;

        viewPhone.textContent =
            student.phone;

        viewRegistered.textContent =
            formatDate(student.registered);

        viewStatus.textContent =
            student.status;


        viewStatus.className =
            `profile-status status-${student.status.toLowerCase()}`;


        openModal(viewStudentModal);

    }


    /* =================================================
       EDIT FROM VIEW MODAL
    ================================================= */

    viewEditBtn.addEventListener("click", () => {

        if (!selectedStudentId) return;

        closeModal(viewStudentModal);

        setTimeout(() => {

            openEditStudent(selectedStudentId);

        }, 150);

    });


    /* =================================================
       ADD STUDENT
    ================================================= */

    addStudentBtn.addEventListener("click", () => {

        resetStudentForm();

        formModalTitle.textContent =
            "Add Student";

        openModal(studentFormModal);

    });


    /* =================================================
       EDIT STUDENT
    ================================================= */

    function openEditStudent(id) {

        const student =
            students.find(
                item => item.id === id
            );

        if (!student) return;


        formModalTitle.textContent =
            "Edit Student";


        studentId.value =
            student.id;

        studentName.value =
            student.name;

        studentEmail.value =
            student.email;

        studentGrade.value =
            student.grade;

        studentPhone.value =
            student.phone;

        studentStatus.value =
            student.status;


        openModal(studentFormModal);

    }


    /* =================================================
       FORM SUBMIT
    ================================================= */

    studentForm.addEventListener("submit", event => {

        event.preventDefault();


        const id =
            Number(studentId.value);


        const studentData = {

            name: studentName.value.trim(),

            email: studentEmail.value.trim(),

            grade: studentGrade.value,

            phone: studentPhone.value.trim(),

            status: studentStatus.value

        };


        if (id) {

            const index =
                students.findIndex(
                    student => student.id === id
                );


            if (index !== -1) {

                students[index] = {

                    ...students[index],

                    ...studentData

                };

                showToast(
                    "Student Updated",
                    `${studentData.name} has been updated successfully.`
                );

            }

        } else {

            const newStudent = {

                id:
                    Date.now(),

                ...studentData,

                registered:
                    getTodayDate()

            };


            students.unshift(newStudent);

            currentPage = 1;


            showToast(
                "Student Added",
                `${studentData.name} has been added successfully.`
            );

        }


        closeModal(studentFormModal);

        resetStudentForm();

        renderStudents();

        updateStats();

    });


    /* =================================================
       RESET FORM
    ================================================= */

    function resetStudentForm() {

        studentForm.reset();

        studentId.value = "";

        studentStatus.value = "Active";

    }


    /* =================================================
       DELETE STUDENT
    ================================================= */

    function openDeleteStudent(id) {

        const student =
            students.find(
                item => item.id === id
            );

        if (!student) return;


        selectedStudentId = id;

        deleteStudentName.textContent =
            student.name;


        openModal(deleteModal);

    }


    confirmDelete.addEventListener("click", () => {

        if (!selectedStudentId) return;


        const student =
            students.find(
                item => item.id === selectedStudentId
            );


        students =
            students.filter(
                item => item.id !== selectedStudentId
            );


        closeModal(deleteModal);


        showToast(
            "Student Deleted",
            `${student?.name || "Student"} was removed successfully.`
        );


        selectedStudentId = null;


        renderStudents();

        updateStats();

    });


    /* =================================================
       SEARCH
    ================================================= */

    studentSearch.addEventListener("input", () => {

        currentPage = 1;

        renderStudents();

    });


    /* =================================================
       FILTERS
    ================================================= */

    gradeFilter.addEventListener("change", () => {

        currentPage = 1;

        renderStudents();

    });


    statusFilter.addEventListener("change", () => {

        currentPage = 1;

        renderStudents();

    });


    /* =================================================
       CLEAR FILTERS
    ================================================= */

    clearFilters.addEventListener("click", () => {

        studentSearch.value = "";

        gradeFilter.value = "all";

        statusFilter.value = "all";

        currentPage = 1;

        renderStudents();

        showToast(
            "Filters Cleared",
            "All student filters have been reset."
        );

    });


    /* =================================================
       SELECT ALL
    ================================================= */

    selectAll.addEventListener("change", () => {

        const checkboxes =
            document.querySelectorAll(
                ".student-checkbox"
            );


        checkboxes.forEach(checkbox => {

            checkbox.checked =
                selectAll.checked;

        });

    });


    /* =================================================
       PAGINATION
    ================================================= */

    function renderPagination(totalPages) {

        pagination.innerHTML = "";


        const previous =
            document.createElement("button");

        previous.className =
            "page-btn";

        previous.innerHTML =
            '<i class="fa-solid fa-chevron-left"></i>';

        if (currentPage === 1) {
            previous.classList.add("disabled");
        }


        previous.addEventListener("click", () => {

            if (currentPage > 1) {

                currentPage--;

                renderStudents();

            }

        });


        pagination.appendChild(previous);


        for (
            let page = 1;
            page <= totalPages;
            page++
        ) {

            const button =
                document.createElement("button");

            button.className =
                "page-btn";

            if (page === currentPage) {
                button.classList.add("active");
            }

            button.textContent =
                page;


            button.addEventListener("click", () => {

                currentPage = page;

                renderStudents();

            });


            pagination.appendChild(button);

        }


        const next =
            document.createElement("button");

        next.className =
            "page-btn";

        next.innerHTML =
            '<i class="fa-solid fa-chevron-right"></i>';


        if (currentPage === totalPages) {
            next.classList.add("disabled");
        }


        next.addEventListener("click", () => {

            if (currentPage < totalPages) {

                currentPage++;

                renderStudents();

            }

        });


        pagination.appendChild(next);

    }


    /* =================================================
       UPDATE STATS
    ================================================= */

    function updateStats() {

        totalStudents.textContent =
            students.length;


        activeStudents.textContent =
            students.filter(
                student =>
                    student.status === "Active"
            ).length;


        pendingStudents.textContent =
            students.filter(
                student =>
                    student.status === "Pending"
            ).length;


        const currentMonth =
            new Date().getMonth();

        const currentYear =
            new Date().getFullYear();


        const newCount =
            students.filter(student => {

                const date =
                    new Date(student.registered);

                return (
                    date.getMonth() === currentMonth &&
                    date.getFullYear() === currentYear
                );

            }).length;


        newStudents.textContent =
            newCount;

    }


    /* =================================================
       TOAST
    ================================================= */

    let toastTimer;


    function showToast(title, message) {

        toastTitle.textContent =
            title;

        toastMessage.textContent =
            message;


        toast.classList.add("show");


        clearTimeout(toastTimer);


        toastTimer =
            setTimeout(() => {

                toast.classList.remove("show");

            }, 3500);

    }


    closeToast.addEventListener("click", () => {

        toast.classList.remove("show");

    });


    /* =================================================
       LOGOUT
    ================================================= */

    logoutBtn.addEventListener("click", () => {

        openModal(logoutModal);

    });


    confirmLogout.addEventListener("click", () => {

        closeModal(logoutModal);


        showToast(
            "Logged Out",
            "Demo logout completed."
        );


        setTimeout(() => {

            window.location.href =
                "../login.html";

        }, 1200);

    });


    /* =================================================
       NOTIFICATION
    ================================================= */

    notificationBtn.addEventListener("click", () => {

        showToast(
            "Notifications",
            "You have 5 new notifications."
        );

    });


    /* =================================================
       PROFILE
    ================================================= */

    profileBtn.addEventListener("click", () => {

        window.location.href =
            "settings.html#profile";

    });


    /* =================================================
       KEYBOARD
    ================================================= */

    document.addEventListener("keydown", event => {

        if (event.key !== "Escape") return;


        document
            .querySelectorAll(".modal-overlay.show")
            .forEach(modal => {

                closeModal(modal);

            });


        closeSidebar();

    });


    /* =================================================
       HELPERS
    ================================================= */

    function getInitials(name) {

        return name
            .split(" ")
            .map(word => word.charAt(0))
            .slice(0, 2)
            .join("")
            .toUpperCase();

    }


    function formatDate(dateString) {

        const date =
            new Date(dateString);


        return date.toLocaleDateString(
            "en-GB",
            {
                day: "2-digit",
                month: "short",
                year: "numeric"
            }
        );

    }


    function getTodayDate() {

        const today =
            new Date();


        return today
            .toISOString()
            .split("T")[0];

    }


    /* =================================================
       CONSOLE
    ================================================= */

    console.log(
        "%c MathsWorld Students Management Loaded ",
        "background:#071b3a;color:#38bdf8;padding:8px;border-radius:5px;"
    );

});