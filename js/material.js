/* =========================================================
   MATHSWORLD - MATERIAL FILTER
========================================================= */

const searchInput = document.getElementById("searchInput");

const subjectFilter = document.getElementById("subjectFilter");

const yearFilter = document.getElementById("yearFilter");

const typeFilter = document.getElementById("typeFilter");

const sortFilter = document.getElementById("sortFilter");

const resetFilters = document.getElementById("resetFilters");

const clearSearch = document.getElementById("clearSearch");

const materialsGrid = document.getElementById("materialsGrid");

const resultCount = document.getElementById("resultCount");

const noResults = document.getElementById("noResults");


function filterMaterials() {

    const searchValue =
        searchInput.value.toLowerCase().trim();

    const subjectValue =
        subjectFilter.value;

    const yearValue =
        yearFilter.value;

    const typeValue =
        typeFilter.value;


    const cards =
        Array.from(
            materialsGrid.querySelectorAll(".pdf-card")
        );


    let visibleCards = [];


    cards.forEach(card => {

        const title =
            card.dataset.title.toLowerCase();

        const subject =
            card.dataset.subject;

        const year =
            card.dataset.year;

        const type =
            card.dataset.type;


        const matchesSearch =
            title.includes(searchValue);


        const matchesSubject =
            subjectValue === "all" ||
            subject === subjectValue;


        const matchesYear =
            yearValue === "all" ||
            year === yearValue;


        const matchesType =
            typeValue === "all" ||
            type === typeValue;


        if (
            matchesSearch &&
            matchesSubject &&
            matchesYear &&
            matchesType
        ) {

            card.style.display = "flex";

            visibleCards.push(card);

        } else {

            card.style.display = "none";

        }

    });


    updateResultCount(visibleCards.length);


    if (visibleCards.length === 0) {

        noResults.classList.add("show");

    } else {

        noResults.classList.remove("show");

    }


    sortCards(visibleCards);

}


/* ================= RESULT COUNT ================= */

function updateResultCount(count) {

    resultCount.textContent =
        `Showing ${count} material${count !== 1 ? "s" : ""}`;

}


/* ================= SORT ================= */

function sortCards(cards) {

    const sortValue =
        sortFilter.value;


    cards.sort((a, b) => {

        if (sortValue === "newest") {

            return (
                Number(b.dataset.year) -
                Number(a.dataset.year)
            );

        }


        if (sortValue === "oldest") {

            return (
                Number(a.dataset.year) -
                Number(b.dataset.year)
            );

        }


        if (sortValue === "az") {

            return a.dataset.title.localeCompare(
                b.dataset.title
            );

        }

    });


    cards.forEach(card => {

        materialsGrid.appendChild(card);

    });

}


/* ================= SEARCH ================= */

searchInput.addEventListener(
    "input",
    () => {

        clearSearch.style.display =
            searchInput.value
                ? "block"
                : "none";

        filterMaterials();

    }
);


/* ================= FILTERS ================= */

subjectFilter.addEventListener(
    "change",
    filterMaterials
);


yearFilter.addEventListener(
    "change",
    filterMaterials
);


typeFilter.addEventListener(
    "change",
    filterMaterials
);


sortFilter.addEventListener(
    "change",
    filterMaterials
);


/* ================= CLEAR SEARCH ================= */

clearSearch.addEventListener(
    "click",
    () => {

        searchInput.value = "";

        clearSearch.style.display =
            "none";

        filterMaterials();

        searchInput.focus();

    }
);


/* ================= RESET ================= */

resetFilters.addEventListener(
    "click",
    () => {

        searchInput.value = "";

        subjectFilter.value = "all";

        yearFilter.value = "all";

        typeFilter.value = "all";

        sortFilter.value = "newest";

        clearSearch.style.display =
            "none";

        filterMaterials();

    }
);


/* ================= INITIAL LOAD ================= */

filterMaterials();