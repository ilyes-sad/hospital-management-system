document.addEventListener("DOMContentLoaded", () => {
    initLoginValidation();
    initRegisterValidation();
    initCreateValidation();
});

function showError(input, message) {
    clearError(input);

    input.classList.add("input-error");

    const error = document.createElement("small");
    error.className = "field-error js-error";
    error.textContent = message;

    input.parentElement.appendChild(error);
}

function clearError(input) {
    input.classList.remove("input-error");

    const oldError = input.parentElement.querySelector(".js-error");
    if (oldError) {
        oldError.remove();
    }
}

function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validatePhone(phone) {
    return /^[0-9+\s-]{8,20}$/.test(phone);
}

function validatePassword(password) {
    return password.length >= 6;
}

function initLoginValidation() {
    const form = document.querySelector("#loginForm");
    if (!form) return;

    form.addEventListener("submit", (e) => {
        let isValid = true;

        const email = form.querySelector('input[name="email"]');
        const password = form.querySelector('input[name="motDePasse"]');

        [email, password].forEach(clearError);

        if (!email.value.trim()) {
            showError(email, "L’email est obligatoire.");
            isValid = false;
        } else if (!validateEmail(email.value.trim())) {
            showError(email, "Format d’email invalide.");
            isValid = false;
        }

        if (!password.value.trim()) {
            showError(password, "Le mot de passe est obligatoire.");
            isValid = false;
        } else if (!validatePassword(password.value.trim())) {
            showError(password, "Le mot de passe doit contenir au moins 6 caractères.");
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
}

function initRegisterValidation() {
    const form = document.querySelector("#registerForm");
    if (!form) return;

    form.addEventListener("submit", (e) => {
        let isValid = true;

        const nom = form.querySelector('input[name="nom"]');
        const prenom = form.querySelector('input[name="prenom"]');
        const email = form.querySelector('input[name="email"]');
        const password = form.querySelector('input[name="motDePasse"]');
        const telephone = form.querySelector('input[name="telephone"]');
        const adresse = form.querySelector('input[name="adresse"]');

        [nom, prenom, email, password, telephone, adresse].forEach((input) => {
            if (input) clearError(input);
        });

        if (!nom.value.trim()) {
            showError(nom, "Le nom est obligatoire.");
            isValid = false;
        }

        if (!prenom.value.trim()) {
            showError(prenom, "Le prénom est obligatoire.");
            isValid = false;
        }

        if (!email.value.trim()) {
            showError(email, "L’email est obligatoire.");
            isValid = false;
        } else if (!validateEmail(email.value.trim())) {
            showError(email, "Format d’email invalide.");
            isValid = false;
        }

        if (!password.value.trim()) {
            showError(password, "Le mot de passe est obligatoire.");
            isValid = false;
        } else if (!validatePassword(password.value.trim())) {
            showError(password, "Le mot de passe doit contenir au moins 6 caractères.");
            isValid = false;
        }

        if (telephone && telephone.value.trim() && !validatePhone(telephone.value.trim())) {
            showError(telephone, "Numéro de téléphone invalide.");
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
}

function initCreateValidation() {
    const form = document.querySelector("#createUserForm");
    if (!form) return;

    form.addEventListener("submit", (e) => {
        let isValid = true;

        const nom = form.querySelector('input[name="nom"]');
        const prenom = form.querySelector('input[name="prenom"]');
        const email = form.querySelector('input[name="email"]');
        const password = form.querySelector('input[name="motDePasse"]');
        const telephone = form.querySelector('input[name="telephone"]');
        const statut = form.querySelector('select[name="statutCompte"]');
        const role = form.querySelector('select[name="idRole"]');

        [nom, prenom, email, password, telephone, statut, role].forEach((input) => {
            if (input) clearError(input);
        });

        if (!nom.value.trim()) {
            showError(nom, "Le nom est obligatoire.");
            isValid = false;
        }

        if (!prenom.value.trim()) {
            showError(prenom, "Le prénom est obligatoire.");
            isValid = false;
        }

        if (!email.value.trim()) {
            showError(email, "L’email est obligatoire.");
            isValid = false;
        } else if (!validateEmail(email.value.trim())) {
            showError(email, "Format d’email invalide.");
            isValid = false;
        }

        if (!password.value.trim()) {
            showError(password, "Le mot de passe est obligatoire.");
            isValid = false;
        } else if (!validatePassword(password.value.trim())) {
            showError(password, "Le mot de passe doit contenir au moins 6 caractères.");
            isValid = false;
        }

        if (telephone && telephone.value.trim() && !validatePhone(telephone.value.trim())) {
            showError(telephone, "Numéro de téléphone invalide.");
            isValid = false;
        }

        if (!statut.value.trim()) {
            showError(statut, "Le statut est obligatoire.");
            isValid = false;
        }

        if (!role.value.trim() || role.value === "0") {
            showError(role, "Le rôle est obligatoire.");
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
}