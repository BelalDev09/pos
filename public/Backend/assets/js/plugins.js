document.addEventListener("DOMContentLoaded", function () {

    if (document.querySelector("[toast-list]")) {
        let toastScript = document.createElement("script");
        toastScript.src = "https://cdn.jsdelivr.net/npm/toastify-js";
        document.body.appendChild(toastScript);
    }

    if (document.querySelector("[data-choices]")) {
        let choicesScript = document.createElement("script");
        choicesScript.src = "/assets/libs/choices.js/choices.min.js";
        document.body.appendChild(choicesScript);
    }

    if (document.querySelector("[data-provider]")) {
        let flatpickrScript = document.createElement("script");
        flatpickrScript.src = "/assets/libs/flatpickr/flatpickr.min.js";
        document.body.appendChild(flatpickrScript);
    }

});
