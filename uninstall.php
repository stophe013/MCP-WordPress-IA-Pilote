<?php
/**
 * Uninstall Script - IA Pilote MCP Ability
 * 
 * Ce fichier est exécuté automatiquement lors de la suppression du plugin.
 * Il nettoie toutes les données créées par le plugin.
 * 
 * @package IA_Pilote_MCP
 */

// Sécurité : vérifier que WordPress appelle bien ce fichier
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Supprimer les options
delete_option('adjm_mcp_abilities');
delete_option('adjm_mcp_version');
delete_option('adjm_mcp_api_key');
delete_option('ia_pilote_license_key');
delete_option('ia_pilote_license_data');

// Supprimer les transients
delete_transient('ia_pilote_license_status');

// Nettoyer les transients de requêtes quotidiennes
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'ia_pilote_requests_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_ia_pilote%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_ia_pilote%'");

// Optionnel : Supprimer la table de logs (décommenter si désiré)
// $table = $wpdb->prefix . 'adjm_mcp_logs';
// $wpdb->query("DROP TABLE IF EXISTS $table");

// Note : Par défaut, on garde la table de logs pour permettre
// une réinstallation sans perte de données. Pour supprimer
// complètement, décommenter les lignes ci-dessus.
