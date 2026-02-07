# 🌉 Pont MCP pour IA Pilote WordPress

Ce serveur MCP permet de connecter Agravity (Cursor) ou Claude Desktop à votre site WordPress équipé du plugin "IA Pilote".

## 🛠️ Installation

1.  **Prérequis** : Node.js installé.
2.  **Installation** :
    ```bash
    cd MCP
    npm install
    npm run build
    ```

## ⚙️ Configuration

1.  Ouvrez le fichier `.env` dans le dossier `MCP`.
2.  Remplissez vos informations :
    *   `WP_URL` : L'URL de votre site (ex: `https://adjm-evenementiel.fr`)
    *   `WP_USERNAME` : Votre identifiant administrateur WordPress.
    *   `WP_APP_PASSWORD` : Votre mot de passe d'application.
        *   *Pour le créer : Admin WP > Utilisateurs > Profil > Mots de passe d'application.*

## 🚀 Utilisation dans Cursor / Claude Desktop

Ajoutez cette configuration à votre fichier `mcp_config.json` (ou paramètres Cursor) :

```json
{
  "mcpServers": {
    "ia-pilote-bridge": {
      "command": "node",
      "args": [
        "D:/Projet/06_DEV/wordpress-plugins/MCP/build/index.js"
      ],
      "env": {
        "WP_URL": "https://votre-site.fr",
        "WP_USERNAME": "votre_identifiant",
        "WP_APP_PASSWORD": "VOTRE_MOT_DE_PASSE_APPLICATION"
      }
    }
  }
}
```

> **Note**: Le serveur inclut une sécurité (troncation automatique) pour empêcher l'IA de planter si une réponse dépasse 25 000 caractères.

## 🔍 Dépannage

*   Si l'erreur "404 No Route" persiste, assurez-vous que les **Permaliens** sont activés sur WordPress (Réglages > Permaliens > Titre de la publication).
*   Si l'authentification échoue, vérifiez que le mot de passe d'application est correct et sans espaces superflus.
