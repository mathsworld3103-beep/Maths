/* =====================================================
   MATHSWORLD PAST PAPERS
   FRONTEND ONLY
   ===================================================== */

document.addEventListener("DOMContentLoaded", () => {

    const searchInput =
        document.getElementById("paperSearch");

    const clearSearch =
        document.getElementById("clearSearch");

    const gradeFilter =
        document.getElementById("gradeFilter");

    const subjectFilter =
        document.getElementById("subjectFilter");

    const yearFilter =
        document.getElementById("yearFilter");

    const typeFilter =
        document.getElementById("typeFilter");

    const sortFilter =
        document.getElementById("sortFilter");

    const resetFilters =
        document.getElementById("resetFilters");

    const resetNoResults =
        document.getElementById("resetNoResults");

    const papersGrid =
        document.getElementById("papersGrid");

    const resultCount =
        document.getElementById("resultCount");

    const noResults =
        document.getElementById("noResults");


    let papers =
        Array.from(
            document.querySelectorAll(".paper-card")
        );


    /* =================================================
       FILTER
    ================================================== */

    function filterPapers() {

        const searchValue =
            searchInput.value
                .trim()
                .toLowerCase();

        const selectedGrade =
            gradeFilter.value.toLowerCase();

        const selectedSubject =
            subjectFilter.value.toLowerCase();

        const selectedYear =
            yearFilter.value.toLowerCase();

        const selectedType =
            typeFilter.value.toLowerCase();


        let visiblePapers = [];


        papers.forEach(paper => {

            const grade =
                paper.dataset.grade.toLowerCase();

            const subject =
                paper.dataset.subject.toLowerCase();

            const year =
                paper.dataset.year.toLowerCase();

            const type =
                paper.dataset.type.toLowerCase();

            const title =
                paper.dataset.title.toLowerCase();


            const description =
                paper.querySelector("p")
                    ?.textContent
                    .toLowerCase() || "";


            const searchMatch =
                searchValue === "" ||
                grade.includes(searchValue) ||
                subject.includes(searchValue) ||
                year.includes(searchValue) ||
                type.includes(searchValue) ||
                title.includes(searchValue) ||
                description.includes(searchValue);


            const gradeMatch =
                selectedGrade === "all" ||
                grade === selectedGrade;


            const subjectMatch =
                selectedSubject === "all" ||
                subject === selectedSubject;


            const yearMatch =
                selectedYear === "all" ||
                year === selectedYear;


            const typeMatch =
                selectedType === "all" ||
                type === selectedType;


            if (
                searchMatch &&
                gradeMatch &&
                subjectMatch &&
                yearMatch &&
                typeMatch
            ) {

                paper.classList.remove("hide");

                paper.classList.add(
                    "show-animation"
                );

                visiblePapers.push(paper);

            } else {

                paper.classList.add("hide");

                paper.classList.remove(
                    "show-animation"
                );

            }

        });


        updateResultCount(
            visiblePapers.length
        );

    }


    /* =================================================
       RESULT COUNT
    ================================================== */

    function updateResultCount(count) {

        if (count === 0) {

            resultCount.textContent =
                "No papers found";

            noResults.classList.add("show");

            papersGrid.style.display =
                "none";

        } else {

            resultCount.textContent =
                `Showing ${count} ${
                    count === 1
                        ? "paper"
                        : "papers"
                }`;

            noResults.classList.remove(
                "show"
            );

            papersGrid.style.display =
                "grid";

        }

    }


    /* =================================================
       SORT
    ================================================== */

    function sortPapers() {

        const sortValue =
            sortFilter.value;


        if (sortValue === "newest") {

            papers.sort((a, b) => {

                return (
                    Number(b.dataset.year) -
                    Number(a.dataset.year)
                );

            });

        }


        else if (sortValue === "oldest") {

            papers.sort((a, b) => {

                return (
                    Number(a.dataset.year) -
                    Number(b.dataset.year)
                );

            });

        }


        else if (sortValue === "az") {

            papers.sort((a, b) => {

                return a.dataset.title
                    .localeCompare(
                        b.dataset.title
                    );

            });

        }


        papers.forEach(paper => {

            papersGrid.appendChild(paper);

        });


        filterPapers();

    }


    /* =================================================
       RESET
    ================================================== */

    function resetAllFilters() {

        searchInput.value = "";

        gradeFilter.value = "all";

        subjectFilter.value = "all";

        yearFilter.value = "all";

        typeFilter.value = "all";

        sortFilter.value = "newest";


        papers.forEach(paper => {

            paper.classList.remove("hide");

        });


        sortPapers();

    }


    /* =================================================
       SEARCH
    ================================================== */

    searchInput.addEventListener(
        "input",
        filterPapers
    );


    /* =================================================
       FILTER EVENTS
    ================================================== */

    gradeFilter.addEventListener(
        "change",
        filterPapers
    );

    subjectFilter.addEventListener(
        "change",
        filterPapers
    );

    yearFilter.addEventListener(
        "change",
        filterPapers
    );

    typeFilter.addEventListener(
        "change",
        filterPapers
    );


    /* =================================================
       SORT
    ================================================== */

    sortFilter.addEventListener(
        "change",
        sortPapers
    );


    /* =================================================
       CLEAR SEARCH
    ================================================== */

    clearSearch.addEventListener(
        "click",
        () => {

            searchInput.value = "";

            filterPapers();

            searchInput.focus();

        }
    );


    /* =================================================
       RESET BUTTON
    ================================================== */

    resetFilters.addEventListener(
        "click",
        resetAllFilters
    );


    resetNoResults.addEventListener(
        "click",
        resetAllFilters
    );


    /* =================================================
       INITIAL LOAD
    ================================================== */

    sortPapers();

});