import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

async function main() {
    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "info-getter",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    // 1. Liste des pages
    console.log("=== PAGES ===");
    const pagesResult = await client.callTool({
        name: 'adjm__list-pages',
        arguments: {}
    });
    console.log(pagesResult.content[0].text);
    console.log("\n");

    // 2. Infos du thème
    console.log("=== THÈME ===");
    const themeResult = await client.callTool({
        name: 'adjm__get-theme-info',
        arguments: {}
    });
    console.log(themeResult.content[0].text);
    console.log("\n");

    // 3. Menus
    console.log("=== MENUS ===");
    const menusResult = await client.callTool({
        name: 'adjm__list-menus',
        arguments: {}
    });
    console.log(menusResult.content[0].text);
    console.log("\n");

    // 4. Page builders
    console.log("=== PAGE BUILDERS ===");
    const buildersResult = await client.callTool({
        name: 'adjm__detect-page-builders',
        arguments: {}
    });
    console.log(buildersResult.content[0].text);

    await client.close();
    process.exit(0);
}

main().catch(console.error);
