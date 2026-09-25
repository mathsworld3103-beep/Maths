/* =====================================================
   MATHSWORLD NOTES PAGE
   FRONTEND ONLY
   ===================================================== */

document.addEventListener("DOMContentLoaded", () => {

    const searchInput = document.getElementById("notesSearch");

    const clearSearch = document.getElementById("clearSearch");

    const subjectFilter =
        document.getElementById("subjectFilter");

    const gradeFilter =
        document.getElementById("gradeFilter");

    const yearFilter =
        document.getElementById("yearFilter");

    const sortFilter =
        document.getElementById("sortFilter");

    const resetFilters =
        document.getElementById("resetFilters");

    const resetNoResults =
        document.getElementById("resetNoResults");

    const notesGrid =
        document.getElementById("notesGrid");

    const resultCount =
        document.getElementById("resultCount");

    const noResults =
        document.getElementById("noResults");


    let notes =
        Array.from(
            document.querySelectorAll(".note-card")
        );


    /* =================================================
       FILTER NOTES
       ================================================= */

    function filterNotes() {

        const searchValue =
            searchInput.value
                .trim()
                .toLowerCase();

        const selectedSubject =
            subjectFilter.value
                .toLowerCase();

        const selectedGrade =
            gradeFilter.value
                .toLowerCase();

        const selectedYear =
            yearFilter.value
                .toLowerCase();


        let visibleNotes = [];


        notes.forEach(note => {

            const subject =
                note.dataset.subject
                    .toLowerCase();

            const grade =
                note.dataset.grade
                    .toLowerCase();

            const year =
                note.dataset.year
                    .toLowerCase();

            const title =
                note.dataset.title
                    .toLowerCase();

            const description =
                note.querySelector("p")
                    ?.textContent
                    .toLowerCase() || "";


            const searchMatch =
                searchValue === "" ||
                subject.includes(searchValue) ||
                grade.includes(searchValue) ||
                year.includes(searchValue) ||
                title.includes(searchValue) ||
                description.includes(searchValue);


            const subjectMatch =
                selectedSubject === "all" ||
                subject === selectedSubject;


            const gradeMatch =
                selectedGrade === "all" ||
                grade === selectedGrade;


            const yearMatch =
                selectedYear === "all" ||
                year === selectedYear;


            if (
                searchMatch &&
                subjectMatch &&
                gradeMatch &&
                yearMatch
            ) {

                note.classList.remove("hide");

                note.classList.add("show-animation");

                visibleNotes.push(note);

            } else {

                note.classList.add("hide");

                note.classList.remove("show-animation");

            }

        });


        updateResultCount(visibleNotes.length);

    }


    /* =================================================
       UPDATE RESULT COUNT
       ================================================= */

    function updateResultCount(count) {

        if (count === 0) {

            resultCount.textContent =
                "No notes found";

            noResults.classList.add("show");

            notesGrid.style.display = "none";

        } else {

            resultCount.textContent =
                `Showing ${count} ${
                    count === 1 ? "note" : "notes"
                }`;

            noResults.classList.remove("show");

            notesGrid.style.display = "grid";

        }

    }


    /* =================================================
       SORT NOTES
       ================================================= */

    function sortNotes() {

        const sortValue =
            sortFilter.value;


        if (sortValue === "newest") {

            notes.sort((a, b) => {

                return (
                    Number(b.dataset.year) -
                    Number(a.dataset.year)
                );

            });

        }


        else if (sortValue === "oldest") {

            notes.sort((a, b) => {

                return (
                    Number(a.dataset.year) -
                    Number(b.dataset.year)
                );

            });

        }


        else if (sortValue === "az") {

            notes.sort((a, b) => {

                return a.dataset.title
                    .localeCompare(
                        b.dataset.title
                    );

            });

        }


        notes.forEach(note => {

            notesGrid.appendChild(note);

        });


        filterNotes();

    }


    /* =================================================
       RESET
       ================================================= */

    function resetAllFilters() {

        searchInput.value = "";

        subjectFilter.value = "all";

        gradeFilter.value = "all";

        yearFilter.value = "all";

        sortFilter.value = "newest";


        notes.forEach(note => {

            note.classList.remove("hide");

        });


        sortNotes();

    }


    /* =================================================
       SEARCH
       ================================================= */

    searchInput.addEventListener(
        "input",
        filterNotes
    );


    /* =================================================
       FILTER EVENTS
       ================================================= */

    subjectFilter.addEventListener(
        "change",
        filterNotes
    );

    gradeFilter.addEventListener(
        "change",
        filterNotes
    );

    yearFilter.addEventListener(
        "change",
        filterNotes
    );


    /* =================================================
       SORT EVENT
       ================================================= */

    sortFilter.addEventListener(
        "change",
        sortNotes
    );


    /* =================================================
       CLEAR SEARCH
       ================================================= */

    clearSearch.addEventListener(
        "click",
        () => {

            searchInput.value = "";

            filterNotes();

            searchInput.focus();

        }
    );


    /* =================================================
       RESET BUTTON
       ================================================= */

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
       ================================================= */

    sortNotes();

});