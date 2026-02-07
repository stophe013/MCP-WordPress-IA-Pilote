import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

async function main() {
    console.log("========================================");
    console.log("  SUPPRESSION DU CSS FOOTER MODERNE");
    console.log("  Retour aux parametres d'origine");
    console.log("========================================\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "footer-reverter",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        // 1. Recuperer le CSS actuel
        console.log("1. Recuperation du CSS actuel...");
        const currentCSS = await client.callTool({
            name: 'adjm__get-custom-css',
            arguments: {}
        });

        const cssData = JSON.parse(currentCSS.content[0].text);
        let css = cssData.css || "";

        console.log(`   Longueur actuelle: ${css.length} caracteres`);

        // 2. Supprimer le bloc CSS du footer moderne
        // Le bloc commence par "/* ================================================"
        // et contient "FOOTER MODERNE ADJM EVENEMENTIEL"

        const footerCSSStart = css.indexOf("/* ================================================\n   FOOTER MODERNE ADJM EVENEMENTIEL");

        if (footerCSSStart === -1) {
            console.log("\n   Le CSS du footer moderne n'a pas ete trouve.");
            console.log("   Aucune modification necessaire.");
        } else {
            // Trouver la fin du bloc (chercher le dernier commentaire de fin)
            const footerCSSEnd = css.indexOf("/* === FIN DU FOOTER MODERNISE === */", footerCSSStart);

            if (footerCSSEnd !== -1) {
                // Trouver la vraie fin (apres le commentaire de fin + quelques caracteres)
                const realEnd = css.indexOf("\n", footerCSSEnd + 40) + 1;

                // Supprimer le bloc
                const beforeFooter = css.substring(0, footerCSSStart);
                const afterFooter = css.substring(realEnd);

                // Nettoyer les "/* Ajoute via MCP */" en trop
                let newCSS = beforeFooter + afterFooter;

                // Supprimer le dernier "/* Ajoute via MCP */" qui precede le bloc footer
                newCSS = newCSS.replace(/\n\n\/\* Ajouté via MCP \*\/\n\n$/, '');
                newCSS = newCSS.replace(/\n\/\* Ajouté via MCP \*\/\n\n$/, '');

                console.log(`\n2. CSS du footer trouve et supprime.`);
                console.log(`   Nouvelle longueur: ${newCSS.length} caracteres`);
                console.log(`   Reduction: ${css.length - newCSS.length} caracteres`);

                // 3. Appliquer le nouveau CSS
                console.log("\n3. Application du CSS sans le footer moderne...");

                const result = await client.callTool({
                    name: 'adjm__set-custom-css',
                    arguments: { css: newCSS }
                });

                console.log("\nResultat:", result.content[0].text);

                console.log("\n" + "=".repeat(60));
                console.log("  FOOTER REMIS A L'ORIGINE AVEC SUCCES !");
                console.log("=".repeat(60));
                console.log("\nLe CSS du footer moderne a ete supprime.");
                console.log("Le footer utilisera maintenant les styles par defaut.");
                console.log("\nRechargez votre site: https://adjmevenementiel.fr");
            } else {
                console.log("\n   Erreur: Impossible de trouver la fin du bloc CSS footer.");
            }
        }

    } catch (error) {
        console.error("Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
