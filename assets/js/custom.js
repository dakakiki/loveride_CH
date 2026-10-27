/*
* Global JS: header scroll state, mega menu (hover/click, GSAP slide), mobile menu (GSAP fade) with accordion (GSAP fade + height), all pages
*/

(function () {
    "use strict";

    const header = document.querySelector("[data-header]");

    if (!header) return;

    const mobileQuery = window.matchMedia("(max-width: 1279px)");

    const onScroll = function () {
        header.classList.toggle("is-scrolled", window.scrollY > 10);
    };

    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });

    /* Mega menu */

    const megaToggles = Array.from(header.querySelectorAll("[data-mega-toggle]"));
    const hoverQuery = window.matchMedia("(hover: hover) and (pointer: fine)");
    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
    const CLIP_OPEN = "inset(0% 0% -25% 0%)";
    const CLIP_CLOSED = "inset(0% 0% 100% 0%)";

    let activeToggle = null;
    let openTimer = null;
    let closeTimer = null;
    let skipFocusOpen = false;

    const panelOf = function (toggle) {
        return toggle ? document.getElementById(toggle.getAttribute("aria-controls")) : null;
    };

    const innerOf = function (panel) {
        return panel ? panel.querySelector(".mega__inner") : null;
    };

    const canAnimate = function () {
        return typeof window.gsap !== "undefined" && !reduceMotion.matches;
    };

    const clearTimers = function () {
        clearTimeout(openTimer);
        clearTimeout(closeTimer);
    };

    const showPanel = function (panel, slide) {
        const inner = innerOf(panel);

        panel.hidden = false;

        if (!canAnimate()) return;

        gsap.killTweensOf([panel, inner]);

        if (slide) {
            gsap.fromTo(panel, { clipPath: CLIP_CLOSED }, { clipPath: CLIP_OPEN, duration: 0.4, ease: "power3.out" });
            gsap.fromTo(inner, { y: -16, opacity: 0 }, { y: 0, opacity: 1, duration: 0.35, delay: 0.08, ease: "power2.out" });
        } else {
            gsap.set(panel, { clipPath: CLIP_OPEN });
            gsap.fromTo(inner, { opacity: 0 }, { opacity: 1, duration: 0.2, ease: "power1.out" });
        }
    };

    const hidePanel = function (panel, slide) {
        const inner = innerOf(panel);

        if (!canAnimate() || !slide) {
            if (canAnimate()) {
                gsap.killTweensOf([panel, inner]);
                gsap.set([panel, inner], { clearProps: "all" });
            }

            panel.hidden = true;
            return;
        }

        gsap.killTweensOf([panel, inner]);
        gsap.to(inner, { y: -8, opacity: 0, duration: 0.15, ease: "power1.in" });
        gsap.to(panel, {
            clipPath: CLIP_CLOSED,
            duration: 0.3,
            ease: "power2.in",
            onComplete: function () {
                panel.hidden = true;
                gsap.set([panel, inner], { clearProps: "all" });
            }
        });
    };

    const openMega = function (toggle) {
        clearTimers();

        if (activeToggle === toggle) return;

        const switching = activeToggle !== null;

        if (switching) {
            activeToggle.setAttribute("aria-expanded", "false");
            hidePanel(panelOf(activeToggle), false);
        }

        activeToggle = toggle;
        toggle.setAttribute("aria-expanded", "true");
        header.classList.add("is-mega-open");

        showPanel(panelOf(toggle), !switching);
    };

    const closeMega = function (focusToggle) {
        clearTimers();

        if (!activeToggle) return;

        const toggle = activeToggle;

        activeToggle = null;
        toggle.setAttribute("aria-expanded", "false");
        header.classList.remove("is-mega-open");

        hidePanel(panelOf(toggle), true);

        if (focusToggle) {
            skipFocusOpen = true;
            toggle.focus();
            skipFocusOpen = false;
        }
    };

    const scheduleClose = function () {
        clearTimeout(openTimer);
        clearTimeout(closeTimer);
        closeTimer = setTimeout(function () {
            closeMega(false);
        }, 180);
    };

    megaToggles.forEach(function (toggle) {
        const item = toggle.closest(".nav-main__item");

        toggle.addEventListener("click", function (event) {
            if (hoverQuery.matches || activeToggle === toggle) return;

            event.preventDefault();
            openMega(toggle);
        });

        toggle.addEventListener("focus", function () {
            if (skipFocusOpen || !toggle.matches(":focus-visible")) return;

            openMega(toggle);
        });

        item.addEventListener("mouseenter", function () {
            if (!hoverQuery.matches || mobileQuery.matches) return;

            clearTimeout(closeTimer);
            clearTimeout(openTimer);
            openTimer = setTimeout(function () {
                openMega(toggle);
            }, activeToggle ? 0 : 80);
        });

        item.addEventListener("mouseleave", function () {
            if (!hoverQuery.matches) return;

            scheduleClose();
        });
    });

    document.addEventListener("click", function (event) {
        if (activeToggle && !event.target.closest(".nav-main")) {
            closeMega(false);
        }
    });

    header.addEventListener("focusout", function () {
        setTimeout(function () {
            if (activeToggle && !document.activeElement.closest(".nav-main")) {
                closeMega(false);
            }
        }, 0);
    });

    header.querySelectorAll(".mega a").forEach(function (link) {
        link.addEventListener("click", function () {
            closeMega(false);
        });
    });

    /* Mobile menu */

    const menuToggle = header.querySelector("[data-menu-toggle]");
    const mobileMenu = document.getElementById("mobile-menu");

    const setMenu = function (open) {
        if (!menuToggle || !mobileMenu) return;

        const root = document.documentElement;
        const scrollY = window.scrollY;

        if (open) {
            root.style.setProperty("--lr-header-bottom", header.getBoundingClientRect().bottom + "px");
            root.style.setProperty("--lr-scrollbar", window.innerWidth - root.clientWidth + "px");
        }

        menuToggle.setAttribute("aria-expanded", open ? "true" : "false");
        menuToggle.setAttribute("aria-label", open ? menuToggle.dataset.labelClose : menuToggle.dataset.labelOpen);
        root.classList.toggle("has-mobile-menu", open);

        if (!open && window.scrollY !== scrollY) {
            window.scrollTo({ top: scrollY, behavior: "instant" });
        }

        if (!canAnimate()) {
            mobileMenu.hidden = !open;
            return;
        }

        gsap.killTweensOf(mobileMenu);

        if (open) {
            mobileMenu.hidden = false;
            gsap.fromTo(mobileMenu, { opacity: 0 }, { opacity: 1, duration: 0.3, ease: "power1.out" });
            return;
        }

        if (mobileMenu.hidden) return;

        gsap.to(mobileMenu, {
            opacity: 0,
            duration: 0.25,
            ease: "power1.in",
            onComplete: function () {
                mobileMenu.hidden = true;
                gsap.set(mobileMenu, { clearProps: "opacity" });
            }
        });
    };

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener("click", function () {
            setMenu(menuToggle.getAttribute("aria-expanded") !== "true");
        });

        mobileMenu.querySelectorAll("a").forEach(function (link) {
            link.addEventListener("click", function () {
                setMenu(false);
            });
        });

        const setAccordion = function (sub, open) {
            if (!canAnimate()) {
                sub.hidden = !open;
                return;
            }

            gsap.killTweensOf(sub);

            if (open) {
                sub.hidden = false;
                gsap.set(sub, { clearProps: "height,paddingBottom,opacity,overflow" });

                const paddingBottom = getComputedStyle(sub).paddingBottom;

                gsap.fromTo(sub,
                    { height: 0, paddingBottom: 0, opacity: 0, overflow: "hidden" },
                    {
                        height: "auto",
                        paddingBottom: paddingBottom,
                        opacity: 1,
                        duration: 0.3,
                        ease: "power2.out",
                        onComplete: function () {
                            gsap.set(sub, { clearProps: "height,paddingBottom,opacity,overflow" });
                        }
                    }
                );
                return;
            }

            if (sub.hidden) return;

            gsap.to(sub, {
                height: 0,
                paddingBottom: 0,
                opacity: 0,
                overflow: "hidden",
                duration: 0.25,
                ease: "power2.in",
                onComplete: function () {
                    sub.hidden = true;
                    gsap.set(sub, { clearProps: "height,paddingBottom,opacity,overflow" });
                }
            });
        };

        mobileMenu.querySelectorAll("[data-accordion-toggle]").forEach(function (toggle) {
            toggle.addEventListener("click", function () {
                const sub = document.getElementById(toggle.getAttribute("aria-controls"));
                const open = toggle.getAttribute("aria-expanded") !== "true";

                toggle.setAttribute("aria-expanded", open ? "true" : "false");

                if (sub) setAccordion(sub, open);
            });
        });
    }

    document.addEventListener("keydown", function (event) {
        if (event.key !== "Escape") return;

        if (header.classList.contains("is-mega-open")) {
            closeMega(true);
        }

        if (menuToggle && menuToggle.getAttribute("aria-expanded") === "true") {
            setMenu(false);
            menuToggle.focus();
        }
    });

    mobileQuery.addEventListener("change", function () {
        closeMega(false);
        setMenu(false);
    });
})();
