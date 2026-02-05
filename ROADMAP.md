# 📋 ROADMAP - IA Pilote MCP Ability

> **Version actuelle**: 1.6.0
> **Dernière mise à jour**: 2026-02-05  
> **Statut**: ✅ Production Ready

---

## 📊 Progression Globale

| Module | Progression | Statut |
|--------|-------------|--------|
| Core MCP Server | 100% | ✅ Complet |
| Système de Licence | 100% | ✅ Complet |
| Abilities Content | 100% | ✅ Complet |
| Abilities System | 100% | ✅ Complet |
| Abilities Extensions | 80% | 🔄 En cours |
| Interface Admin | 100% | ✅ Complet |
| Quota & Alertes | 100% | ✅ Complet |
| Build System | 100% | ✅ Complet |

---

## ✅ Version 1.6.0 (Actuelle)

### 🆕 Nouvelles Fonctionnalités
- [x] **System de Build WordPress** - Script automatisé pour créer ZIP compatible
- [x] **Vérification Quota** (`adjm/check-quota`) - Alerte quand quota dépassé
- [x] **Statistiques d'Usage** (`adjm/get-usage-stats`) - Historique 7 jours
- [x] **Info Licence** (`adjm/get-license-info`) - Détails licence complète
- [x] **Vérification Accès Feature** (`adjm/check-feature-access`) - Vérifie accès avant action
- [x] **Liste Features** (`adjm/list-available-features`) - Liste toutes les features avec statut

### 🔧 Corrections
- [x] **CRITIQUE** : Structure ZIP WordPress corrigée (pas de dossier versionné)
- [x] Script `build.ps1` et `build.bat` pour générer ZIP correct

---

## 🛠️ Build System

### Comment générer le ZIP

```bash
# Windows PowerShell
.\build.ps1 -Version "1.6.0"

# Windows CMD
build.bat 1.6.0
```

### Structure générée

Le ZIP crée **toujours** le dossier `ia-pilote-mcp-ability/` (sans version) :

```
ia-pilote-mcp-ability/
├── ia-pilote-mcp-ability.php
├── includes/
│   ├── class-ability.php
│   ├── class-license.php
│   ├── class-mcp-server.php
│   └── abilities-functions.php
├── abilities/
│   ├── system.php
│   ├── content.php
│   └── extensions.php
├── assets/
├── docs/
└── README.md
```

Cela garantit que WordPress installe dans `/wp-content/plugins/ia-pilote-mcp-ability/`.

---

## 📦 Abilities Disponibles (v1.6.0)

### Contenu (Content)
| Ability | Description | Plan |
|---------|-------------|------|
| `adjm/list-pages` | Lister les pages | FREE |
| `adjm/get-page` | Récupérer une page | FREE |
| `adjm/create-page` | Créer une page | FREE |
| `adjm/update-page` | Modifier une page | PRO |
| `adjm/delete-page` | Supprimer une page | PRO |
| `adjm/list-posts` | Lister les articles | FREE |
| `adjm/get-post` | Récupérer un article | FREE |
| `adjm/create-post` | Créer un article | FREE |
| `adjm/update-post` | Modifier un article | PRO |
| `adjm/delete-post` | Supprimer un article | PRO |
| `adjm/list-media` | Lister les médias | FREE |
| `adjm/upload-media` | Uploader un média | PRO |
| `adjm/delete-media` | Supprimer un média | PRO |
| `adjm/list-taxonomies` | Lister catégories/tags | FREE |

### Système & Quota
| Ability | Description | Plan |
|---------|-------------|------|
| `adjm/get-site-info` | Info du site | FREE |
| `adjm/check-quota` | ⚠️ Vérifier quota + alertes | FREE |
| `adjm/get-usage-stats` | Statistiques 7 jours | FREE |
| `adjm/get-license-info` | Détails licence | FREE |
| `adjm/check-feature-access` | Vérifier accès feature | FREE |
| `adjm/list-available-features` | Lister toutes features | FREE |
| `adjm/get-option` | Lire une option | PRO |
| `adjm/update-option` | Modifier une option | PRO |
| `adjm/list-plugins` | Lister les plugins | PRO |

### Apparence
| Ability | Description | Plan |
|---------|-------------|------|
| `adjm/list-menus` | Lister les menus | PRO |
| `adjm/get-menu-items` | Récupérer items menu | PRO |
| `adjm/get-header-settings` | Config header | PRO |
| `adjm/set-header-logo` | Modifier logo | PRO |
| `adjm/get-footer-settings` | Config footer | PRO |
| `adjm/set-footer-style` | Style footer | PRO |
| `adjm/get-theme-info` | Info thème actif | PRO |

### Extensions (si plugin installé)
| Ability | Description | Plugin Requis |
|---------|-------------|---------------|
| `adjm/woo-list-products` | Lister produits | WooCommerce |
| `adjm/woo-get-product` | Récupérer produit | WooCommerce |
| `adjm/woo-list-orders` | Lister commandes | WooCommerce |
| `adjm/seo-get-meta` | Récupérer SEO | Yoast/RankMath |
| `adjm/seo-update-meta` | Modifier SEO | Yoast/RankMath |
| `adjm/acf-get-fields` | Récupérer champs ACF | ACF |
| `adjm/acf-update-field` | Modifier champ ACF | ACF |

---

## 🔐 Plans et Licences

### Plan FREE (Par défaut)
- ✅ Pages/Posts : list, get, create
- ✅ Médias : list
- ✅ Catégories : list
- ✅ Site Info : get
- ✅ Quota/Stats : toujours disponible
- ⚠️ Limite : 100 requêtes/jour

### Plan PRO (49€/an)
- ✅ Toutes les abilities
- ✅ Update/Delete content
- ✅ WooCommerce, SEO, ACF
- ✅ Bulk operations
- ✅ Requêtes illimitées
- ✅ Support prioritaire

### Clés de Test Disponibles
```
PRO:      IAPILOTE-PRO-TEST-2026
BUSINESS: IAPILOTE-BIZ-TEST-2026
AGENCY:   IAPILOTE-AGY-TEST-2026
```

---

## 📝 Changelog

### v1.6.0 (2026-02-05)
- ✨ Abilities quota : `check-quota`, `get-usage-stats`, `get-license-info`
- ✨ Abilities accès : `check-feature-access`, `list-available-features`
- 🔧 Alertes automatiques quand quota dépassé avec liens upgrade
- 🛠️ Build system PowerShell pour ZIP WordPress valide
- 🚑 Correction structure ZIP (dossier sans version)

### v1.5.0 (2026-02-05)
- ✨ Gestion menus navigation
- ✨ Abilities header/footer
- ✨ Support Divi et FSE

### v1.0.3 (2026-02-05)
- 🚑 Fix Critique : Structure d'archive ZIP corrigée

### v1.0.2 (2026-02-05)
- 🐛 Fix compatibilité PHP 7.4

### v1.0.1
- ✨ Système de licence complet
- ✨ Clés de test

### v1.0.0
- 🎉 Version initiale

---

## 🚀 Installation

1. **Générer le ZIP** : `.\build.ps1 -Version "1.6.0"`
2. **Téléverser** via WordPress Admin > Plugins > Ajouter > Téléverser
3. **Activer** le plugin
4. **Configurer** dans Admin > IA Pilote MCP
5. **Optionnel** : Entrer clé de licence PRO

---

## 📞 Support

- **Documentation** : Admin > IA Pilote MCP > Documentation
- **Site** : https://centerhome.net
- **Email** : support@centerhome.net
