import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

// CSS pour un header moderne, compact avec fond #2B34A6
const modernHeaderCSS = `
/* ================================================
   HEADER MODERNISE - Compact & Elegant
   Couleur de fond: #2B34A6
   ================================================ */

/* Reset et container principal du header */
header,
.wp-block-template-part.site-header,
.site-header,
body > .wp-site-blocks > header,
.wp-block-template-part[class*="header"] {
    background-color: #2B34A6 !important;
    padding: 0 !important;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 4px 20px rgba(43, 52, 166, 0.3);
    border-bottom: none !important;
}

/* Inner wrapper - structure compacte */
.site-header > .wp-block-group,
.site-header .wp-block-group:first-child,
header > .wp-block-group,
header .wp-block-group.alignfull {
    background-color: #2B34A6 !important;
    padding: 12px 40px !important;
    max-width: 100%;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Masquer la top bar (reseaux sociaux/telephone) pour un look epure */
.site-header .wp-block-group .wp-block-group:has(.wp-block-social-links),
.header-top-bar,
.top-bar,
header > .wp-block-group > .wp-block-group:first-child:has(.wp-block-social-link) {
    display: none !important;
}

/* Logo compact */
.wp-block-site-logo,
.custom-logo-link,
.site-logo {
    max-height: 50px !important;
    display: flex;
    align-items: center;
}

.wp-block-site-logo img,
.custom-logo,
.site-logo img {
    height: 50px !important;
    width: auto !important;
    max-width: 180px;
    object-fit: contain;
    filter: brightness(0) invert(1); /* Logo en blanc */
    transition: transform 0.3s ease;
}

.wp-block-site-logo:hover img {
    transform: scale(1.02);
}

/* Titre du site (si pas de logo) */
.wp-block-site-title a,
.site-title a {
    color: #ffffff !important;
    font-family: 'Barlow', sans-serif !important;
    font-size: 1.6rem !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    letter-spacing: -0.5px;
}

/* ================================================
   NAVIGATION PRINCIPALE
   ================================================ */

.wp-block-navigation,
.wp-block-navigation__container,
nav.wp-block-navigation {
    background-color: transparent !important;
}

/* Conteneur des items de nav */
.wp-block-navigation__container {
    gap: 8px;
}

/* Liens de navigation */
.wp-block-navigation-item__content,
.wp-block-navigation-item a,
.wp-block-navigation .wp-block-navigation-item a {
    font-family: 'Barlow', sans-serif !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    font-size: 0.85rem !important;
    color: #ffffff !important;
    padding: 10px 18px !important;
    transition: all 0.3s ease !important;
    letter-spacing: 0.8px;
    border-radius: 4px;
    text-decoration: none !important;
}

.wp-block-navigation-item__content:hover,
.wp-block-navigation-item a:hover {
    color: #C6A87C !important;
    background-color: rgba(255, 255, 255, 0.1) !important;
}

/* Item actif/courant */
.wp-block-navigation-item.current-menu-item .wp-block-navigation-item__content,
.wp-block-navigation-item.current-menu-item a,
.current_page_item .wp-block-navigation-item__content {
    color: #C6A87C !important;
    position: relative;
}

.wp-block-navigation-item.current-menu-item .wp-block-navigation-item__content::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 30px;
    height: 2px;
    background-color: #C6A87C;
    border-radius: 2px;
}

/* Bouton CTA - Devis Gratuit (dernier item) */
.wp-block-navigation .wp-block-navigation-item:last-child .wp-block-navigation-item__content,
.wp-block-navigation .wp-block-navigation-item:last-child a,
.menu-item-type-custom:last-child .wp-block-navigation-item__content {
    background: linear-gradient(135deg, #C6A87C 0%, #D4B896 100%) !important;
    color: #2B34A6 !important;
    padding: 12px 28px !important;
    border-radius: 50px !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 15px rgba(198, 168, 124, 0.3);
}

.wp-block-navigation .wp-block-navigation-item:last-child .wp-block-navigation-item__content:hover,
.wp-block-navigation .wp-block-navigation-item:last-child a:hover {
    background: linear-gradient(135deg, #ffffff 0%, #f5f5f5 100%) !important;
    color: #2B34A6 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 255, 255, 0.4);
}

/* Supprimer les points separateurs */
.wp-block-navigation-item::after,
.wp-block-navigation-item .wp-block-navigation-item__separator {
    display: none !important;
}

/* ================================================
   SOUS-MENUS DROPDOWNS
   ================================================ */

.wp-block-navigation__submenu-container,
.wp-block-navigation .has-child .wp-block-navigation__submenu-container {
    background-color: #2B34A6 !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    padding: 10px 0 !important;
    min-width: 240px;
    margin-top: 8px;
}

.wp-block-navigation__submenu-container .wp-block-navigation-item__content,
.wp-block-navigation__submenu-container a {
    font-size: 0.82rem !important;
    text-transform: none !important;
    padding: 12px 24px !important;
    letter-spacing: 0.3px;
    border-radius: 0 !important;
}

.wp-block-navigation__submenu-container .wp-block-navigation-item__content:hover,
.wp-block-navigation__submenu-container a:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
    padding-left: 28px !important;
}

/* Fleche dropdown */
.wp-block-navigation-submenu__toggle svg,
.wp-block-navigation-item__has-submenu-icon svg {
    fill: #ffffff !important;
    width: 12px;
    height: 12px;
}

/* ================================================
   MENU MOBILE / HAMBURGER
   ================================================ */

/* Icone hamburger */
.wp-block-navigation__responsive-container-open,
button.wp-block-navigation__responsive-container-open {
    color: #ffffff !important;
    background-color: transparent !important;
    border: none !important;
}

.wp-block-navigation__responsive-container-open svg {
    fill: #ffffff !important;
    width: 28px;
    height: 28px;
}

/* Menu mobile ouvert */
.wp-block-navigation__responsive-container.is-menu-open {
    background-color: #2B34A6 !important;
    padding: 30px !important;
}

/* Bouton fermer */
.wp-block-navigation__responsive-container-close,
button.wp-block-navigation__responsive-container-close {
    color: #ffffff !important;
}

.wp-block-navigation__responsive-container-close svg {
    fill: #ffffff !important;
}

/* Items mobile */
.wp-block-navigation__responsive-container.is-menu-open .wp-block-navigation-item__content {
    font-size: 1.1rem !important;
    padding: 16px 20px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

/* ================================================
   RESEAUX SOCIAUX DANS HEADER (si gardes)
   ================================================ */

.site-header .wp-block-social-links .wp-block-social-link,
header .wp-block-social-links .wp-block-social-link {
    background-color: rgba(255, 255, 255, 0.15) !important;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.site-header .wp-block-social-links .wp-block-social-link:hover,
header .wp-block-social-links .wp-block-social-link:hover {
    background-color: #C6A87C !important;
    transform: translateY(-2px);
}

.site-header .wp-block-social-links svg,
header .wp-block-social-links svg {
    fill: #ffffff !important;
}

/* ================================================
   RESPONSIVE
   ================================================ */

@media (max-width: 1024px) {
    .site-header > .wp-block-group,
    header > .wp-block-group {
        padding: 10px 24px !important;
    }

    .wp-block-navigation-item__content {
        font-size: 0.8rem !important;
        padding: 8px 14px !important;
    }
}

@media (max-width: 768px) {
    .site-header > .wp-block-group,
    header > .wp-block-group {
        padding: 10px 16px !important;
    }

    .wp-block-site-logo img,
    .custom-logo {
        height: 40px !important;
    }

    .wp-block-navigation-item__content {
        font-size: 1rem !important;
        padding: 14px 20px !important;
    }
}

/* Compensation barre admin WordPress */
body.logged-in header,
body.logged-in .site-header,
body.admin-bar header,
body.admin-bar .site-header {
    top: 32px;
}

@media (max-width: 782px) {
    body.logged-in header,
    body.logged-in .site-header,
    body.admin-bar header,
    body.admin-bar .site-header {
        top: 46px;
    }
}

/* ================================================
   ANIMATIONS SUBTILES
   ================================================ */

@keyframes headerFadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

header,
.site-header {
    animation: headerFadeIn 0.5s ease-out;
}
`;

async function main() {
    console.log("========================================");
    console.log("  MODERNISATION DU HEADER ADJM");
    console.log("  Couleur: #2B34A6 | Style: Compact");
    console.log("========================================\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "header-modernizer",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        console.log("Connexion au serveur MCP WordPress...");
        console.log("Application du CSS moderne pour le header...\n");

        const cssResult = await client.callTool({
            name: 'adjm__append-custom-css',
            arguments: { css: modernHeaderCSS }
        });

        console.log("\n" + "=".repeat(60));
        console.log("  HEADER MODERNISE AVEC SUCCES !");
        console.log("=".repeat(60));
        console.log("\nChangements appliques:");
        console.log("  - Fond bleu royal #2B34A6");
        console.log("  - Header compact (60px de hauteur)");
        console.log("  - Logo en blanc (filtre invert)");
        console.log("  - Navigation en blanc avec hover dore");
        console.log("  - Bouton CTA (Devis) en dore #C6A87C");
        console.log("  - Position sticky (reste en haut au scroll)");
        console.log("  - Sous-menus elegants");
        console.log("  - Menu mobile optimise");
        console.log("  - Animations subtiles");
        console.log("\nRechargez votre site: https://adjmevenementiel.fr");

    } catch (error) {
        console.error("Erreur:", error.message);
        console.error("\nAssurez-vous que:");
        console.error("  1. Le serveur MCP est configure dans .env");
        console.error("  2. Le plugin ADJM MCP Abilities est actif sur WordPress");
        console.error("  3. L'ability 'adjm/append-custom-css' existe");
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
