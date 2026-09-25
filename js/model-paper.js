/* =====================================================
   MATHSWORLD MODEL PAPERS
   FRONTEND ONLY
===================================================== */

document.addEventListener("DOMContentLoaded", () => {


    /* =================================================
       GRADE 6-11 ELEMENTS
    ================================================== */

    const gradeFilter =
        document.getElementById("gradeFilter");

    const gradeSubjectFilter =
        document.getElementById("gradeSubjectFilter");

    const gradeYearFilter =
        document.getElementById("gradeYearFilter");

    const gradeSearch =
        document.getElementById("gradeSearch");

    const resetGrade =
        document.getElementById("resetGrade");

    const gradeGrid =
        document.getElementById("gradeGrid");

    const gradeCount =
        document.getElementById("gradeCount");

    const gradeNoResults =
        document.getElementById("gradeNoResults");


    let gradeCards =
        Array.from(
            document.querySelectorAll(
                "#gradeGrid .model-card"
            )
        );


    /* =================================================
       FILTER GRADE PAPERS
    ================================================== */

    function filterGradePapers() {

        const selectedGrade =
            gradeFilter.value.toLowerCase();

        const selectedSubject =
            gradeSubjectFilter.value.toLowerCase();

        const selectedYear =
            gradeYearFilter.value.toLowerCase();

        const search =
            gradeSearch.value
                .trim()
                .toLowerCase();


        let visible = 0;


        gradeCards.forEach(card => {

            const grade =
                card.dataset.grade.toLowerCase();

            const subject =
                card.dataset.subject.toLowerCase();

            const year =
                card.dataset.year.toLowerCase();

            const title =
                card.dataset.title.toLowerCase();


            const gradeMatch =
                selectedGrade === "all" ||
                grade === selectedGrade;


            const subjectMatch =
                selectedSubject === "all" ||
                subject === selectedSubject;


            const yearMatch =
                selectedYear === "all" ||
                year === selectedYear;


            const searchMatch =
                search === "" ||
                grade.includes(search) ||
                subject.includes(search) ||
                year.includes(search) ||
                title.includes(search);


            if (
                gradeMatch &&
                subjectMatch &&
                yearMatch &&
                searchMatch
            ) {

                card.classList.remove("hide");

                visible++;

            } else {

                card.classList.add("hide");

            }

        });


        gradeCount.textContent =
            visible;


        if (visible === 0) {

            gradeNoResults.classList.add(
                "show"
            );

            gradeGrid.style.display =
                "none";

        } else {

            gradeNoResults.classList.remove(
                "show"
            );

            gradeGrid.style.display =
                "grid";

        }

    }


    /* =================================================
       GRADE EVENTS
    ================================================== */

    gradeFilter.addEventListener(
        "change",
        filterGradePapers
    );

    gradeSubjectFilter.addEventListener(
        "change",
        filterGradePapers
    );

    gradeYearFilter.addEventListener(
        "change",
        filterGradePapers
    );

    gradeSearch.addEventListener(
        "input",
        filterGradePapers
    );


    /* =================================================
       RESET GRADE
    ================================================== */

    resetGrade.addEventListener(
        "click",
        () => {

            gradeFilter.value = "all";

            gradeSubjectFilter.value = "all";

            gradeYearFilter.value = "all";

            gradeSearch.value = "";

            filterGradePapers();

        }
    );



    /* =================================================
       A/L ELEMENTS
    ================================================== */

    const alSearch =
        document.getElementById("alSearch");

    const alGrid =
        document.getElementById("alGrid");

    const alCount =
        document.getElementById("alCount");

    const alNoResults =
        document.getElementById("alNoResults");

    const alTabs =
        document.querySelectorAll(".al-tab");

    const alCards =
        Array.from(
            document.querySelectorAll(
                "#alGrid .al-card"
            )
        );


    let selectedALSubject = "all";


    /* =================================================
       FILTER A/L
    ================================================== */

    function filterALPapers() {

        const search =
            alSearch.value
                .trim()
                .toLowerCase();


        let visible = 0;


        alCards.forEach(card => {

            const subject =
                card.dataset.subject
                    .toLowerCase();

            const title =
                card.dataset.title
                    .toLowerCase();

            const year =
                card.dataset.year
                    .toLowerCase();


            const subjectMatch =
                selectedALSubject === "all" ||
                subject === selectedALSubject;


            const searchMatch =
                search === "" ||
                subject.includes(search) ||
                title.includes(search) ||
                year.includes(search);


            if (
                subjectMatch &&
                searchMatch
            ) {

                card.classList.remove(
                    "hide"
                );

                visible++;

            } else {

                card.classList.add(
                    "hide"
                );

            }

        });


        alCount.textContent =
            visible;


        if (visible === 0) {

            alNoResults.classList.add(
                "show"
            );

            alGrid.style.display =
                "none";

        } else {

            alNoResults.classList.remove(
                "show"
            );

            alGrid.style.display =
                "grid";

        }

    }


    /* =================================================
       A/L SUBJECT TABS
    ================================================== */

    alTabs.forEach(tab => {

        tab.addEventListener(
            "click",
            () => {

                alTabs.forEach(item => {

                    item.classList.remove(
                        "active"
                    );

                });


                tab.classList.add(
                    "active"
                );


                selectedALSubject =
                    tab.dataset.subject
                        .toLowerCase();


                filterALPapers();

            }
        );

    });


    /* =================================================
       A/L SEARCH
    ================================================== */

    alSearch.addEventListener(
        "input",
        filterALPapers
    );


    /* =================================================
       INITIAL LOAD
    ================================================== */

    filterGradePapers();

    filterALPapers();

});