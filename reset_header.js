import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

// CSS minimal - Juste la charte graphique de base SANS modifications du header
const minimalCSS = `
/* === CHARTE GRAPHIQUE ADJM - VERSION MINIMALE === */
/* Header geré par WordPress par défaut */

@import url('https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap');

:root {
    --adjm-bleu-royal: #2B34A6;
    --adjm-bleu-fonce: #132060;
    --adjm-bleu-clair: #8895DD;
    --adjm-creme: #FFFDF3;
}

/* Typographie de base */
body {
    font-family: 'Inter', sans-serif;
}

h1, h2, h3, h4, h5, h6 {
    font-family: 'Barlow', sans-serif;
}

/* Boutons */
.wp-block-button__link {
    border-radius: 8px;
    font-family: 'Barlow', sans-serif;
    font-weight: 600;
}
`;

async function main() {
    console.log("Reinitialisation du header WordPress...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "header-reset",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        // Remplacer tout le CSS par la version minimale
        const cssResult = await client.callTool({
            name: 'adjm__set-custom-css',
            arguments: { css: minimalCSS }
        });

        console.log("Header reinitialise !");
        console.log("Le header utilise maintenant les styles WordPress par defaut.");
        console.log("\nRechargez: https://adjmevenementiel.fr");

    } catch (error) {
        console.error("Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
