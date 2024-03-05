document.getElementById("firstname").addEventListener("focusout", validateFirstName);
document.getElementById("firstname").addEventListener("input", countNameLen);

document.getElementById("lastname").addEventListener("focusout", validateSurName);
document.getElementById("lastname").addEventListener("input", countNameLen);

document.getElementById("email").addEventListener("focusout", validateEmail);
document.getElementById("email").addEventListener("input", countEmailLen);

document.getElementById("login").addEventListener("focusout", validateLogin);
document.getElementById("login").addEventListener("input", countNameLen);

document.getElementById("password").addEventListener("focusout", validatePassword);

document.getElementById("submit-btn").addEventListener("click", checkValues);


function displayError(input, msg, errInput) {
    errInput.style.display = 'block';
    errInput.innerHTML = msg;
    input.style.border = '3px solid red';
}

function hideError(input, errInput) {
    errInput.style.display = 'none';
    errInput.innerHTML = "";
    input.style.border = '';
}

function countLenght(spanCounter, input, countTo) {

    spanCounter.style.display = "flex";

    if (input.value.length > countTo) {
        input.value = input.value.substring(0, countTo);
    }

    spanCounter.textContent = input.value.length + " / " + countTo;
}

function hideCharCounter(spanCounter) {
    spanCounter.style.display = "none";
}

function validateInput(inputId, errMessage, errInputId) {
    let input = document.getElementById(inputId);
    let errInput = document.getElementById(errInputId);

    if (input.value.trim() === "" || input.value == "null") {
        displayError(input, errMessage, errInput);
        return 1;
    } else {
        hideError(input, errInput);
        return 0;
    }
}

function validateFirstName() {
    let input = document.getElementById("firstname");
    let errInput = document.getElementById("err-firstname");

    hideCharCounter(input.nextElementSibling);
    let output = validateInput("firstname", "Vyplňte Vaše meno.", "err-firstname");

    if (output == 0) {
        if(!checkChars(input.value)) {
            displayError(input, "Meno môže obsahovať iba veľké, malé písmená, číslice a podtržník.", errInput);
            return 1;
        } 
    }

    return output;
}

function validateSurName() {
    let input = document.getElementById("lastname");
    let errInput = document.getElementById("err-surname");

    hideCharCounter(input.nextElementSibling);
    let output = validateInput("lastname", "Vyplňte Vaše priezvisko.", "err-surname");

    if (output == 0) {
        if(!checkChars(input.value)) {
            displayError(input, "Priezvisko môže obsahovať iba veľké, malé písmená, číslice a podtržník.", errInput);
            return 1;
        } 
    }

    return output;
}

function validateEmail() {
    let input = document.getElementById("email");
    hideCharCounter(input.nextElementSibling);

    let output = validateInput("email", "Zadajte Vašu emailovú adresu.", "err-mail");
    let errInput = document.getElementById("err-mail");

    if (output == 0) {
        let email = input.value;

        let emailRegex = /^[^\s@]{3,}@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            displayError(input, "Zadajte správny formát emailovej adresy.", errInput);
            return 1;
        }
    }

    return output;

}

function validateLogin() {
    let input = document.getElementById("login");
    let errInput = document.getElementById("err-login");

    hideCharCounter(input.nextElementSibling);
    let output = validateInput("login", "Zadajte správny login.", "err-login");

    if (output == 0) {
        if (input.value.length < 5 || input.value.length > 32) {
            displayError(input, "Login musi mat min. 5 a max. 32 znakov.", errInput);
            return 1;
        } 
        if (!checkChars(input.value)) {
            displayError(input, "Login môže obsahovať iba veľké, malé písmená, číslice a podtržník.", errInput);
            return 1;
        }
    }


    return output;
}

function validatePassword() {
    let input = document.getElementById("password");
    let errInput = document.getElementById("err-password");

    let output = validateInput("password", "Zadajte správne heslo.", "err-password");

    if (output == 0) {
        if (input.value.length < 5 || input.value.length > 255) {
            displayError(input, "Heslo musí mať min. 5 a max. 255 znakov.", errInput);
            return 1;
        } 
    }

    return output;
}

function checkChars(stringToCheck) {
    let regex = /^[a-zA-Z0-9_]+$/;
    // Test, ci obsahuje iba regex znaky - ak nie - vrat false
    if (!regex.test(stringToCheck.trim())) {
        return false;
    }
    return true;
}

function countNameLen() {
    countLenght(this.nextElementSibling, this, 32);
}

function countEmailLen() {
    countLenght(this.nextElementSibling, this, 64);
}



function checkValues() {
    if (validateFirstName() == 1) {
        return false;
    }
    if (validateSurName() == 1) {
        return false;
    }
    if (validateEmail() == 1) {
        return false;
    }
    if (validateLogin() == 1) {
        return false;
    }
    if (validatePassword() == 1) {
        return false;
    }
}

let modal = document.getElementById("modal");

window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
}