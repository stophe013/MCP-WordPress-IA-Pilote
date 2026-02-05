<?php
/**
 * Serveur MCP - Endpoints REST API
 * 
 * @package IA_Pilote_MCP
 */

if (!defined('ABSPATH')) exit;

class IA_Pilote_Server {
    
    private $namespace = 'adjm-mcp/v1';
    
    /**
     * Enregistrer les routes REST
     */
    public function register_routes() {
        // Discovery - Liste toutes les abilities
        register_rest_route($this->namespace, '/discover', [
            'methods' => 'GET',
            'callback' => [$this, 'discover_abilities'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        // Execute - Exécuter une ability
        register_rest_route($this->namespace, '/execute', [
            'methods' => 'POST',
            'callback' => [$this, 'execute_ability'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        // Schema - Obtenir le schéma d'une ability
        register_rest_route($this->namespace, '/schema/(?P<ability>[a-zA-Z0-9\-_\/]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_ability_schema'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        // Health check
        register_rest_route($this->namespace, '/health', [
            'methods' => 'GET',
            'callback' => [$this, 'health_check'],
            'permission_callback' => '__return_true',
        ]);
        
        // MCP Protocol endpoints
        register_rest_route($this->namespace, '/mcp/tools/list', [
            'methods' => 'GET',
            'callback' => [$this, 'mcp_list_tools'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        register_rest_route($this->namespace, '/mcp/tools/call', [
            'methods' => 'POST',
            'callback' => [$this, 'mcp_call_tool'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
    }
    
    /**
     * Vérifier l'authentification
     */
    public function check_auth($request) {
        // Vérifier si l'utilisateur est connecté
        if (is_user_logged_in()) {
            return true;
        }
        
        // Vérifier l'authentification par Application Password
        $auth = $request->get_header('Authorization');
        if ($auth && strpos($auth, 'Basic ') === 0) {
            $credentials = base64_decode(substr($auth, 6));
            list($username, $password) = explode(':', $credentials, 2);
            
            $user = wp_authenticate_application_password(null, $username, $password);
            if ($user && !is_wp_error($user)) {
                wp_set_current_user($user->ID);
                return true;
            }
        }
        
        // Vérifier Bearer token (API Key custom)
        if ($auth && strpos($auth, 'Bearer ') === 0) {
            $token = substr($auth, 7);
            $stored_key = get_option('adjm_mcp_api_key');
            
            if ($token === $stored_key) {
                // Utiliser l'admin par défaut pour les API keys
                $admins = get_users(['role' => 'administrator', 'number' => 1]);
                if (!empty($admins)) {
                    wp_set_current_user($admins[0]->ID);
                    return true;
                }
            }
        }
        
        return new WP_Error('unauthorized', 'Authentification requise', ['status' => 401]);
    }
    
    /**
     * Discovery - Liste les abilities publiques
     */
    public function discover_abilities($request) {
        $abilities = IA_Pilote_Core::get_all_abilities();
        $result = [];
        
        foreach ($abilities as $name => $ability) {
            if (!$ability->is_public()) continue;
            if (!$ability->check_permission()) continue;
            
            $result[] = [
                'name' => $name,
                'label' => $ability->get_label(),
                'description' => $ability->get_description(),
                'category' => $ability->get_category(),
                'inputSchema' => $ability->get_input_schema(),
                'outputSchema' => $ability->get_output_schema(),
                'meta' => $ability->get_meta(),
            ];
        }
        
        return rest_ensure_response([
            'abilities' => $result,
            'total' => count($result),
            'version' => IA_PILOTE_VERSION,
        ]);
    }
    
    /**
     * Exécuter une ability
     */
    public function execute_ability($request) {
        $ability_name = $request->get_param('ability');
        $params = $request->get_param('params') ?? [];
        
        if (empty($ability_name)) {
            return new WP_Error('missing_ability', 'Le paramètre "ability" est requis', ['status' => 400]);
        }
        
        $ability = IA_Pilote_Core::get_ability($ability_name);
        
        if (!$ability) {
            return new WP_Error('not_found', 'Ability non trouvée: ' . $ability_name, ['status' => 404]);
        }
        
        $result = $ability->execute($params);
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        return rest_ensure_response([
            'success' => true,
            'ability' => $ability_name,
            'result' => $result,
        ]);
    }
    
    /**
     * Obtenir le schéma d'une ability
     */
    public function get_ability_schema($request) {
        $ability_name = $request->get_param('ability');
        $ability = IA_Pilote_Core::get_ability($ability_name);
        
        if (!$ability) {
            return new WP_Error('not_found', 'Ability non trouvée', ['status' => 404]);
        }
        
        return rest_ensure_response([
            'name' => $ability_name,
            'label' => $ability->get_label(),
            'description' => $ability->get_description(),
            'inputSchema' => $ability->get_input_schema(),
            'outputSchema' => $ability->get_output_schema(),
        ]);
    }
    
    /**
     * Health check
     */
    public function health_check($request) {
        return rest_ensure_response([
            'status' => 'ok',
            'version' => IA_PILOTE_VERSION,
            'wordpress' => get_bloginfo('version'),
            'php' => PHP_VERSION,
            'timestamp' => current_time('c'),
        ]);
    }
    
    /**
     * MCP Protocol - List tools
     */
    public function mcp_list_tools($request) {
        $abilities = IA_Pilote_Core::get_all_abilities();
        $tools = [];
        
        foreach ($abilities as $name => $ability) {
            if (!$ability->is_public()) continue;
            if (!$ability->check_permission()) continue;
            
            $tools[] = $ability->to_mcp_format();
        }
        
        return rest_ensure_response(['tools' => $tools]);
    }
    
    /**
     * MCP Protocol - Call tool
     */
    public function mcp_call_tool($request) {
        $name = $request->get_param('name');
        $arguments = $request->get_param('arguments') ?? [];
        
        $ability = IA_Pilote_Core::get_ability($name);
        
        if (!$ability) {
            return new WP_Error('not_found', 'Tool non trouvé: ' . $name, ['status' => 404]);
        }
        
        $result = $ability->execute($arguments);
        
        if (is_wp_error($result)) {
            return rest_ensure_response([
                'content' => [
                    ['type' => 'text', 'text' => $result->get_error_message()]
                ],
                'isError' => true,
            ]);
        }
        
        return rest_ensure_response([
            'content' => [
                ['type' => 'text', 'text' => is_string($result) ? $result : wp_json_encode($result, JSON_PRETTY_PRINT)]
            ],
            'isError' => false,
        ]);
    }
}
