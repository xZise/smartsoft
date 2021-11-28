"use strict";

function registerPassword() {

    const checkbox = document.getElementById("SetPassword");
    const password = document.getElementById("NewPassword");
    checkbox.addEventListener("change", function() {
        password.disabled = !this.checked;
    })

    password.disabled = true;

}