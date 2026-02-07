import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

async function main() {
    console.log("📋 Liste des outils MCP disponibles...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "tools-lister",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        const tools = await client.listTools();

        console.log("=".repeat(60));
        console.log("OUTILS DISPONIBLES");
        console.log("=".repeat(60));

        tools.tools.forEach(tool => {
            console.log(`\n📌 ${tool.name}`);
            console.log(`   ${tool.description}`);
        });

        console.log("\n" + "=".repeat(60));
        console.log(`Total: ${tools.tools.length} outils`);

    } catch (error) {
        console.error("❌ Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
