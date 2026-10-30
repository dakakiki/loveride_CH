/*
* Galerie-Slider (layout_galerie): photos and the aftermovie (YouTube without cookies, loaded only on click) open in the shared lightbox (modules/lightbox.js); photo slider = native scroll-snap with dots per scroll position and prev / next arrows
*/

import { createLightbox } from "./modules/lightbox.js";

(function () {
    "use strict";

    document.querySelectorAll(".layout-galerie").forEach(function (section) {
        const lightbox = createLightbox(section.querySelector("[data-lightbox]"));
        const links = Array.from(section.querySelectorAll("[data-lightbox-index]"));
        const play = section.querySelector(".galerie__play[data-youtube]");

        if (!lightbox) return;

        links.forEach(function (link) {
            link.addEventListener("click", function (event) {
                event.preventDefault();
                lightbox.images(links, Number(link.dataset.lightboxIndex));
            });
        });

        if (play) {
            play.addEventListener("click", function () {
                lightbox.video(play.dataset.youtube, play.dataset.title);
            });
        }
    });

    document.querySelectorAll("[data-galerie]").forEach(function (slider) {
        const track = slider.querySelector(".galerie__track");
        const items = Array.from(track.children);
        const dotsBox = slider.querySelector("[data-galerie-dots]");
        const controls = slider.querySelector(".galerie__controls");
        const prev = slider.querySelector("[data-galerie-prev]");
        const next = slider.querySelector("[data-galerie-next]");

        if (!controls || items.length < 2) return;

        let positions = [];
        let starts = [];
        let dots = [];
        let active = 0;
        let target = null;

        const measure = function () {
            const max = track.scrollWidth - track.clientWidth;
            const origin = items[0].offsetLeft;

            positions = [];
            starts = [];

            items.forEach(function (item, index) {
                const left = Math.min(item.offsetLeft - origin, max);

                if (!positions.length || left - positions[positions.length - 1] > 2) {
                    positions.push(left);
                    starts.push(index);
                }
            });
        };

        const render = function () {
            dotsBox.innerHTML = "";
            dots = positions.map(function (left, index) {
                const dot = document.createElement("button");

                dot.type = "button";
                dot.className = "galerie__dot";
                dot.setAttribute("aria-label", "Zu Bild " + (starts[index] + 1) + " von " + items.length);
                dot.addEventListener("click", function () {
                    go(index);
                });
                dotsBox.appendChild(dot);

                return dot;
            });

            controls.hidden = positions.length < 2;
        };

        const mark = function () {
            const left = track.scrollLeft;
            const max = track.scrollWidth - track.clientWidth;

            dots.forEach(function (dot, index) {
                dot.classList.toggle("is-active", index === active);

                if (index === active) {
                    dot.setAttribute("aria-current", "true");
                } else {
                    dot.removeAttribute("aria-current");
                }
            });

            prev.disabled = (target === null ? left : positions[target]) <= 1;
            next.disabled = (target === null ? left : positions[target]) >= max - 1;
        };

        const update = function () {
            const left = track.scrollLeft;

            if (target !== null && Math.abs(left - positions[target]) > 2) return;

            target = null;
            active = positions.reduce(function (best, position, index) {
                return Math.abs(position - left) < Math.abs(positions[best] - left) ? index : best;
            }, 0);

            mark();
        };

        const go = function (index) {
            target = Math.max(0, Math.min(index, positions.length - 1));
            active = target;
            mark();
            track.scrollTo({ left: positions[target] });
        };

        const setup = function () {
            target = null;
            measure();
            render();
            update();
        };

        prev.addEventListener("click", function () {
            go(active - 1);
        });

        next.addEventListener("click", function () {
            go(active + 1);
        });

        track.addEventListener("scroll", update, { passive: true });
        ["pointerdown", "wheel", "scrollend"].forEach(function (type) {
            track.addEventListener(type, function () {
                target = null;

                if (type === "scrollend") update();
            }, { passive: true });
        });

        window.addEventListener("resize", setup);

        setup();
    });
})();
