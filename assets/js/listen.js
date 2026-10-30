/*
* Lists: Sponsoren (layout_sponsoren) „Alle … anzeigen“ shows the hidden names; Aussteller (layout_aussteller) filter buttons show the exhibitors of one category
*/

document.querySelectorAll("[data-sponsoren] [data-alle]").forEach(function (button) {
    button.addEventListener("click", function () {
        const list = button.previousElementSibling;

        list.querySelectorAll("li[hidden]").forEach(function (item) {
            item.hidden = false;
        });

        button.remove();
        list.querySelector("li").setAttribute("tabindex", "-1");
        list.querySelector("li").focus({ preventScroll: true });
    });
});

document.querySelectorAll("[data-aussteller]").forEach(function (section) {
    const buttons = section.querySelectorAll("[data-filter]");
    const items = section.querySelectorAll(".aussteller-karte");

    buttons.forEach(function (button) {
        button.addEventListener("click", function () {
            const filter = button.dataset.filter;

            buttons.forEach(function (other) {
                other.setAttribute("aria-pressed", other === button ? "true" : "false");
            });

            items.forEach(function (item) {
                item.hidden = filter !== "" && !(" " + item.dataset.kategorien + " ").includes(" " + filter + " ");
            });
        });
    });
});
