import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

const adminBarFixCSS = `
/* === CORRECTION BARRE ADMIN WORDPRESS === */

#wpadminbar,
#wpadminbar * {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif !important;
}

#wpadminbar a,
#wpadminbar .ab-item,
#wpadminbar .ab-label,
#wpadminbar .ab-icon,
#wpadminbar #wp-admin-bar-site-name a,
#wpadminbar #wp-admin-bar-site-name .ab-item,
#wpadminbar #wp-admin-bar-my-account a,
#wpadminbar .quicklinks a,
#wpadminbar .quicklinks .ab-item,
#wpadminbar .ab-top-menu > li > a,
#wpadminbar .ab-top-menu > li > .ab-item {
    color: #ffffff !important;
    background: transparent !important;
}

#wpadminbar a:hover,
#wpadminbar .ab-item:hover,
#wpadminbar .quicklinks a:hover {
    color: #00b9eb !important;
}

#wpadminbar .ab-top-secondary a,
#wpadminbar .ab-top-secondary .ab-item {
    color: #ffffff !important;
}

/* Sous-menus admin bar */
#wpadminbar .ab-submenu,
#wpadminbar .ab-sub-wrapper {
    background: #1d2327 !important;
}

#wpadminbar .ab-submenu a,
#wpadminbar .ab-sub-wrapper a {
    color: #ffffff !important;
}

#wpadminbar .ab-submenu a:hover,
#wpadminbar .ab-sub-wrapper a:hover {
    background: #2c3338 !important;
    color: #00b9eb !important;
}

/* Icones */
#wpadminbar .ab-icon,
#wpadminbar .ab-icon:before,
#wpadminbar .ab-item:before {
    color: #a7aaad !important;
}

#wpadminbar a:hover .ab-icon:before,
#wpadminbar .ab-item:hover:before {
    color: #00b9eb !important;
}
`;

async function main() {
    console.log("Correction de la barre admin WordPress...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "adminbar-fixer",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        const cssResult = await client.callTool({
            name: 'adjm__append-custom-css',
            arguments: { css: adminBarFixCSS }
        });

        console.log("Barre admin corrigee - textes en blanc !");
        console.log("Rechargez: https://adjmevenementiel.fr");

    } catch (error) {
        console.error("Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
