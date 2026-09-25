/* =========================================================
   MathsWorld Admin - Teachers Management
   File: admin/js/teachers.js
   Frontend Only
========================================================= */

document.addEventListener("DOMContentLoaded", () => {

    /* =====================================================
       DEMO TEACHER DATA
    ===================================================== */

    let teachers = [
        {
            id: 1,
            name: "J. Abishek",
            email: "abishek@mathsworld.lk",
            phone: "0771055842",
            subject: "Mathematics",
            experience: "8 Years",
            qualification: "B.Sc Mathematics",
            registered: "2026-08-12",
            status: "Active",
            bio: "Experienced Mathematics teacher specializing in Advanced Level Mathematics."
        },
        {
            id: 2,
            name: "S. Kavitha",
            email: "kavitha@mathsworld.lk",
            phone: "0772345678",
            subject: "Physics",
            experience: "6 Years",
            qualification: "B.Sc Physics",
            registered: "2026-07-18",
            status: "Active",
            bio: "Physics teacher focused on practical and examination-oriented learning."
        },
        {
            id: 3,
            name: "R. Mohamed",
            email: "mohamed@mathsworld.lk",
            phone: "0773456789",
            subject: "Chemistry",
            experience: "5 Years",
            qualification: "B.Sc Chemistry",
            registered: "2026-06-22",
            status: "Active",
            bio: "Chemistry teacher for A/L students."
        },
        {
            id: 4,
            name: "N. Tharshini",
            email: "tharshini@mathsworld.lk",
            phone: "0774567890",
            subject: "Biology",
            experience: "7 Years",
            qualification: "B.Sc Biology",
            registered: "2026-05-15",
            status: "Active",
            bio: "Biology teacher specializing in A/L Biology."
        },
        {
            id: 5,
            name: "K. Pradeep",
            email: "pradeep@mathsworld.lk",
            phone: "0775678901",
            subject: "ICT",
            experience: "4 Years",
            qualification: "B.Sc IT",
            registered: "2026-08-28",
            status: "Pending",
            bio: "ICT teacher with experience in web development and programming."
        },
        {
            id: 6,
            name: "A. Fathima",
            email: "fathima@mathsworld.lk",
            phone: "0776789012",
            subject: "Mathematics",
            experience: "3 Years",
            qualification: "B.Sc Mathematics",
            registered: "2026-08-30",
            status: "Pending",
            bio: "Mathematics teacher for Grade 6-11 students."
        },
        {
            id: 7,
            name: "D. Suresh",
            email: "suresh@mathsworld.lk",
            phone: "0777890123",
            subject: "Physics",
            experience: "10 Years",
            qualification: "M.Sc Physics",
            registered: "2026-03-11",
            status: "Suspended",
            bio: "Experienced Physics educator."
        },
        {
            id: 8,
            name: "P. Nirosha",
            email: "nirosha@mathsworld.lk",
            phone: "0778901234",
            subject: "Chemistry",
            experience: "2 Years",
            qualification: "B.Sc Chemistry",
            registered: "2026-08-05",
            status: "Active",
            bio: "Chemistry teacher for school and A/L students."
        }
    ];


    /* =====================================================
       VARIABLES
    ===================================================== */

    let filteredTeachers = [...teachers];

    let currentPage = 1;

    const rowsPerPage = 5;

    let editingTeacherId = null;

    let deletingTeacherId = null;


    /* =====================================================
       ELEMENTS
    ===================================================== */

    const sidebar = document.getElementById("adminSidebar");
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const sidebarOverlay = document.getElementById("sidebarOverlay");

    const teacherSearch = document.getElementById("teacherSearch");
    const subjectFilter = document.getElementById("subjectFilter");
    const statusFilter = document.getElementById("statusFilter");
    const clearFilters = document.getElementById("clearFilters");

    const teachersTableBody = document.getElementById("teachersTableBody");
    const emptyState = document.getElementById("emptyState");

    const resultCount = document.getElementById("resultCount");

    const paginationInfo = document.getElementById("paginationInfo");
    const pagination = document.getElementById("pagination");

    const selectAll = document.getElementById("selectAll");

    const addTeacherBtn = document.getElementById("addTeacherBtn");

    const teacherFormModal = document.getElementById("teacherFormModal");
    const viewTeacherModal = document.getElementById("viewTeacherModal");
    const deleteModal = document.getElementById("deleteModal");
    const logoutModal = document.getElementById("logoutModal");

    const teacherForm = document.getElementById("teacherForm");


    /* =====================================================
       HELPER
    ===================================================== */

    function getInitials(name) {
        return name
            .split(" ")
            .map(word => word.charAt(0))
            .slice(0, 2)
            .join("")
            .toUpperCase();
    }


    function escapeHTML(text) {
        const div = document.createElement("div");
        div.textContent = text ?? "";
        return div.innerHTML;
    }


    function getSubjectClass(subject) {

        const classes = {
            "Mathematics": "maths",
            "Physics": "physics",
            "Chemistry": "chemistry",
            "Biology": "biology",
            "ICT": "ict"
        };

        return classes[subject] || "";
    }


    /* =====================================================
       RENDER TEACHERS
    ===================================================== */

    function renderTeachers() {

        if (!teachersTableBody) return;

        teachersTableBody.innerHTML = "";

        const start = (currentPage - 1) * rowsPerPage;

        const end = start + rowsPerPage;

        const pageTeachers = filteredTeachers.slice(start, end);


        if (pageTeachers.length === 0) {

            if (emptyState) {
                emptyState.style.display = "flex";
            }

            updatePagination();

            return;
        }


        if (emptyState) {
            emptyState.style.display = "none";
        }


        pageTeachers.forEach(teacher => {

            const row = document.createElement("tr");

            row.innerHTML = `
                <td>
                    <input 
                        type="checkbox" 
                        class="teacher-checkbox"
                        value="${teacher.id}"
                    >
                </td>

                <td>
                    <div class="teacher-cell">

                        <div class="teacher-avatar">
                            ${getInitials(teacher.name)}
                        </div>

                        <div>
                            <div class="teacher-name">
                                ${escapeHTML(teacher.name)}
                            </div>

                            <div class="teacher-sub">
                                ${escapeHTML(teacher.qualification)}
                            </div>
                        </div>

                    </div>
                </td>

                <td>
                    <div class="email-cell">
                        ${escapeHTML(teacher.email)}
                    </div>
                </td>

                <td>
                    <span class="subject-badge ${getSubjectClass(teacher.subject)}">
                        ${escapeHTML(teacher.subject)}
                    </span>
                </td>

                <td>
                    <span class="experience-badge">
                        ${escapeHTML(teacher.experience)}
                    </span>
                </td>

                <td>
                    <span class="status-badge status-${teacher.status.toLowerCase()}">
                        ${escapeHTML(teacher.status)}
                    </span>
                </td>

                <td>
                    <div class="action-buttons">

                        <button 
                            class="table-action view-action"
                            title="View"
                            data-id="${teacher.id}"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>

                        <button 
                            class="table-action edit-action"
                            title="Edit"
                            data-id="${teacher.id}"
                        >
                            <i class="fa-solid fa-pen"></i>
                        </button>

                        <button 
                            class="table-action delete-action"
                            title="Delete"
                            data-id="${teacher.id}"
                        >
                            <i class="fa-solid fa-trash"></i>
                        </button>

                    </div>
                </td>
            `;

            teachersTableBody.appendChild(row);
        });


        attachRowEvents();

        updatePagination();

        updateSelectAllState();
    }


    /* =====================================================
       FILTER TEACHERS
    ===================================================== */

    function filterTeachers() {

        const searchValue =
            teacherSearch?.value.trim().toLowerCase() || "";

        const subjectValue =
            subjectFilter?.value || "";

        const statusValue =
            statusFilter?.value || "";


        filteredTeachers = teachers.filter(teacher => {

            const matchesSearch =
                !searchValue ||
                teacher.name.toLowerCase().includes(searchValue) ||
                teacher.email.toLowerCase().includes(searchValue) ||
                teacher.phone.toLowerCase().includes(searchValue);


            const matchesSubject =
                !subjectValue ||
                teacher.subject === subjectValue;


            const matchesStatus =
                !statusValue ||
                teacher.status === statusValue;


            return (
                matchesSearch &&
                matchesSubject &&
                matchesStatus
            );
        });


        currentPage = 1;

        renderTeachers();

        updateStats();
    }


    /* =====================================================
       CLEAR FILTERS
    ===================================================== */

    clearFilters?.addEventListener("click", () => {

        if (teacherSearch) teacherSearch.value = "";

        if (subjectFilter) subjectFilter.value = "";

        if (statusFilter) statusFilter.value = "";

        filterTeachers();

        showToast(
            "Filters Cleared",
            "All teacher filters have been reset.",
            "success"
        );
    });


    teacherSearch?.addEventListener("input", filterTeachers);

    subjectFilter?.addEventListener("change", filterTeachers);

    statusFilter?.addEventListener("change", filterTeachers);


    /* =====================================================
       UPDATE STATS
    ===================================================== */

    function updateStats() {

        const total = teachers.length;

        const active =
            teachers.filter(t => t.status === "Active").length;

        const pending =
            teachers.filter(t => t.status === "Pending").length;

        const suspended =
            teachers.filter(t => t.status === "Suspended").length;


        const totalElement =
            document.getElementById("totalTeachers");

        const activeElement =
            document.getElementById("activeTeachers");

        const pendingElement =
            document.getElementById("pendingTeachers");

        const suspendedElement =
            document.getElementById("suspendedTeachers");


        if (totalElement) totalElement.textContent = total;

        if (activeElement) activeElement.textContent = active;

        if (pendingElement) pendingElement.textContent = pending;

        if (suspendedElement) suspendedElement.textContent = suspended;
    }


    /* =====================================================
       PAGINATION
    ===================================================== */

    function updatePagination() {

        if (!pagination) return;

        const totalPages =
            Math.ceil(filteredTeachers.length / rowsPerPage);


        if (paginationInfo) {

            if (filteredTeachers.length === 0) {

                paginationInfo.textContent =
                    "Showing 0 of 0 teachers";

            } else {

                const start =
                    (currentPage - 1) * rowsPerPage + 1;

                const end =
                    Math.min(
                        currentPage * rowsPerPage,
                        filteredTeachers.length
                    );

                paginationInfo.textContent =
                    `Showing ${start}-${end} of ${filteredTeachers.length} teachers`;
            }
        }


        pagination.innerHTML = "";


        if (totalPages <= 1) return;


        const previous = document.createElement("button");

        previous.className = "page-btn";

        previous.innerHTML =
            `<i class="fa-solid fa-chevron-left"></i>`;

        previous.disabled = currentPage === 1;

        previous.addEventListener("click", () => {

            if (currentPage > 1) {

                currentPage--;

                renderTeachers();
            }
        });

        pagination.appendChild(previous);


        for (let i = 1; i <= totalPages; i++) {

            const button = document.createElement("button");

            button.className =
                `page-btn ${i === currentPage ? "active" : ""}`;

            button.textContent = i;

            button.addEventListener("click", () => {

                currentPage = i;

                renderTeachers();
            });

            pagination.appendChild(button);
        }


        const next = document.createElement("button");

        next.className = "page-btn";

        next.innerHTML =
            `<i class="fa-solid fa-chevron-right"></i>`;

        next.disabled =
            currentPage === totalPages;

        next.addEventListener("click", () => {

            if (currentPage < totalPages) {

                currentPage++;

                renderTeachers();
            }
        });

        pagination.appendChild(next);
    }


    /* =====================================================
       RESULT COUNT
    ===================================================== */

    function updateResultCount() {

        if (resultCount) {

            resultCount.textContent =
                `${filteredTeachers.length} teacher${filteredTeachers.length !== 1 ? "s" : ""}`;
        }
    }


    /* =====================================================
       ROW EVENTS
    ===================================================== */

    function attachRowEvents() {

        document.querySelectorAll(".view-action")
            .forEach(button => {

                button.addEventListener("click", () => {

                    const id =
                        Number(button.dataset.id);

                    viewTeacher(id);
                });
            });


        document.querySelectorAll(".edit-action")
            .forEach(button => {

                button.addEventListener("click", () => {

                    const id =
                        Number(button.dataset.id);

                    openEditTeacher(id);
                });
            });


        document.querySelectorAll(".delete-action")
            .forEach(button => {

                button.addEventListener("click", () => {

                    const id =
                        Number(button.dataset.id);

                    openDeleteModal(id);
                });
            });


        document.querySelectorAll(".teacher-checkbox")
            .forEach(checkbox => {

                checkbox.addEventListener("change", updateSelectAllState);
            });
    }


    /* =====================================================
       VIEW TEACHER
    ===================================================== */

    function viewTeacher(id) {

        const teacher =
            teachers.find(t => t.id === id);

        if (!teacher) return;


        const name =
            document.getElementById("viewTeacherName");

        const email =
            document.getElementById("viewTeacherEmail");

        const phone =
            document.getElementById("viewTeacherPhone");

        const subject =
            document.getElementById("viewTeacherSubject");

        const experience =
            document.getElementById("viewTeacherExperience");

        const qualification =
            document.getElementById("viewTeacherQualification");

        const registered =
            document.getElementById("viewTeacherRegistered");

        const status =
            document.getElementById("viewTeacherStatus");

        const bio =
            document.getElementById("viewTeacherBio");

        const avatar =
            document.getElementById("viewTeacherAvatar");


        if (name) name.textContent = teacher.name;

        if (email) email.textContent = teacher.email;

        if (phone) phone.textContent = teacher.phone;

        if (subject) subject.textContent = teacher.subject;

        if (experience) experience.textContent = teacher.experience;

        if (qualification)
            qualification.textContent = teacher.qualification;

        if (registered)
            registered.textContent = formatDate(teacher.registered);

        if (bio)
            bio.textContent = teacher.bio;

        if (avatar)
            avatar.textContent = getInitials(teacher.name);


        if (status) {

            status.textContent = teacher.status;

            status.className =
                `profile-status status-${teacher.status.toLowerCase()}`;
        }


        openModal(viewTeacherModal);
    }


    /* =====================================================
       ADD TEACHER
    ===================================================== */

    addTeacherBtn?.addEventListener("click", () => {

        editingTeacherId = null;

        resetTeacherForm();

        const title =
            document.getElementById("teacherModalTitle");

        if (title)
            title.textContent = "Add New Teacher";


        openModal(teacherFormModal);
    });


    /* =====================================================
       EDIT TEACHER
    ===================================================== */

    function openEditTeacher(id) {

        const teacher =
            teachers.find(t => t.id === id);

        if (!teacher) return;


        editingTeacherId = id;


        setField("teacherName", teacher.name);

        setField("teacherEmail", teacher.email);

        setField("teacherPhone", teacher.phone);

        setField("teacherSubject", teacher.subject);

        setField("teacherExperience", teacher.experience);

        setField("teacherQualification", teacher.qualification);

        setField("teacherStatus", teacher.status);

        setField("teacherBio", teacher.bio);


        const title =
            document.getElementById("teacherModalTitle");

        if (title)
            title.textContent = "Edit Teacher";


        openModal(teacherFormModal);
    }


    function setField(id, value) {

        const element =
            document.getElementById(id);

        if (element)
            element.value = value;
    }


    function resetTeacherForm() {

        teacherForm?.reset();

        setField("teacherStatus", "Active");
    }


    /* =====================================================
       SAVE TEACHER
    ===================================================== */

    teacherForm?.addEventListener("submit", event => {

        event.preventDefault();


        const name =
            getFieldValue("teacherName");

        const email =
            getFieldValue("teacherEmail");

        const phone =
            getFieldValue("teacherPhone");

        const subject =
            getFieldValue("teacherSubject");

        const experience =
            getFieldValue("teacherExperience");

        const qualification =
            getFieldValue("teacherQualification");

        const status =
            getFieldValue("teacherStatus") || "Active";

        const bio =
            getFieldValue("teacherBio");


        if (!name || !email || !subject) {

            showToast(
                "Missing Information",
                "Please complete the required fields.",
                "error"
            );

            return;
        }


        if (editingTeacherId) {

            const teacher =
                teachers.find(
                    t => t.id === editingTeacherId
                );


            if (teacher) {

                teacher.name = name;

                teacher.email = email;

                teacher.phone = phone;

                teacher.subject = subject;

                teacher.experience = experience;

                teacher.qualification = qualification;

                teacher.status = status;

                teacher.bio = bio;
            }


            showToast(
                "Teacher Updated",
                `${name}'s information has been updated.`,
                "success"
            );

        } else {

            const newTeacher = {

                id: Date.now(),

                name,

                email,

                phone,

                subject,

                experience,

                qualification,

                registered:
                    new Date().toISOString().split("T")[0],

                status,

                bio
            };


            teachers.unshift(newTeacher);


            showToast(
                "Teacher Added",
                `${name} has been added successfully.`,
                "success"
            );
        }


        filteredTeachers = [...teachers];

        currentPage = 1;

        closeModal(teacherFormModal);

        renderTeachers();

        updateStats();

        updateResultCount();
    });


    function getFieldValue(id) {

        return (
            document.getElementById(id)?.value.trim() || ""
        );
    }


    /* =====================================================
       DELETE TEACHER
    ===================================================== */

    function openDeleteModal(id) {

        deletingTeacherId = id;

        const teacher =
            teachers.find(t => t.id === id);

        if (!teacher) return;


        const deleteName =
            document.getElementById("deleteTeacherName");

        if (deleteName)
            deleteName.textContent = teacher.name;


        openModal(deleteModal);
    }


    const confirmDelete =
        document.getElementById("confirmDelete");


    confirmDelete?.addEventListener("click", () => {

        if (!deletingTeacherId) return;


        const teacher =
            teachers.find(
                t => t.id === deletingTeacherId
            );


        teachers =
            teachers.filter(
                t => t.id !== deletingTeacherId
            );


        filteredTeachers =
            filteredTeachers.filter(
                t => t.id !== deletingTeacherId
            );


        closeModal(deleteModal);


        if (
            currentPage > 1 &&
            (currentPage - 1) * rowsPerPage >=
            filteredTeachers.length
        ) {

            currentPage--;
        }


        renderTeachers();

        updateStats();

        updateResultCount();


        showToast(
            "Teacher Deleted",
            `${teacher?.name || "Teacher"} has been removed.`,
            "success"
        );


        deletingTeacherId = null;
    });


    /* =====================================================
       SELECT ALL
    ===================================================== */

    selectAll?.addEventListener("change", () => {

        document
            .querySelectorAll(".teacher-checkbox")
            .forEach(checkbox => {

                checkbox.checked =
                    selectAll.checked;
            });
    });


    function updateSelectAllState() {

        if (!selectAll) return;


        const checkboxes =
            document.querySelectorAll(".teacher-checkbox");


        if (checkboxes.length === 0) {

            selectAll.checked = false;

            return;
        }


        selectAll.checked =
            [...checkboxes].every(
                checkbox => checkbox.checked
            );
    }


    /* =====================================================
       MODAL FUNCTIONS
    ===================================================== */

    function openModal(modal) {

        if (!modal) return;

        modal.classList.add("show");

        document.body.classList.add("modal-open");
    }


    function closeModal(modal) {

        if (!modal) return;

        modal.classList.remove("show");

        document.body.classList.remove("modal-open");
    }


    document.querySelectorAll("[data-close-modal]")
        .forEach(button => {

            button.addEventListener("click", () => {

                const modal =
                    button.closest(".modal-overlay");

                closeModal(modal);
            });
        });


    document.querySelectorAll(".modal-overlay")
        .forEach(modal => {

            modal.addEventListener("click", event => {

                if (event.target === modal) {

                    closeModal(modal);
                }
            });
        });


    /* =====================================================
       MOBILE SIDEBAR
    ===================================================== */

    mobileMenuBtn?.addEventListener("click", () => {

        sidebar?.classList.toggle("open");

        sidebarOverlay?.classList.toggle("show");
    });


    sidebarOverlay?.addEventListener("click", closeSidebar);


    function closeSidebar() {

        sidebar?.classList.remove("open");

        sidebarOverlay?.classList.remove("show");
    }


    /* =====================================================
       NOTIFICATION
    ===================================================== */

    const notificationBtn =
        document.getElementById("notificationBtn");


    notificationBtn?.addEventListener("click", () => {

        showToast(
            "Notifications",
            "You have 3 new teacher-related notifications.",
            "info"
        );
    });


    /* =====================================================
       PROFILE
    ===================================================== */

    const profileBtn =
        document.getElementById("profileBtn");


    profileBtn?.addEventListener("click", () => {

        window.location.href =
            "settings.html#profile";
    });


    /* =====================================================
       LOGOUT
    ===================================================== */

    const logoutBtn =
        document.getElementById("logoutBtn");

    const confirmLogout =
        document.getElementById("confirmLogout");


    logoutBtn?.addEventListener("click", () => {

        openModal(logoutModal);
    });


    confirmLogout?.addEventListener("click", () => {

        showToast(
            "Logged Out",
            "You have been logged out successfully.",
            "success"
        );


        setTimeout(() => {

            window.location.href = "../index.html";

        }, 1000);
    });


    /* =====================================================
       TOAST
    ===================================================== */

    function showToast(title, message, type = "success") {

        const toast =
            document.getElementById("toast");

        const toastTitle =
            document.getElementById("toastTitle");

        const toastMessage =
            document.getElementById("toastMessage");

        const toastIcon =
            toast?.querySelector(".toast-icon");


        if (!toast) return;


        if (toastTitle)
            toastTitle.textContent = title;

        if (toastMessage)
            toastMessage.textContent = message;


        if (toastIcon) {

            if (type === "error") {

                toastIcon.innerHTML =
                    '<i class="fa-solid fa-circle-xmark"></i>';

            } else if (type === "info") {

                toastIcon.innerHTML =
                    '<i class="fa-solid fa-circle-info"></i>';

            } else {

                toastIcon.innerHTML =
                    '<i class="fa-solid fa-circle-check"></i>';
            }
        }


        toast.classList.add("show");


        clearTimeout(window.mathsWorldToastTimer);


        window.mathsWorldToastTimer =
            setTimeout(() => {

                toast.classList.remove("show");

            }, 3500);
    }


    /* =====================================================
       DATE FORMAT
    ===================================================== */

    function formatDate(dateString) {

        if (!dateString) return "-";


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


    /* =====================================================
       ESCAPE KEY
    ===================================================== */

    document.addEventListener("keydown", event => {

        if (event.key !== "Escape") return;


        document
            .querySelectorAll(".modal-overlay.show")
            .forEach(modal => {

                closeModal(modal);
            });


        closeSidebar();
    });


    /* =====================================================
       INITIAL LOAD
    ===================================================== */

    renderTeachers();

    updateStats();

    updateResultCount();

});