=== IA Pilote MCP Ability ===
Contributors: centerhome
Tags: ai, mcp, automation, assistant
Requires at least: 6.0
Tested up to: 6.4
Stable tag: 1.6.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Expose WordPress as an MCP-compatible server (Model Context Protocol) with tools/list + tools/call, plus REST endpoints to discover and execute abilities.

== Description ==

IA Pilote MCP Ability transforme votre site WordPress en serveur compatible MCP.

Endpoints principaux:

* Health: `/wp-json/adjm-mcp/v1/health`
* REST discover: `/wp-json/adjm-mcp/v1/discover`
* REST execute: `/wp-json/adjm-mcp/v1/execute`
* MCP tools list: `/wp-json/adjm-mcp/v1/mcp/tools/list`
* MCP tools call: `/wp-json/adjm-mcp/v1/mcp/tools/call`

Authentification:

* Basic Auth via "Mots de passe d'application" WordPress
* Bearer token via API Key (option `adjm_mcp_api_key`)

== Installation ==

1. Telechargez le fichier ZIP du plugin.
2. WordPress > Extensions > Ajouter > Televerser une extension.
3. Activez l'extension.
4. Allez dans le menu "IA Pilote MCP" pour voir la documentation et les endpoints.

== Frequently Asked Questions ==

= Comment creer un mot de passe d'application ? =
WordPress > Utilisateurs > Votre profil > "Mots de passe d'application".

= Quels clients MCP sont compatibles ? =
Tout client MCP capable d'appeler un endpoint HTTP MCP (ex: Claude Desktop via mcp-remote, Cursor, VS Code, etc.).

== Changelog ==

= 1.6.0 =
* Documentation MCP integree dans l'admin + docs (guide complet + fiche rapide)
* Endpoints MCP tools/list + tools/call
* Exemples generiques (pas de domaine en dur)

