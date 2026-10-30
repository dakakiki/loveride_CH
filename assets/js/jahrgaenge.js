/*
* Jahrgänge (layout_jahrgaenge): year buttons (tabs, arrow keys) and the „Ältere Jahre“ select switch the year panel and the year in the head; aftermovie plays inline (YouTube without cookies, loaded on click); gallery „Mehr Bilder laden“ and lightbox; helper list „Alle … anzeigen“
*/

import { createLightbox, youtubeEmbed } from "./modules/lightbox.js";

document.querySelectorAll("[data-jahrgaenge]").forEach(function (section) {
    const tabs = Array.from(section.querySelectorAll("[data-jahr]"));
    const select = section.querySelector("[data-jahr-select]");
    const panels = Array.from(section.querySelectorAll("[data-jahr-panel]"));
    const texts = section.querySelectorAll("[data-jahr-text]");
    const lightbox = createLightbox(section.querySelector("[data-lightbox]"));

    const stopVideos = function () {
        section.querySelectorAll(".jahrgang-video.is-playing").forEach(function (video) {
            video.querySelector("iframe").remove();
            video.classList.remove("is-playing");
        });
    };

    const show = function (jahr, fromSelect) {
        stopVideos();

        panels.forEach(function (panel) {
            panel.hidden = panel.dataset.jahrPanel !== jahr;
        });

        tabs.forEach(function (tab) {
            const active = tab.dataset.jahr === jahr;

            tab.setAttribute("aria-selected", active ? "true" : "false");
            tab.tabIndex = active || (fromSelect && tab === tabs[0]) ? 0 : -1;
        });

        if (select) {
            select.classList.toggle("is-active", fromSelect);

            if (!fromSelect) select.value = "";
        }

        texts.forEach(function (text) {
            text.textContent = jahr;
        });
    };

    tabs.forEach(function (tab, index) {
        tab.addEventListener("click", function () {
            show(tab.dataset.jahr, false);
        });

        tab.addEventListener("keydown", function (event) {
            const step = { ArrowRight: 1, ArrowLeft: -1 }[event.key];

            if (!step) return;

            const next = tabs[(index + step + tabs.length) % tabs.length];

            event.preventDefault();
            next.focus();
            show(next.dataset.jahr, false);
        });
    });

    if (select) {
        select.addEventListener("change", function () {
            if (select.value) show(select.value, true);
        });
    }

    section.querySelectorAll(".jahrgang-video__play[data-youtube]").forEach(function (play) {
        play.addEventListener("click", function () {
            const video = play.closest(".jahrgang-video");
            const frame = document.createElement("iframe");

            frame.className = "jahrgang-video__frame";
            frame.src = youtubeEmbed(play.dataset.youtube);
            frame.title = play.dataset.title || "Aftermovie";
            frame.allow = "autoplay; encrypted-media; picture-in-picture; fullscreen";
            frame.allowFullscreen = true;

            video.appendChild(frame);
            video.classList.add("is-playing");
            frame.focus();
        });
    });

    panels.forEach(function (panel) {
        const links = Array.from(panel.querySelectorAll("[data-lightbox-index]"));
        const more = panel.querySelector("[data-mehr]");
        const list = panel.querySelector("[data-schritt]");
        const all = panel.querySelector("[data-alle]");

        if (lightbox) {
            links.forEach(function (link) {
                link.addEventListener("click", function (event) {
                    event.preventDefault();
                    lightbox.images(links, Number(link.dataset.lightboxIndex));
                });
            });
        }

        if (more && list) {
            more.addEventListener("click", function () {
                const hidden = Array.from(list.querySelectorAll("li[hidden]"));
                const next = hidden.slice(0, Number(list.dataset.schritt) || 8);

                next.forEach(function (item) {
                    item.hidden = false;
                });

                if (next[0]) next[0].querySelector("a").focus({ preventScroll: true });
                if (hidden.length <= next.length) more.remove();
            });
        }

        if (all) {
            all.addEventListener("click", function () {
                const names = panel.querySelector(".namensliste");
                const first = names.querySelector("li[hidden]");

                names.querySelectorAll("li[hidden]").forEach(function (item) {
                    item.hidden = false;
                });

                all.remove();

                if (first) {
                    first.setAttribute("tabindex", "-1");
                    first.focus({ preventScroll: true });
                }
            });
        }
    });
});
