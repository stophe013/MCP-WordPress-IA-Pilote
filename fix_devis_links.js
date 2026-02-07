import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

async function main() {
    console.log("🔗 Correction des liens de devis...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "links-fixer",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        // 1. Récupérer la page actuelle
        console.log("📄 Récupération de la page d'accueil...");
        const pageResult = await client.callTool({
            name: 'adjm__get-page',
            arguments: { id: 753 }
        });

        const pageData = JSON.parse(pageResult.content[0].text);
        let content = pageData.content;

        // 2. Remplacer les liens de devis
        const oldLink = 'https://adjmevenementiel.fr/besoin-dune-prestation/';
        const newLink = 'https://adjmevenementiel.fr/devis/';

        const occurrences = (content.match(new RegExp(oldLink, 'g')) || []).length;
        console.log(`\n🔍 Liens trouvés à remplacer: ${occurrences}`);

        if (occurrences === 0) {
            console.log("✅ Aucun lien à remplacer (déjà corrigé ou pas trouvé)");
            await client.close();
            process.exit(0);
        }

        // Remplacer tous les liens vers /besoin-dune-prestation/ par /devis/
        content = content.replace(new RegExp(oldLink, 'g'), newLink);

        // 3. Mettre à jour la page
        console.log("📝 Mise à jour de la page...");
        const updateResult = await client.callTool({
            name: 'adjm__update-page',
            arguments: {
                id: 753,
                content: content
            }
        });

        console.log("\n" + "=".repeat(60));
        console.log("✅ LIENS MIS À JOUR");
        console.log("=".repeat(60));
        console.log(`${occurrences} lien(s) remplacé(s):`);
        console.log(`  ${oldLink}`);
        console.log(`  → ${newLink}`);
        console.log("\n🌐 Rechargez la page pour voir les changements!");

    } catch (error) {
        console.error("❌ Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
