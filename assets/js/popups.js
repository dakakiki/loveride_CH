/*
* Popups (inc/popups.php): links with data-popup or to #loverider-anmeldung / #aussteller-anmeldung open the <dialog>, also from the URL hash; close on X, Esc and outside click with a short fade out, focus back to the opener; data-popup-wahl preselects the radio option starting with it; Loverider form: partner and children only for „Family“ (hidden fields disabled), „Weiteres Kind hinzufügen“ shows up to 3 children
*/

(function () {
    "use strict";

    const dialogs = Array.from(document.querySelectorAll("[data-popup-dialog]"));

    if (!dialogs.length) return;

    const reduce = window.matchMedia("(prefers-reduced-motion: reduce)");
    let opener = null;

    const find = function (id) {
        return dialogs.find(function (dialog) {
            return dialog.id === id;
        });
    };

    const toggle = function (element, show) {
        element.hidden = !show;
        element.querySelectorAll("input, select, textarea").forEach(function (field) {
            field.disabled = !show;
        });
    };

    const setupForm = function (dialog) {
        const form = dialog.querySelector("form");
        const family = form ? Array.from(form.querySelectorAll(".lr-form__abschnitt--family")) : [];
        const kinder = form ? Array.from(form.querySelectorAll(".lr-form__kind")) : [];
        let more = null;

        if (!form || !family.length) return;

        const isFamily = function () {
            const checked = form.querySelector('input[type="radio"]:checked');

            return !!checked && /^family/i.test(checked.value);
        };

        const showKinder = function (count) {
            kinder.forEach(function (kind, index) {
                toggle(kind, index < count);
            });

            if (more) more.hidden = count >= kinder.length;
        };

        const sync = function () {
            const active = isFamily();

            family.forEach(function (section) {
                toggle(section, active);
            });

            if (active) {
                showKinder(Math.max(1, kinder.filter(function (kind) {
                    return !kind.hidden;
                }).length));
            }
        };

        if (kinder.length > 1) {
            more = document.createElement("button");
            more.type = "button";
            more.className = "lr-form__mehr";
            more.textContent = "Weiteres Kind hinzufügen";
            more.addEventListener("click", function () {
                const next = kinder.find(function (kind) {
                    return kind.hidden;
                });

                showKinder(kinder.indexOf(next) + 1);
                next.querySelector("input").focus();
            });
            kinder[kinder.length - 1].after(more);
        }

        form.addEventListener("change", function (event) {
            if (event.target.type === "radio") sync();
        });

        form.addEventListener("reset", function () {
            window.setTimeout(function () {
                showKinder(1);
                sync();
            });
        });

        showKinder(1);
        sync();
    };

    const hide = function (dialog) {
        if (!dialog.open || dialog.classList.contains("is-closing")) return;

        if (reduce.matches) {
            dialog.close();
            return;
        }

        const done = function () {
            window.clearTimeout(timer);
            dialog.removeEventListener("animationend", end);
            dialog.classList.remove("is-closing");
            dialog.close();
        };
        const end = function (event) {
            if (event.target === dialog) done();
        };
        const timer = window.setTimeout(done, 400);

        dialog.addEventListener("animationend", end);
        dialog.classList.add("is-closing");
    };

    const open = function (dialog, trigger) {
        const wahl = trigger && trigger.dataset.popupWahl;

        if (wahl) {
            const radio = Array.from(dialog.querySelectorAll('input[type="radio"]')).find(function (item) {
                return item.value.toLowerCase().indexOf(wahl.toLowerCase()) === 0;
            });

            if (radio) {
                radio.checked = true;
                radio.dispatchEvent(new Event("change", { bubbles: true }));
            }
        }

        opener = trigger || null;
        document.documentElement.classList.add("has-popup");

        if (!dialog.open) dialog.showModal();

        dialog.scrollTop = 0;
    };

    dialogs.forEach(function (dialog) {
        setupForm(dialog);

        dialog.querySelector("[data-popup-close]").addEventListener("click", function () {
            hide(dialog);
        });

        dialog.addEventListener("click", function (event) {
            if (event.target === dialog) hide(dialog);
        });

        dialog.addEventListener("cancel", function (event) {
            event.preventDefault();
            hide(dialog);
        });

        dialog.addEventListener("close", function () {
            document.documentElement.classList.remove("has-popup");

            if (window.location.hash === "#" + dialog.id) {
                window.history.replaceState(null, "", window.location.pathname + window.location.search);
            }

            if (opener) opener.focus();

            opener = null;
        });
    });

    document.addEventListener("click", function (event) {
        const link = event.target.closest('a[href^="#"]');
        const dialog = link ? find(link.getAttribute("href").slice(1)) : null;

        if (!dialog) return;

        event.preventDefault();
        open(dialog, link);
    });

    const fromHash = function () {
        const dialog = find(decodeURIComponent(window.location.hash.slice(1)));

        if (dialog && !dialog.open) open(dialog, null);
    };

    window.addEventListener("hashchange", fromHash);
    fromHash();
})();
