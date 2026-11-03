document.querySelectorAll("[data-faq] .faq-item__button").forEach(function (button) {
    button.addEventListener("click", function () {
        const open = button.getAttribute("aria-expanded") !== "true";

        button.setAttribute("aria-expanded", open ? "true" : "false");
        button.closest(".faq-item").classList.toggle("is-open", open);
    });
});
