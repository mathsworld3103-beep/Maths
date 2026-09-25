document.addEventListener("DOMContentLoaded", function () {

    const addModal =
        document.getElementById("addModal");

    const openAddModal =
        document.getElementById("openAddModal");

    const closeAddModal =
        document.getElementById("closeAddModal");

    const cancelAdd =
        document.getElementById("cancelAdd");

    const form =
        document.getElementById("addPaperForm");

    const paperFile =
        document.getElementById("paperFile");

    const fileName =
        document.getElementById("fileName");

    const searchInput =
        document.getElementById("searchInput");

    const subjectFilter =
        document.getElementById("subjectFilter");

    const gradeFilter =
        document.getElementById("gradeFilter");

    const resetFilters =
        document.getElementById("resetFilters");


    let searchTimer;


    /* =========================
       INITIAL LOAD
    ========================= */

    loadPapers();


    /* =========================
       OPEN MODAL
    ========================= */

    openAddModal.addEventListener(
        "click",
        function () {

            addModal.classList.add("show");

        }
    );


    /* =========================
       CLOSE MODAL
    ========================= */

    function closeModal() {

        addModal.classList.remove("show");

        form.reset();

        fileName.textContent =
            "Choose PDF file";

        document.getElementById(
            "formMessage"
        ).textContent = "";

    }


    closeAddModal.addEventListener(
        "click",
        closeModal
    );


    cancelAdd.addEventListener(
        "click",
        closeModal
    );


    addModal.addEventListener(
        "click",
        function (event) {

            if (event.target === addModal) {
                closeModal();
            }

        }
    );


    /* =========================
       FILE NAME
    ========================= */

    paperFile.addEventListener(
        "change",
        function () {

            if (paperFile.files.length > 0) {

                fileName.textContent =
                    paperFile.files[0].name;

            } else {

                fileName.textContent =
                    "Choose PDF file";

            }

        }
    );


    /* =========================
       SEARCH
    ========================= */

    searchInput.addEventListener(
        "input",
        function () {

            clearTimeout(searchTimer);

            searchTimer = setTimeout(
                loadPapers,
                300
            );

        }
    );


    subjectFilter.addEventListener(
        "change",
        loadPapers
    );


    gradeFilter.addEventListener(
        "change",
        loadPapers
    );


    resetFilters.addEventListener(
        "click",
        function () {

            searchInput.value = "";

            subjectFilter.value = "";

            gradeFilter.value = "";

            loadPapers();

        }
    );


    /* =========================
       ADD PAPER
    ========================= */

    form.addEventListener(
        "submit",
        async function (event) {

            event.preventDefault();


            const saveButton =
                document.getElementById(
                    "savePaper"
                );

            const message =
                document.getElementById(
                    "formMessage"
                );


            const formData =
                new FormData(form);

            formData.append(
                "action",
                "add"
            );


            saveButton.disabled = true;

            saveButton.innerHTML =
                '<i class="fa-solid fa-spinner fa-spin"></i> Uploading...';


            message.textContent = "";

            message.className =
                "form-message";


            try {

                const response =
                    await fetch(
                        "past-paper-api.php",
                        {
                            method: "POST",
                            body: formData
                        }
                    );


                const data =
                    await response.json();


                if (!data.success) {

                    throw new Error(
                        data.message ||
                        "Upload failed."
                    );

                }


                message.textContent =
                    data.message;

                message.className =
                    "form-message success";


                form.reset();

                fileName.textContent =
                    "Choose PDF file";


                loadPapers();


                setTimeout(
                    closeModal,
                    800
                );


            } catch (error) {

                message.textContent =
                    error.message;

                message.className =
                    "form-message error";

            } finally {

                saveButton.disabled = false;

                saveButton.innerHTML =
                    '<i class="fa-solid fa-cloud-arrow-up"></i> Upload Paper';

            }

        }
    );

});


/* =========================
   LOAD PAPERS
========================= */

async function loadPapers() {

    showLoading();


    const search =
        document.getElementById(
            "searchInput"
        ).value.trim();


    const subject =
        document.getElementById(
            "subjectFilter"
        ).value;


    const grade =
        document.getElementById(
            "gradeFilter"
        ).value;


    const params =
        new URLSearchParams();

    params.append(
        "action",
        "list"
    );


    if (search) {
        params.append(
            "search",
            search
        );
    }


    if (subject) {
        params.append(
            "subject",
            subject
        );
    }


    if (grade) {
        params.append(
            "grade",
            grade
        );
    }


    try {

        const response =
            await fetch(
                "past-paper-api.php?" +
                params.toString()
            );


        const data =
            await response.json();


        if (!data.success) {
            throw new Error(
                data.message
            );
        }


        renderPapers(
            data.papers || []
        );


    } catch (error) {

        console.error(error);

        showError();

    }

}


/* =========================
   RENDER
========================= */

function renderPapers(papers) {

    const tbody =
        document.getElementById(
            "papersTableBody"
        );


    const table =
        document.getElementById(
            "tableWrapper"
        );


    const empty =
        document.getElementById(
            "emptyState"
        );


    const loading =
        document.getElementById(
            "loadingState"
        );


    const error =
        document.getElementById(
            "errorState"
        );


    const count =
        document.getElementById(
            "paperCount"
        );


    loading.style.display =
        "none";

    error.style.display =
        "none";


    count.textContent =
        papers.length;


    tbody.innerHTML = "";


    if (papers.length === 0) {

        table.style.display =
            "none";

        empty.style.display =
            "block";

        return;

    }


    empty.style.display =
        "none";

    table.style.display =
        "block";


    papers.forEach(function (paper) {

        const row =
            document.createElement("tr");


        const filePath =
            "../" + paper.file_path;


        row.innerHTML = `

            <td>

                <div class="paper-info">

                    <div class="pdf-icon">

                        <i class="fa-solid fa-file-pdf"></i>

                    </div>

                    <div>

                        <strong>
                            ${escapeHtml(paper.title)}
                        </strong>

                        <span>
                            ${escapeHtml(
                                paper.file_name
                            )}
                        </span>

                    </div>

                </div>

            </td>


            <td>
                ${escapeHtml(paper.subject)}
            </td>


            <td>

                <span class="grade-badge">
                    ${escapeHtml(paper.grade)}
                </span>

            </td>


            <td>

                <strong>
                    ${escapeHtml(
                        paper.paper_year
                    )}
                </strong>

            </td>


            <td>

                <span class="type-badge">
                    ${escapeHtml(
                        paper.paper_type
                    )}
                </span>

            </td>


            <td>
                ${formatDate(
                    paper.created_at
                )}
            </td>


            <td>

                <div class="actions">

                    <a
                        href="${filePath}"
                        target="_blank"
                        class="action-view"
                        title="View">

                        <i class="fa-solid fa-eye"></i>

                    </a>


                    <button
                        type="button"
                        class="action-delete"
                        onclick="deletePaper(${paper.id})"
                        title="Delete">

                        <i class="fa-solid fa-trash"></i>

                    </button>

                </div>

            </td>

        `;


        tbody.appendChild(row);

    });

}


/* =========================
   DELETE
========================= */

async function deletePaper(id) {

    if (
        !confirm(
            "Are you sure you want to delete this past paper?"
        )
    ) {
        return;
    }


    const formData =
        new FormData();

    formData.append(
        "action",
        "delete"
    );

    formData.append(
        "id",
        id
    );


    try {

        const response =
            await fetch(
                "past-paper-api.php",
                {
                    method: "POST",
                    body: formData
                }
            );


        const data =
            await response.json();


        if (!data.success) {

            throw new Error(
                data.message
            );

        }


        loadPapers();


    } catch (error) {

        alert(
            error.message ||
            "Unable to delete paper."
        );

    }

}


/* =========================
   STATES
========================= */

function showLoading() {

    document.getElementById(
        "loadingState"
    ).style.display = "flex";


    document.getElementById(
        "tableWrapper"
    ).style.display = "none";


    document.getElementById(
        "emptyState"
    ).style.display = "none";


    document.getElementById(
        "errorState"
    ).style.display = "none";

}


function showError() {

    document.getElementById(
        "loadingState"
    ).style.display = "none";


    document.getElementById(
        "tableWrapper"
    ).style.display = "none";


    document.getElementById(
        "emptyState"
    ).style.display = "none";


    document.getElementById(
        "errorState"
    ).style.display = "block";

}


/* =========================
   HELPERS
========================= */

function escapeHtml(value) {

    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");

}


function formatDate(dateString) {

    if (!dateString) {
        return "-";
    }

    const date =
        new Date(
            dateString.replace(" ", "T")
        );

    if (isNaN(date.getTime())) {
        return dateString;
    }

    return date.toLocaleDateString(
        "en-GB",
        {
            day: "2-digit",
            month: "short",
            year: "numeric"
        }
    );

}