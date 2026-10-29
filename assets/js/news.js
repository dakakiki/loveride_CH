/*
* News: „Ältere Beiträge laden“ appends the next page of cards without reload (News-Archiv); „Link kopieren“ copies the post URL (single News)
*/

(function () {
    "use strict";

    document.querySelectorAll("[data-news-archiv]").forEach(function (section) {
        const grid = section.querySelector("[data-news-grid]");
        const status = section.querySelector("[data-news-status]");

        section.addEventListener("click", function (event) {
            const link = event.target.closest("[data-news-more]");

            if (!link || !grid) return;

            event.preventDefault();

            if (link.getAttribute("aria-busy") === "true") return;

            const label = link.textContent;

            link.setAttribute("aria-busy", "true");
            link.textContent = "Wird geladen …";

            fetch(link.href, { credentials: "same-origin" })
                .then(function (response) {
                    if (!response.ok) throw new Error(String(response.status));

                    return response.text();
                })
                .then(function (html) {
                    const next = new DOMParser().parseFromString(html, "text/html").querySelector("[data-news-archiv]");
                    const items = next ? Array.from(next.querySelectorAll("[data-news-grid] > li")) : [];
                    const more = next ? next.querySelector("[data-news-more]") : null;
                    const start = grid.children.length;

                    items.forEach(function (item) {
                        grid.appendChild(document.importNode(item, true));
                    });

                    if (more) {
                        link.href = more.href;
                        link.textContent = label;
                        link.removeAttribute("aria-busy");
                    } else {
                        link.parentElement.remove();
                    }

                    status.textContent = items.length === 1 ? "1 weiterer Beitrag geladen" : items.length + " weitere Beiträge geladen";

                    const first = grid.children[start] ? grid.children[start].querySelector("a") : null;

                    if (first) first.focus({ preventScroll: true });
                })
                .catch(function () {
                    window.location.href = link.href;
                });
        });
    });

    document.querySelectorAll("[data-share-copy]").forEach(function (button) {
        const label = button.textContent;
        const status = button.parentElement.querySelector("[data-share-status]");

        const done = function () {
            button.textContent = "Link kopiert";

            if (status) status.textContent = "Link kopiert";

            window.setTimeout(function () {
                button.textContent = label;
            }, 2000);
        };

        const fallback = function () {
            const field = document.createElement("textarea");

            field.value = button.dataset.shareCopy;
            field.setAttribute("readonly", "");
            field.style.position = "fixed";
            field.style.opacity = "0";
            document.body.appendChild(field);
            field.select();

            try {
                document.execCommand("copy");
                done();
            } finally {
                field.remove();
                button.focus();
            }
        };

        button.addEventListener("click", function () {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(button.dataset.shareCopy).then(done, fallback);
            } else {
                fallback();
            }
        });
    });
})();
