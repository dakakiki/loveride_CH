/*
* Logo-Wand (layout_logo_wand): shows a random selection of partner logos on each load (count from data-logo-wand); the „Dein Logo hier?“ tile stays last
*/

(function () {
    "use strict";

    document.querySelectorAll("[data-logo-wand]").forEach(function (grid) {
        const items = Array.from(grid.querySelectorAll("[data-logo]"));
        const cta = grid.querySelector("[data-logo-cta]");
        const max = Number(grid.dataset.logoWand) || 5;

        for (let i = items.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            const item = items[i];

            items[i] = items[j];
            items[j] = item;
        }

        items.forEach(function (item, index) {
            item.hidden = index >= max;
            grid.insertBefore(item, cta);
        });
    });
})();
