const modalOverlay = document.getElementById("modalOverlay");
const openModalButton = document.getElementById("openModalButton");
const closeModalButton = document.getElementById("closeModalButton");
const cancelButton = document.getElementById("cancelButton");

const form = document.getElementById("modelPaperForm");
const fileInput = document.getElementById("paperFile");
const fileName = document.getElementById("fileName");

const tableBody = document.getElementById("papersTableBody");

const searchInput = document.getElementById("searchInput");
const subjectFilter = document.getElementById("subjectFilter");
const gradeFilter = document.getElementById("gradeFilter");
const resetFilters = document.getElementById("resetFilters");

const resultText = document.getElementById("resultText");
const messageBox = document.getElementById("messageBox");

const formMessage = document.getElementById("formMessage");
const saveButton = document.getElementById("saveButton");

const menuButton = document.getElementById("menuButton");
const sidebar = document.getElementById("sidebar");

let searchTimer;


/* ==========================================
   MODAL
========================================== */

openModalButton.addEventListener("click", function () {

    form.reset();

    fileName.textContent = "Choose PDF file";

    formMessage.textContent = "";

    modalOverlay.classList.add("show");

});


function closeModal() {

    modalOverlay.classList.remove("show");

}


closeModalButton.addEventListener(
    "click",
    closeModal
);

cancelButton.addEventListener(
    "click",
    closeModal
);


modalOverlay.addEventListener(
    "click",
    function (event) {

        if (event.target === modalOverlay) {
            closeModal();
        }

    }
);


/* ==========================================
   FILE NAME
========================================== */

fileInput.addEventListener(
    "change",
    function () {

        if (fileInput.files.length > 0) {

            fileName.textContent =
                fileInput.files[0].name;

        } else {

            fileName.textContent =
                "Choose PDF file";

        }

    }
);


/* ==========================================
   LOAD PAPERS
========================================== */

async function loadPapers() {

    tableBody.innerHTML = `
        <tr>
            <td colspan="6" class="loading-cell">
                <div class="spinner"></div>
                Loading model papers...
            </td>
        </tr>
    `;

    const params = new URLSearchParams();

    const search = searchInput.value.trim();
    const subject = subjectFilter.value;
    const grade = gradeFilter.value;

    if (search) {
        params.append("search", search);
    }

    if (subject) {
        params.append("subject", subject);
    }

    if (grade) {
        params.append("grade", grade);
    }

    params.append("action", "list");

    try {

        const response = await fetch(
            "model-paper-api.php?" + params.toString()
        );

        if (!response.ok) {
            throw new Error(
                "HTTP Error: " + response.status
            );
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(
                data.message || "Unable to load papers."
            );
        }

        renderPapers(data.papers || []);

    } catch (error) {

        console.error(error);

        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="empty-cell">
                    Unable to load model papers.
                    <br>
                    ${escapeHTML(error.message)}
                </td>
            </tr>
        `;

    }

}


/* ==========================================
   RENDER
========================================== */

function renderPapers(papers) {

    tableBody.innerHTML = "";

    resultText.textContent =
        papers.length +
        (papers.length === 1
            ? " paper found"
            : " papers found");


    document.getElementById("totalPapers").textContent =
        papers.length;


    let mathCount = 0;
    let scienceCount = 0;


    papers.forEach(function (paper) {

        const subject =
            paper.subject || "";

        if (
            subject === "Mathematics" ||
            subject === "Combined Mathematics"
        ) {
            mathCount++;
        }

        if (
            subject === "Physics" ||
            subject === "Chemistry" ||
            subject === "Biology"
        ) {
            scienceCount++;
        }


        const row = document.createElement("tr");


        const filePath =
            "../" + paper.file_path;


        row.innerHTML = `

            <td>

                <div class="paper-title">
                    ${escapeHTML(paper.title)}
                </div>

                <div class="paper-file">
                    ${escapeHTML(paper.file_name)}
                </div>

            </td>


            <td>
                ${escapeHTML(paper.subject)}
            </td>


            <td>
                <span class="badge">
                    ${escapeHTML(paper.grade)}
                </span>
            </td>


            <td>
                ${escapeHTML(paper.paper_type)}
            </td>


            <td>
                ${escapeHTML(String(paper.paper_year))}
            </td>


            <td>

                <div class="action-buttons">

                    <a
                        href="${filePath}"
                        target="_blank"
                        class="view-action"
                        title="View PDF"
                    >
                        <i class="fa-solid fa-eye"></i>
                    </a>


                    <button
                        type="button"
                        class="delete-action"
                        title="Delete"
                        onclick="deletePaper(${paper.id})"
                    >
                        <i class="fa-solid fa-trash"></i>
                    </button>

                </div>

            </td>

        `;


        tableBody.appendChild(row);

    });


    document.getElementById("mathPapers").textContent =
        mathCount;

    document.getElementById("sciencePapers").textContent =
        scienceCount;


    if (papers.length === 0) {

        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="empty-cell">
                    No model papers found.
                </td>
            </tr>
        `;

    }

}


/* ==========================================
   UPLOAD
========================================== */

form.addEventListener(
    "submit",
    async function (event) {

        event.preventDefault();

        formMessage.textContent = "";

        const file = fileInput.files[0];

        if (!file) {

            formMessage.textContent =
                "Please select a PDF file.";

            formMessage.className =
                "form-message error";

            return;

        }


        if (
            file.type !== "application/pdf" &&
            !file.name.toLowerCase().endsWith(".pdf")
        ) {

            formMessage.textContent =
                "Only PDF files are allowed.";

            formMessage.className =
                "form-message error";

            return;

        }


        if (file.size > 10 * 1024 * 1024) {

            formMessage.textContent =
                "Maximum file size is 10 MB.";

            formMessage.className =
                "form-message error";

            return;

        }


        const formData = new FormData();

        formData.append("action", "add");

        formData.append(
            "title",
            document.getElementById("paperTitle").value.trim()
        );

        formData.append(
            "description",
            document.getElementById("paperDescription").value.trim()
        );

        formData.append(
            "subject",
            document.getElementById("paperSubject").value
        );

        formData.append(
            "grade",
            document.getElementById("paperGrade").value
        );

        formData.append(
            "paper_type",
            document.getElementById("paperType").value
        );

        formData.append(
            "paper_year",
            document.getElementById("paperYear").value
        );

        formData.append(
            "paper",
            file
        );


        saveButton.disabled = true;

        saveButton.querySelector("span").textContent =
            "Uploading...";


        try {

            const response = await fetch(
                "model-paper-api.php",
                {
                    method: "POST",
                    body: formData
                }
            );


            const data = await response.json();


            if (!data.success) {

                throw new Error(
                    data.message ||
                    "Upload failed."
                );

            }


            formMessage.textContent =
                "Model paper uploaded successfully.";

            formMessage.className =
                "form-message success";


            setTimeout(function () {

                closeModal();

                loadPapers();

            }, 700);


        } catch (error) {

            console.error(error);

            formMessage.textContent =
                error.message;

            formMessage.className =
                "form-message error";

        } finally {

            saveButton.disabled = false;

            saveButton.querySelector("span").textContent =
                "Upload Paper";

        }

    }
);


/* ==========================================
   DELETE
========================================== */

async function deletePaper(id) {

    if (
        !confirm(
            "Are you sure you want to delete this model paper?"
        )
    ) {
        return;
    }


    const formData = new FormData();

    formData.append("action", "delete");
    formData.append("id", id);


    try {

        const response = await fetch(
            "model-paper-api.php",
            {
                method: "POST",
                body: formData
            }
        );


        const data = await response.json();


        if (!data.success) {

            throw new Error(
                data.message ||
                "Delete failed."
            );

        }


        showMessage(
            "Model paper deleted successfully.",
            "success"
        );

        loadPapers();


    } catch (error) {

        showMessage(
            error.message,
            "error"
        );

    }

}


/* ==========================================
   FILTERS
========================================== */

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


/* ==========================================
   MESSAGE
========================================== */

function showMessage(message, type) {

    messageBox.textContent = message;

    messageBox.className =
        "message-box " + type;

    messageBox.style.display = "block";


    setTimeout(function () {

        messageBox.style.display = "none";

    }, 3000);

}


/* ==========================================
   MOBILE SIDEBAR
========================================== */

menuButton.addEventListener(
    "click",
    function () {

        sidebar.classList.toggle("show");

    }
);


/* ==========================================
   HTML ESCAPE
========================================== */

function escapeHTML(value) {

    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");

}


/* ==========================================
   START
========================================== */

loadPapers();