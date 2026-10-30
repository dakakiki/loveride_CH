/*
* Geländeplan (layout_gelaendeplan): Leaflet map with swisstopo aerial tiles (no key, no cookies) or the site plan graphic, markers = black labels (number + title) with an arrow pointing to the place; legend buttons zoom to the place and highlight it, markers do the same
*/

import L from "leaflet";

document.querySelectorAll("[data-gelaendeplan]").forEach(function (section) {
    const mapEl = section.querySelector("[data-map]");
    const buttons = Array.from(section.querySelectorAll("[data-marker]"));

    if (!mapEl) return;

    const config = JSON.parse(mapEl.dataset.map || "{}");
    const plan = config.mode === "plan" && config.plan ? config.plan : null;
    const mobile = window.matchMedia("(pointer: coarse)").matches;
    let map;
    let toLatLng;
    let focusZoom;

    if (plan) {
        const bounds = [[0, 0], [plan.h, plan.w]];

        map = L.map(mapEl, {
            crs: L.CRS.Simple,
            minZoom: -3,
            maxZoom: 2,
            zoomSnap: 0.25,
            scrollWheelZoom: false,
            dragging: !mobile,
            attributionControl: false
        });

        L.imageOverlay(plan.url, bounds).addTo(map);
        map.fitBounds(bounds);
        map.setMaxBounds(bounds);

        toLatLng = function (pos) {
            return [plan.h * (1 - pos[1] / 100), plan.w * pos[0] / 100];
        };
        focusZoom = map.getZoom() + 1;
    } else {
        map = L.map(mapEl, {
            center: config.center,
            zoom: config.zoom,
            minZoom: 12,
            maxZoom: 20,
            scrollWheelZoom: false,
            dragging: !mobile
        });

        L.tileLayer("https://wmts.geo.admin.ch/1.0.0/ch.swisstopo.swissimage/default/current/3857/{z}/{x}/{y}.jpeg", {
            maxNativeZoom: 19,
            maxZoom: 20,
            attribution: '&copy; <a href="https://www.swisstopo.admin.ch/" target="_blank" rel="noopener">swisstopo</a>'
        }).addTo(map);

        toLatLng = function (pos) {
            return pos;
        };
        focusZoom = Math.min(config.zoom + 1, 19);
    }

    map.attributionControl && map.attributionControl.setPrefix(false);

    const markers = buttons.map(function (button, index) {
        const red = button.classList.contains("gelaende__ort--rot");
        const title = button.querySelector(".gelaende__titel").textContent;
        const label = document.createElement("span");
        const number = document.createElement("span");
        const text = document.createElement("span");

        label.className = "gelaende-marker__label";
        number.className = "gelaende-marker__nr";
        number.textContent = String(index + 1);
        text.className = "gelaende-marker__text";
        text.textContent = title;
        label.append(number, text);

        const marker = L.marker(toLatLng(JSON.parse(button.dataset.marker)), {
            icon: L.divIcon({
                className: "gelaende-marker" + (red ? " gelaende-marker--rot" : ""),
                html: label,
                iconSize: [0, 0],
                iconAnchor: [0, 0]
            }),
            keyboard: false,
            riseOnHover: true
        }).addTo(map);

        marker.on("click", function () {
            select(index);
        });

        return marker;
    });

    function select(index) {
        buttons.forEach(function (button, i) {
            const active = i === index;

            button.setAttribute("aria-pressed", active ? "true" : "false");
            markers[i].getElement() && markers[i].getElement().classList.toggle("is-active", active);
            markers[i].setZIndexOffset(active ? 1000 : 0);
        });

        map.flyTo(markers[index].getLatLng(), Math.max(map.getZoom(), focusZoom), {
            duration: window.matchMedia("(prefers-reduced-motion: reduce)").matches ? 0 : 0.6
        });
    }

    buttons.forEach(function (button, index) {
        button.addEventListener("click", function () {
            select(index);
        });
    });

    if (!plan && markers.length) {
        map.fitBounds(L.featureGroup(markers).getBounds(), { paddingTopLeft: [48, 72], paddingBottomRight: [48, 32], maxZoom: config.zoom + 1 });
    }

    new ResizeObserver(function () {
        map.invalidateSize();
    }).observe(mapEl);
});
