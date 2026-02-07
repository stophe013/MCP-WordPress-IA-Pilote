import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";
import fs from "fs";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

async function main() {
    console.log("📄 Récupération de la page d'accueil...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "homepage-getter",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        // Récupérer la page d'accueil (ID 753)
        console.log("🔍 Récupération de la page d'accueil (ID 753)...");
        const pageResult = await client.callTool({
            name: 'adjm__get-page',
            arguments: { id: 753 }
        });

        const pageData = JSON.parse(pageResult.content[0].text);

        console.log("\n" + "=".repeat(80));
        console.log("📊 PAGE D'ACCUEIL ACTUELLE");
        console.log("=".repeat(80));
        console.log(`Titre: ${pageData.title}`);
        console.log(`Slug: ${pageData.slug}`);
        console.log(`Status: ${pageData.status}`);
        console.log(`URL: ${pageData.url}`);
        console.log(`Template: ${pageData.template}`);
        console.log(`Dernière modification: ${pageData.modified}`);
        console.log("\n" + "=".repeat(80));
        console.log("CONTENU:");
        console.log("=".repeat(80));
        console.log(pageData.content.substring(0, 1000));
        console.log("\n[...] (contenu tronqué)");
        console.log("=".repeat(80));
        console.log(`\nLongueur totale du contenu: ${pageData.content.length} caractères`);

        // Sauvegarder dans un fichier
        const outputPath = path.join(__dirname, 'homepage_backup.json');
        fs.writeFileSync(outputPath, JSON.stringify(pageData, null, 2));
        console.log(`\n💾 Backup sauvegardé: ${outputPath}`);

    } catch (error) {
        console.error("❌ Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
