import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const bridgePath = path.join(__dirname, "build/index.js");

// Contenu corrigé avec les bonnes couleurs
const fixedContent = `<!-- wp:cover {"url":"https://adjmevenementiel.fr/wp-content/uploads/2023/06/blue-pic-4-scaled.jpg","id":755,"dimRatio":80,"customOverlayColor":"#2B34A6","align":"full","style":{"spacing":{"padding":{"top":"120px","bottom":"120px"}}}} -->
<div class="wp-block-cover alignfull" style="padding-top:120px;padding-bottom:120px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-80 has-background-dim" style="background-color:#2B34A6"></span><img class="wp-block-cover__image-background wp-image-755" alt="" src="https://adjmevenementiel.fr/wp-content/uploads/2023/06/blue-pic-4-scaled.jpg" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:group {"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group"><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"4.5rem","fontWeight":"800"},"spacing":{"margin":{"bottom":"30px"}},"color":{"text":"#FFFDF3"}}} -->
<h1 class="wp-block-heading has-text-align-center" style="color:#FFFDF3;margin-bottom:30px;font-size:4.5rem;font-weight:800">ADJM Événementiel</h1>
<!-- /wp:heading -->

<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"2rem","fontWeight":"300"},"spacing":{"margin":{"bottom":"20px"}},"color":{"text":"#FFFDF3"}}} -->
<h2 class="wp-block-heading has-text-align-center" style="color:#FFFDF3;margin-bottom:20px;font-size:2rem;font-weight:300">Créateurs d'Émotions &amp; d'Expériences Inoubliables</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.3rem"},"spacing":{"margin":{"bottom":"40px"}},"color":{"text":"#FFFDF3"}}} -->
<p class="has-text-align-center" style="color:#FFFDF3;margin-bottom:40px;font-size:1.3rem">Depuis plus de 15 ans, nous transformons vos rêves en réalité</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"color":{"background":"#FFFDF3","text":"#2B34A6"},"border":{"radius":"8px"},"spacing":{"padding":{"top":"18px","bottom":"18px","left":"45px","right":"45px"}}},"fontSize":"large"} -->
<div class="wp-block-button has-custom-font-size has-large-font-size"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="https://adjmevenementiel.fr/besoin-dune-prestation/" style="border-radius:8px;color:#2B34A6;background-color:#FFFDF3;padding-top:18px;padding-right:45px;padding-bottom:18px;padding-left:45px"><strong>Demander un Devis Gratuit</strong></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}},"color":{"background":"#FFFDF3"}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull" style="background-color:#FFFDF3;padding-top:80px;padding-bottom:80px"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"3rem"},"color":{"text":"#2B34A6"}}} -->
<h2 class="wp-block-heading has-text-align-center" style="color:#2B34A6;font-size:3rem"><strong>Qui Sommes-Nous ?</strong></h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"30px"} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.2rem","lineHeight":"1.8"},"color":{"text":"#132060"}}} -->
<p class="has-text-align-center" style="color:#132060;font-size:1.2rem;line-height:1.8">ADJM Événementiel est votre partenaire de confiance pour la création et l'organisation d'événements exceptionnels. Que ce soit pour un mariage de rêve, un événement d'entreprise marquant, ou une célébration privée mémorable, notre équipe de professionnels passionnés met son expertise à votre service.</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"50px"} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"},"color":{"background":"#ffffff"}}} -->
<div class="wp-block-column" style="background-color:#ffffff;border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"3.5rem","fontWeight":"700"},"color":{"text":"#2B34A6"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="color:#2B34A6;font-size:3.5rem;font-weight:700">15+</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem"},"color":{"text":"#132060"}}} -->
<p class="has-text-align-center" style="color:#132060;font-size:1.1rem"><strong>Ans d'expérience</strong><br>dans l'événementiel</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"},"color":{"background":"#ffffff"}}} -->
<div class="wp-block-column" style="background-color:#ffffff;border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"3.5rem","fontWeight":"700"},"color":{"text":"#2B34A6"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="color:#2B34A6;font-size:3.5rem;font-weight:700">500+</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem"},"color":{"text":"#132060"}}} -->
<p class="has-text-align-center" style="color:#132060;font-size:1.1rem"><strong>Événements réussis</strong><br>et clients satisfaits</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"},"color":{"background":"#ffffff"}}} -->
<div class="wp-block-column" style="background-color:#ffffff;border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"3.5rem","fontWeight":"700"},"color":{"text":"#2B34A6"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="color:#2B34A6;font-size:3.5rem;font-weight:700">50+</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem"},"color":{"text":"#132060"}}} -->
<p class="has-text-align-center" style="color:#132060;font-size:1.1rem"><strong>Prestataires partenaires</strong><br>certifiés et qualifiés</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}},"color":{"background":"#2B34A6"}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull" style="background-color:#2B34A6;padding-top:80px;padding-bottom:80px"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"3rem"},"color":{"text":"#FFFDF3"}}} -->
<h2 class="wp-block-heading has-text-align-center" style="color:#FFFDF3;font-size:3rem"><strong>Nos Prestations</strong></h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"50px"} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"},"color":{"background":"#FFFDF3"}}} -->
<div class="wp-block-column" style="background-color:#FFFDF3;border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3,"style":{"color":{"text":"#2B34A6"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="color:#2B34A6">🎵 Animation Musicale &amp; DJ</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#132060"}}} -->
<p class="has-text-align-center" style="color:#132060">Nos DJ professionnels créent l'ambiance parfaite pour votre événement. Playlist personnalisée, équipement haut de gamme.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"color":{"background":"#2B34A6","text":"#FFFDF3"},"border":{"radius":"8px"}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="https://adjmevenementiel.fr/nos-dj/" style="border-radius:8px;color:#FFFDF3;background-color:#2B34A6">En savoir plus</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"},"color":{"background":"#FFFDF3"}}} -->
<div class="wp-block-column" style="background-color:#FFFDF3;border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3,"style":{"color":{"text":"#2B34A6"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="color:#2B34A6">📸 Photographie &amp; Vidéo</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#132060"}}} -->
<p class="has-text-align-center" style="color:#132060">Immortalisez vos moments précieux avec nos photographes et vidéastes experts. Des souvenirs de qualité professionnelle.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"color":{"background":"#2B34A6","text":"#FFFDF3"},"border":{"radius":"8px"}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="https://adjmevenementiel.fr/nos-photographes/" style="border-radius:8px;color:#FFFDF3;background-color:#2B34A6">En savoir plus</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"},"color":{"background":"#FFFDF3"}}} -->
<div class="wp-block-column" style="background-color:#FFFDF3;border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3,"style":{"color":{"text":"#2B34A6"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="color:#2B34A6">🍸 Bar &amp; Cocktails</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#132060"}}} -->
<p class="has-text-align-center" style="color:#132060">Nos barmans professionnels préparent des cocktails signature et des boissons premium. Service de bar complet.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"color":{"background":"#2B34A6","text":"#FFFDF3"},"border":{"radius":"8px"}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="https://adjmevenementiel.fr/nos-barmans/" style="border-radius:8px;color:#FFFDF3;background-color:#2B34A6">En savoir plus</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"30px"} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"},"color":{"background":"#FFFDF3"}}} -->
<div class="wp-block-column" style="background-color:#FFFDF3;border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3,"style":{"color":{"text":"#2B34A6"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="color:#2B34A6">🏢 Événements Entreprise</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#132060"}}} -->
<p class="has-text-align-center" style="color:#132060">Séminaires, team building, lancements de produits, soirées d'entreprise... Nous gérons tous vos événements professionnels.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"color":{"background":"#2B34A6","text":"#FFFDF3"},"border":{"radius":"8px"}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="https://adjmevenementiel.fr/evenement-entreprises/" style="border-radius:8px;color:#FFFDF3;background-color:#2B34A6">En savoir plus</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"},"color":{"background":"#FFFDF3"}}} -->
<div class="wp-block-column" style="background-color:#FFFDF3;border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3,"style":{"color":{"text":"#2B34A6"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="color:#2B34A6">🎨 Décoration &amp; Scénographie</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#132060"}}} -->
<p class="has-text-align-center" style="color:#132060">Création d'ambiances uniques avec décoration personnalisée, éclairage professionnel, et mise en scène spectaculaire.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"color":{"background":"#2B34A6","text":"#FFFDF3"},"border":{"radius":"8px"}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="https://adjmevenementiel.fr/nos-prestations/" style="border-radius:8px;color:#FFFDF3;background-color:#2B34A6">En savoir plus</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"},"color":{"background":"#FFFDF3"}}} -->
<div class="wp-block-column" style="background-color:#FFFDF3;border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:heading {"textAlign":"center","level":3,"style":{"color":{"text":"#2B34A6"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="color:#2B34A6">🎤 Sonorisation &amp; Éclairage</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#132060"}}} -->
<p class="has-text-align-center" style="color:#132060">Équipement professionnel pour une qualité audio et visuelle irréprochable. Techniciens qualifiés pour la gestion technique.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"color":{"background":"#2B34A6","text":"#FFFDF3"},"border":{"radius":"8px"}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="https://adjmevenementiel.fr/nos-prestations/" style="border-radius:8px;color:#FFFDF3;background-color:#2B34A6">En savoir plus</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}},"color":{"background":"#FFFDF3"}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull" style="background-color:#FFFDF3;padding-top:80px;padding-bottom:80px"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"3rem"},"color":{"text":"#2B34A6"}}} -->
<h2 class="wp-block-heading has-text-align-center" style="color:#2B34A6;font-size:3rem"><strong>Ils Nous Recommandent</strong></h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"50px"} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"},"color":{"background":"#ffffff"}}} -->
<div class="wp-block-column" style="background-color:#ffffff;border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:image {"align":"center","id":757,"width":"120px","sizeSlug":"medium"} -->
<figure class="wp-block-image aligncenter size-medium is-resized"><img src="https://adjmevenementiel.fr/wp-content/uploads/2023/06/5-star-rating-2141414060-3-300x241.png" alt="" class="wp-image-757" style="width:120px"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem"},"color":{"text":"#132060"}}} -->
<p class="has-text-align-center" style="color:#132060;font-size:1.1rem">"Une soirée qui restera dans la mémoire de tous les invités! Deux DJ au top de leur forme Christophe et Fabien jusqu'au bout de la nuit. Encore un grand merci!"</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"600"},"color":{"text":"#2B34A6"}}} -->
<p class="has-text-align-center" style="color:#2B34A6;font-weight:600">Padovani - Anniversaire</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}},"border":{"radius":"12px"},"color":{"background":"#ffffff"}}} -->
<div class="wp-block-column" style="background-color:#ffffff;border-radius:12px;padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px"><!-- wp:image {"align":"center","id":758,"width":"120px","sizeSlug":"medium"} -->
<figure class="wp-block-image aligncenter size-medium is-resized"><img src="https://adjmevenementiel.fr/wp-content/uploads/2023/06/5-star-rating-2141414060-4-300x241.png" alt="" class="wp-image-758" style="width:120px"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem"},"color":{"text":"#132060"}}} -->
<p class="has-text-align-center" style="color:#132060;font-size:1.1rem">"Bravo au DJ et à l'équipe de ADJM Événementiel, un très grand professionnalisme et une très bonne ambiance tout au long de la nuit. Merci encore."</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"600"},"color":{"text":"#2B34A6"}}} -->
<p class="has-text-align-center" style="color:#2B34A6;font-weight:600">Alice - Mariage</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}},"color":{"background":"#8895DD"}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull" style="background-color:#8895DD;padding-top:80px;padding-bottom:80px"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"3rem"},"color":{"text":"#FFFDF3"}}} -->
<h2 class="wp-block-heading has-text-align-center" style="color:#FFFDF3;font-size:3rem"><strong>Prêt à Créer Votre Événement de Rêve ?</strong></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.3rem"},"spacing":{"margin":{"top":"20px","bottom":"40px"}},"color":{"text":"#FFFDF3"}}} -->
<p class="has-text-align-center" style="color:#FFFDF3;margin-top:20px;margin-bottom:40px;font-size:1.3rem">Contactez-nous dès aujourd'hui pour discuter de votre projet.<br>Devis personnalisé gratuit et sans engagement.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"color":{"background":"#FFFDF3","text":"#2B34A6"},"border":{"radius":"8px"},"spacing":{"padding":{"top":"18px","bottom":"18px","left":"45px","right":"45px"}}},"fontSize":"large"} -->
<div class="wp-block-button has-custom-font-size has-large-font-size"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="https://adjmevenementiel.fr/besoin-dune-prestation/" style="border-radius:8px;color:#2B34A6;background-color:#FFFDF3;padding-top:18px;padding-right:45px;padding-bottom:18px;padding-left:45px"><strong>Demander un Devis</strong></a></div>
<!-- /wp:button -->

<!-- wp:button {"style":{"color":{"background":"#2B34A6","text":"#FFFDF3"},"border":{"radius":"8px"},"spacing":{"padding":{"top":"18px","bottom":"18px","left":"45px","right":"45px"}}},"fontSize":"large"} -->
<div class="wp-block-button has-custom-font-size has-large-font-size"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="https://adjmevenementiel.fr/creez-votre-evenement/" style="border-radius:8px;color:#FFFDF3;background-color:#2B34A6;padding-top:18px;padding-right:45px;padding-bottom:18px;padding-left:45px"><strong>Créer Votre Événement</strong></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}},"color":{"background":"#FFFDF3"}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull" style="background-color:#FFFDF3;padding-top:80px;padding-bottom:80px"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"3rem"},"color":{"text":"#2B34A6"}}} -->
<h2 class="wp-block-heading has-text-align-center" style="color:#2B34A6;font-size:3rem"><strong>Ils Nous Font Confiance</strong></h2>
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
    console.log("🎨 Correction des couleurs de la page d'accueil...\n");

    const transport = new StdioClientTransport({
        command: "node",
        args: [bridgePath],
    });

    const client = new Client({
        name: "color-fixer",
        version: "1.0.0",
    }, {
        capabilities: {},
    });

    await client.connect(transport);

    try {
        console.log("📝 Application des couleurs corrigées...");
        const updateResult = await client.callTool({
            name: 'adjm__update-page',
            arguments: {
                id: 753,
                content: fixedContent
            }
        });

        console.log("\n" + "=".repeat(80));
        console.log("✅ COULEURS CORRIGÉES AVEC SUCCÈS !");
        console.log("=".repeat(80));
        console.log("\n🎨 Palette de couleurs appliquée :");
        console.log("  • Hero : Fond bleu royal #2B34A6 + texte crème #FFFDF3");
        console.log("  • Section Qui Sommes-Nous : Fond crème #FFFDF3 + texte bleu foncé #132060");
        console.log("  • Section Prestations : Fond bleu royal #2B34A6 + cartes crème #FFFDF3");
        console.log("  • Section Témoignages : Fond crème #FFFDF3 + cartes blanches");
        console.log("  • Section CTA : Fond bleu clair #8895DD + texte crème");
        console.log("  • Section Clients : Fond crème #FFFDF3");
        console.log("\n🌐 Visitez: https://adjmevenementiel.fr");
        console.log("\n✨ Les textes sont maintenant bien visibles avec un bon contraste !");

    } catch (error) {
        console.error("❌ Erreur:", error.message);
    }

    await client.close();
    process.exit(0);
}

main().catch(console.error);
