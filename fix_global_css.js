import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

// CSS pour supprimer les bordures blanches sur les pages pleine largeur
const fullWidthCSS = `
/* ========================================
   FULL WIDTH PAGES - Anti-bordures blanches
   ======================================== */

/* Supprimer les marges/padding du conteneur principal */
body.page-template-page-no-title .entry-content,
body.page-template-page-wide .entry-content,
body.page-template-page-no-title .wp-site-blocks,
body.page-template-page-wide .wp-site-blocks {
    padding-left: 0 !important;
    padding-right: 0 !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    max-width: 100% !important;
}

/* Assurer que les blocs alignfull touchent les bords */
body.page-template-page-no-title .alignfull,
body.page-template-page-wide .alignfull {
    width: 100vw !important;
    max-width: 100vw !important;
    margin-left: calc(-50vw + 50%) !important;
    margin-right: calc(-50vw + 50%) !important;
}

/* Fix pour les thèmes avec conteneur centré */
body.page-template-page-no-title .wp-block-group.alignfull,
body.page-template-page-wide .wp-block-group.alignfull,
body.page-template-page-no-title .wp-block-cover.alignfull,
body.page-template-page-wide .wp-block-cover.alignfull {
    width: 100vw !important;
    max-width: 100vw !important;
    margin-left: calc(-50vw + 50%) !important;
    margin-right: calc(-50vw + 50%) !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
}

/* Supprimer le padding du body si nécessaire */
body.page-template-page-no-title,
body.page-template-page-wide {
    overflow-x: hidden;
}

/* Fix spécifique pour certains thèmes FSE */
.wp-site-blocks > .alignfull:first-child {
    margin-top: 0 !important;
}

/* Assurer que le contenu interne reste lisible */
body.page-template-page-no-title .alignfull > .wp-block-group__inner-container,
body.page-template-page-wide .alignfull > .wp-block-group__inner-container {
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
    padding-left: 20px;
    padding-right: 20px;
}
`;

async function main() {
    console.log("🎨 Ajout du CSS global pour pages pleine largeur...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "css-fixer",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        // 1. Récupérer le CSS actuel
        console.log("📋 Récupération du CSS personnalisé actuel...");
        const currentCssResult = await client.callTool({
            name: 'adjm__get-custom-css',
            arguments: {}
        });

        const currentCssData = JSON.parse(currentCssResult.content[0].text);
        console.log(`CSS actuel: ${currentCssData.css ? currentCssData.css.length + ' caractères' : 'vide'}`);

        // 2. Vérifier si notre CSS est déjà présent
        if (currentCssData.css && currentCssData.css.includes('FULL WIDTH PAGES - Anti-bordures')) {
            console.log("\n✅ Le CSS pleine largeur est déjà présent!");
            await client.close();
            process.exit(0);
        }

        // 3. Ajouter le CSS
        console.log("\n📝 Ajout du CSS pleine largeur...");
        const appendResult = await client.callTool({
            name: 'adjm__append-custom-css',
            arguments: {
                css: fullWidthCSS
            }
        });

        const result = JSON.parse(appendResult.content[0].text);

        console.log("\n" + "=".repeat(60));
        console.log("✅ CSS AJOUTÉ AVEC SUCCÈS");
        console.log("=".repeat(60));
        console.log(`Nouveau total: ${result.total_length || 'N/A'} caractères`);
        console.log("\n🌐 Rechargez la page pour voir les changements!");
        console.log("\nCSS ajouté:");
        console.log("- Suppression des marges du conteneur principal");
        console.log("- Blocs alignfull étendus à 100vw");
        console.log("- Fix pour thèmes FSE");

    } catch (error) {
        console.error("❌ Erreur:", error.message);
        if (error.stack) console.error(error.stack);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
