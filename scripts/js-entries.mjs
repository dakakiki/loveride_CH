/*
* JS entries: source → dist (used by build-js.mjs and watch-js.mjs)
*/

export const entries = [
  { in: "assets/js/custom.js", out: "dist/js/custom.min.js", bundle: false },
  { in: "assets/js/gsap.js", out: "dist/js/gsap.min.js", bundle: true },
  { in: "assets/js/admin-acf.js", out: "dist/js/admin-acf.min.js", bundle: false },
  { in: "assets/js/slick.min.js", out: "dist/js/slick.min.js", bundle: false },
  { in: "assets/js/partner-slider.js", out: "dist/js/partner-slider.min.js", bundle: false },
  { in: "assets/js/hero-slider.js", out: "dist/js/hero-slider.min.js", bundle: false },
  { in: "assets/js/zahlenband.js", out: "dist/js/zahlenband.min.js", bundle: false },
  { in: "assets/js/countdown.js", out: "dist/js/countdown.min.js", bundle: false },
  { in: "assets/js/galerie.js", out: "dist/js/galerie.min.js", bundle: false },
  { in: "assets/js/news.js", out: "dist/js/news.min.js", bundle: false },
  { in: "assets/js/logo-wand.js", out: "dist/js/logo-wand.min.js", bundle: false },
  { in: "assets/js/programm.js", out: "dist/js/programm.min.js", bundle: false },
  { in: "assets/js/read-aloud.js", out: "dist/js/read-aloud.min.js", bundle: false },
];
