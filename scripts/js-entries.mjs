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
];
