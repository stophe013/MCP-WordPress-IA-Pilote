import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

// CSS global pour tout le thème
const globalThemeCSS = `
/* ============================================
   THÈME GLOBAL ADJM ÉVÉNEMENTIEL
   ============================================ */

/* === BODY ET FOND GÉNÉRAL === */
body,
.wp-site-blocks,
html {
  background-color: #FFFDF3 !important;
  color: #132060 !important;
  font-family: 'Inter', sans-serif !important;
}

/* Supprimer les marges/padding par défaut */
body > .wp-site-blocks {
  padding: 0 !important;
  margin: 0 !important;
}

/* === HEADER COMPLET === */
header,
.wp-block-template-part[class*="header"],
body > .wp-site-blocks > header,
.site-header,
#masthead {
  background-color: #ffffff !important;
  border-bottom: 3px solid #8895DD !important;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05) !important;
  padding: 20px 40px !important;
  position: sticky !important;
  top: 0 !important;
  z-index: 999 !important;
  margin: 0 !important;
}

/* === LOGO === */
.wp-block-site-logo,
.wp-block-site-logo a,
.custom-logo-link {
  display: inline-block;
  max-width: 200px;
}

.wp-block-site-logo img,
.custom-logo,
.site-logo img {
  max-height: 70px !important;
  width: auto !important;
  height: auto !important;
  object-fit: contain !important;
}

/* === TITRE DU SITE === */
.wp-block-site-title,
.site-title {
  margin: 0 !important;
  padding: 0 !important;
}

.wp-block-site-title a,
.site-title a,
h1.site-title a {
  color: #2B34A6 !important;
  font-family: 'Barlow', sans-serif !important;
  font-weight: 700 !important;
  font-size: 1.8rem !important;
  text-decoration: none !important;
  transition: color 0.3s ease;
}

.wp-block-site-title a:hover,
.site-title a:hover {
  color: #132060 !important;
}

/* === NAVIGATION === */
.wp-block-navigation,
.wp-block-navigation__container,
.wp-block-navigation-item,
nav,
.main-navigation {
  background-color: transparent !important;
  border: none !important;
}

/* Liens du menu */
.wp-block-navigation-item a,
.wp-block-navigation-item__content,
.wp-block-navigation .wp-block-navigation-item > a,
.main-navigation a,
header nav a,
.menu a {
  color: #2B34A6 !important;
  font-family: 'Barlow', sans-serif !important;
  font-weight: 600 !important;
  font-size: 1.1rem !important;
  text-decoration: none !important;
  padding: 10px 15px !important;
  transition: all 0.3s ease;
  display: inline-block;
}

.wp-block-navigation-item a:hover,
.main-navigation a:hover,
header nav a:hover {
  color: #132060 !important;
  background-color: rgba(136, 149, 221, 0.1) !important;
  border-radius: 6px;
}

/* Sous-menus */
.wp-block-navigation__submenu-container,
.sub-menu,
.wp-block-navigation .has-child .wp-block-navigation__submenu-container {
  background-color: #ffffff !important;
  border: 2px solid #8895DD !important;
  border-radius: 8px !important;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
  padding: 10px 0 !important;
  margin-top: 10px !important;
}

.wp-block-navigation__submenu-container a,
.sub-menu a {
  padding: 12px 20px !important;
  display: block !important;
}

/* === FOOTER === */
footer,
.wp-block-template-part[class*="footer"],
.site-footer,
#colophon {
  background-color: #132060 !important;
  color: #FFFDF3 !important;
  padding: 60px 40px 30px !important;
  margin-top: 0 !important;
}

footer h1, footer h2, footer h3, footer h4, footer h5, footer h6,
.site-footer h1, .site-footer h2, .site-footer h3 {
  color: #FFFDF3 !important;
  font-family: 'Barlow', sans-serif !important;
}

footer p,
.site-footer p {
  color: rgba(255, 253, 243, 0.9) !important;
  font-family: 'Inter', sans-serif !important;
}

footer a,
.site-footer a {
  color: #8895DD !important;
  text-decoration: none !important;
  transition: color 0.3s ease;
}

footer a:hover,
.site-footer a:hover {
  color: #FFFDF3 !important;
}

/* === BOUTONS GLOBAUX === */
.wp-element-button,
.wp-block-button__link,
button:not(.components-button),
input[type="submit"],
.button,
.btn {
  background-color: #2B34A6 !important;
  color: #FFFDF3 !important;
  border: none !important;
  border-radius: 8px !important;
  padding: 14px 32px !important;
  font-family: 'Barlow', sans-serif !important;
  font-weight: 600 !important;
  font-size: 1.05rem !important;
  cursor: pointer !important;
  transition: all 0.3s ease !important;
  text-decoration: none !important;
  display: inline-block !important;
}

.wp-element-button:hover,
.wp-block-button__link:hover,
button:not(.components-button):hover,
input[type="submit"]:hover {
  background-color: #132060 !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(43, 52, 166, 0.3) !important;
}

/* === TITRES GLOBAUX === */
h1, h2, h3, h4, h5, h6,
.wp-block-heading {
  font-family: 'Barlow', sans-serif !important;
  color: #2B34A6 !important;
  font-weight: 700 !important;
}

/* === LIENS GLOBAUX === */
a {
  color: #2B34A6 !important;
  transition: color 0.3s ease;
}

a:hover {
  color: #132060 !important;
}

/* === SECTIONS === */
.wp-block-group,
section {
  margin: 0 !important;
}

/* Contenu principal */
main,
.site-main,
.wp-site-blocks > * {
  background-color: inherit !important;
}

/* === MENU MOBILE === */
.wp-block-navigation__responsive-container-open,
.wp-block-navigation__responsive-container-close,
.mobile-menu-toggle {
  color: #2B34A6 !important;
  background: none !important;
  border: 2px solid #2B34A6 !important;
  border-radius: 6px;
  padding: 8px 12px !important;
}

.wp-block-navigation__responsive-container.is-menu-open,
.mobile-menu-container {
  background-color: #ffffff !important;
  border-left: 3px solid #8895DD !important;
}

/* === ADMIN BAR ADJUSTMENTS === */
body.admin-bar header {
  top: 32px !important;
}

@media screen and (max-width: 782px) {
  body.admin-bar header {
    top: 46px !important;
  }
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
  header,
  .site-header {
    padding: 15px 20px !important;
  }

  .wp-block-site-logo img,
  .custom-logo {
    max-height: 60px !important;
  }

  .wp-block-navigation-item a {
    font-size: 1rem !important;
    padding: 12px 20px !important;
  }
}

/* === CORRECTIONS SUPPLÉMENTAIRES === */

/* Forcer les couleurs du header même en mode édition */
.editor-styles-wrapper header,
.block-editor-block-list__layout > header {
  background-color: #ffffff !important;
  border-bottom: 3px solid #8895DD !important;
}

/* Assurer que les blocs full-width ont les bonnes couleurs */
.alignfull {
  width: 100vw !important;
  max-width: 100vw !important;
  margin-left: calc(50% - 50vw) !important;
  margin-right: calc(50% - 50vw) !important;
}

/* Fix pour les groupes avec background */
.wp-block-group.has-background {
  padding: 0 !important;
}

/* Espacement cohérent */
.wp-block-group > .wp-block-group__inner-container {
  padding: inherit !important;
}

/* === SIDEBAR (si utilisée) === */
aside,
.sidebar,
.widget-area {
  background-color: #ffffff !important;
  padding: 30px !important;
  border-radius: 12px !important;
}

/* === FORMULAIRES === */
input[type="text"],
input[type="email"],
input[type="tel"],
input[type="url"],
input[type="password"],
input[type="search"],
textarea,
select {
  border: 2px solid #8895DD !important;
  border-radius: 6px !important;
  padding: 12px 16px !important;
  font-family: 'Inter', sans-serif !important;
  color: #132060 !important;
  background-color: #FFFDF3 !important;
}

input:focus,
textarea:focus,
select:focus {
  outline: none !important;
  border-color: #2B34A6 !important;
  box-shadow: 0 0 0 3px rgba(43, 52, 166, 0.1) !important;
}

/* === CORRECTIONS SPÉCIFIQUES TWENTY TWENTY-FOUR === */

/* Reset des styles par défaut du thème */
.wp-block-group.is-layout-constrained > :where(:not(.alignleft):not(.alignright):not(.alignfull)) {
  max-width: 1200px !important;
  margin-left: auto !important;
  margin-right: auto !important;
}

/* Espacements */
.wp-block-group {
  margin-block-start: 0 !important;
  margin-block-end: 0 !important;
}
`;

async function main() {
    console.log("🎨 Application du thème global ADJM...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "global-theme-applier",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        console.log("📝 Application du CSS global du thème...");
        const cssResult = await client.callTool({
            name: 'adjm__append-custom-css',
            arguments: { css: globalThemeCSS }
        });

        console.log("\n" + "=".repeat(80));
        console.log("✅ THÈME GLOBAL ADJM APPLIQUÉ AVEC SUCCÈS !");
        console.log("=".repeat(80));
        console.log("\n🎨 Styles appliqués :");
        console.log("  • Body : Fond crème #FFFDF3");
        console.log("  • Header : Fond blanc + bordure bleu clair sticky");
        console.log("  • Navigation : Liens bleu royal avec hover");
        console.log("  • Footer : Fond bleu foncé #132060");
        console.log("  • Boutons : Bleu royal avec effects hover");
        console.log("  • Typographie : Barlow (titres) + Inter (texte)");
        console.log("\n🔒 CSS renforcé avec !important pour forcer les styles");
        console.log("\n🌐 Rechargez: https://adjmevenementiel.fr");
        console.log("\n💡 Conseil: CTRL+F5 pour vider le cache !");

    } catch (error) {
        console.error("❌ Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
