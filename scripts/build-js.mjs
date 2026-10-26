/*
* JS build: terser for plain files, esbuild for bundles (entries in js-entries.mjs)
*/

import fs from "fs";
import path from "path";
import { minify } from "terser";
import { build } from "esbuild";
import { entries } from "./js-entries.mjs";

const isProd = process.env.NODE_ENV === "production";
const themeRoot = process.cwd();

function ensureDir(filePath) {
  fs.mkdirSync(path.dirname(filePath), { recursive: true });
}

async function buildWithTerser(inputRel, outputRel) {
  const inputPath = path.join(themeRoot, inputRel);
  const outputPath = path.join(themeRoot, outputRel);

  if (!fs.existsSync(inputPath)) {
    console.warn(`[skip] ${inputRel} not found`);
    return;
  }

  const code = fs.readFileSync(inputPath, "utf8");

  const result = await minify(code, {
    compress: isProd
      ? {
          passes: 2,
          drop_console: ["log", "info"],
          drop_debugger: true,
          dead_code: true,
        }
      : {
          passes: 1,
          dead_code: true,
        },
    mangle: {
      safari10: true,
    },
    format: {
      comments: false,
    },
  });

  if (!result.code) {
    throw new Error(`Minification failed for ${inputRel}`);
  }

  ensureDir(outputPath);
  fs.writeFileSync(outputPath, result.code, "utf8");

  console.log(`[js:${isProd ? "prod" : "dev"}] ${inputRel} → ${outputRel}`);
}

async function buildWithEsbuild(inputRel, outputRel) {
  const inputPath = path.join(themeRoot, inputRel);
  const outputPath = path.join(themeRoot, outputRel);

  if (!fs.existsSync(inputPath)) {
    console.warn(`[skip] ${inputRel} not found`);
    return;
  }

  ensureDir(outputPath);

  await build({
    entryPoints: [inputPath],
    outfile: outputPath,
    bundle: true,
    minify: true,
    sourcemap: !isProd,
    target: ["es2018"],
    format: "iife",
    platform: "browser",
    logLevel: "silent",
  });

  console.log(`[js:${isProd ? "prod" : "dev"}][bundle] ${inputRel} → ${outputRel}`);
}

async function run() {
  for (const entry of entries) {
    if (entry.bundle) {
      await buildWithEsbuild(entry.in, entry.out);
    } else {
      await buildWithTerser(entry.in, entry.out);
    }
  }
}

run().catch((err) => {
  console.error(err);
  process.exit(1);
});
