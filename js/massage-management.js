/* =========================================================
   MathsWorld Admin - Messages Management
   File: admin/js/messages.js
   Frontend Only
========================================================= */

document.addEventListener("DOMContentLoaded", () => {

    /* =====================================================
       DEMO MESSAGE DATA
    ===================================================== */

    let messages = [
        {
            id: 1,
            name: "Ahamed Rizwan",
            email: "rizwan@gmail.com",
            phone: "0771234567",
            subject: "A/L Combined Mathematics Class",
            message:
                "I would like to know more about the 2028 A/L Combined Mathematics classes. Please send me the class schedule and registration details.",
            type: "Enquiry",
            status: "Unread",
            date: "2026-09-17T09:30:00"
        },
        {
            id: 2,
            name: "Fathima Nusrath",
            email: "fathima@gmail.com",
            phone: "0772345678",
            subject: "Past Papers",
            message:
                "Can you please upload the latest Grade 11 Mathematics past papers?",
            type: "Request",
            status: "Read",
            date: "2026-09-16T15:20:00"
        },
        {
            id: 3,
            name: "Mohamed Azeem",
            email: "azeem@gmail.com",
            phone: "0773456789",
            subject: "Physics Class",
            message:
                "I need information about the A/L Physics online class.",
            type: "Enquiry",
            status: "Unread",
            date: "2026-09-16T11:45:00"
        },
        {
            id: 4,
            name: "Kavindi Perera",
            email: "kavindi@gmail.com",
            phone: "0774567890",
            subject: "Website Feedback",
            message:
                "The new MathsWorld website is very useful. I would like to suggest adding more model papers.",
            type: "Feedback",
            status: "Read",
            date: "2026-09-15T18:10:00"
        },
        {
            id: 5,
            name: "S. Tharindu",
            email: "tharindu@gmail.com",
            phone: "0775678901",
            subject: "Registration Help",
            message:
                "I am having trouble registering for the online Mathematics class. Please help me.",
            type: "Support",
            status: "Unread",
            date: "2026-09-15T13:25:00"
        },
        {
            id: 6,
            name: "Nirosha Fernando",
            email: "nirosha@gmail.com",
            phone: "0776789012",
            subject: "Chemistry Materials",
            message:
                "Are Chemistry notes available for A/L students?",
            type: "Enquiry",
            status: "Read",
            date: "2026-09-14T10:15:00"
        },
        {
            id: 7,
            name: "R. Suresh",
            email: "suresh@gmail.com",
            phone: "0777890123",
            subject: "Teacher Registration",
            message:
                "I would like to apply as a teacher on the MathsWorld platform.",
            type: "Enquiry",
            status: "Unread",
            date: "2026-09-13T16:40:00"
        },
        {
            id: 8,
            name: "A. Hansika",
            email: "hansika@gmail.com",
            phone: "0778901234",
            subject: "Grade 10 Materials",
            message:
                "Please add more Grade 10 Mathematics study materials.",
            type: "Request",
            status: "Read",
            date: "2026-09-12T12:30:00"
        }
    ];


    /* =====================================================
       VARIABLES
    ===================================================== */

    let filteredMessages = [...messages];

    let currentPage = 1;

    const rowsPerPage = 5;

    let selectedMessageId = null;

    let deletingMessageId = null;


    /* =====================================================
       ELEMENTS
    ===================================================== */

    const sidebar =
        document.getElementById("adminSidebar");

    const mobileMenuBtn =
        document.getElementById("mobileMenuBtn");

    const sidebarOverlay =
        document.getElementById("sidebarOverlay");

    const messageSearch =
        document.getElementById("messageSearch");

    const statusFilter =
        document.getElementById("statusFilter");

    const typeFilter =
        document.getElementById("typeFilter");

    const clearFilters =
        document.getElementById("clearFilters");

    const messagesTableBody =
        document.getElementById("messagesTableBody");

    const emptyState =
        document.getElementById("emptyState");

    const resultCount =
        document.getElementById("resultCount");

    const paginationInfo =
        document.getElementById("paginationInfo");

    const pagination =
        document.getElementById("pagination");

    const selectAll =
        document.getElementById("selectAll");

    const viewMessageModal =
        document.getElementById("viewMessageModal");

    const deleteModal =
        document.getElementById("deleteModal");

    const logoutModal =
        document.getElementById("logoutModal");


    /* =====================================================
       HELPER FUNCTIONS
    ===================================================== */

    function escapeHTML(text) {

        const div = document.createElement("div");

        div.textContent = text ?? "";

        return div.innerHTML;
    }


    function getInitials(name) {

        return name
            .split(" ")
            .map(word => word.charAt(0))
            .slice(0, 2)
            .join("")
            .toUpperCase();
    }


    function formatDate(dateString) {

        const date = new Date(dateString);

        return date.toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric"
        });
    }


    function formatDateTime(dateString) {

        const date = new Date(dateString);

        return date.toLocaleString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit"
        });
    }


    function getTypeClass(type) {

        const classes = {
            "Enquiry": "enquiry",
            "Request": "request",
            "Feedback": "feedback",
            "Support": "support"
        };

        return classes[type] || "";
    }


    /* =====================================================
       RENDER MESSAGES
    ===================================================== */

    function renderMessages() {

        if (!messagesTableBody) return;

        messagesTableBody.innerHTML = "";

        const start =
            (currentPage - 1) * rowsPerPage;

        const end =
            start + rowsPerPage;

        const pageMessages =
            filteredMessages.slice(start, end);


        if (pageMessages.length === 0) {

            if (emptyState) {
                emptyState.style.display = "flex";
            }

            updatePagination();

            return;
        }


        if (emptyState) {
            emptyState.style.display = "none";
        }


        pageMessages.forEach(message => {

            const row = document.createElement("tr");

            row.className =
                message.status === "Unread"
                    ? "message-unread"
                    : "";


            row.innerHTML = `
                <td>
                    <input
                        type="checkbox"
                        class="message-checkbox"
                        value="${message.id}"
                    >
                </td>

                <td>
                    <div class="message-sender">

                        <div class="message-avatar">
                            ${getInitials(message.name)}
                        </div>

                        <div>
                            <div class="sender-name">
                                ${escapeHTML(message.name)}
                            </div>

                            <div class="sender-email">
                                ${escapeHTML(message.email)}
                            </div>
                        </div>

                    </div>
                </td>

                <td>
                    <div class="message-subject">
                        ${escapeHTML(message.subject)}
                    </div>

                    <div class="message-preview">
                        ${escapeHTML(
                            message.message.substring(0, 55)
                        )}${message.message.length > 55 ? "..." : ""}
                    </div>
                </td>

                <td>
                    <span class="message-type ${getTypeClass(message.type)}">
                        ${escapeHTML(message.type)}
                    </span>
                </td>

                <td>
                    <span class="message-status status-${message.status.toLowerCase()}">
                        ${escapeHTML(message.status)}
                    </span>
                </td>

                <td>
                    <span class="message-date">
                        ${formatDate(message.date)}
                    </span>
                </td>

                <td>
                    <div class="action-buttons">

                        <button
                            class="table-action view-action"
                            title="View Message"
                            data-id="${message.id}"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>

                        <button
                            class="table-action read-action"
                            title="${message.status === "Read" ? "Mark Unread" : "Mark Read"}"
                            data-id="${message.id}"
                        >
                            <i class="fa-solid ${
                                message.status === "Read"
                                    ? "fa-envelope"
                                    : "fa-envelope-open"
                            }"></i>
                        </button>

                        <button
                            class="table-action delete-action"
                            title="Delete Message"
                            data-id="${message.id}"
                        >
                            <i class="fa-solid fa-trash"></i>
                        </button>

                    </div>
                </td>
            `;

            messagesTableBody.appendChild(row);
        });


        attachRowEvents();

        updatePagination();

        updateSelectAllState();
    }


    /* =====================================================
       FILTER MESSAGES
    ===================================================== */

    function filterMessages() {

        const search =
            messageSearch?.value
                .trim()
                .toLowerCase() || "";

        const status =
            statusFilter?.value || "";

        const type =
            typeFilter?.value || "";


        filteredMessages = messages.filter(message => {

            const matchesSearch =
                !search ||
                message.name.toLowerCase().includes(search) ||
                message.email.toLowerCase().includes(search) ||
                message.subject.toLowerCase().includes(search) ||
                message.message.toLowerCase().includes(search);


            const matchesStatus =
                !status ||
                message.status === status;


            const matchesType =
                !type ||
                message.type === type;


            return (
                matchesSearch &&
                matchesStatus &&
                matchesType
            );
        });


        currentPage = 1;

        renderMessages();

        updateResultCount();

        updateStats();
    }


    messageSearch?.addEventListener(
        "input",
        filterMessages
    );


    statusFilter?.addEventListener(
        "change",
        filterMessages
    );


    typeFilter?.addEventListener(
        "change",
        filterMessages
    );


    /* =====================================================
       CLEAR FILTERS
    ===================================================== */

    clearFilters?.addEventListener("click", () => {

        if (messageSearch)
            messageSearch.value = "";

        if (statusFilter)
            statusFilter.value = "";

        if (typeFilter)
            typeFilter.value = "";


        filterMessages();


        showToast(
            "Filters Cleared",
            "Message filters have been reset.",
            "success"
        );
    });


    /* =====================================================
       STATS
    ===================================================== */

    function updateStats() {

        const total =
            messages.length;

        const unread =
            messages.filter(
                message => message.status === "Unread"
            ).length;

        const read =
            messages.filter(
                message => message.status === "Read"
            ).length;

        const enquiries =
            messages.filter(
                message => message.type === "Enquiry"
            ).length;


        setText("totalMessages", total);

        setText("unreadMessages", unread);

        setText("readMessages", read);

        setText("enquiryMessages", enquiries);
    }


    function setText(id, value) {

        const element =
            document.getElementById(id);

        if (element)
            element.textContent = value;
    }


    /* =====================================================
       RESULT COUNT
    ===================================================== */

    function updateResultCount() {

        if (!resultCount) return;


        resultCount.textContent =
            `${filteredMessages.length} message${
                filteredMessages.length !== 1
                    ? "s"
                    : ""
            }`;
    }


    /* =====================================================
       ROW EVENTS
    ===================================================== */

    function attachRowEvents() {

        document
            .querySelectorAll(".view-action")
            .forEach(button => {

                button.addEventListener("click", () => {

                    viewMessage(
                        Number(button.dataset.id)
                    );
                });
            });


        document
            .querySelectorAll(".read-action")
            .forEach(button => {

                button.addEventListener("click", () => {

                    toggleMessageStatus(
                        Number(button.dataset.id)
                    );
                });
            });


        document
            .querySelectorAll(".delete-action")
            .forEach(button => {

                button.addEventListener("click", () => {

                    openDeleteModal(
                        Number(button.dataset.id)
                    );
                });
            });


        document
            .querySelectorAll(".message-checkbox")
            .forEach(checkbox => {

                checkbox.addEventListener(
                    "change",
                    updateSelectAllState
                );
            });
    }


    /* =====================================================
       VIEW MESSAGE
    ===================================================== */

    function viewMessage(id) {

        const message =
            messages.find(
                item => item.id === id
            );

        if (!message) return;


        selectedMessageId = id;


        setText(
            "viewMessageName",
            message.name
        );

        setText(
            "viewMessageEmail",
            message.email
        );

        setText(
            "viewMessagePhone",
            message.phone
        );

        setText(
            "viewMessageSubject",
            message.subject
        );

        setText(
            "viewMessageType",
            message.type
        );

        setText(
            "viewMessageDate",
            formatDateTime(message.date)
        );

        setText(
            "viewMessageContent",
            message.message
        );


        const avatar =
            document.getElementById(
                "viewMessageAvatar"
            );

        if (avatar) {

            avatar.textContent =
                getInitials(message.name);
        }


        const status =
            document.getElementById(
                "viewMessageStatus"
            );

        if (status) {

            status.textContent =
                message.status;

            status.className =
                `message-status status-${message.status.toLowerCase()}`;
        }


        /*
           Opening a message automatically
           marks it as read.
        */

        if (message.status === "Unread") {

            message.status = "Read";

            updateStats();
        }


        openModal(viewMessageModal);

        renderMessages();
    }


    /* =====================================================
       MARK READ / UNREAD
    ===================================================== */

    function toggleMessageStatus(id) {

        const message =
            messages.find(
                item => item.id === id
            );

        if (!message) return;


        if (message.status === "Unread") {

            message.status = "Read";

            showToast(
                "Message Read",
                `${message.name}'s message was marked as read.`,
                "success"
            );

        } else {

            message.status = "Unread";

            showToast(
                "Message Unread",
                `${message.name}'s message was marked as unread.`,
                "info"
            );
        }


        filterMessages();
    }


    /* =====================================================
       MARK SELECTED AS READ
    ===================================================== */

    const markReadBtn =
        document.getElementById("markReadBtn");


    markReadBtn?.addEventListener("click", () => {

        const selected =
            getSelectedMessageIds();


        if (selected.length === 0) {

            showToast(
                "No Messages Selected",
                "Please select at least one message.",
                "error"
            );

            return;
        }


        messages.forEach(message => {

            if (selected.includes(message.id)) {

                message.status = "Read";
            }
        });


        filterMessages();


        showToast(
            "Messages Updated",
            `${selected.length} message${
                selected.length > 1 ? "s" : ""
            } marked as read.`,
            "success"
        );
    });


    /* =====================================================
       DELETE SELECTED
    ===================================================== */

    const deleteSelectedBtn =
        document.getElementById("deleteSelectedBtn");


    deleteSelectedBtn?.addEventListener("click", () => {

        const selected =
            getSelectedMessageIds();


        if (selected.length === 0) {

            showToast(
                "No Messages Selected",
                "Please select at least one message.",
                "error"
            );

            return;
        }


        messages =
            messages.filter(
                message =>
                    !selected.includes(message.id)
            );


        filteredMessages =
            filteredMessages.filter(
                message =>
                    !selected.includes(message.id)
            );


        currentPage = 1;


        renderMessages();

        updateStats();

        updateResultCount();


        showToast(
            "Messages Deleted",
            `${selected.length} message${
                selected.length > 1 ? "s" : ""
            } deleted successfully.`,
            "success"
        );
    });


    function getSelectedMessageIds() {

        return [
            ...document.querySelectorAll(
                ".message-checkbox:checked"
            )
        ].map(
            checkbox =>
                Number(checkbox.value)
        );
    }


    /* =====================================================
       SELECT ALL
    ===================================================== */

    selectAll?.addEventListener("change", () => {

        document
            .querySelectorAll(".message-checkbox")
            .forEach(checkbox => {

                checkbox.checked =
                    selectAll.checked;
            });
    });


    function updateSelectAllState() {

        if (!selectAll) return;


        const checkboxes =
            document.querySelectorAll(
                ".message-checkbox"
            );


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
       DELETE MODAL
    ===================================================== */

    function openDeleteModal(id) {

        deletingMessageId = id;


        const message =
            messages.find(
                item => item.id === id
            );

        if (!message) return;


        setText(
            "deleteMessageName",
            message.name
        );


        openModal(deleteModal);
    }


    const confirmDelete =
        document.getElementById(
            "confirmDelete"
        );


    confirmDelete?.addEventListener("click", () => {

        if (!deletingMessageId) return;


        const message =
            messages.find(
                item =>
                    item.id === deletingMessageId
            );


        messages =
            messages.filter(
                item =>
                    item.id !== deletingMessageId
            );


        filteredMessages =
            filteredMessages.filter(
                item =>
                    item.id !== deletingMessageId
            );


        closeModal(deleteModal);


        currentPage = 1;


        renderMessages();

        updateStats();

        updateResultCount();


        showToast(
            "Message Deleted",
            `${message?.name || "Message"} has been deleted.`,
            "success"
        );


        deletingMessageId = null;
    });


    /* =====================================================
       PAGINATION
    ===================================================== */

    function updatePagination() {

        if (!pagination) return;


        const totalPages =
            Math.ceil(
                filteredMessages.length /
                rowsPerPage
            );


        if (paginationInfo) {

            if (filteredMessages.length === 0) {

                paginationInfo.textContent =
                    "Showing 0 of 0 messages";

            } else {

                const start =
                    (currentPage - 1) *
                    rowsPerPage + 1;

                const end =
                    Math.min(
                        currentPage * rowsPerPage,
                        filteredMessages.length
                    );


                paginationInfo.textContent =
                    `Showing ${start}-${end} of ${filteredMessages.length} messages`;
            }
        }


        pagination.innerHTML = "";


        if (totalPages <= 1) return;


        const previous =
            document.createElement("button");

        previous.className = "page-btn";

        previous.innerHTML =
            `<i class="fa-solid fa-chevron-left"></i>`;

        previous.disabled =
            currentPage === 1;


        previous.addEventListener("click", () => {

            if (currentPage > 1) {

                currentPage--;

                renderMessages();
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
                `page-btn ${
                    page === currentPage
                        ? "active"
                        : ""
                }`;

            button.textContent = page;


            button.addEventListener("click", () => {

                currentPage = page;

                renderMessages();
            });


            pagination.appendChild(button);
        }


        const next =
            document.createElement("button");

        next.className = "page-btn";

        next.innerHTML =
            `<i class="fa-solid fa-chevron-right"></i>`;

        next.disabled =
            currentPage === totalPages;


        next.addEventListener("click", () => {

            if (currentPage < totalPages) {

                currentPage++;

                renderMessages();
            }
        });


        pagination.appendChild(next);
    }


    /* =====================================================
       MODAL FUNCTIONS
    ===================================================== */

    function openModal(modal) {

        if (!modal) return;

        modal.classList.add("show");

        document.body.classList.add(
            "modal-open"
        );
    }


    function closeModal(modal) {

        if (!modal) return;

        modal.classList.remove("show");

        document.body.classList.remove(
            "modal-open"
        );
    }


    document
        .querySelectorAll("[data-close-modal]")
        .forEach(button => {

            button.addEventListener("click", () => {

                const modal =
                    button.closest(
                        ".modal-overlay"
                    );

                closeModal(modal);
            });
        });


    document
        .querySelectorAll(".modal-overlay")
        .forEach(modal => {

            modal.addEventListener(
                "click",
                event => {

                    if (
                        event.target === modal
                    ) {

                        closeModal(modal);
                    }
                }
            );
        });


    /* =====================================================
       REPLY BUTTON
    ===================================================== */

    const replyMessageBtn =
        document.getElementById(
            "replyMessageBtn"
        );


    replyMessageBtn?.addEventListener(
        "click",
        () => {

            const message =
                messages.find(
                    item =>
                        item.id ===
                        selectedMessageId
                );


            if (!message) return;


            /*
               Frontend demo only.
               Later this can connect to
               your backend/email service.
            */

            closeModal(viewMessageModal);


            showToast(
                "Reply",
                `Reply window for ${message.name} will be connected to the backend later.`,
                "info"
            );
        }
    );


    /* =====================================================
       MOBILE SIDEBAR
    ===================================================== */

    mobileMenuBtn?.addEventListener(
        "click",
        () => {

            sidebar?.classList.toggle("open");

            sidebarOverlay?.classList.toggle(
                "show"
            );
        }
    );


    sidebarOverlay?.addEventListener(
        "click",
        closeSidebar
    );


    function closeSidebar() {

        sidebar?.classList.remove("open");

        sidebarOverlay?.classList.remove(
            "show"
        );
    }


    /* =====================================================
       NOTIFICATIONS
    ===================================================== */

    const notificationBtn =
        document.getElementById(
            "notificationBtn"
        );


    notificationBtn?.addEventListener(
        "click",
        () => {

            const unread =
                messages.filter(
                    message =>
                        message.status ===
                        "Unread"
                ).length;


            showToast(
                "Notifications",
                `You have ${unread} unread message${
                    unread !== 1 ? "s" : ""
                }.`,
                "info"
            );
        }
    );


    /* =====================================================
       PROFILE
    ===================================================== */

    const profileBtn =
        document.getElementById(
            "profileBtn"
        );


    profileBtn?.addEventListener(
        "click",
        () => {

            window.location.href =
                "settings.html#profile";
        }
    );


    /* =====================================================
       LOGOUT
    ===================================================== */

    const logoutBtn =
        document.getElementById(
            "logoutBtn"
        );

    const confirmLogout =
        document.getElementById(
            "confirmLogout"
        );


    logoutBtn?.addEventListener(
        "click",
        () => {

            openModal(logoutModal);
        }
    );


    confirmLogout?.addEventListener(
        "click",
        () => {

            showToast(
                "Logged Out",
                "You have been logged out successfully.",
                "success"
            );


            setTimeout(() => {

                window.location.href =
                    "../index.html";

            }, 1000);
        }
    );


    /* =====================================================
       TOAST
    ===================================================== */

    function showToast(
        title,
        message,
        type = "success"
    ) {

        const toast =
            document.getElementById(
                "toast"
            );

        const toastTitle =
            document.getElementById(
                "toastTitle"
            );

        const toastMessage =
            document.getElementById(
                "toastMessage"
            );

        const toastIcon =
            toast?.querySelector(
                ".toast-icon"
            );


        if (!toast) return;


        if (toastTitle)
            toastTitle.textContent = title;


        if (toastMessage)
            toastMessage.textContent =
                message;


        if (toastIcon) {

            if (type === "error") {

                toastIcon.innerHTML =
                    `<i class="fa-solid fa-circle-xmark"></i>`;

            } else if (type === "info") {

                toastIcon.innerHTML =
                    `<i class="fa-solid fa-circle-info"></i>`;

            } else {

                toastIcon.innerHTML =
                    `<i class="fa-solid fa-circle-check"></i>`;
            }
        }


        toast.classList.add("show");


        clearTimeout(
            window.mathsWorldToastTimer
        );


        window.mathsWorldToastTimer =
            setTimeout(() => {

                toast.classList.remove(
                    "show"
                );

            }, 3500);
    }


    /* =====================================================
       ESCAPE KEY
    ===================================================== */

    document.addEventListener(
        "keydown",
        event => {

            if (event.key !== "Escape")
                return;


            document
                .querySelectorAll(
                    ".modal-overlay.show"
                )
                .forEach(modal => {

                    closeModal(modal);
                });


            closeSidebar();
        }
    );


    /* =====================================================
       INITIAL LOAD
    ===================================================== */

    renderMessages();

    updateStats();

    updateResultCount();

});

