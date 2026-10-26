/*
* CSS assets: copies vendor CSS from assets/css → dist/css
*/

import fs from "fs";
import path from "path";

const srcDir = "assets/css";
const destDir = "dist/css";

fs.mkdirSync(destDir, { recursive: true });

if (fs.existsSync(srcDir)) {
    for (const file of fs.readdirSync(srcDir)) {
        if (file.endsWith(".css")) {
            fs.copyFileSync(
                path.join(srcDir, file),
                path.join(destDir, file)
            );

            console.log(`[copy:css] ${file} copied`);
        }
    }
}