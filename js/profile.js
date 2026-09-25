/* =====================================================
   MATHSWORLD PROFILE
===================================================== */

document.addEventListener("DOMContentLoaded", () => {


    /* ================================================
       MOBILE SIDEBAR
    ================================================ */

    const menuBtn =
        document.getElementById("profileMenuBtn");

    const sidebar =
        document.getElementById("profileSidebar");

    const overlay =
        document.getElementById("profileOverlay");


    if (menuBtn && sidebar && overlay) {

        menuBtn.addEventListener("click", () => {

            sidebar.classList.toggle("open");

            overlay.classList.toggle("active");

        });


        overlay.addEventListener("click", () => {

            sidebar.classList.remove("open");

            overlay.classList.remove("active");

        });

    }


    /* ================================================
       EDIT PROFILE
    ================================================ */

    const editButton =
        document.getElementById("editProfileBtn");

    const profileActions =
        document.getElementById("profileActions");

    const profileForm =
        document.getElementById("profileForm");


    const profileInputs =
        profileForm
            ? profileForm.querySelectorAll("input, select")
            : [];


    if (editButton) {

        editButton.addEventListener("click", () => {

            profileInputs.forEach(input => {

                input.disabled = false;

            });

            profileActions.classList.add("visible");

            editButton.innerHTML =
                '<i class="fa-solid fa-pen"></i> Editing';

        });

    }


    /* ================================================
       CANCEL EDIT
    ================================================ */

    const cancelButton =
        document.getElementById("cancelProfile");


    if (cancelButton) {

        cancelButton.addEventListener("click", () => {

            profileInputs.forEach(input => {

                input.disabled = true;

            });

            profileActions.classList.remove("visible");

            editButton.innerHTML =
                '<i class="fa-solid fa-pen"></i> Edit';

        });

    }


    /* ================================================
       SAVE PROFILE
    ================================================ */

    if (profileForm) {

        profileForm.addEventListener("submit", event => {

            event.preventDefault();


            profileInputs.forEach(input => {

                input.disabled = true;

            });

            profileActions.classList.remove("visible");

            editButton.innerHTML =
                '<i class="fa-solid fa-check"></i> Saved';


            setTimeout(() => {

                editButton.innerHTML =
                    '<i class="fa-solid fa-pen"></i> Edit';

            }, 2000);

        });

    }


    /* ================================================
       CHANGE PASSWORD MODAL
    ================================================ */

    const changePasswordBtn =
        document.getElementById("changePasswordBtn");

    const passwordModal =
        document.getElementById("passwordModal");

    const modalClose =
        document.getElementById("modalClose");


    if (changePasswordBtn && passwordModal) {

        changePasswordBtn.addEventListener("click", () => {

            passwordModal.classList.add("show");

        });

    }


    if (modalClose && passwordModal) {

        modalClose.addEventListener("click", () => {

            passwordModal.classList.remove("show");

        });

    }


    if (passwordModal) {

        passwordModal.addEventListener("click", event => {

            if (event.target === passwordModal) {

                passwordModal.classList.remove("show");

            }

        });

    }


    /* ================================================
       PASSWORD FORM
    ================================================ */

    const passwordForm =
        document.getElementById("passwordForm");

    const passwordMessage =
        document.getElementById("passwordMessage");


    if (passwordForm) {

        passwordForm.addEventListener("submit", event => {

            event.preventDefault();


            const current =
                document.getElementById("currentPassword").value;

            const newPassword =
                document.getElementById("newPassword").value;

            const confirm =
                document.getElementById("confirmNewPassword").value;


            if (newPassword.length < 6) {

                passwordMessage.textContent =
                    "New password must contain at least 6 characters.";

                passwordMessage.className =
                    "password-message error";

                return;

            }


            if (newPassword !== confirm) {

                passwordMessage.textContent =
                    "New passwords do not match.";

                passwordMessage.className =
                    "password-message error";

                return;

            }


            if (!current) {

                passwordMessage.textContent =
                    "Please enter your current password.";

                passwordMessage.className =
                    "password-message error";

                return;

            }


            passwordMessage.textContent =
                "Password updated successfully in this frontend demo.";

            passwordMessage.className =
                "password-message success";


            passwordForm.reset();


            setTimeout(() => {

                passwordModal.classList.remove("show");

                passwordMessage.textContent = "";

            }, 1800);

        });

    }


    /* ================================================
       AVATAR
    ================================================ */

    const avatarEdit =
        document.getElementById("avatarEdit");


    if (avatarEdit) {

        avatarEdit.addEventListener("click", () => {

            alert(
                "Profile photo upload will be connected when the backend is added."
            );

        });

    }


    /* ================================================
       DELETE ACCOUNT
    ================================================ */

    const deleteAccount =
        document.getElementById("deleteAccountBtn");


    if (deleteAccount) {

        deleteAccount.addEventListener("click", () => {

            const confirmDelete =
                confirm(
                    "Are you sure you want to delete your account?"
                );


            if (confirmDelete) {

                alert(
                    "Account deletion will be connected to the backend later."
                );

            }

        });

    }


    /* ================================================
       LOGOUT
    ================================================ */

    const logout =
        document.getElementById("profileLogout");


    if (logout) {

        logout.addEventListener("click", event => {

            event.preventDefault();

            const confirmLogout =
                confirm("Are you sure you want to logout?");


            if (confirmLogout) {

                window.location.href = "index.html";

            }

        });

    }


});