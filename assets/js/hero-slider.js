/*
* Hero slider (Hero-Slider, Page-Hero): crossfade with zoom, staggered content, counter roll, autoplay, arrows, swipe, keyboard, MP4 backgrounds
*/

(function () {
    "use strict";

    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

    const pad = function (n) {
        return String(n).padStart(2, "0");
    };

    const init = function (root) {
        const gsap = window.gsap;
        const slides = Array.from(root.querySelectorAll("[data-hero-slide]"));
        const dots = Array.from(root.querySelectorAll("[data-hero-dot]"));
        const current = root.querySelector("[data-hero-current]");
        const live = root.querySelector("[data-hero-slides]");
        const interval = Math.max(3, parseInt(root.dataset.interval, 10) || 7);
        const autoplay = root.dataset.autoplay === "true" && slides.length > 1 && !reduceMotion.matches;

        let index = 0;
        let timer = null;
        let transition = null;
        let hovered = false;
        let focused = false;
        let offscreen = false;

        const animated = function () {
            return typeof gsap !== "undefined" && !reduceMotion.matches;
        };

        const playVideo = function (slide) {
            const video = slide.querySelector("[data-hero-video]");

            if (!video || reduceMotion.matches) return;

            video.preload = "auto";

            const promise = video.play();

            if (promise && promise.catch) promise.catch(function () {});
        };

        const pauseVideo = function (slide) {
            const video = slide.querySelector("[data-hero-video]");

            if (video) video.pause();
        };

        root.querySelectorAll("[data-hero-video]").forEach(function (video) {
            video.addEventListener("playing", function () {
                video.classList.add("is-playing");
            });
        });

        if (!slides.length) return;

        root.classList.add("is-ready");
        playVideo(slides[0]);

        if (slides.length < 2) return;

        const isPaused = function () {
            return hovered || focused || offscreen || document.hidden;
        };

        const stopTimer = function () {
            if (timer) timer.kill();

            timer = null;
        };

        const syncPause = function () {
            live.setAttribute("aria-live", autoplay && !isPaused() ? "off" : "polite");

            if (!timer) return;

            if (isPaused()) timer.pause();
            else timer.resume();
        };

        const startTimer = function () {
            stopTimer();

            if (!autoplay || typeof gsap === "undefined") return;

            timer = gsap.delayedCall(interval, function () {
                go(index + 1, 1);
            });

            syncPause();
        };

        const setDots = function (to) {
            dots.forEach(function (dot, i) {
                const active = i === to;

                dot.classList.toggle("is-active", active);

                if (active) dot.setAttribute("aria-current", "true");
                else dot.removeAttribute("aria-current");
            });
        };

        const setCounter = function (to, dir) {
            if (!current) return;

            while (current.children.length > 1) current.firstElementChild.remove();

            const prev = current.firstElementChild;
            const next = document.createElement("span");

            next.textContent = pad(to + 1);
            current.appendChild(next);

            if (!animated() || !prev) {
                if (prev) prev.remove();
                return;
            }

            gsap.fromTo(next, { yPercent: 100 * dir }, { yPercent: 0, duration: 0.6, ease: "power3.out" });
            gsap.to(prev, {
                yPercent: -100 * dir,
                duration: 0.6,
                ease: "power3.out",
                onComplete: function () {
                    prev.remove();
                }
            });
        };

        const go = function (to, dir) {
            const count = slides.length;

            to = (to + count) % count;

            if (to === index) return;

            if (transition) transition.progress(1).kill();

            dir = dir || (to > index ? 1 : -1);

            const from = slides[index];
            const next = slides[to];

            index = to;

            from.classList.remove("is-active");
            from.setAttribute("aria-hidden", "true");
            from.inert = true;

            next.classList.add("is-active");
            next.removeAttribute("aria-hidden");
            next.inert = false;

            setDots(to);
            setCounter(to, dir);
            playVideo(next);
            startTimer();

            if (!animated()) {
                pauseVideo(from);
                return;
            }

            const media = next.querySelector(".hero-media");
            const items = next.querySelectorAll("[data-hero-item]");
            const fromItems = from.querySelectorAll("[data-hero-item]");

            transition = gsap.timeline({
                onComplete: function () {
                    gsap.set(from, { clearProps: "visibility,opacity,zIndex" });
                    gsap.set(next, { clearProps: "zIndex" });
                    gsap.set(fromItems, { clearProps: "opacity,visibility,transform" });
                    pauseVideo(from);
                    transition = null;
                }
            });

            transition
                .set(from, { visibility: "visible", opacity: 1, zIndex: 1 }, 0)
                .set(next, { zIndex: 2 }, 0)
                .to(fromItems, { autoAlpha: 0, y: -16, duration: 0.35, ease: "power2.in" }, 0);

            if (media) {
                transition
                    .fromTo(media, { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.9, ease: "power2.inOut" }, 0.1)
                    .fromTo(media, { scale: 1.08 }, { scale: 1, duration: 1.8, ease: "power2.out", clearProps: "transform" }, 0.1);
            }

            if (items.length) {
                transition.fromTo(items, { autoAlpha: 0, y: 32 }, {
                    autoAlpha: 1,
                    y: 0,
                    duration: 0.8,
                    stagger: 0.1,
                    ease: "power3.out",
                    clearProps: "transform"
                }, 0.45);
            }
        };

        const nudge = function (button) {
            button.classList.remove("is-nudge");
            void button.offsetWidth;
            button.classList.add("is-nudge");
        };

        root.querySelectorAll("[data-hero-prev], [data-hero-next]").forEach(function (button) {
            const step = button.hasAttribute("data-hero-next") ? 1 : -1;

            button.addEventListener("click", function () {
                nudge(button);
                go(index + step, step);
            });

            button.addEventListener("animationend", function () {
                button.classList.remove("is-nudge");
            });
        });

        dots.forEach(function (dot, i) {
            dot.addEventListener("click", function () {
                go(i);
            });
        });

        root.addEventListener("keydown", function (event) {
            if (event.key === "ArrowRight") go(index + 1, 1);
            if (event.key === "ArrowLeft") go(index - 1, -1);
        });

        let startX = null;
        let startY = 0;

        root.addEventListener("pointerdown", function (event) {
            if (event.pointerType === "mouse") return;

            startX = event.clientX;
            startY = event.clientY;
        }, { passive: true });

        root.addEventListener("pointerup", function (event) {
            if (startX === null) return;

            const dx = event.clientX - startX;
            const dy = event.clientY - startY;

            startX = null;

            if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.2) {
                go(index + (dx < 0 ? 1 : -1), dx < 0 ? 1 : -1);
            }
        });

        root.addEventListener("pointercancel", function () {
            startX = null;
        });

        const controls = root.querySelector(".hero-controls");

        if (controls) {
            controls.addEventListener("pointerenter", function (event) {
                if (event.pointerType !== "mouse") return;

                hovered = true;
                syncPause();
            });

            controls.addEventListener("pointerleave", function (event) {
                if (event.pointerType !== "mouse") return;

                hovered = false;
                syncPause();
            });
        }

        root.addEventListener("focusin", function (event) {
            focused = event.target.matches(":focus-visible");
            syncPause();
        });

        root.addEventListener("focusout", function (event) {
            focused = root.contains(event.relatedTarget);
            syncPause();
        });

        document.addEventListener("visibilitychange", syncPause);

        if ("IntersectionObserver" in window) {
            new IntersectionObserver(function (entries) {
                offscreen = !entries[0].isIntersecting;

                if (offscreen) pauseVideo(slides[index]);
                else playVideo(slides[index]);

                syncPause();
            }).observe(root);
        }

        startTimer();
    };

    document.querySelectorAll("[data-hero]").forEach(init);
})();
