/*
* Partner logo slider in the footer (random order on each load; Slick: infinite autoplay, pauses on hover/focus)
*/

jQuery(function ($) {
    "use strict";

    const $slider = $("[data-partner-slider]");

    if (!$slider.length || $slider.hasClass("slick-initialized")) return;

    const items = $slider.children().get();

    for (let i = items.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        const item = items[i];

        items[i] = items[j];
        items[j] = item;
    }

    $slider.append(items);

    if (!$.fn.slick) return;

    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    $slider.slick({
        rows: 0,
        slidesToShow: 5,
        slidesToScroll: 1,
        infinite: true,
        autoplay: !reduceMotion,
        autoplaySpeed: 2500,
        speed: 600,
        arrows: false,
        dots: false,
        pauseOnHover: true,
        pauseOnFocus: true,
        swipeToSlide: true,
        responsive: [
            { breakpoint: 1025, settings: { slidesToShow: 4 } },
            { breakpoint: 769, settings: { slidesToShow: 3 } },
            { breakpoint: 641, settings: { slidesToShow: 2 } }
        ]
    });
});
