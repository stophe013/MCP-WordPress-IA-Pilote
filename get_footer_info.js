import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";
import fs from "fs";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

async function main() {
    console.log("========================================");
    console.log("  ANALYSE DU FOOTER ADJM");
    console.log("========================================\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "footer-analyzer",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        // 1. Recuperer les settings du footer
        console.log("1. Recuperation des parametres du footer...");
        const footerSettings = await client.callTool({
            name: 'adjm__get-footer-settings',
            arguments: {}
        });
        console.log("\nFooter Settings:");
        console.log(footerSettings.content[0].text);

        // 2. Recuperer les styles du theme
        console.log("\n2. Recuperation des styles globaux...");
        const globalStyles = await client.callTool({
            name: 'adjm__get-global-styles',
            arguments: {}
        });
        console.log("\nGlobal Styles:");
        console.log(globalStyles.content[0].text);

        // 3. Recuperer le CSS actuel
        console.log("\n3. Recuperation du CSS personnalise...");
        const customCSS = await client.callTool({
            name: 'adjm__get-custom-css',
            arguments: {}
        });
        console.log("\nCustom CSS:");
        console.log(customCSS.content[0].text);

        // 4. Recuperer les template parts (footer FSE)
        console.log("\n4. Recuperation des template parts...");
        const templateParts = await client.callTool({
            name: 'adjm__list-template-parts',
            arguments: {}
        });
        console.log("\nTemplate Parts:");
        console.log(templateParts.content[0].text);

        // 5. Recuperer les reseaux sociaux
        console.log("\n5. Recuperation des liens sociaux...");
        const socialLinks = await client.callTool({
            name: 'adjm__get-social-links',
            arguments: {}
        });
        console.log("\nSocial Links:");
        console.log(socialLinks.content[0].text);

        // Sauvegarder toutes les infos
        const footerInfo = {
            footerSettings: JSON.parse(footerSettings.content[0].text),
            globalStyles: JSON.parse(globalStyles.content[0].text),
            templateParts: JSON.parse(templateParts.content[0].text),
            socialLinks: JSON.parse(socialLinks.content[0].text),
            customCSS: customCSS.content[0].text
        };

        const outputPath = path.join(__dirname, 'footer_info_backup.json');
        fs.writeFileSync(outputPath, JSON.stringify(footerInfo, null, 2));
        console.log(`\nBackup sauvegarde: ${outputPath}`);

    } catch (error) {
        console.error("Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
