import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

// CSS pour corriger le logo - retirer le contour/bouton
const logoFixCSS = `
/* === CORRECTION LOGO - Retirer le contour/bouton === */

/* Reset complet du logo */
.wp-block-site-logo,
.custom-logo-link,
.site-logo,
.wp-block-site-logo a {
    background: none !important;
    background-color: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    box-shadow: none !important;
}

/* Image du logo - juste l'image, rien d'autre */
.wp-block-site-logo img,
.custom-logo,
.site-logo img {
    background: none !important;
    background-color: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    padding: 0 !important;
    box-shadow: none !important;
    filter: none !important; /* Pas d'inversion de couleur */
    height: 60px !important;
    width: auto !important;
}

/* Supprimer tout wrapper ou conteneur avec style */
.site-header .wp-block-group:has(.wp-block-site-logo),
header .wp-block-group:has(.wp-block-site-logo) {
    background: transparent !important;
}

/* Si le logo est dans un bouton ou lien stylé */
a:has(.custom-logo),
a:has(.wp-block-site-logo img) {
    background: none !important;
    border: none !important;
    padding: 0 !important;
}
`;

async function main() {
    console.log("Correction du logo - Retrait du contour...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "logo-fixer",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        const cssResult = await client.callTool({
            name: 'adjm__append-custom-css',
            arguments: { css: logoFixCSS }
        });

        console.log("Logo corrige - contour retire !");
        console.log("Rechargez: https://adjmevenementiel.fr");

    } catch (error) {
        console.error("Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
