/*
* JS entries: source → dist (used by build-js.mjs and watch-js.mjs)
*/

export const entries = [
  { in: "assets/js/custom.js", out: "dist/js/custom.min.js", bundle: false },
  { in: "assets/js/gsap.js", out: "dist/js/gsap.min.js", bundle: true },
];
