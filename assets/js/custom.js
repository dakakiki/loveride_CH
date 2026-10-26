/*
* Global JS: header scroll state (all pages)
*/

(function () {
    "use strict";

    const header = document.querySelector(".header");

    if (!header) return;

    const onScroll = function () {
        header.classList.toggle("is-scrolled", window.scrollY > 10);
    };

    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
})();
