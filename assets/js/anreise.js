/*
* Anreise & Parken (layout_anreise): Leaflet map with the grey swisstopo national map (no key, no cookies), red destination marker, one layer per Ebene (label markers with icon + GeoJSON lines); layer checkboxes toggle the layers
*/

import L from "leaflet";

document.querySelectorAll("[data-anreise]").forEach(function (section) {
    const mapEl = section.querySelector("[data-map]");

    if (!mapEl) return;

    const config = JSON.parse(mapEl.dataset.map || "{}");
    const mobile = window.matchMedia("(pointer: coarse)").matches;
    const bounds = [config.ziel.pos];

    const map = L.map(mapEl, {
        center: config.ziel.pos,
        zoom: config.zoom,
        minZoom: 10,
        maxZoom: 19,
        zoomSnap: 0.5,
        scrollWheelZoom: false,
        dragging: !mobile
    });

    L.tileLayer("https://wmts.geo.admin.ch/1.0.0/ch.swisstopo.pixelkarte-grau/default/current/3857/{z}/{x}/{y}.jpeg", {
        maxNativeZoom: 18,
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.swisstopo.admin.ch/" target="_blank" rel="noopener">swisstopo</a>'
    }).addTo(map);

    map.attributionControl.setPrefix(false);

    function label(icon, title, modifier) {
        const wrap = document.createElement("span");
        const text = document.createElement("span");

        wrap.className = "anreise-marker__label";
        wrap.innerHTML = icon || "";
        text.className = "anreise-marker__text";
        text.textContent = title;
        wrap.append(text);

        return L.divIcon({
            className: "anreise-marker" + (modifier ? " anreise-marker--" + modifier : ""),
            html: wrap,
            iconSize: [0, 0],
            iconAnchor: [0, 0]
        });
    }

    function popup(title, text) {
        const box = document.createElement("div");
        const head = document.createElement("strong");
        const body = document.createElement("p");

        head.textContent = title;
        body.textContent = text;
        box.append(head, body);

        return box;
    }

    function marker(pos, icon, title, text, modifier) {
        const item = L.marker(pos, {
            icon: label(icon, title, modifier),
            title: title,
            riseOnHover: true
        });

        if (text) {
            item.bindPopup(popup(title, text), { offset: [0, -36], closeButton: true });
        }

        item.on("click", function () {
            const el = item.getElement();

            el && el.classList.toggle("is-active");
        });

        return item;
    }

    marker(config.ziel.pos, config.ziel.icon, config.ziel.titel, "", "ziel").setZIndexOffset(2000).addTo(map);

    const layers = (config.ebenen || []).map(function (ebene) {
        const group = L.layerGroup();

        (ebene.marker || []).forEach(function (punkt) {
            group.addLayer(marker(punkt.pos, ebene.icon, punkt.titel, punkt.text));

            if (ebene.aktiv) bounds.push(punkt.pos);
        });

        if (ebene.geojson) {
            group.addLayer(L.geoJSON(ebene.geojson, {
                style: { color: "#CE0E2D", weight: 5, opacity: 0.9, fillOpacity: 0.15 },
                pointToLayer: function (feature, latlng) {
                    return L.circleMarker(latlng, { radius: 6, color: "#CE0E2D", weight: 3, fillColor: "#FFFFFF", fillOpacity: 1 });
                }
            }));
        }

        if (ebene.aktiv) group.addTo(map);

        return group;
    });

    section.querySelectorAll("[data-ebene]").forEach(function (input) {
        input.addEventListener("change", function () {
            const group = layers[Number(input.dataset.ebene)];

            if (!group) return;

            input.checked ? group.addTo(map) : map.removeLayer(group);
        });
    });

    if (bounds.length > 1) {
        const side = mapEl.clientWidth < 600 ? 32 : 80;

        map.fitBounds(L.latLngBounds(bounds), { paddingTopLeft: [side, 64], paddingBottomRight: [side, 24], maxZoom: config.zoom });
    }

    new ResizeObserver(function () {
        map.invalidateSize();
    }).observe(mapEl);
});
