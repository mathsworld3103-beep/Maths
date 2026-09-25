document.addEventListener(
    "DOMContentLoaded",
    function () {

        const search =
            document.getElementById(
                "activitySearch"
            );

        const actionFilter =
            document.getElementById(
                "activityActionFilter"
            );

        const moduleFilter =
            document.getElementById(
                "activityModuleFilter"
            );

        const rows =
            Array.from(
                document.querySelectorAll(
                    ".activity-row"
                )
            );


        function filterActivities() {

            const searchValue =
                search.value
                    .trim()
                    .toLowerCase();

            const action =
                actionFilter.value;

            const module =
                moduleFilter.value;


            rows.forEach(function (row) {

                const rowSearch =
                    row.dataset.search || "";

                const rowAction =
                    row.dataset.action || "";

                const rowModule =
                    row.dataset.module || "";


                const matchesSearch =
                    rowSearch.includes(
                        searchValue
                    );


                const matchesAction =
                    action === "all" ||
                    rowAction === action;


                const matchesModule =
                    module === "all" ||
                    rowModule === module;


                row.style.display =
                    matchesSearch &&
                    matchesAction &&
                    matchesModule
                        ? ""
                        : "none";

            });

        }


        if (search) {

            search.addEventListener(
                "input",
                filterActivities
            );

        }


        if (actionFilter) {

            actionFilter.addEventListener(
                "change",
                filterActivities
            );

        }


        if (moduleFilter) {

            moduleFilter.addEventListener(
                "change",
                filterActivities
            );

        }

    }
);