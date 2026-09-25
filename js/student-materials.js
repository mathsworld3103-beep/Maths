let searchTimer = null;


document.addEventListener("DOMContentLoaded", function () {

    const searchInput =
        document.getElementById("searchInput");

    const subjectFilter =
        document.getElementById("subjectFilter");


    loadMaterials();


    searchInput.addEventListener("input", function () {

        clearTimeout(searchTimer);

        searchTimer = setTimeout(function () {

            loadMaterials();

        }, 300);

    });


    subjectFilter.addEventListener("change", function () {

        loadMaterials();

    });

});


async function loadMaterials() {

    const searchInput =
        document.getElementById("searchInput");

    const subjectFilter =
        document.getElementById("subjectFilter");


    const search =
        searchInput.value.trim();

    const subject =
        subjectFilter.value;


    showLoading();


    try {

        const params = new URLSearchParams();

        if (search !== "") {
            params.append("search", search);
        }

        if (subject !== "") {
            params.append("subject", subject);
        }


        const response =
            await fetch(
                "materials-api.php?" +
                params.toString()
            );


        if (!response.ok) {
            throw new Error("Server error");
        }


        const data =
            await response.json();


        if (!data.success) {

            throw new Error(
                data.message ||
                "Unable to load materials."
            );

        }


        renderMaterials(
            data.materials || []
        );


    } catch (error) {

        console.error(error);

        showError();

    }

}


function renderMaterials(materials) {

    const grid =
        document.getElementById("materialsGrid");

    const empty =
        document.getElementById("emptyState");

    const loading =
        document.getElementById("loadingState");

    const error =
        document.getElementById("errorState");

    const count =
        document.getElementById("materialCount");


    loading.style.display = "none";

    error.style.display = "none";


    count.textContent =
        materials.length;


    grid.innerHTML = "";


    if (materials.length === 0) {

        grid.style.display = "none";

        empty.style.display = "block";

        return;

    }


    empty.style.display = "none";

    grid.style.display = "grid";


    materials.forEach(function (material) {

        const card =
            document.createElement("div");

        card.className = "material-card";


        const filePath =
            "../" + material.file_path;


        card.innerHTML = `

            <div class="material-icon">

                <i class="fa-solid fa-file-pdf"></i>

            </div>


            <div class="material-content">

                <span class="material-type">
                    ${escapeHtml(material.material_type || "Notes")}
                </span>

                <h3>
                    ${escapeHtml(material.title)}
                </h3>

                <p>
                    ${escapeHtml(
                        material.description ||
                        "Study material"
                    )}
                </p>


                <div class="material-meta">

                    <span>
                        <i class="fa-solid fa-book"></i>
                        ${escapeHtml(material.subject)}
                    </span>

                    ${
                        material.material_year
                        ? `
                        <span>
                            <i class="fa-solid fa-calendar"></i>
                            ${escapeHtml(
                                material.material_year
                            )}
                        </span>
                        `
                        : ""
                    }

                </div>

            </div>


            <div class="material-actions">

                <a
                    href="${filePath}"
                    target="_blank"
                    class="view-btn">

                    <i class="fa-solid fa-eye"></i>
                    View

                </a>


                <a
                    href="${filePath}"
                    download
                    class="download-btn">

                    <i class="fa-solid fa-download"></i>
                    Download

                </a>

            </div>

        `;


        grid.appendChild(card);

    });

}


function showLoading() {

    document.getElementById(
        "loadingState"
    ).style.display = "flex";


    document.getElementById(
        "materialsGrid"
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
        "materialsGrid"
    ).style.display = "none";


    document.getElementById(
        "emptyState"
    ).style.display = "none";


    document.getElementById(
        "errorState"
    ).style.display = "block";

}


function escapeHtml(value) {

    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");

}