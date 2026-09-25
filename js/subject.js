/* =========================================================
   MATHSWORLD - SUBJECTS PAGE
   Frontend Only
========================================================= */


document.addEventListener("DOMContentLoaded", () => {

    const searchInput = document.getElementById("subjectSearch");
    const clearButton = document.getElementById("clearSearch");
    const resetButton = document.getElementById("resetSubjects");

    const subjectCards = document.querySelectorAll(".subject-card");

    const searchCount = document.getElementById("searchCount");

    const noResults = document.getElementById("noSubjectResults");


    /* =====================================================
       UPDATE COUNT
    ===================================================== */

    function updateCount(count) {

        if (count === 1) {

            searchCount.textContent = "1 subject found";

        } else {

            searchCount.textContent = `${count} subjects available`;

        }

    }


    /* =====================================================
       SEARCH SUBJECTS
    ===================================================== */

    function searchSubjects() {

        const searchValue =
            searchInput.value
                .trim()
                .toLowerCase();


        let visibleCount = 0;


        subjectCards.forEach(card => {

            const subjectName =
                card.dataset.subject.toLowerCase();


            const cardText =
                card.textContent.toLowerCase();


            const matches =
                subjectName.includes(searchValue) ||
                cardText.includes(searchValue);


            if (matches) {

                card.classList.remove("hidden");

                visibleCount++;

            } else {

                card.classList.add("hidden");

            }

        });


        /* Search button */

        if (searchValue.length > 0) {

            clearButton.style.display = "flex";

        } else {

            clearButton.style.display = "none";

        }


        /* Count */

        updateCount(visibleCount);


        /* No results */

        if (visibleCount === 0) {

            noResults.style.display = "block";

        } else {

            noResults.style.display = "none";

        }

    }


    /* =====================================================
       INPUT EVENT
    ===================================================== */

    searchInput.addEventListener("input", searchSubjects);


    /* =====================================================
       CLEAR SEARCH
    ===================================================== */

    clearButton.addEventListener("click", () => {

        searchInput.value = "";

        searchSubjects();

        searchInput.focus();

    });


    /* =====================================================
       RESET SUBJECTS
    ===================================================== */

    resetButton.addEventListener("click", () => {

        searchInput.value = "";

        searchSubjects();

        window.scrollTo({
            top: document.getElementById("school-subjects").offsetTop - 90,
            behavior: "smooth"
        });

    });


    /* =====================================================
       CARD CLICK ANIMATION
    ===================================================== */

    subjectCards.forEach(card => {

        card.addEventListener("mouseenter", () => {

            card.style.zIndex = "3";

        });


        card.addEventListener("mouseleave", () => {

            card.style.zIndex = "1";

        });

    });


    /* =====================================================
       INITIAL STATE
    ===================================================== */

    updateCount(subjectCards.length);

});