/*
* Spenden (layout_spenden): "IBAN kopieren" copies the IBAN to the clipboard and confirms it on the button for a moment
*/

document.querySelectorAll("[data-spenden] [data-copy]").forEach(function (button) {
    const label = button.querySelector("span");
    const text = label.textContent;
    let timer;

    button.addEventListener("click", function () {
        const done = function () {
            label.textContent = button.dataset.copied;
            clearTimeout(timer);
            timer = setTimeout(function () {
                label.textContent = text;
            }, 2500);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(button.dataset.copy).then(done);
            return;
        }

        const field = document.createElement("textarea");

        field.value = button.dataset.copy;
        field.setAttribute("readonly", "");
        field.style.position = "fixed";
        field.style.opacity = "0";
        document.body.append(field);
        field.select();
        document.execCommand("copy");
        field.remove();
        done();
    });
});
