import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";
import fs from "fs";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

async function main() {
    console.log("📄 Recherche de la page Nos DJ...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "page-finder",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        // 1. Lister les pages pour trouver "nos-dj"
        console.log("📋 Liste des pages...");
        const pagesResult = await client.callTool({
            name: 'adjm__list-pages',
            arguments: { per_page: 100 }
        });

        const pages = JSON.parse(pagesResult.content[0].text);
        const djPage = pages.find(p => p.slug === 'nos-dj' || p.slug.includes('dj'));

        if (!djPage) {
            console.log("❌ Page 'nos-dj' non trouvée");
            console.log("Pages disponibles:");
            pages.forEach(p => console.log(`  - ${p.id}: ${p.slug} (${p.title})`));
            await client.close();
            process.exit(1);
        }

        console.log(`\n✅ Page trouvée: ID ${djPage.id} - ${djPage.title}`);

        // 2. Récupérer les détails complets
        console.log("\n📄 Récupération du contenu...");
        const pageResult = await client.callTool({
            name: 'adjm__get-page',
            arguments: { id: djPage.id }
        });

        const pageData = JSON.parse(pageResult.content[0].text);

        console.log("\n" + "=".repeat(60));
        console.log("PAGE NOS DJ");
        console.log("=".repeat(60));
        console.log(`ID: ${pageData.id}`);
        console.log(`Titre: ${pageData.title}`);
        console.log(`Slug: ${pageData.slug}`);
        console.log(`Template: ${pageData.template}`);
        console.log(`URL: ${pageData.url}`);
        console.log("\nContenu (début):");
        console.log(pageData.content.substring(0, 2000));
        console.log("\n[...]");
        console.log(`\nLongueur totale: ${pageData.content.length} caractères`);

        // Sauvegarder
        const outputPath = path.join(__dirname, 'dj_page_backup.json');
        fs.writeFileSync(outputPath, JSON.stringify(pageData, null, 2));
        console.log(`\n💾 Backup: ${outputPath}`);

    } catch (error) {
        console.error("❌ Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
