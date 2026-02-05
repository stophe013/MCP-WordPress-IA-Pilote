<?php
/**
 * Classe Ability - Représente une ability MCP
 * 
 * @package IA_Pilote_MCP
 */

if (!defined('ABSPATH')) exit;

class IA_Pilote_Ability {
    
    private $name;
    private $label;
    private $description;
    private $category;
    private $input_schema;
    private $output_schema;
    private $execute_callback;
    private $permission_callback;
    private $meta;
    
    /**
     * Constructeur
     */
    public function __construct($name, $args) {
        $this->name = $name;
        $this->label = $args['label'] ?? $name;
        $this->description = $args['description'] ?? '';
        $this->category = $args['category'] ?? 'uncategorized';
        $this->input_schema = $args['input_schema'] ?? ['type' => 'object', 'properties' => []];
        $this->output_schema = $args['output_schema'] ?? ['type' => 'object'];
        $this->execute_callback = $args['execute_callback'] ?? null;
        $this->permission_callback = $args['permission_callback'] ?? '__return_true';
        $this->meta = $args['meta'] ?? [];
        
        // Assurer que mcp.public = true par défaut
        if (!isset($this->meta['mcp'])) {
            $this->meta['mcp'] = ['public' => true, 'type' => 'tool'];
        }
    }
    
    // Getters
    public function get_name() { return $this->name; }
    public function get_label() { return $this->label; }
    public function get_description() { return $this->description; }
    public function get_category() { return $this->category; }
    public function get_input_schema() { return $this->input_schema; }
    public function get_output_schema() { return $this->output_schema; }
    public function get_meta() { return $this->meta; }
    
    /**
     * Vérifier si l'ability est publique (MCP)
     */
    public function is_public() {
        return !empty($this->meta['mcp']['public']);
    }
    
    /**
     * Vérifier les permissions
     */
    public function check_permission() {
        if (is_callable($this->permission_callback)) {
            return call_user_func($this->permission_callback);
        }
        return true;
    }
    
    /**
     * Exécuter l'ability
     */
    public function execute($input = []) {
        // Vérifier les permissions
        if (!$this->check_permission()) {
            return new WP_Error('forbidden', 'Permission refusée', ['status' => 403]);
        }
        
        // Valider le schéma d'entrée
        $validation = $this->validate_input($input);
        if (is_wp_error($validation)) {
            return $validation;
        }
        
        // Logger le début
        $start_time = microtime(true);
        
        // Exécuter le callback
        if (!is_callable($this->execute_callback)) {
            return new WP_Error('no_callback', 'Pas de callback défini');
        }
        
        $result = call_user_func($this->execute_callback, $input);
        
        // Logger la fin
        $duration = microtime(true) - $start_time;
        $this->log_execution($input, $result, $duration);
        
        return $result;
    }
    
    /**
     * Valider les paramètres d'entrée
     */
    private function validate_input($input) {
        $schema = $this->input_schema;
        
        // Vérifier les champs requis
        if (!empty($schema['required'])) {
            foreach ($schema['required'] as $field) {
                if (!isset($input[$field]) || $input[$field] === '') {
                    return new WP_Error(
                        'missing_field',
                        sprintf('Le champ "%s" est requis', $field),
                        ['status' => 400]
                    );
                }
            }
        }
        
        // Valider les types
        if (!empty($schema['properties'])) {
            foreach ($input as $key => $value) {
                if (isset($schema['properties'][$key]['type'])) {
                    $expected = $schema['properties'][$key]['type'];
                    $actual = gettype($value);
                    
                    // Mapping PHP -> JSON types
                    $type_map = [
                        'integer' => ['integer'],
                        'number' => ['integer', 'double'],
                        'string' => ['string'],
                        'boolean' => ['boolean'],
                        'array' => ['array'],
                        'object' => ['array', 'object'],
                    ];
                    
                    if (isset($type_map[$expected]) && !in_array($actual, $type_map[$expected])) {
                        return new WP_Error(
                            'invalid_type',
                            sprintf('Le champ "%s" doit être de type %s', $key, $expected),
                            ['status' => 400]
                        );
                    }
                }
                
                // Valider enum
                if (isset($schema['properties'][$key]['enum'])) {
                    if (!in_array($value, $schema['properties'][$key]['enum'])) {
                        return new WP_Error(
                            'invalid_value',
                            sprintf('Valeur invalide pour "%s". Attendu: %s', $key, implode(', ', $schema['properties'][$key]['enum'])),
                            ['status' => 400]
                        );
                    }
                }
            }
        }
        
        return true;
    }
    
    /**
     * Logger l'exécution
     */
    private function log_execution($input, $result, $duration) {
        if (!IA_Pilote_Core::is_group_enabled('logs')) {
            return;
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'adjm_mcp_logs';
        
        $wpdb->insert($table, [
            'ability' => $this->name,
            'user_id' => get_current_user_id(),
            'input' => wp_json_encode($input),
            'output' => wp_json_encode($result),
            'status' => is_wp_error($result) ? 'error' : 'success',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'duration' => $duration,
        ]);
    }
    
    /**
     * Convertir en format MCP
     */
    public function to_mcp_format() {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'inputSchema' => $this->input_schema,
            'annotations' => $this->meta['annotations'] ?? [
                'readonly' => false,
                'destructive' => false,
                'idempotent' => true,
            ],
        ];
    }
}
