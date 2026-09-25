/* =========================================
   MODEL PAPERS
========================================= */

const API_URL = "admin/model-paper-api.php";

let allModelPapers = [];


/* =========================================
   LOAD MODEL PAPERS
========================================= */

async function loadModelPapers() {

    const loading = document.getElementById("loading");
    const error = document.getElementById("errorMessage");
    const empty = document.getElementById("emptyMessage");
    const grid = document.getElementById("modelGrid");

    loading.style.display = "block";
    error.style.display = "none";
    empty.style.display = "none";
    grid.innerHTML = "";

    try {

        const response = await fetch(API_URL);

        if (!response.ok) {
            throw new Error("Server error");
        }

        const data = await response.json();

        console.log("Model Papers API:", data);

        /*
         * Support common API formats:
         *
         * { success: true, data: [...] }
         * { success: true, papers: [...] }
         * [...]
         */

        if (Array.isArray(data)) {

            allModelPapers = data;

        } else if (Array.isArray(data.data)) {

            allModelPapers = data.data;

        } else if (Array.isArray(data.papers)) {

            allModelPapers = data.papers;

        } else {

            allModelPapers = [];

        }

        loading.style.display = "none";

        applyFilters();

    } catch (errorObject) {

        console.error(errorObject);

        loading.style.display = "none";
        error.style.display = "block";

    }

}


/* =========================================
   FILTER
========================================= */

function applyFilters() {

    const search =
        document
            .getElementById("searchModel")
            .value
            .toLowerCase()
            .trim();

    const subject =
        document.getElementById("subjectFilter").value;

    const level =
        document.getElementById("levelFilter").value;

    const year =
        document.getElementById("yearFilter").value;

    const type =
        document.getElementById("typeFilter").value;


    const filtered = allModelPapers.filter(paper => {

        const title =
            String(
                paper.title ||
                paper.model_title ||
                paper.name ||
                ""
            ).toLowerCase();

        const paperSubject =
            String(
                paper.subject ||
                paper.model_subject ||
                ""
            );

        const paperLevel =
            String(
                paper.level ||
                paper.model_level ||
                ""
            );

        const paperYear =
            String(
                paper.year ||
                paper.model_year ||
                ""
            );

        const paperType =
            String(
                paper.type ||
                paper.model_type ||
                ""
            );


        const matchesSearch =
            !search ||
            title.includes(search) ||
            paperSubject.toLowerCase().includes(search);


        const matchesSubject =
            !subject ||
            paperSubject === subject;


        const matchesLevel =
            !level ||
            paperLevel === level;


        const matchesYear =
            !year ||
            paperYear === year;


        const matchesType =
            !type ||
            paperType === type;


        return (
            matchesSearch &&
            matchesSubject &&
            matchesLevel &&
            matchesYear &&
            matchesType
        );

    });


    renderPapers(filtered);

}


/* =========================================
   RENDER
========================================= */

function renderPapers(papers) {

    const grid =
        document.getElementById("modelGrid");

    const empty =
        document.getElementById("emptyMessage");

    const resultCount =
        document.getElementById("resultCount");


    resultCount.textContent = papers.length;

    grid.innerHTML = "";


    if (papers.length === 0) {

        empty.style.display = "block";

        return;

    }


    empty.style.display = "none";


    papers.forEach(paper => {

        const title =
            paper.title ||
            paper.model_title ||
            paper.name ||
            "Model Paper";


        const subject =
            paper.subject ||
            paper.model_subject ||
            "Subject";


        const level =
            paper.level ||
            paper.model_level ||
            "Level";


        const year =
            paper.year ||
            paper.model_year ||
            "";


        const type =
            paper.type ||
            paper.model_type ||
            "Model Paper";


        const description =
            paper.description ||
            paper.model_description ||
            "Practice this model paper to improve your exam preparation.";


        const file =
            paper.file ||
            paper.file_path ||
            paper.pdf_file ||
            paper.model_file ||
            "#";


        const card =
            document.createElement("article");


        card.className = "model-card";


        card.innerHTML = `

            <div class="paper-icon">
                <i class="bi bi-file-earmark-pdf"></i>
            </div>

            <h3>
                ${escapeHTML(title)}
            </h3>

            <p class="model-description">
                ${escapeHTML(description)}
            </p>

            <div class="paper-meta">

                <span>
                    ${escapeHTML(subject)}
                </span>

                <span>
                    ${escapeHTML(level)}
                </span>

                ${year ? `
                    <span>
                        ${escapeHTML(year)}
                    </span>
                ` : ""}

                <span>
                    ${escapeHTML(type)}
                </span>

            </div>

            <div class="card-actions">

                <a
                    href="${file}"
                    target="_blank"
                    class="view-btn"
                >
                    <i class="bi bi-eye"></i>
                    View
                </a>

                <a
                    href="${file}"
                    download
                    class="download-btn"
                >
                    <i class="bi bi-download"></i>
                    Download
                </a>

            </div>

        `;


        grid.appendChild(card);

    });

}


/* =========================================
   ESCAPE HTML
========================================= */

function escapeHTML(value) {

    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");

}


/* =========================================
   EVENTS
========================================= */

document
    .getElementById("searchModel")
    .addEventListener(
        "input",
        applyFilters
    );


document
    .getElementById("subjectFilter")
    .addEventListener(
        "change",
        applyFilters
    );


document
    .getElementById("levelFilter")
    .addEventListener(
        "change",
        applyFilters
    );


document
    .getElementById("yearFilter")
    .addEventListener(
        "change",
        applyFilters
    );


document
    .getElementById("typeFilter")
    .addEventListener(
        "change",
        applyFilters
    );


/* =========================================
   START
========================================= */

document.addEventListener(
    "DOMContentLoaded",
    loadModelPapers
);