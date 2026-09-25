document.addEventListener("DOMContentLoaded", function () {

    /* =====================================================
       MOBILE MENU
    ===================================================== */

    const menuBtn = document.getElementById("menuBtn");
    const mobileMenu = document.getElementById("mobileMenu");

    if (menuBtn && mobileMenu) {

        menuBtn.addEventListener("click", function () {

            mobileMenu.classList.toggle("show");

            const icon = menuBtn.querySelector("i");

            if (mobileMenu.classList.contains("show")) {

                icon.classList.remove("bi-list");
                icon.classList.add("bi-x-lg");

            } else {

                icon.classList.remove("bi-x-lg");
                icon.classList.add("bi-list");

            }

        });


        /* Close mobile menu after clicking */

        mobileMenu.querySelectorAll("a").forEach(function (link) {

            link.addEventListener("click", function () {

                mobileMenu.classList.remove("show");

                const icon = menuBtn.querySelector("i");

                icon.classList.remove("bi-x-lg");
                icon.classList.add("bi-list");

            });

        });

    }


    /* =====================================================
       SCROLL REVEAL
    ===================================================== */

    const revealElements = document.querySelectorAll(
        ".section-heading, " +
        ".subject-card, " +
        ".material-card, " +
        ".paper-banner, " +
        ".about-content, " +
        ".about-box, " +
        ".contact-container"
    );


    revealElements.forEach(function (element) {

        element.classList.add("reveal");

    });


    const revealObserver = new IntersectionObserver(
        function (entries, observer) {

            entries.forEach(function (entry) {

                if (entry.isIntersecting) {

                    entry.target.classList.add("active");

                    observer.unobserve(entry.target);

                }

            });

        },
        {
            threshold: 0.12
        }
    );


    revealElements.forEach(function (element) {

        revealObserver.observe(element);

    });


    /* =====================================================
       NAVBAR ACTIVE LINK
    ===================================================== */

    const sections = document.querySelectorAll("section[id]");
    const navLinks = document.querySelectorAll(".nav-menu a");


    window.addEventListener("scroll", function () {

        let currentSection = "";

        sections.forEach(function (section) {

            const sectionTop =
                section.offsetTop - 150;

            const sectionHeight =
                section.offsetHeight;

            if (
                window.scrollY >= sectionTop &&
                window.scrollY < sectionTop + sectionHeight
            ) {

                currentSection = section.getAttribute("id");

            }

        });


        navLinks.forEach(function (link) {

            link.classList.remove("active");

            if (
                link.getAttribute("href") ===
                "#" + currentSection
            ) {

                link.classList.add("active");

            }

        });

    });


    /* =====================================================
       SMOOTH SCROLL
    ===================================================== */

    document.querySelectorAll('a[href^="#"]').forEach(function (link) {

        link.addEventListener("click", function (event) {

            const targetId =
                this.getAttribute("href");

            if (
                targetId === "#" ||
                !document.querySelector(targetId)
            ) {
                return;
            }

            event.preventDefault();

            const target =
                document.querySelector(targetId);

            target.scrollIntoView({
                behavior: "smooth",
                block: "start"
            });

        });

    });


    /* =====================================================
       BUTTON ARROW ANIMATION
    ===================================================== */

    document.querySelectorAll(
        ".primary-btn, .secondary-btn, .white-btn"
    ).forEach(function (button) {

        button.addEventListener("mouseenter", function () {

            const arrow =
                this.querySelector(".bi-arrow-right");

            if (arrow) {

                arrow.style.transform =
                    "translateX(5px)";

            }

        });


        button.addEventListener("mouseleave", function () {

            const arrow =
                this.querySelector(".bi-arrow-right");

            if (arrow) {

                arrow.style.transform =
                    "translateX(0)";

            }

        });

    });

});