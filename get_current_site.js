import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";
import fs from "fs";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

async function main() {
    console.log("🔍 Récupération des informations du site ADJM...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client(
        {
            name: "site-analyzer",
            version: "1.0.0",
        },
        {
            capabilities: {},
        }
    );

    await client.connect(transport);

    const results = {};

    try {
        // 1. Infos générales du site
        console.log("📊 Récupération des infos du site...");
        const siteInfo = await client.callTool({
            name: 'adjm__get-site-info',
            arguments: {}
        });
        results.siteInfo = JSON.parse(siteInfo.content[0].text);
        console.log(`✅ Site: ${results.siteInfo.name}`);

        // 2. Liste des pages
        console.log("\n📄 Récupération de la liste des pages...");
        const pages = await client.callTool({
            name: 'adjm__list-pages',
            arguments: { per_page: 100 }
        });
        results.pages = JSON.parse(pages.content[0].text);
        console.log(`✅ ${results.pages.length} pages trouvées`);

        // 3. Page d'accueil
        const homePage = results.pages.find(p => p.slug === 'accueil' || p.title.toLowerCase() === 'accueil' || p.id === 1);
        if (homePage) {
            console.log(`\n🏠 Récupération de la page d'accueil (ID: ${homePage.id})...`);
            const homeContent = await client.callTool({
                name: 'adjm__get-page',
                arguments: { id: homePage.id }
            });
            results.homePage = JSON.parse(homeContent.content[0].text);
            console.log(`✅ Page d'accueil récupérée: "${results.homePage.title}"`);
        }

        // 4. Options du site
        console.log("\n⚙️ Récupération des options du site...");
        const blogname = await client.callTool({
            name: 'adjm__get-option',
            arguments: { option_name: 'blogname' }
        });
        results.blogname = JSON.parse(blogname.content[0].text);

        const blogdescription = await client.callTool({
            name: 'adjm__get-option',
            arguments: { option_name: 'blogdescription' }
        });
        results.blogdescription = JSON.parse(blogdescription.content[0].text);

        console.log(`✅ Titre: ${results.blogname}`);
        console.log(`✅ Tagline: ${results.blogdescription}`);

        // 5. Informations du thème
        console.log("\n🎨 Récupération des infos du thème...");
        const themeInfo = await client.callTool({
            name: 'adjm__get-theme-info',
            arguments: {}
        });
        results.themeInfo = JSON.parse(themeInfo.content[0].text);
        console.log(`✅ Thème actif: ${results.themeInfo.name} v${results.themeInfo.version}`);

        // 6. Menus
        console.log("\n📋 Récupération des menus...");
        const menus = await client.callTool({
            name: 'adjm__list-menus',
            arguments: {}
        });
        results.menus = JSON.parse(menus.content[0].text);
        console.log(`✅ ${results.menus.length} menus trouvés`);

        // 7. Détection des page builders
        console.log("\n🔧 Détection des page builders...");
        const pageBuilders = await client.callTool({
            name: 'adjm__detect-page-builders',
            arguments: {}
        });
        results.pageBuilders = JSON.parse(pageBuilders.content[0].text);
        console.log(`✅ Page builders détectés`);

        // Sauvegarder dans un fichier
        const outputPath = path.join(__dirname, 'current_site_data.json');
        fs.writeFileSync(outputPath, JSON.stringify(results, null, 2));
        console.log(`\n💾 Données sauvegardées dans: ${outputPath}`);

        // Afficher un résumé
        console.log("\n" + "=".repeat(60));
        console.log("📊 RÉSUMÉ DU SITE ADJM ÉVÉNEMENTIEL");
        console.log("=".repeat(60));
        console.log(`🏠 Nom: ${results.blogname}`);
        console.log(`📝 Tagline: ${results.blogdescription}`);
        console.log(`🔗 URL: ${results.siteInfo.url}`);
        console.log(`🎨 Thème: ${results.themeInfo.name} v${results.themeInfo.version}`);
        console.log(`📄 Pages: ${results.pages.length}`);
        console.log(`📋 Menus: ${results.menus.length}`);
        console.log(`🔧 Page builders: ${Object.keys(results.pageBuilders.detected).filter(k => results.pageBuilders.detected[k]).join(', ') || 'Aucun'}`);
        console.log("=".repeat(60));

    } catch (error) {
        console.error("❌ Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
