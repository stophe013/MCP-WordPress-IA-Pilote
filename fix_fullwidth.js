import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

async function main() {
    console.log("🔧 Correction pleine largeur de la page d'accueil...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "fullwidth-fixer",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        // 1. Lister les templates disponibles
        console.log("📋 Récupération des templates disponibles...");
        const templatesResult = await client.callTool({
            name: 'adjm__list-templates',
            arguments: {}
        });

        const templatesData = JSON.parse(templatesResult.content[0].text);
        const templates = templatesData.templates || templatesData || [];
        console.log("Templates disponibles:");
        console.log(JSON.stringify(templates, null, 2));

        // 2. Récupérer la page actuelle
        console.log("\n📄 Récupération de la page d'accueil (ID 753)...");
        const pageResult = await client.callTool({
            name: 'adjm__get-page',
            arguments: { id: 753 }
        });

        const pageData = JSON.parse(pageResult.content[0].text);
        console.log(`Template actuel: ${pageData.template || 'default'}`);

        // 3. Trouver un template pleine largeur
        // Les templates sont un objet { slug: title }
        const templateSlugs = Object.keys(templates);
        const fullWidthTemplates = ['page-no-title', 'page-wide', 'blank', 'full-width'];
        let selectedTemplate = '';

        for (const tpl of fullWidthTemplates) {
            if (templateSlugs.includes(tpl)) {
                selectedTemplate = tpl;
                break;
            }
        }

        // Fallback: chercher un template qui contient "wide", "full", "blank" ou "no-title"
        if (!selectedTemplate) {
            const wideTemplate = templateSlugs.find(slug =>
                slug.includes('wide') ||
                slug.includes('full') ||
                slug.includes('blank') ||
                slug.includes('no-title')
            );
            if (wideTemplate) {
                selectedTemplate = wideTemplate;
            }
        }

        console.log(`\nTemplate sélectionné: ${selectedTemplate || 'aucun trouvé - garder default'}`);

        // 4. Mettre à jour la page avec le template et forcer alignfull sur le wrapper
        console.log("\n📝 Mise à jour de la page...");

        // Envelopper tout le contenu dans un groupe alignfull si ce n'est pas déjà fait
        let content = pageData.content;

        // Si le contenu ne commence pas par un groupe alignfull, l'envelopper
        if (!content.trim().startsWith('<!-- wp:group {"align":"full"')) {
            content = `<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0">
${content}
</div>
<!-- /wp:group -->`;
        }

        const updateArgs = {
            id: 753,
            content: content
        };

        // Ajouter le template si trouvé
        if (selectedTemplate) {
            updateArgs.template = selectedTemplate;
        }

        const updateResult = await client.callTool({
            name: 'adjm__update-page',
            arguments: updateArgs
        });

        const result = JSON.parse(updateResult.content[0].text);

        console.log("\n" + "=".repeat(60));
        console.log("✅ PAGE MISE À JOUR");
        console.log("=".repeat(60));
        console.log(`Titre: ${result.title}`);
        console.log(`Template: ${result.template || 'default'}`);
        console.log(`URL: ${result.url}`);
        console.log("\n🌐 Rechargez la page pour voir les changements!");

    } catch (error) {
        console.error("❌ Erreur:", error.message);
        if (error.stack) console.error(error.stack);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
