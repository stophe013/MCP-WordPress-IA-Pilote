import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

// CSS pour un footer moderne et elegant
// Charte graphique ADJM: Bleu #2B34A6, Dore #C6A87C, Bleu fonce #132060
const modernFooterCSS = `
/* ================================================
   FOOTER MODERNE ADJM EVENEMENTIEL
   Design: Elegant, Contraste WCAG AA+
   Couleurs: Bleu fonce #132060, Dore #C6A87C
   ================================================ */

/* === RESET ET STRUCTURE PRINCIPALE === */
footer,
.wp-block-template-part.site-footer,
.site-footer,
body > .wp-site-blocks > footer,
.wp-block-template-part[class*="footer"] {
    background: linear-gradient(180deg, #132060 0%, #0D1642 100%) !important;
    color: #ffffff !important;
    padding: 0 !important;
    margin-top: 0 !important;
    position: relative;
    overflow: hidden;
}

/* Effet decoratif en haut du footer */
footer::before,
.wp-block-template-part[class*="footer"]::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #C6A87C 0%, #D4B896 50%, #C6A87C 100%);
}

/* === CONTAINER PRINCIPAL === */
footer > .wp-block-group,
footer .wp-block-group.alignfull,
.site-footer > .wp-block-group {
    background: transparent !important;
    max-width: 1200px;
    margin: 0 auto;
    padding: 70px 40px 30px !important;
}

/* === ZONE SUPERIEURE (Colonnes) === */
footer .wp-block-columns,
.site-footer .wp-block-columns {
    gap: 50px;
    margin-bottom: 50px;
    align-items: flex-start;
}

footer .wp-block-column,
.site-footer .wp-block-column {
    background: transparent !important;
    box-shadow: none !important;
    padding: 0 !important;
    border-radius: 0 !important;
}

/* === LOGO ET BRANDING === */
footer .wp-block-site-logo img,
footer .custom-logo,
.site-footer .wp-block-site-logo img {
    max-height: 60px !important;
    width: auto !important;
    filter: brightness(0) invert(1);
    margin-bottom: 20px;
    transition: opacity 0.3s ease;
}

footer .wp-block-site-logo:hover img {
    opacity: 0.85;
}

footer .wp-block-site-title a,
.site-footer .wp-block-site-title a {
    color: #ffffff !important;
    font-family: 'Barlow', sans-serif !important;
    font-size: 1.8rem !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    display: block;
    margin-bottom: 15px;
}

/* Tagline / Description */
footer .wp-block-site-tagline,
.site-footer .wp-block-site-tagline {
    color: rgba(255, 255, 255, 0.7) !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 25px;
}

/* === TITRES DES SECTIONS === */
footer h2, footer h3, footer h4, footer h5, footer h6,
.site-footer h2, .site-footer h3, .site-footer h4,
footer .wp-block-heading,
.site-footer .wp-block-heading {
    color: #C6A87C !important;
    font-family: 'Barlow', sans-serif !important;
    font-weight: 700 !important;
    font-size: 1.15rem !important;
    text-transform: uppercase !important;
    letter-spacing: 1.5px !important;
    margin-bottom: 25px !important;
    position: relative;
    padding-bottom: 12px;
}

footer h3::after, footer h4::after,
.site-footer h3::after, .site-footer h4::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 2px;
    background: linear-gradient(90deg, #C6A87C, transparent);
    border-radius: 2px;
}

/* === TEXTE ET PARAGRAPHES === */
footer p,
.site-footer p {
    color: rgba(255, 255, 255, 0.85) !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 0.95rem !important;
    line-height: 1.7 !important;
    margin-bottom: 15px;
}

/* === LIENS NAVIGATION FOOTER === */
footer a,
.site-footer a,
footer .wp-block-navigation-item a,
footer .wp-block-navigation-item__content {
    color: rgba(255, 255, 255, 0.85) !important;
    font-family: 'Inter', sans-serif !important;
    font-weight: 400 !important;
    font-size: 0.95rem !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
    display: inline-block;
    padding: 6px 0 !important;
    position: relative;
}

footer a:hover,
.site-footer a:hover,
footer .wp-block-navigation-item a:hover {
    color: #C6A87C !important;
    padding-left: 8px !important;
}

/* Indicateur hover elegant */
footer a::before,
.site-footer .wp-block-navigation-item a::before {
    content: '';
    position: absolute;
    left: -15px;
    top: 50%;
    transform: translateY(-50%);
    width: 0;
    height: 2px;
    background-color: #C6A87C;
    transition: width 0.3s ease;
}

footer a:hover::before,
.site-footer .wp-block-navigation-item a:hover::before {
    width: 10px;
}

/* Navigation bloc dans footer */
footer .wp-block-navigation,
.site-footer .wp-block-navigation {
    background: transparent !important;
}

footer .wp-block-navigation__container {
    flex-direction: column !important;
    gap: 0 !important;
}

footer .wp-block-navigation-item {
    display: block !important;
}

/* === LISTES === */
footer ul, footer ol,
.site-footer ul, .site-footer ol {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

footer li,
.site-footer li {
    margin-bottom: 12px;
}

/* === BOUTONS FOOTER === */
footer .wp-block-button__link,
footer .wp-element-button,
.site-footer .wp-block-button__link {
    background: linear-gradient(135deg, #C6A87C 0%, #D4B896 100%) !important;
    color: #132060 !important;
    font-family: 'Barlow', sans-serif !important;
    font-weight: 700 !important;
    font-size: 0.9rem !important;
    padding: 14px 32px !important;
    border-radius: 50px !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    border: none !important;
    box-shadow: 0 4px 20px rgba(198, 168, 124, 0.3);
    transition: all 0.3s ease !important;
}

footer .wp-block-button__link:hover,
footer .wp-element-button:hover {
    background: linear-gradient(135deg, #ffffff 0%, #f5f5f5 100%) !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 8px 30px rgba(255, 255, 255, 0.3) !important;
}

/* Bouton outline */
footer .wp-block-button.is-style-outline .wp-block-button__link {
    background: transparent !important;
    border: 2px solid #C6A87C !important;
    color: #C6A87C !important;
}

footer .wp-block-button.is-style-outline .wp-block-button__link:hover {
    background: #C6A87C !important;
    color: #132060 !important;
}

/* === RESEAUX SOCIAUX === */
footer .wp-block-social-links,
.site-footer .wp-block-social-links {
    gap: 12px !important;
    margin-top: 20px;
}

footer .wp-block-social-link,
.site-footer .wp-block-social-link {
    background: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 50% !important;
    width: 44px !important;
    height: 44px !important;
    transition: all 0.3s ease !important;
}

footer .wp-block-social-link:hover,
.site-footer .wp-block-social-link:hover {
    background: #C6A87C !important;
    border-color: #C6A87C !important;
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0 8px 25px rgba(198, 168, 124, 0.4);
}

footer .wp-block-social-link a,
.site-footer .wp-block-social-link a {
    padding: 0 !important;
    display: flex !important;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

footer .wp-block-social-link svg,
.site-footer .wp-block-social-link svg {
    fill: #ffffff !important;
    width: 20px !important;
    height: 20px !important;
    transition: fill 0.3s ease;
}

footer .wp-block-social-link:hover svg {
    fill: #132060 !important;
}

/* === INFORMATIONS DE CONTACT === */
footer .contact-info,
footer address,
.site-footer address {
    font-style: normal;
    color: rgba(255, 255, 255, 0.85);
    line-height: 2;
}

footer .contact-info a,
footer address a {
    color: #C6A87C !important;
}

/* === SEPARATEUR === */
footer .wp-block-separator,
footer hr,
.site-footer .wp-block-separator {
    border: none !important;
    height: 1px !important;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent) !important;
    margin: 40px 0 !important;
}

/* === BARRE DE COPYRIGHT === */
footer .copyright-bar,
footer > .wp-block-group > .wp-block-group:last-child,
footer .footer-bottom,
.site-footer .footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 25px !important;
    margin-top: 30px;
    text-align: center;
}

footer .copyright-bar p,
footer .footer-bottom p {
    font-size: 0.85rem !important;
    color: rgba(255, 255, 255, 0.6) !important;
    margin: 0 !important;
}

footer .copyright-bar a,
footer .footer-bottom a {
    color: #C6A87C !important;
    font-size: 0.85rem !important;
}

/* === PATTERN DECORATIF OPTIONNEL === */
footer::after {
    content: '';
    position: absolute;
    bottom: 0;
    right: 0;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(198,168,124,0.05) 0%, transparent 70%);
    pointer-events: none;
}

/* === ANIMATIONS === */
@keyframes footerFadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

footer .wp-block-column {
    animation: footerFadeIn 0.6s ease-out forwards;
}

footer .wp-block-column:nth-child(1) { animation-delay: 0.1s; }
footer .wp-block-column:nth-child(2) { animation-delay: 0.2s; }
footer .wp-block-column:nth-child(3) { animation-delay: 0.3s; }
footer .wp-block-column:nth-child(4) { animation-delay: 0.4s; }

/* === RESPONSIVE === */
@media (max-width: 1024px) {
    footer > .wp-block-group,
    .site-footer > .wp-block-group {
        padding: 50px 30px 25px !important;
    }

    footer .wp-block-columns {
        gap: 40px;
    }
}

@media (max-width: 768px) {
    footer > .wp-block-group,
    .site-footer > .wp-block-group {
        padding: 40px 20px 20px !important;
    }

    footer .wp-block-columns {
        flex-direction: column !important;
        gap: 35px;
    }

    footer .wp-block-column {
        width: 100% !important;
        flex-basis: 100% !important;
        text-align: center;
    }

    footer h3::after, footer h4::after {
        left: 50%;
        transform: translateX(-50%);
    }

    footer a::before {
        display: none;
    }

    footer a:hover {
        padding-left: 0 !important;
    }

    footer .wp-block-social-links {
        justify-content: center;
    }

    footer .wp-block-site-logo {
        display: flex;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    footer h3, footer h4,
    .site-footer h3, .site-footer h4 {
        font-size: 1rem !important;
    }

    footer p, footer a,
    .site-footer p, .site-footer a {
        font-size: 0.9rem !important;
    }

    footer .wp-block-button__link {
        padding: 12px 24px !important;
        font-size: 0.85rem !important;
    }

    footer .wp-block-social-link {
        width: 40px !important;
        height: 40px !important;
    }
}

/* === ACCESSIBILITE : Focus visible === */
footer a:focus-visible,
footer button:focus-visible,
footer .wp-block-button__link:focus-visible,
footer .wp-block-social-link:focus-visible {
    outline: 2px solid #C6A87C !important;
    outline-offset: 3px !important;
}

/* === CONTRAST BOOST pour texte clair sur fond sombre === */
footer strong, footer b {
    color: #ffffff !important;
    font-weight: 600;
}

footer em, footer i {
    color: rgba(255, 255, 255, 0.9);
}

/* === WIDGET AREAS (si utilises) === */
footer .widget-area,
footer .footer-widget {
    margin-bottom: 20px;
}

footer .widget-title {
    color: #C6A87C !important;
    font-family: 'Barlow', sans-serif !important;
    font-weight: 700 !important;
    font-size: 1.1rem !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    margin-bottom: 20px !important;
}

/* === NEWSLETTER (si present) === */
footer .newsletter-form input[type="email"],
footer input[type="email"] {
    background: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    border-radius: 50px !important;
    padding: 14px 24px !important;
    color: #ffffff !important;
    font-family: 'Inter', sans-serif !important;
    width: 100%;
    margin-bottom: 12px;
    transition: all 0.3s ease;
}

footer input[type="email"]:focus {
    border-color: #C6A87C !important;
    background: rgba(255, 255, 255, 0.15) !important;
    outline: none;
}

footer input[type="email"]::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

/* === FIN DU FOOTER MODERNISE === */
`;

async function main() {
    console.log("========================================");
    console.log("  MODERNISATION DU FOOTER ADJM");
    console.log("  Style: Moderne & Elegant");
    console.log("  Couleurs: Bleu fonce #132060, Dore #C6A87C");
    console.log("========================================\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "footer-modernizer",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        console.log("Connexion au serveur MCP WordPress...");
        console.log("Application du CSS moderne pour le footer...\n");

        const cssResult = await client.callTool({
            name: 'adjm__append-custom-css',
            arguments: { css: modernFooterCSS }
        });

        console.log("Resultat:", JSON.stringify(cssResult, null, 2));

        console.log("\n" + "=".repeat(60));
        console.log("  FOOTER MODERNISE AVEC SUCCES !");
        console.log("=".repeat(60));
        console.log("\nChangements appliques:");
        console.log("  - Fond bleu fonce degrade (#132060 -> #0D1642)");
        console.log("  - Barre doree decorative en haut");
        console.log("  - Titres en dore #C6A87C avec soulignement");
        console.log("  - Texte blanc avec bonne lisibilite (WCAG AA+)");
        console.log("  - Liens avec effet hover elegant");
        console.log("  - Boutons CTA en dore avec ombre");
        console.log("  - Reseaux sociaux avec effet hover");
        console.log("  - Separateur elegant");
        console.log("  - Barre copyright discrete");
        console.log("  - Animations subtiles sur les colonnes");
        console.log("  - Design responsive (mobile-friendly)");
        console.log("  - Focus visible pour accessibilite");
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
