/*
* GSAP scroll reveals: .fade-up / .fade-left / .fade-right, .group-* stagger, [data-gsap-cards] (all pages)
*/

import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

window.gsap = gsap;

const reduceMotionQuery = window.matchMedia("(prefers-reduced-motion: reduce)");
const pendingReveals = [];

function prefersReducedMotion() {
    return reduceMotionQuery.matches;
}

function createReveal(targets, trigger, from, options) {
    const opts = options || {};
    const items = Array.isArray(targets) ? targets : [targets];

    if (!items.length || prefersReducedMotion()) return;

    gsap.set(items, Object.assign({ autoAlpha: 0 }, from));

    let played = false;

    const entry = { trigger: trigger, play: null };

    entry.play = function () {
        if (played) return;

        played = true;

        const index = pendingReveals.indexOf(entry);

        if (index > -1) {
            pendingReveals.splice(index, 1);
        }

        gsap.to(items, {
            autoAlpha: 1,
            x: 0,
            y: 0,
            duration: opts.duration || 1,
            stagger: opts.stagger || 0,
            ease: "power2.out",
            overwrite: "auto",
            clearProps: "opacity,visibility,transform"
        });
    };

    if (opts.playNow) {
        entry.play();
        return;
    }

    pendingReveals.push(entry);

    ScrollTrigger.create({
        trigger: trigger,
        start: opts.start || "top bottom",
        once: true,
        onEnter: entry.play
    });
}

function revealVisiblePending() {
    if (!pendingReveals.length) return;

    const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    pendingReveals.slice().forEach(function (entry) {
        if (!entry.trigger || !entry.trigger.isConnected || entry.trigger.getBoundingClientRect().top < viewportHeight) {
            entry.play();
        }
    });
}

window.revealVisiblePending = revealVisiblePending;

window.initFadeUpAnimations = function (scope = document, animateNow = false) {
    const animations = [
        { name: "fade-up", group: "group-fade-up", from: { y: 40 } },
        { name: "fade-left", group: "group-fade-left", from: { x: -50 } },
        { name: "fade-right", group: "group-fade-right", from: { x: 50 } }
    ];

    animations.forEach(({ name, group: groupName, from }) => {
        const initializedClass = `${name}-initialized`;
        const itemSelector = `.${name}:not(.${initializedClass})`;
        const groups = Array.from(scope.querySelectorAll(`.${groupName}`));

        if (scope.classList && scope.classList.contains(groupName)) {
            groups.unshift(scope);
        }

        groups.forEach((group) => {
            const items = Array.from(group.querySelectorAll(itemSelector))
                .filter((item) => item.closest(`.${groupName}`) === group);

            if (!items.length) return;

            items.forEach((item) => item.classList.add(initializedClass));

            createReveal(items, group, from, { stagger: 0.2, start: "top bottom-=80", playNow: animateNow });
        });

        const singles = Array.from(scope.querySelectorAll(itemSelector));

        if (scope.matches && scope.matches(itemSelector)) {
            singles.unshift(scope);
        }

        singles
            .filter((item) => !item.closest(`.${groupName}`))
            .forEach((item) => {
                item.classList.add(initializedClass);

                createReveal([item], item, from, { start: "top bottom-=80", playNow: animateNow });
            });
    });

    if (!animateNow && !prefersReducedMotion()) {
        ScrollTrigger.refresh();
    }
};

window.initCardReveal = function (scope = document, animateNow = false) {
    const grids = Array.from(scope.querySelectorAll("[data-gsap-cards]"));

    if (scope.matches && scope.matches("[data-gsap-cards]")) {
        grids.unshift(scope);
    }

    grids.forEach(function (grid) {
        const cards = Array.from(grid.children).filter((card) => !card.classList.contains("cards-initialized"));

        if (!cards.length) return;

        cards.forEach((card) => card.classList.add("cards-initialized"));
        grid.classList.add("cards-initialized");

        createReveal(cards, grid, { y: 40 }, {
            duration: 0.6,
            stagger: 0.08,
            start: "top bottom-=80",
            playNow: animateNow
        });
    });
};

function initializeScrollAnimations() {
    document.querySelectorAll("[data-animate-now]").forEach((scope) => {
        window.initFadeUpAnimations(scope, true);
    });

    window.initFadeUpAnimations(document);
    window.initCardReveal(document);

    ScrollTrigger.refresh();

    window.__animationsReady = true;

    revealVisiblePending();
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeScrollAnimations);
} else {
    initializeScrollAnimations();
}

window.addEventListener("load", () => {
    ScrollTrigger.refresh();
    revealVisiblePending();
});

if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(() => {
        ScrollTrigger.refresh();
        revealVisiblePending();
    });
}

function recoverAfterRestore() {
    if (document.hidden) return;

    ScrollTrigger.refresh();

    requestAnimationFrame(function () {
        requestAnimationFrame(function () {
            ScrollTrigger.refresh();
            revealVisiblePending();
        });
    });
}

document.addEventListener("visibilitychange", recoverAfterRestore);
window.addEventListener("pageshow", recoverAfterRestore);
window.addEventListener("orientationchange", recoverAfterRestore);
