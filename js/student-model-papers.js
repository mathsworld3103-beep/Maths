document.addEventListener("DOMContentLoaded", function () {


    // ==========================================
    // ELEMENTS
    // ==========================================

    const searchInput =
        document.getElementById("searchInput");

    const subjectFilter =
        document.getElementById("subjectFilter");

    const resetFilters =
        document.getElementById("resetFilters");

    const papersGrid =
        document.getElementById("papersGrid");

    const loadingState =
        document.getElementById("loadingState");

    const errorState =
        document.getElementById("errorState");

    const errorMessage =
        document.getElementById("errorMessage");

    const emptyState =
        document.getElementById("emptyState");

    const resultText =
        document.getElementById("resultText");

    const retryButton =
        document.getElementById("retryButton");

    const menuButton =
        document.getElementById("menuButton");

    const sidebar =
        document.getElementById("sidebar");


    let searchTimer = null;



    // ==========================================
    // LOAD MODEL PAPERS
    // ==========================================

    async function loadModelPapers() {


        // Show loading

        loadingState.style.display = "block";

        errorState.style.display = "none";

        emptyState.style.display = "none";

        papersGrid.innerHTML = "";

        resultText.textContent =
            "Loading model papers...";


        // Get filters

        const search =
            searchInput.value.trim();

        const subject =
            subjectFilter.value;


        // Create query

        const params =
            new URLSearchParams();


        if (search !== "") {

            params.append(
                "search",
                search
            );

        }


        if (subject !== "") {

            params.append(
                "subject",
                subject
            );

        }


        try {


            // ==================================
            // API REQUEST
            // ==================================

            const response =
                await fetch(
                    "model-papers-api.php?" +
                    params.toString(),
                    {
                        method: "GET",
                        headers: {
                            "Accept": "application/json"
                        }
                    }
                );


            // Check HTTP status

            if (!response.ok) {

                throw new Error(
                    "Server returned status " +
                    response.status
                );

            }


            const data =
                await response.json();


            // Hide loading

            loadingState.style.display =
                "none";


            // ==================================
            // API ERROR
            // ==================================

            if (!data.success) {

                showError(
                    data.message ||
                    "Unable to load model papers."
                );

                return;

            }


            // ==================================
            // GET PAPERS
            // ==================================

            const papers =
                Array.isArray(data.papers)
                    ? data.papers
                    : [];


            // Result text

            if (papers.length === 1) {

                resultText.textContent =
                    "1 model paper available.";

            } else {

                resultText.textContent =
                    papers.length +
                    " model papers available.";

            }


            // ==================================
            // EMPTY
            // ==================================

            if (papers.length === 0) {

                emptyState.style.display =
                    "block";

                return;

            }


            // ==================================
            // DISPLAY PAPERS
            // ==================================

            papers.forEach(function (paper) {

                papersGrid.insertAdjacentHTML(
                    "beforeend",
                    createPaperCard(paper)
                );

            });


        } catch (error) {


            console.error(
                "Model papers error:",
                error
            );


            loadingState.style.display =
                "none";


            showError(
                "Unable to connect to the server. " +
                "Please try again."
            );

        }

    }



    // ==========================================
    // CREATE PAPER CARD
    // ==========================================

    function createPaperCard(paper) {


        const title =
            escapeHtml(
                paper.title || "Model Paper"
            );


        const subject =
            escapeHtml(
                paper.subject || "Subject"
            );


        const description =
            escapeHtml(
                paper.description ||
                "Model examination paper."
            );


        const grade =
            escapeHtml(
                paper.grade || ""
            );


        const paperType =
            escapeHtml(
                paper.paper_type ||
                "Model Paper"
            );


        const year =
            escapeHtml(
                paper.paper_year || ""
            );


        // ======================================
        // FILE PATH
        // ======================================

        let filePath =
            paper.file_path || "";


        /*
         * Database path example:
         *
         * uploads/model-papers/file.pdf
         *
         * Student page is inside:
         *
         * /mathsworld/user/
         *
         * Therefore:
         *
         * ../uploads/model-papers/file.pdf
         */

        const fileUrl =
            "../" +
            filePath
                .replace(/^\/+/, "")
                .replace(/\\/g, "/");


        // ======================================
        // CARD
        // ======================================

        return `

            <article class="paper-card">


                <!-- PDF ICON -->

                <div class="paper-icon">

                    <i class="fa-solid fa-file-pdf"></i>

                </div>


                <!-- TITLE -->

                <h3>

                    ${title}

                </h3>


                <!-- SUBJECT -->

                <span class="paper-subject">

                    ${subject}

                </span>


                <!-- DESCRIPTION -->

                <p class="paper-description">

                    ${description}

                </p>


                <!-- META -->

                <div class="paper-meta">


                    <span>

                        <i class="fa-solid fa-graduation-cap"></i>

                        ${grade}

                    </span>


                    <span>

                        <i class="fa-solid fa-calendar"></i>

                        ${year}

                    </span>


                    <span>

                        ${paperType}

                    </span>


                </div>


                <!-- ACTIONS -->

                <div class="paper-actions">


                    <!-- VIEW -->

                    <a
                        href="${fileUrl}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="view-button"
                    >

                        <i class="fa-solid fa-eye"></i>

                        View

                    </a>


                    <!-- DOWNLOAD -->

                    <a
                        href="${fileUrl}"
                        download
                        class="download-button"
                    >

                        <i class="fa-solid fa-download"></i>

                        Download

                    </a>


                </div>


            </article>

        `;

    }



    // ==========================================
    // SHOW ERROR
    // ==========================================

    function showError(message) {


        loadingState.style.display =
            "none";


        emptyState.style.display =
            "none";


        errorState.style.display =
            "block";


        errorMessage.textContent =
            message;


        resultText.textContent =
            "Unable to load model papers.";

    }



    // ==========================================
    // ESCAPE HTML
    // ==========================================

    function escapeHtml(value) {


        const div =
            document.createElement("div");


        div.textContent =
            value ?? "";


        return div.innerHTML;

    }



    // ==========================================
    // SEARCH
    // ==========================================

    searchInput.addEventListener(
        "input",
        function () {


            clearTimeout(searchTimer);


            searchTimer =
                setTimeout(
                    function () {

                        loadModelPapers();

                    },
                    300
                );

        }
    );



    // ==========================================
    // SUBJECT FILTER
    // ==========================================

    subjectFilter.addEventListener(
        "change",
        function () {

            loadModelPapers();

        }
    );



    // ==========================================
    // RESET
    // ==========================================

    resetFilters.addEventListener(
        "click",
        function () {


            searchInput.value =
                "";


            subjectFilter.value =
                "";


            loadModelPapers();

        }
    );



    // ==========================================
    // RETRY
    // ==========================================

    retryButton.addEventListener(
        "click",
        function () {

            loadModelPapers();

        }
    );



    // ==========================================
    // MOBILE MENU
    // ==========================================

    if (
        menuButton &&
        sidebar
    ) {


        menuButton.addEventListener(
            "click",
            function () {

                sidebar.classList.toggle(
                    "show"
                );

            }
        );

    }



    // ==========================================
    // INITIAL LOAD
    // ==========================================

    loadModelPapers();

});