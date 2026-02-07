import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

// Nouveau contenu de la page d'accueil
const newContent = `<!-- wp:cover {"url":"https://adjmevenementiel.fr/wp-content/uploads/2023/06/blue-pic-4-scaled.jpg","id":755,"dimRatio":70,"overlayColor":"custom","customOverlayColor":"#2B34A6","align":"full","style":{"spacing":{"padding":{"top":"120px","bottom":"120px"}}},"className":"hero-section"} -->
<div class="wp-block-cover alignfull hero-section" style="padding-top:120px;padding-bottom:120px"><span aria-hidden="true" class="wp-block-cover__background has-custom-background-color has-background-dim-70 has-background-dim" style="background-color:#2B34A6"></span><img class="wp-block-cover__image-background wp-image-755" alt="" src="https://adjmevenementiel.fr/wp-content/uploads/2023/06/blue-pic-4-scaled.jpg" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:group {"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group"><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"4rem","fontWeight":"800"},"spacing":{"margin":{"bottom":"30px"}}},"textColor":"base"} -->
<h1 class="wp-block-heading has-text-align-center has-base-color has-text-color" style="margin-bottom:30px;font-size:4rem;font-weight:800">ADJM Événementiel</h1>
<!-- /wp:heading -->

<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"2rem","fontWeight":"300"},"spacing":{"margin":{"bottom":"20px"}}},"textColor":"base"} -->
<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color" style="margin-bottom:20px;font-size:2rem;font-weight:300">Créateurs d'Émotions &amp; d'Expériences Inoubliables</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.3rem"},"spacing":{"margin":{"bottom":"40px"}}},"textColor":"base"} -->
<p class="has-text-align-center has-base-color has-text-color" style="margin-bottom:40px;font-size:1.3rem">Depuis plus de 15 ans, nous transformons vos rêves en réalité</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"border":{"radius":"8px"},"spacing":{"padding":{"top":"16px","bottom":"16px","left":"40px","right":"40px"}}},"fontSize":"large"} -->
<div class="wp-block-button has-custom-font-size has-large-font-size"><a class="wp-block-button__link wp-element-button" href="https://adjmevenementiel.fr/besoin-dune-prestation/" style="border-radius:8px;padding-top:16px;padding-right:40px;padding-bottom:16px;padding-left:40px">Demander un Devis Gratuit</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}}},"backgroundColor":"base","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:80px;padding-bottom:80px"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"3rem"}},"textColor":"contrast"} -->
<h2 class="wp-block-heading has-text-align-center has-contrast-color has-text-color" style="font-size:3rem">Qui Sommes-Nous ?</h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"30px"} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.2rem","lineHeight":"1.8"}}} -->
<p class="has-text-align-center" style="font-size:1.2rem;line-height:1.8">ADJM Événementiel est votre partenaire de confiance pour la création et l'organisation d'événements exceptionnels. Que ce soit pour un mariage de rêve, un événement d'entreprise marquant, ou une célébration privée mémorable, notre équipe de professionnels passionnés met son expertise à votre service.</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"40px"} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}},"border":{"radius":"12px"}},"backgroundColor":"base-2"} -->
<div class="wp-block-column has-base-2-background-color has-background" style="border-radius:12px;padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"3rem","fontWeight":"700"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="font-size:3rem;font-weight:700">15+</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem"}}} -->
<p class="has-text-align-center" style="font-size:1.1rem"><strong>Ans d'expérience</strong><br>dans l'événementiel</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}},"border":{"radius":"12px"}},"backgroundColor":"base-2"} -->
<div class="wp-block-column has-base-2-background-color has-background" style="border-radius:12px;padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"3rem","fontWeight":"700"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="font-size:3rem;font-weight:700">500+</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem"}}} -->
<p class="has-text-align-center" style="font-size:1.1rem"><strong>Événements réussis</strong><br>et clients satisfaits</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}},"border":{"radius":"12px"}},"backgroundColor":"base-2"} -->
<div class="wp-block-column has-base-2-background-color has-background" style="border-radius:12px;padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"3rem","fontWeight":"700"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="font-size:3rem;font-weight:700">50+</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem"}}} -->
<p class="has-text-align-center" style="font-size:1.1rem"><strong>Prestataires partenaires</strong><br>certifiés et qualifiés</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}}},"backgroundColor":"base-2","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull has-base-2-background-color has-background" style="padding-top:80px;padding-bottom:80px"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"3rem"}}} -->
<h2 class="wp-block-heading has-text-align-center" style="font-size:3rem">Nos Prestations</h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"50px"} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<div class="wp-block-column has-base-background-color has-background" style="border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center">🎵 Animation Musicale &amp; DJ</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Nos DJ professionnels créent l'ambiance parfaite pour votre événement. Playlist personnalisée, équipement haut de gamme.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="https://adjmevenementiel.fr/nos-dj/">En savoir plus</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<div class="wp-block-column has-base-background-color has-background" style="border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center">📸 Photographie &amp; Vidéo</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Immortalisez vos moments précieux avec nos photographes et vidéastes experts. Des souvenirs de qualité professionnelle.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="https://adjmevenementiel.fr/nos-photographes/">En savoir plus</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<div class="wp-block-column has-base-background-color has-background" style="border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center">🍸 Bar &amp; Cocktails</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Nos barmans professionnels préparent des cocktails signature et des boissons premium. Service de bar complet.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="https://adjmevenementiel.fr/nos-barmans/">En savoir plus</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"30px"} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<div class="wp-block-column has-base-background-color has-background" style="border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center">🏢 Événements Entreprise</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Séminaires, team building, lancements de produits, soirées d'entreprise... Nous gérons tous vos événements professionnels.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="https://adjmevenementiel.fr/evenement-entreprises/">En savoir plus</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<div class="wp-block-column has-base-background-color has-background" style="border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center">🎨 Décoration &amp; Scénographie</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Création d'ambiances uniques avec décoration personnalisée, éclairage professionnel, et mise en scène spectaculaire.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="https://adjmevenementiel.fr/nos-prestations/">En savoir plus</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<div class="wp-block-column has-base-background-color has-background" style="border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center">🎤 Sonorisation &amp; Éclairage</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Équipement professionnel pour une qualité audio et visuelle irréprochable. Techniciens qualifiés pour la gestion technique.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="https://adjmevenementiel.fr/nos-prestations/">En savoir plus</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}}},"backgroundColor":"base","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:80px;padding-bottom:80px"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"3rem"}}} -->
<h2 class="wp-block-heading has-text-align-center" style="font-size:3rem">Ils Nous Recommandent</h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"50px"} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}},"border":{"radius":"12px"}},"backgroundColor":"base-2"} -->
<div class="wp-block-column has-base-2-background-color has-background" style="border-radius:12px;padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:image {"align":"center","id":757,"width":"120px","sizeSlug":"medium"} -->
<figure class="wp-block-image aligncenter size-medium is-resized"><img src="https://adjmevenementiel.fr/wp-content/uploads/2023/06/5-star-rating-2141414060-3-300x241.png" alt="" class="wp-image-757" style="width:120px"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem"}}} -->
<p class="has-text-align-center" style="font-size:1.1rem">"Une soirée qui restera dans la mémoire de tous les invités! Deux DJ au top de leur forme Christophe et Fabien jusqu'au bout de la nuit. Encore un grand merci!"</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"600"}}} -->
<p class="has-text-align-center" style="font-weight:600">Padovani - Anniversaire</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}},"border":{"radius":"12px"}},"backgroundColor":"base-2"} -->
<div class="wp-block-column has-base-2-background-color has-background" style="border-radius:12px;padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:image {"align":"center","id":758,"width":"120px","sizeSlug":"medium"} -->
<figure class="wp-block-image aligncenter size-medium is-resized"><img src="https://adjmevenementiel.fr/wp-content/uploads/2023/06/5-star-rating-2141414060-4-300x241.png" alt="" class="wp-image-758" style="width:120px"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem"}}} -->
<p class="has-text-align-center" style="font-size:1.1rem">"Bravo au DJ et à l'équipe de ADJM Événementiel, un très grand professionnalisme et une très bonne ambiance tout au long de la nuit. Merci encore."</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"600"}}} -->
<p class="has-text-align-center" style="font-weight:600">Alice - Mariage</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"60px","bottom":"60px"}},"color":{"background":"#2B34A6"}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull has-background" style="background-color:#2B34A6;padding-top:60px;padding-bottom:60px"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"3rem"},"elements":{"link":{"color":{"text":"var:preset|color|base"}}}},"textColor":"base"} -->
<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color has-link-color" style="font-size:3rem">Prêt à Créer Votre Événement de Rêve ?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.3rem"},"spacing":{"margin":{"top":"20px","bottom":"40px"}}},"textColor":"base"} -->
<p class="has-text-align-center has-base-color has-text-color" style="margin-top:20px;margin-bottom:40px;font-size:1.3rem">Contactez-nous dès aujourd'hui pour discuter de votre projet.<br>Devis personnalisé gratuit et sans engagement.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"color":{"background":"#ffffff","text":"#2B34A6"},"border":{"radius":"8px"},"spacing":{"padding":{"top":"16px","bottom":"16px","left":"40px","right":"40px"}}},"fontSize":"large"} -->
<div class="wp-block-button has-custom-font-size has-large-font-size"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="https://adjmevenementiel.fr/besoin-dune-prestation/" style="border-radius:8px;color:#2B34A6;background-color:#ffffff;padding-top:16px;padding-right:40px;padding-bottom:16px;padding-left:40px">Demander un Devis</a></div>
<!-- /wp:button -->

<!-- wp:button {"style":{"color":{"background":"#8895DD","text":"#ffffff"},"border":{"radius":"8px"},"spacing":{"padding":{"top":"16px","bottom":"16px","left":"40px","right":"40px"}}},"fontSize":"large"} -->
<div class="wp-block-button has-custom-font-size has-large-font-size"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="https://adjmevenementiel.fr/creez-votre-evenement/" style="border-radius:8px;color:#ffffff;background-color:#8895DD;padding-top:16px;padding-right:40px;padding-bottom:16px;padding-left:40px">Créer Votre Événement</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}}},"backgroundColor":"base-2","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull has-base-2-background-color has-background" style="padding-top:80px;padding-bottom:80px"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"3rem"}}} -->
<h2 class="wp-block-heading has-text-align-center" style="font-size:3rem">Ils Nous Font Confiance</h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"50px"} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:image {"id":742,"width":"150px","aspectRatio":"1","scale":"cover","sizeSlug":"thumbnail"} -->
<figure class="wp-block-image size-thumbnail is-resized"><img src="https://adjmevenementiel.fr/wp-content/uploads/2023/06/logo-ville-de-marseille-1699115743-1-150x150.jpg" alt="" class="wp-image-742" style="aspect-ratio:1;object-fit:cover;width:150px"/></figure>
<!-- /wp:image -->

<!-- wp:image {"id":744,"width":"154px","height":"auto","aspectRatio":"1","scale":"contain","sizeSlug":"full"} -->
<figure class="wp-block-image size-full is-resized"><img src="https://adjmevenementiel.fr/wp-content/uploads/2023/06/cmc.jpg" alt="" class="wp-image-744" style="aspect-ratio:1;object-fit:contain;width:154px;height:auto"/></figure>
<!-- /wp:image -->

<!-- wp:image {"id":746,"width":"144px","height":"auto","scale":"contain","sizeSlug":"thumbnail"} -->
<figure class="wp-block-image size-thumbnail is-resized"><img src="https://adjmevenementiel.fr/wp-content/uploads/2023/06/images-150x150.png" alt="" class="wp-image-746" style="object-fit:contain;width:144px;height:auto"/></figure>
<!-- /wp:image -->

<!-- wp:image {"id":747,"width":"150px","aspectRatio":"1","scale":"cover","sizeSlug":"thumbnail"} -->
<figure class="wp-block-image size-thumbnail is-resized"><img src="https://adjmevenementiel.fr/wp-content/uploads/2023/06/logo-compass-group-qapa-150x150.jpg" alt="" class="wp-image-747" style="aspect-ratio:1;object-fit:cover;width:150px"/></figure>
<!-- /wp:image --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->`;

async function main() {
    console.log("🚀 Mise à jour de la page d'accueil...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "homepage-updater",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        // Mettre à jour la page d'accueil (ID 753)
        console.log("📝 Mise à jour du contenu de la page d'accueil...");
        const updateResult = await client.callTool({
            name: 'adjm__update-page',
            arguments: {
                id: 753,
                content: newContent,
                title: "Accueil - ADJM Événementiel"
            }
        });

        const result = JSON.parse(updateResult.content[0].text);

        console.log("\n" + "=".repeat(80));
        console.log("🎉 PAGE D'ACCUEIL MISE À JOUR AVEC SUCCÈS !");
        console.log("=".repeat(80));
        console.log(`✅ Titre: ${result.title}`);
        console.log(`✅ URL: ${result.url}`);
        console.log(`✅ Status: ${result.status}`);
        console.log("\n📋 Nouvelles sections ajoutées:");
        console.log("  - Hero section moderne avec call-to-action");
        console.log("  - Section 'Qui sommes-nous ?' avec statistiques");
        console.log("  - Section 'Nos Prestations' avec 6 services");
        console.log("  - Section 'Ils Nous Recommandent' (témoignages)");
        console.log("  - Section CTA avec fond bleu royal");
        console.log("  - Section 'Ils Nous Font Confiance' (logos clients)");
        console.log("\n🌐 Visitez: https://adjmevenementiel.fr");

    } catch (error) {
        console.error("❌ Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
