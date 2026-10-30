/*
* Lightbox (template_parts/components/lightbox.php): photos with prev / next, keys and swipe, or the aftermovie (YouTube without cookies) in a <dialog>; used by galerie.js and jahrgaenge.js
*/

export function createLightbox(dialog) {
    if (!dialog || typeof dialog.showModal !== "function") return null;

    const stage = dialog.querySelector("[data-lightbox-stage]");
    const counter = dialog.querySelector("[data-lightbox-counter]");
    let links = [];
    let index = 0;
    let startX = null;

    const isVideo = function () {
        return dialog.classList.contains("lightbox--video");
    };

    const showImage = function (next) {
        const link = links[(next + links.length) % links.length];
        const thumb = link.querySelector("img");
        const image = document.createElement("img");

        index = (next + links.length) % links.length;
        image.className = "lightbox__image";
        image.src = link.href;
        image.alt = thumb ? thumb.alt : "";

        stage.replaceChildren(image);
        counter.textContent = (index + 1) + " / " + links.length;
    };

    const open = function (mode) {
        dialog.classList.toggle("lightbox--video", mode === "video");
        dialog.classList.toggle("lightbox--single", mode !== "video" && links.length < 2);
        document.documentElement.classList.add("has-lightbox");

        if (!dialog.open) dialog.showModal();
    };

    dialog.querySelector("[data-lightbox-close]").addEventListener("click", function () {
        dialog.close();
    });

    dialog.querySelector("[data-lightbox-prev]").addEventListener("click", function () {
        showImage(index - 1);
    });

    dialog.querySelector("[data-lightbox-next]").addEventListener("click", function () {
        showImage(index + 1);
    });

    dialog.addEventListener("keydown", function (event) {
        if (isVideo() || links.length < 2) return;

        if (event.key === "ArrowLeft") showImage(index - 1);
        if (event.key === "ArrowRight") showImage(index + 1);
    });

    dialog.addEventListener("click", function (event) {
        if (event.target === dialog || event.target === stage) dialog.close();
    });

    dialog.addEventListener("pointerdown", function (event) {
        startX = event.pointerType === "touch" ? event.clientX : null;
    });

    dialog.addEventListener("pointerup", function (event) {
        if (startX === null || isVideo() || links.length < 2) return;

        const distance = event.clientX - startX;

        startX = null;

        if (Math.abs(distance) > 50) showImage(index + (distance < 0 ? 1 : -1));
    });

    dialog.addEventListener("close", function () {
        if (dialog.open) return;

        stage.replaceChildren();
        document.documentElement.classList.remove("has-lightbox");
    });

    return {
        images: function (list, start) {
            links = list;
            showImage(start);
            open("bild");
        },
        video: function (id, title) {
            const frame = document.createElement("iframe");

            frame.className = "lightbox__video";
            frame.src = youtubeEmbed(id);
            frame.title = title || "Aftermovie";
            frame.allow = "autoplay; encrypted-media; picture-in-picture; fullscreen";
            frame.allowFullscreen = true;

            stage.replaceChildren(frame);
            counter.textContent = "";
            open("video");
        },
    };
}

export function youtubeEmbed(id) {
    return "https://www.youtube-nocookie.com/embed/" + encodeURIComponent(id) + "?autoplay=1&rel=0";
}
