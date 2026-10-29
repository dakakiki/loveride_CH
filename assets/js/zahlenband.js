/*
* Zahlenband (layout_zahlenband): numbers count up once when they enter the viewport (GSAP); years and texts without numbers stay as they are, reduced motion shows the final value
*/

(function () {
    "use strict";

    const items = Array.from(document.querySelectorAll(".layout-zahlenband [data-count]"));

    if (!items.length || typeof window.gsap === "undefined" || !("IntersectionObserver" in window)) return;

    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    const parse = function (text) {
        const match = text.match(/(\d{1,3}(?:'\d{3})+|\d+)(?:,(\d+))?/);

        if (!match) return null;

        const whole = match[1];
        const decimals = match[2] || "";

        if (!decimals && /^(19|20)\d{2}$/.test(whole)) return null;

        return {
            prefix: text.slice(0, match.index),
            suffix: text.slice(match.index + match[0].length),
            value: parseFloat(whole.replace(/'/g, "") + (decimals ? "." + decimals : "")),
            decimals: decimals.length,
            grouped: whole.indexOf("'") > -1
        };
    };

    const format = function (number, data) {
        const parts = number.toFixed(data.decimals).split(".");
        const whole = data.grouped ? parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, "'") : parts[0];

        return data.prefix + whole + (parts[1] ? "," + parts[1] : "") + data.suffix;
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;

            observer.unobserve(entry.target);

            const el = entry.target;
            const data = el.__count;
            const counter = { value: 0 };

            gsap.to(counter, {
                value: data.value,
                duration: 1.8,
                delay: 0.2,
                ease: "power2.out",
                onUpdate: function () {
                    el.textContent = format(counter.value, data);
                },
                onComplete: function () {
                    el.textContent = data.text;
                }
            });
        });
    }, { threshold: 0.6 });

    items.forEach(function (el) {
        const text = el.textContent.trim();
        const data = parse(text);

        if (!data) return;

        data.text = text;
        el.__count = data;
        el.textContent = format(0, data);
        observer.observe(el);
    });
})();
