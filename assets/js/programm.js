/*
* Programm & Line-up (layout_programm): day tabs (arrow keys; today's tab opens on a programme day, otherwise the event day) and the live bar – the running programme point is marked red and moves with the time
*/

(function () {
    "use strict";

    document.querySelectorAll("[data-programm]").forEach(function (section) {
        const tabs = Array.from(section.querySelectorAll("[role='tab']"));
        const panels = Array.from(section.querySelectorAll("[data-programm-day]"));

        const select = function (index, focus) {
            tabs.forEach(function (tab, i) {
                const active = i === index;

                tab.setAttribute("aria-selected", active ? "true" : "false");
                tab.tabIndex = active ? 0 : -1;
                panels[i].hidden = !active;
            });

            if (focus) tabs[index].focus();
        };

        if (tabs.length) {
            tabs.forEach(function (tab, i) {
                tab.addEventListener("click", function () {
                    select(i);
                });

                tab.addEventListener("keydown", function (event) {
                    const keys = { ArrowRight: i + 1, ArrowLeft: i - 1, Home: 0, End: tabs.length - 1 };

                    if (!(event.key in keys)) return;

                    event.preventDefault();
                    select((keys[event.key] + tabs.length) % tabs.length, true);
                });
            });

            const now = Date.now();
            const today = panels.findIndex(function (panel) {
                return now >= Date.parse(panel.dataset.dayStart) && now < Date.parse(panel.dataset.dayEnd);
            });

            const preset = tabs.findIndex(function (tab) {
                return tab.getAttribute("aria-selected") === "true";
            });

            select(today > -1 ? today : Math.max(preset, 0));
        }

        const update = function () {
            const now = Date.now();

            panels.forEach(function (panel) {
                const items = Array.from(panel.querySelectorAll("[data-start]"));
                const starts = items.map(function (item) {
                    return Date.parse(item.dataset.start);
                });
                const past = starts.filter(function (start) {
                    return start <= now;
                });
                const next = starts.filter(function (start) {
                    return start > now;
                });
                const current = past.length ? Math.max.apply(null, past) : null;
                const until = next.length ? Math.min.apply(null, next) : Date.parse(panel.dataset.end);
                const live = current !== null && now < until;

                items.forEach(function (item, i) {
                    const on = live && starts[i] === current;
                    const label = item.querySelector(".programm-item__now");

                    item.classList.toggle("is-live", on);

                    if (label) label.hidden = !on;
                });
            });
        };

        update();
        window.setInterval(update, 30000);
    });
})();
