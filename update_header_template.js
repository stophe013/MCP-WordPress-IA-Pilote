import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

async function main() {
    console.log("🔍 Récupération des informations du header...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "header-updater",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        // 1. Lister les template parts disponibles
        console.log("📋 Liste des template parts...");
        const partsResult = await client.callTool({
            name: 'adjm__list-template-parts',
            arguments: {}
        });
        console.log(partsResult.content[0].text);

        // 2. Récupérer les paramètres du header
        console.log("\n🎨 Récupération des paramètres du header...");
        const headerSettingsResult = await client.callTool({
            name: 'adjm__get-header-settings',
            arguments: {}
        });
        console.log(headerSettingsResult.content[0].text);

        // 3. Récupérer le logo actuel
        console.log("\n🖼️ Récupération des informations du site...");
        const siteIdentityResult = await client.callTool({
            name: 'adjm__get-site-identity',
            arguments: {}
        });
        console.log(siteIdentityResult.content[0].text);

    } catch (error) {
        console.error("❌ Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
