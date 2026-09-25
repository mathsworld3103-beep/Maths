/* =========================================================
   MATHSWORLD CONTACT PAGE
========================================================= */

document.addEventListener("DOMContentLoaded", () => {

    /* =====================================================
       SCROLL REVEAL
    ===================================================== */

    const revealElements =
        document.querySelectorAll(".reveal");


    const revealObserver =
        new IntersectionObserver(
            (entries, observer) => {

                entries.forEach(entry => {

                    if (entry.isIntersecting) {

                        entry.target.classList.add("show");

                        observer.unobserve(entry.target);

                    }

                });

            },
            {
                threshold: 0.1
            }
        );


    revealElements.forEach(element => {

        revealObserver.observe(element);

    });



    /* =====================================================
       MOBILE MENU
    ===================================================== */

    const menuToggle =
        document.getElementById("menuToggle");

    const mobileMenu =
        document.getElementById("mobileMenu");


    if (menuToggle && mobileMenu) {

        menuToggle.addEventListener(
            "click",
            () => {

                mobileMenu.classList.toggle("open");

                const icon =
                    menuToggle.querySelector("i");


                if (
                    mobileMenu.classList.contains("open")
                ) {

                    icon.classList.remove("fa-bars");

                    icon.classList.add("fa-xmark");

                } else {

                    icon.classList.remove("fa-xmark");

                    icon.classList.add("fa-bars");

                }

            }
        );


        mobileMenu
            .querySelectorAll("a")
            .forEach(link => {

                link.addEventListener(
                    "click",
                    () => {

                        mobileMenu.classList.remove("open");

                        const icon =
                            menuToggle.querySelector("i");

                        icon.classList.remove("fa-xmark");

                        icon.classList.add("fa-bars");

                    }
                );

            });

    }



    /* =====================================================
       FAQ ACCORDION
    ===================================================== */

    const faqQuestions =
        document.querySelectorAll(".faq-question");


    faqQuestions.forEach(question => {

        question.addEventListener(
            "click",
            () => {

                const currentItem =
                    question.closest(".faq-item");


                document
                    .querySelectorAll(".faq-item.active")
                    .forEach(item => {

                        if (item !== currentItem) {

                            item.classList.remove("active");

                        }

                    });


                currentItem.classList.toggle("active");

            }
        );

    });



    /* =====================================================
       CONTACT FORM
       FRONTEND DEMO
    ===================================================== */

    const contactForm =
        document.getElementById("contactForm");

    const formMessage =
        document.getElementById("formMessage");


    if (contactForm) {

        contactForm.addEventListener(
            "submit",
            event => {

                event.preventDefault();


                const submitButton =
                    contactForm.querySelector(
                        ".submit-btn"
                    );


                const name =
                    document
                        .getElementById("name")
                        .value
                        .trim();

                const email =
                    document
                        .getElementById("email")
                        .value
                        .trim();

                const subject =
                    document
                        .getElementById("subject")
                        .value;

                const message =
                    document
                        .getElementById("message")
                        .value
                        .trim();


                if (
                    !name ||
                    !email ||
                    !subject ||
                    !message
                ) {

                    showMessage(
                        "Please fill in all required fields.",
                        "error"
                    );

                    return;

                }


                submitButton.classList.add("loading");

                submitButton.querySelector("span").textContent =
                    "Sending...";


                /*
                    Frontend demonstration.

                    Later this can connect to:
                    - Node.js
                    - Express
                    - MongoDB
                    - Email service
                    - Admin dashboard
                */

                setTimeout(() => {

                    showMessage(
                        "Thank you! Your enquiry has been received.",
                        "success"
                    );


                    contactForm.reset();


                    submitButton.classList.remove(
                        "loading"
                    );


                    submitButton.querySelector("span")
                        .textContent =
                        "Send Message";


                }, 1200);

            }
        );

    }


    function showMessage(text, type) {

        if (!formMessage) return;


        formMessage.textContent = text;

        formMessage.className =
            "form-message " + type;


        setTimeout(() => {

            formMessage.className =
                "form-message";

        }, 5000);

    }

});