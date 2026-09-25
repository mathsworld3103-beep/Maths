const searchInput = document.getElementById("searchInput");
const subjectFilter = document.getElementById("subjectFilter");
const resetFilters = document.getElementById("resetFilters");

const papersGrid = document.getElementById("papersGrid");
const loadingState = document.getElementById("loadingState");
const emptyState = document.getElementById("emptyState");
const errorState = document.getElementById("errorState");

const errorMessage = document.getElementById("errorMessage");
const retryButton = document.getElementById("retryButton");
const resultText = document.getElementById("resultText");

const menuButton = document.getElementById("menuButton");
const sidebar = document.getElementById("sidebar");


/* ==========================================
   LOAD PAPERS
========================================== */

async function loadPapers() {

    showLoading();

    const search = searchInput.value.trim();
    const subject = subjectFilter.value;

    const params = new URLSearchParams();

    if (search !== "") {
        params.append("search", search);
    }

    if (subject !== "") {
        params.append("subject", subject);
    }

    try {

        const response = await fetch(
            "past-papers-api.php?" + params.toString()
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

        renderPapers(data.papers);

    } catch (error) {

        console.error(error);

        showError(error.message);

    }

}


/* ==========================================
   RENDER PAPERS
========================================== */

function renderPapers(papers) {

    loadingState.style.display = "none";
    errorState.style.display = "none";

    papersGrid.innerHTML = "";

    resultText.textContent =
        papers.length +
        (papers.length === 1
            ? " paper available"
            : " papers available");


    if (papers.length === 0) {

        emptyState.style.display = "block";

        return;
    }

    emptyState.style.display = "none";


    papers.forEach(function (paper) {

        const card = document.createElement("div");

        card.className = "paper-card";


        const title =
            escapeHTML(paper.title || "Untitled Paper");

        const description =
            escapeHTML(
                paper.description ||
                "No description available."
            );

        const subject =
            escapeHTML(paper.subject || "Subject");

        const grade =
            escapeHTML(paper.grade || "Grade");

        const paperType =
            escapeHTML(
                paper.paper_type || "Past Paper"
            );

        const year =
            escapeHTML(
                String(paper.paper_year || "")
            );


        const filePath =
            "../" + paper.file_path;


        card.innerHTML = `

            <div class="paper-icon">

                <i class="fa-solid fa-file-pdf"></i>

            </div>


            <h3>
                ${title}
            </h3>


            <p class="paper-description">
                ${description}
            </p>


            <div class="paper-meta">

                <span>
                    ${subject}
                </span>

                <span>
                    ${grade}
                </span>

                <span>
                    ${paperType}
                </span>

                <span>
                    ${year}
                </span>

            </div>


            <div class="paper-actions">

                <a
                    href="${filePath}"
                    target="_blank"
                    class="view-button"
                >

                    <i class="fa-solid fa-eye"></i>

                    View

                </a>


                <a
                    href="${filePath}"
                    download
                    class="download-button"
                >

                    <i class="fa-solid fa-download"></i>

                    Download

                </a>

            </div>

        `;


        papersGrid.appendChild(card);

    });

}


/* ==========================================
   LOADING
========================================== */

function showLoading() {

    loadingState.style.display = "block";

    errorState.style.display = "none";

    emptyState.style.display = "none";

    papersGrid.innerHTML = "";

    resultText.textContent =
        "Loading papers...";

}


/* ==========================================
   ERROR
========================================== */

function showError(message) {

    loadingState.style.display = "none";

    emptyState.style.display = "none";

    errorState.style.display = "block";

    papersGrid.innerHTML = "";

    errorMessage.textContent =
        message || "Unable to load papers.";

    resultText.textContent =
        "Unable to load papers.";

}


/* ==========================================
   RESET
========================================== */

resetFilters.addEventListener(
    "click",
    function () {

        searchInput.value = "";

        subjectFilter.value = "";

        loadPapers();

    }
);


/* ==========================================
   SEARCH
========================================== */

let searchTimer;

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


/* ==========================================
   SUBJECT FILTER
========================================== */

subjectFilter.addEventListener(
    "change",
    function () {

        loadPapers();

    }
);


/* ==========================================
   RETRY
========================================== */

retryButton.addEventListener(
    "click",
    loadPapers
);


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

    return String(value)
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