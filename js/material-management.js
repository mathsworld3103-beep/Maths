document.addEventListener("DOMContentLoaded", function () {

    const modal =
        document.getElementById("materialModal");

    const openButton =
        document.getElementById("openAddMaterial");

    const closeButton =
        document.getElementById("closeMaterialModal");

    const cancelButton =
        document.getElementById("cancelMaterial");

    const form =
        document.getElementById("materialForm");

    const tableBody =
        document.getElementById("materialTableBody");

    const searchInput =
        document.getElementById("searchMaterial");

    const subjectFilter =
        document.getElementById("filterSubject");

    const gradeFilter =
        document.getElementById("filterGrade");

    const resetButton =
        document.getElementById("resetFilters");

    const countElement =
        document.getElementById("materialCount");

    const message =
        document.getElementById("materialMessage");

    const saveButton =
        document.getElementById("saveMaterial");


    // =================================================
    // OPEN MODAL
    // =================================================

    openButton.addEventListener(
        "click",
        function () {

            modal.classList.add("show");

        }
    );


    // =================================================
    // CLOSE MODAL
    // =================================================

    function closeModal() {

        modal.classList.remove("show");

        form.reset();

        message.textContent = "";

        message.className =
            "form-message";
    }


    closeButton.addEventListener(
        "click",
        closeModal
    );


    cancelButton.addEventListener(
        "click",
        closeModal
    );


    modal.addEventListener(
        "click",
        function (e) {

            if (e.target === modal) {

                closeModal();

            }

        }
    );


    // =================================================
    // LOAD MATERIALS
    // =================================================

    async function loadMaterials() {

        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="loading">
                    Loading materials...
                </td>
            </tr>
        `;


        const params =
            new URLSearchParams();


        params.append(
            "action",
            "list"
        );


        if (searchInput.value.trim() !== "") {

            params.append(
                "search",
                searchInput.value.trim()
            );
        }


        if (subjectFilter.value !== "") {

            params.append(
                "subject",
                subjectFilter.value
            );
        }


        if (gradeFilter.value !== "") {

            params.append(
                "grade",
                gradeFilter.value
            );
        }


        try {

            const response =
                await fetch(
                    "material-api.php?" +
                    params.toString()
                );


            const data =
                await response.json();


            if (!data.success) {

                throw new Error(
                    data.message
                );
            }


            countElement.textContent =
                data.count +
                " material(s)";


            renderMaterials(
                data.materials
            );


        } catch (error) {

            console.error(error);

            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="error-row">
                        Failed to load materials.
                    </td>
                </tr>
            `;

        }

    }


    // =================================================
    // RENDER MATERIALS
    // =================================================

    function renderMaterials(materials) {

        if (!materials.length) {

            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="empty-row">
                        No materials found.
                    </td>
                </tr>
            `;

            return;
        }


        tableBody.innerHTML =
            materials.map(
                function (material) {

                    const date =
                        new Date(
                            material.created_at
                        ).toLocaleDateString();


                    return `
                        <tr>

                            <td>
                                #${material.id}
                            </td>

                            <td>

                                <div class="material-name">

                                    <div class="pdf-icon">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </div>

                                    <div>

                                        <strong>
                                            ${escapeHtml(
                                                material.title
                                            )}
                                        </strong>

                                        <small>
                                            ${escapeHtml(
                                                material.file_name
                                            )}
                                        </small>

                                    </div>

                                </div>

                            </td>

                            <td>
                                ${escapeHtml(
                                    material.subject
                                )}
                            </td>

                            <td>
                                <span class="grade-badge">
                                    ${escapeHtml(
                                        material.grade
                                    )}
                                </span>
                            </td>

                            <td>
                                ${escapeHtml(
                                    material.material_type
                                )}
                            </td>

                            <td>
                                ${
                                    material.material_year
                                    || "-"
                                }
                            </td>

                            <td>
                                ${date}
                            </td>

                            <td>

                                <div class="action-buttons">

                                    <a
                                        href="../${material.file_path}"
                                        target="_blank"
                                        class="view-btn"
                                        title="View PDF"
                                    >
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <button
                                        class="delete-btn"
                                        data-id="${material.id}"
                                        title="Delete"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </button>

                                </div>

                            </td>

                        </tr>
                    `;

                }
            ).join("");


        // =================================================
        // DELETE BUTTONS
        // =================================================

        document
            .querySelectorAll(".delete-btn")
            .forEach(
                function (button) {

                    button.addEventListener(
                        "click",
                        function () {

                            deleteMaterial(
                                button.dataset.id
                            );

                        }
                    );

                }
            );

    }


    // =================================================
    // DELETE MATERIAL
    // =================================================

    async function deleteMaterial(id) {

        const confirmed =
            confirm(
                "Are you sure you want to delete this material?"
            );


        if (!confirmed) {
            return;
        }


        const formData =
            new FormData();


        formData.append(
            "action",
            "delete"
        );

        formData.append(
            "id",
            id
        );


        try {

            const response =
                await fetch(
                    "material-api.php",
                    {
                        method: "POST",
                        body: formData
                    }
                );


            const data =
                await response.json();


            if (data.success) {

                loadMaterials();

            } else {

                alert(
                    data.message
                );

            }


        } catch (error) {

            console.error(error);

            alert(
                "Server error."
            );

        }

    }


    // =================================================
    // ADD MATERIAL
    // =================================================

    form.addEventListener(
        "submit",
        async function (e) {

            e.preventDefault();


            saveButton.disabled = true;

            saveButton.innerHTML =
                `<i class="fa-solid fa-spinner fa-spin"></i>
                 Uploading...`;


            const formData =
                new FormData(form);


            formData.append(
                "action",
                "add"
            );


            try {

                const response =
                    await fetch(
                        "material-api.php",
                        {
                            method: "POST",
                            body: formData
                        }
                    );


                const data =
                    await response.json();


                if (data.success) {

                    message.textContent =
                        data.message;

                    message.className =
                        "form-message success";


                    form.reset();


                    setTimeout(
                        function () {

                            closeModal();

                            loadMaterials();

                        },
                        800
                    );


                } else {

                    message.textContent =
                        data.message;

                    message.className =
                        "form-message error";

                }


            } catch (error) {

                console.error(error);

                message.textContent =
                    "Server error. Please try again.";

                message.className =
                    "form-message error";

            }


            saveButton.disabled = false;

            saveButton.innerHTML =
                `<i class="fa-solid fa-floppy-disk"></i>
                 Save Material`;

        }
    );


    // =================================================
    // FILTERS
    // =================================================

    searchInput.addEventListener(
        "input",
        debounce(
            loadMaterials,
            300
        )
    );


    subjectFilter.addEventListener(
        "change",
        loadMaterials
    );


    gradeFilter.addEventListener(
        "change",
        loadMaterials
    );


    resetButton.addEventListener(
        "click",
        function () {

            searchInput.value = "";

            subjectFilter.value = "";

            gradeFilter.value = "";

            loadMaterials();

        }
    );


    // =================================================
    // HTML ESCAPE
    // =================================================

    function escapeHtml(value) {

        const div =
            document.createElement("div");

        div.textContent =
            value ?? "";

        return div.innerHTML;
    }


    // =================================================
    // DEBOUNCE
    // =================================================

    function debounce(
        callback,
        delay
    ) {

        let timeout;

        return function () {

            clearTimeout(timeout);

            timeout =
                setTimeout(
                    callback,
                    delay
                );

        };

    }


    // =================================================
    // INITIAL LOAD
    // =================================================

    loadMaterials();

});