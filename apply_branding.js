import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

const customCSS = `
/* === CHARTE GRAPHIQUE ADJM ÉVÉNEMENTIEL === */

/* Import des polices Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap');

/* Couleurs principales de la charte */
:root {
  --adjm-creme: #FFFDF3;
  --adjm-bleu-clair: #8895DD;
  --adjm-bleu-royal: #2B34A6;
  --adjm-bleu-fonce: #132060;

  /* Variables WordPress */
  --wp--preset--color--primary: var(--adjm-bleu-royal);
  --wp--preset--color--secondary: var(--adjm-bleu-clair);
  --wp--preset--color--background: var(--adjm-creme);
  --wp--preset--color--accent: var(--adjm-bleu-fonce);

  /* Typographie */
  --wp--preset--font-family--heading: 'Barlow', sans-serif;
  --wp--preset--font-family--body: 'Inter', sans-serif;
}

/* === TYPOGRAPHIE === */
body {
  font-family: 'Inter', sans-serif !important;
  color: var(--adjm-bleu-fonce);
  background-color: var(--adjm-creme);
  line-height: 1.7;
}

h1, h2, h3, h4, h5, h6,
.wp-block-heading {
  font-family: 'Barlow', sans-serif !important;
  color: var(--adjm-bleu-royal);
  font-weight: 700;
  line-height: 1.3;
}

h1 { font-size: 3rem; }
h2 { font-size: 2.5rem; }
h3 { font-size: 2rem; }
h4 { font-size: 1.5rem; }
h5 { font-size: 1.25rem; }
h6 { font-size: 1.1rem; }

/* === BOUTONS === */
.wp-block-button__link,
.wp-element-button,
button:not(.components-button),
input[type="submit"],
.button,
.btn {
  background-color: var(--adjm-bleu-royal) !important;
  color: white !important;
  font-family: 'Barlow', sans-serif;
  font-weight: 600;
  border-radius: 8px;
  padding: 14px 32px;
  border: none;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-block;
  cursor: pointer;
  font-size: 1rem;
}

.wp-block-button__link:hover,
.wp-element-button:hover,
button:not(.components-button):hover,
input[type="submit"]:hover,
.button:hover,
.btn:hover {
  background-color: var(--adjm-bleu-fonce) !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(43, 52, 166, 0.3);
}

/* Bouton secondaire */
.wp-block-button.is-style-outline .wp-block-button__link {
  background-color: transparent !important;
  border: 2px solid var(--adjm-bleu-royal);
  color: var(--adjm-bleu-royal) !important;
}

.wp-block-button.is-style-outline .wp-block-button__link:hover {
  background-color: var(--adjm-bleu-royal) !important;
  color: white !important;
}

/* === LIENS === */
a {
  color: var(--adjm-bleu-royal);
  text-decoration: none;
  transition: color 0.3s ease;
}

a:hover {
  color: var(--adjm-bleu-fonce);
  text-decoration: underline;
}

/* === EN-TÊTE / NAVIGATION === */
.wp-block-navigation,
.wp-block-navigation-item,
header,
.site-header {
  background-color: white;
}

.wp-block-navigation:not(.has-background) {
  border-bottom: 2px solid var(--adjm-bleu-clair);
}

.wp-block-navigation-item a {
  color: var(--adjm-bleu-fonce);
  font-family: 'Barlow', sans-serif;
  font-weight: 500;
  font-size: 1.05rem;
}

.wp-block-navigation-item a:hover {
  color: var(--adjm-bleu-royal);
}

/* === SECTIONS === */
.wp-block-group,
section {
  padding: 60px 20px;
}

.wp-block-group.has-background,
.wp-block-cover {
  border-radius: 0;
}

.wp-block-group.alignfull {
  padding-left: 0;
  padding-right: 0;
}

/* === FOOTER === */
footer,
.wp-block-template-part[class*="footer"] {
  background-color: var(--adjm-bleu-fonce) !important;
  color: white !important;
  padding: 60px 20px 30px;
}

footer h2, footer h3, footer h4, footer h5, footer h6,
.wp-block-template-part[class*="footer"] h2,
.wp-block-template-part[class*="footer"] h3 {
  color: white !important;
}

footer a,
.wp-block-template-part[class*="footer"] a {
  color: var(--adjm-bleu-clair);
}

footer a:hover,
.wp-block-template-part[class*="footer"] a:hover {
  color: white;
}

footer p,
.wp-block-template-part[class*="footer"] p {
  color: rgba(255, 255, 255, 0.9);
}

/* === CARTES / COLONNES === */
.wp-block-column,
.service-card,
.wp-block-group.is-style-card {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(19, 32, 96, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.wp-block-column:hover,
.service-card:hover,
.wp-block-group.is-style-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 20px rgba(43, 52, 166, 0.2);
}

/* === COVER / HERO === */
.wp-block-cover {
  min-height: 500px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.wp-block-cover.has-background-dim:before {
  background-color: var(--adjm-bleu-royal);
  opacity: 0.7;
}

.wp-block-cover h1,
.wp-block-cover h2,
.wp-block-cover p {
  color: white !important;
}

/* === LISTES === */
ul, ol {
  line-height: 1.8;
}

.wp-block-list li {
  margin-bottom: 0.5rem;
}

/* === IMAGES === */
.wp-block-image img {
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(19, 32, 96, 0.1);
}

.wp-block-image.is-style-rounded img {
  border-radius: 50%;
}

/* === GALERIE === */
.wp-block-gallery {
  gap: 20px;
}

.wp-block-gallery .wp-block-image img {
  transition: transform 0.3s ease;
}

.wp-block-gallery .wp-block-image:hover img {
  transform: scale(1.05);
}

/* === SÉPARATEUR === */
.wp-block-separator {
  border-color: var(--adjm-bleu-clair);
  opacity: 0.5;
}

/* === CITATION === */
.wp-block-quote,
.wp-block-pullquote {
  border-left-color: var(--adjm-bleu-royal);
}

.wp-block-quote cite,
.wp-block-pullquote cite {
  color: var(--adjm-bleu-royal);
  font-family: 'Barlow', sans-serif;
  font-weight: 600;
}

/* === ANIMATIONS === */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.animate-on-scroll {
  animation: fadeInUp 0.8s ease-out;
}

.fade-in {
  animation: fadeIn 1s ease-in;
}

/* === FORMULAIRES === */
input[type="text"],
input[type="email"],
input[type="tel"],
input[type="url"],
input[type="password"],
input[type="search"],
input[type="number"],
textarea,
select {
  border: 2px solid var(--adjm-bleu-clair);
  border-radius: 6px;
  padding: 12px 16px;
  font-family: 'Inter', sans-serif;
  color: var(--adjm-bleu-fonce);
  transition: border-color 0.3s ease;
}

input:focus,
textarea:focus,
select:focus {
  outline: none;
  border-color: var(--adjm-bleu-royal);
  box-shadow: 0 0 0 3px rgba(43, 52, 166, 0.1);
}

/* === CONTENEUR === */
.wp-block-group.alignwide,
.alignwide {
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
  h1 { font-size: 2.2rem; }
  h2 { font-size: 1.8rem; }
  h3 { font-size: 1.5rem; }

  .wp-block-cover {
    min-height: 400px;
  }

  .wp-block-group,
  section {
    padding: 40px 15px;
  }

  .wp-block-column {
    margin-bottom: 20px;
  }
}

/* === ACCESSIBILITÉ === */
:focus-visible {
  outline: 3px solid var(--adjm-bleu-royal);
  outline-offset: 2px;
}

/* === UTILITAIRES === */
.text-center {
  text-align: center;
}

.text-primary {
  color: var(--adjm-bleu-royal) !important;
}

.bg-primary {
  background-color: var(--adjm-bleu-royal) !important;
}

.bg-secondary {
  background-color: var(--adjm-bleu-clair) !important;
}

.bg-creme {
  background-color: var(--adjm-creme) !important;
}

/* === AMÉLIORATIONS VISUELLES === */
.wp-block-columns {
  gap: 30px;
}

.wp-block-spacer {
  margin-top: 0;
  margin-bottom: 0;
}

/* Smooth scroll */
html {
  scroll-behavior: smooth;
}

/* Sélection de texte */
::selection {
  background-color: var(--adjm-bleu-royal);
  color: white;
}

::-moz-selection {
  background-color: var(--adjm-bleu-royal);
  color: white;
}
`;

async function main() {
    console.log("🎨 Application de la charte graphique ADJM...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "branding-applier",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        // 1. Appliquer le CSS personnalisé
        console.log("📝 Application du CSS personnalisé...");
        const cssResult = await client.callTool({
            name: 'adjm__append-custom-css',
            arguments: { css: customCSS }
        });
        console.log("✅ CSS appliqué !");
        console.log(cssResult.content[0].text);

        // 2. Mettre à jour le titre du site
        console.log("\n📋 Mise à jour du titre du site...");
        const titleResult = await client.callTool({
            name: 'adjm__update-option',
            arguments: {
                option_name: 'blogname',
                option_value: 'ADJM Événementiel'
            }
        });
        console.log("✅ Titre mis à jour !");

        // 3. Mettre à jour la tagline
        console.log("\n📋 Mise à jour de la tagline...");
        const taglineResult = await client.callTool({
            name: 'adjm__update-option',
            arguments: {
                option_name: 'blogdescription',
                option_value: 'Votre agence événementielle de confiance - Créateurs d\'émotions et d\'expériences inoubliables'
            }
        });
        console.log("✅ Tagline mise à jour !");

        console.log("\n" + "=".repeat(60));
        console.log("🎉 CHARTE GRAPHIQUE APPLIQUÉE AVEC SUCCÈS !");
        console.log("=".repeat(60));
        console.log("✅ CSS personnalisé ajouté");
        console.log("✅ Couleurs ADJM configurées");
        console.log("✅ Typographies Barlow & Inter intégrées");
        console.log("✅ Titre et tagline mis à jour");
        console.log("\nVisitez https://adjmevenementiel.fr pour voir les changements !");

    } catch (error) {
        console.error("❌ Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
