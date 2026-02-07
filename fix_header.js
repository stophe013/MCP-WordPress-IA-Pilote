import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

// CSS pour corriger le header
const headerCSS = `
/* === CORRECTION HEADER === */

/* Header principal */
header,
.wp-block-template-part[class*="header"],
.site-header,
body > .wp-site-blocks > header {
  background-color: #ffffff !important;
  border-bottom: 3px solid #8895DD !important;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

/* Navigation */
.wp-block-navigation,
.wp-block-navigation__container,
nav {
  background-color: transparent !important;
}

/* Liens du menu */
.wp-block-navigation-item a,
.wp-block-navigation-item__content,
.wp-block-navigation .wp-block-navigation-item a,
header a {
  color: #2B34A6 !important;
  font-family: 'Barlow', sans-serif !important;
  font-weight: 600 !important;
  font-size: 1.1rem !important;
  text-decoration: none !important;
  transition: all 0.3s ease;
}

.wp-block-navigation-item a:hover,
header a:hover {
  color: #132060 !important;
  text-decoration: none !important;
}

/* Logo du site */
.wp-block-site-logo img,
.custom-logo,
.site-logo img {
  max-height: 80px;
  width: auto;
}

/* Titre du site si pas de logo */
.wp-block-site-title a,
.site-title a {
  color: #2B34A6 !important;
  font-family: 'Barlow', sans-serif !important;
  font-weight: 700 !important;
  font-size: 1.8rem !important;
  text-decoration: none !important;
}

/* Description du site */
.wp-block-site-tagline,
.site-description {
  color: #132060 !important;
  font-family: 'Inter', sans-serif !important;
}

/* Bouton hamburger (mobile) */
.wp-block-navigation__responsive-container-open,
.wp-block-navigation__responsive-container-close {
  color: #2B34A6 !important;
}

/* Menu mobile */
.wp-block-navigation__responsive-container.is-menu-open {
  background-color: #ffffff !important;
}

/* Sous-menus */
.wp-block-navigation .wp-block-navigation__submenu-container {
  background-color: #ffffff !important;
  border: 2px solid #8895DD !important;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.wp-block-navigation .wp-block-navigation-item__content {
  padding: 10px 20px;
}

/* Espacement du header */
header,
.wp-block-template-part[class*="header"] {
  padding: 15px 30px !important;
}

/* Sticky header (si activé) */
.wp-block-navigation.is-fixed {
  background-color: rgba(255, 255, 255, 0.98) !important;
  backdrop-filter: blur(10px);
}

/* Responsive */
@media (max-width: 768px) {
  .wp-block-site-logo img,
  .custom-logo {
    max-height: 60px;
  }

  .wp-block-navigation-item a {
    font-size: 1rem !important;
  }
}
`;

async function main() {
    console.log("🎨 Correction du header...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "header-fixer",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        console.log("📝 Application du CSS pour le header...");
        const cssResult = await client.callTool({
            name: 'adjm__append-custom-css',
            arguments: { css: headerCSS }
        });

        console.log("\n" + "=".repeat(80));
        console.log("✅ HEADER CORRIGÉ AVEC SUCCÈS !");
        console.log("=".repeat(80));
        console.log("\n🎨 Styles appliqués :");
        console.log("  • Fond blanc #ffffff");
        console.log("  • Bordure inférieure bleu clair #8895DD");
        console.log("  • Liens menu en bleu royal #2B34A6");
        console.log("  • Hover en bleu foncé #132060");
        console.log("  • Police Barlow pour les liens");
        console.log("\n🌐 Rechargez: https://adjmevenementiel.fr");

    } catch (error) {
        console.error("❌ Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
