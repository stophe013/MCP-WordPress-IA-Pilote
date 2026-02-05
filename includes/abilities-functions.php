<?php
/**
 * Fonctions d'aide pour les abilities
 * 
 * @package IA_Pilote_MCP
 */

if (!defined('ABSPATH')) exit;

/**
 * Enregistrer une ability (alias global)
 */
if (!function_exists('wp_register_ability')) {
    function wp_register_ability($name, $args) {
        IA_Pilote_Core::register_ability($name, $args);
    }
}

/**
 * Récupérer une ability
 */
if (!function_exists('wp_get_ability')) {
    function wp_get_ability($name) {
        return IA_Pilote_Core::get_ability($name);
    }
}

/**
 * Enregistrer une catégorie
 */
if (!function_exists('wp_register_ability_category')) {
    function wp_register_ability_category($slug, $args) {
        IA_Pilote_Core::register_category($slug, $args);
    }
}

/**
 * Vérifier si un groupe est activé
 */
if (!function_exists('mcp_is_ability_group_enabled')) {
    function mcp_is_ability_group_enabled($group) {
        return IA_Pilote_Core::is_group_enabled($group);
    }
}

/**
 * Helper pour créer les meta MCP standard
 */
function adjm_mcp_meta($readonly = true, $destructive = false) {
    return [
        'annotations' => [
            'readonly' => $readonly,
            'destructive' => $destructive,
            'idempotent' => $readonly,
        ],
        'show_in_rest' => true,
        'mcp' => [
            'public' => true,
            'type' => 'tool',
        ],
    ];
}
