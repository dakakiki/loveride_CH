/*
* Event-Teaser (layout_event_teaser): countdown to the Love Ride opening (Event-Daten), updated every second; hidden once the event has started
*/

(function () {
    "use strict";

    const boxes = Array.from(document.querySelectorAll("[data-countdown]"));

    if (!boxes.length) return;

    const pad = function (value) {
        return String(value).padStart(2, "0");
    };

    boxes.forEach(function (box) {
        const target = Date.parse(box.getAttribute("data-countdown"));
        const sr = box.querySelector("[data-countdown-text]");
        const fields = {
            tage: box.querySelector("[data-countdown-tage]"),
            std: box.querySelector("[data-countdown-std]"),
            min: box.querySelector("[data-countdown-min]"),
            sek: box.querySelector("[data-countdown-sek]")
        };

        if (isNaN(target)) return;

        let timer = null;
        let lastDays = null;

        const update = function () {
            const seconds = Math.floor((target - Date.now()) / 1000);

            if (seconds <= 0) {
                box.hidden = true;
                window.clearInterval(timer);
                return;
            }

            const days = Math.floor(seconds / 86400);

            fields.tage.textContent = String(days);
            fields.std.textContent = pad(Math.floor((seconds % 86400) / 3600));
            fields.min.textContent = pad(Math.floor((seconds % 3600) / 60));
            fields.sek.textContent = pad(seconds % 60);

            if (sr && days !== lastDays) {
                sr.textContent = days === 1 ? "Noch 1 Tag bis zum Love Ride" : "Noch " + days + " Tage bis zum Love Ride";
                lastDays = days;
            }
        };

        update();
        timer = window.setInterval(update, 1000);
    });
})();
