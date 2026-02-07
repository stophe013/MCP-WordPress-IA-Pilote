import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";
import fs from "fs";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

async function main() {
    console.log("Verification du CSS actuel...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "css-checker",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        const currentCSS = await client.callTool({
            name: 'adjm__get-custom-css',
            arguments: {}
        });

        console.log("Reponse brute:");
        console.log(currentCSS.content[0].text);

        // Sauvegarder pour analyse
        fs.writeFileSync(path.join(__dirname, 'current_css.json'), currentCSS.content[0].text);
        console.log("\nSauvegarde dans current_css.json");

    } catch (error) {
        console.error("Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
