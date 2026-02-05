<?php
/**
 * Système de Licence - IA Pilote MCP Ability
 * 
 * Gère les licences FREE/PRO et la vérification
 * 
 * @package IA_Pilote_MCP
 */

if (!defined('ABSPATH')) exit;

class IA_Pilote_License {
    
    /**
     * Instance singleton
     */
    private static $instance = null;
    
    /**
     * URL du serveur de licences
     */
    private $license_server = 'https://centerhome.net/api/licenses';
    
    /**
     * Statut de la licence en cache
     */
    private $license_status = null;
    
    /**
     * Mode développeur - Clés de test
     * ⚠️ À SUPPRIMER EN PRODUCTION ou désactiver via IA_PILOTE_DEV_MODE = false
     */
    private static $dev_keys = [
        // Clés PRO (1 site)
        'IAPILOTE-PRO-TEST-2026' => [
            'plan' => 'pro',
            'valid' => true,
            'expires' => '2027-12-31',
            'sites_allowed' => 1,
        ],
        'CENTER-PRO-XKCD-9999' => [
            'plan' => 'pro',
            'valid' => true,
            'expires' => '2027-12-31',
            'sites_allowed' => 1,
        ],
        
        // Clés BUSINESS (5 sites)
        'IAPILOTE-BIZ-TEST-2026' => [
            'plan' => 'business',
            'valid' => true,
            'expires' => '2027-12-31',
            'sites_allowed' => 5,
        ],
        'CENTER-BIZ-MEGA-8888' => [
            'plan' => 'business',
            'valid' => true,
            'expires' => '2027-12-31',
            'sites_allowed' => 5,
        ],
        
        // Clés AGENCY (illimité)
        'IAPILOTE-AGY-TEST-2026' => [
            'plan' => 'agency',
            'valid' => true,
            'expires' => '2027-12-31',
            'sites_allowed' => -1,
        ],
        'CENTER-AGY-ULTRA-7777' => [
            'plan' => 'agency',
            'valid' => true,
            'expires' => '2027-12-31',
            'sites_allowed' => -1,
        ],
    ];
    
    /**
     * Features FREE
     */
    private static $free_features = [
        'pages' => ['list', 'get', 'create'],
        'posts' => ['list', 'get', 'create'],
        'media' => ['list'],
        'categories' => ['list'],
        'site_info' => ['get'],
    ];
    
    /**
     * Features PRO
     */
    private static $pro_features = [
        'pages' => ['list', 'get', 'create', 'update', 'delete'],
        'posts' => ['list', 'get', 'create', 'update', 'delete'],
        'media' => ['list', 'upload', 'delete'],
        'categories' => ['list', 'create', 'update', 'delete'],
        'menus' => ['list', 'items'],
        'theme' => ['info', 'templates'],
        'users' => ['list', 'get', 'create'],
        'options' => ['get', 'update'],
        'site_info' => ['get'],
        'plugins' => ['list'],
        'woocommerce' => ['products', 'orders', 'customers', 'stats'],
        'seo' => ['get', 'update'],
        'acf' => ['get', 'update'],
        'forms' => ['list'],
        'bulk' => ['update', 'delete'],
        'logs' => ['view', 'clear'],
    ];
    
    /**
     * Limites par plan
     */
    private static $limits = [
        'free' => [
            'abilities_max' => 15,
            'requests_per_day' => 100,
            'sites' => 1,
        ],
        'pro' => [
            'abilities_max' => -1, // Illimité
            'requests_per_day' => -1,
            'sites' => 1,
        ],
        'business' => [
            'abilities_max' => -1,
            'requests_per_day' => -1,
            'sites' => 5,
        ],
        'agency' => [
            'abilities_max' => -1,
            'requests_per_day' => -1,
            'sites' => -1, // Illimité
        ],
    ];
    
    /**
     * Singleton
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructeur
     */
    private function __construct() {
        add_action('admin_init', [$this, 'handle_license_activation']);
        add_action('admin_notices', [$this, 'show_license_notices']);
    }
    
    /**
     * Récupérer le statut de licence
     */
    public function get_status() {
        if ($this->license_status !== null) {
            return $this->license_status;
        }
        
        $license_key = get_option('ia_pilote_license_key', '');
        
        if (empty($license_key)) {
            $this->license_status = [
                'plan' => 'free',
                'valid' => true,
                'expires' => null,
                'sites_used' => 1,
                'sites_allowed' => 1,
            ];
            return $this->license_status;
        }
        
        // Vérifier le cache
        $cached = get_transient('ia_pilote_license_status');
        if ($cached !== false) {
            $this->license_status = $cached;
            return $this->license_status;
        }
        
        // Vérifier auprès du serveur
        $this->license_status = $this->verify_license($license_key);
        
        // Mettre en cache pour 12h
        set_transient('ia_pilote_license_status', $this->license_status, 12 * HOUR_IN_SECONDS);
        
        return $this->license_status;
    }
    
    /**
     * Vérifier la licence auprès du serveur
     */
    private function verify_license($license_key) {
        $response = wp_remote_post($this->license_server . '/verify', [
            'timeout' => 15,
            'body' => [
                'license_key' => $license_key,
                'site_url' => home_url(),
                'plugin_version' => IA_PILOTE_VERSION,
            ],
        ]);
        
        if (is_wp_error($response)) {
            // En cas d'erreur réseau, utiliser le cache local
            $local = get_option('ia_pilote_license_data', []);
            if (!empty($local)) {
                return $local;
            }
            
            return [
                'plan' => 'free',
                'valid' => false,
                'error' => $response->get_error_message(),
            ];
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!empty($body['valid'])) {
            // Sauvegarder localement
            update_option('ia_pilote_license_data', $body);
        }
        
        return $body;
    }
    
    /**
     * Activer une licence
     */
    public function activate($license_key) {
        $license_key = strtoupper(trim($license_key));
        
        // Mode développeur - Vérifier les clés de test
        if ($this->is_dev_mode() && isset(self::$dev_keys[$license_key])) {
            $dev_data = self::$dev_keys[$license_key];
            $dev_data['success'] = true;
            $dev_data['sites_used'] = 1;
            $dev_data['dev_mode'] = true;
            
            update_option('ia_pilote_license_key', $license_key);
            update_option('ia_pilote_license_data', $dev_data);
            delete_transient('ia_pilote_license_status');
            $this->license_status = null;
            
            return $dev_data;
        }
        
        // Mode production - Vérifier auprès du serveur
        $response = wp_remote_post($this->license_server . '/activate', [
            'timeout' => 15,
            'body' => [
                'license_key' => $license_key,
                'site_url' => home_url(),
                'plugin_version' => IA_PILOTE_VERSION,
            ],
        ]);
        
        if (is_wp_error($response)) {
            return [
                'success' => false,
                'message' => $response->get_error_message(),
            ];
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!empty($body['success'])) {
            update_option('ia_pilote_license_key', sanitize_text_field($license_key));
            update_option('ia_pilote_license_data', $body);
            delete_transient('ia_pilote_license_status');
            $this->license_status = null;
        }
        
        return $body;
    }
    
    /**
     * Vérifier si le mode développeur est actif
     */
    private function is_dev_mode() {
        // Actif par défaut pour les tests
        // En production, définir IA_PILOTE_DEV_MODE = false dans wp-config.php
        if (defined('IA_PILOTE_DEV_MODE')) {
            return IA_PILOTE_DEV_MODE;
        }
        return true; // Actif par défaut
    }
    
    /**
     * Désactiver une licence
     */
    public function deactivate() {
        $license_key = get_option('ia_pilote_license_key', '');
        
        if (!empty($license_key)) {
            wp_remote_post($this->license_server . '/deactivate', [
                'timeout' => 15,
                'body' => [
                    'license_key' => $license_key,
                    'site_url' => home_url(),
                ],
            ]);
        }
        
        delete_option('ia_pilote_license_key');
        delete_option('ia_pilote_license_data');
        delete_transient('ia_pilote_license_status');
        $this->license_status = null;
        
        return ['success' => true, 'message' => 'Licence désactivée'];
    }
    
    /**
     * Vérifier si une feature est disponible
     */
    public function can_use_feature($group, $action = null) {
        $status = $this->get_status();
        $plan = $status['plan'] ?? 'free';
        
        // Plan PRO ou supérieur = tout autorisé
        if (in_array($plan, ['pro', 'business', 'agency'])) {
            return true;
        }
        
        // Plan FREE = vérifier les features autorisées
        if (!isset(self::$free_features[$group])) {
            return false;
        }
        
        if ($action === null) {
            return true;
        }
        
        return in_array($action, self::$free_features[$group]);
    }
    
    /**
     * Vérifier la limite de requêtes
     */
    public function check_request_limit() {
        $status = $this->get_status();
        $plan = $status['plan'] ?? 'free';
        $limits = self::$limits[$plan] ?? self::$limits['free'];
        
        if ($limits['requests_per_day'] === -1) {
            return true; // Illimité
        }
        
        $today = date('Y-m-d');
        $count_key = 'ia_pilote_requests_' . $today;
        $count = (int) get_transient($count_key);
        
        if ($count >= $limits['requests_per_day']) {
            return false;
        }
        
        // Incrémenter
        set_transient($count_key, $count + 1, DAY_IN_SECONDS);
        
        return true;
    }
    
    /**
     * Récupérer le nombre de requêtes restantes
     */
    public function get_remaining_requests() {
        $status = $this->get_status();
        $plan = $status['plan'] ?? 'free';
        $limits = self::$limits[$plan] ?? self::$limits['free'];
        
        if ($limits['requests_per_day'] === -1) {
            return -1; // Illimité
        }
        
        $today = date('Y-m-d');
        $count_key = 'ia_pilote_requests_' . $today;
        $count = (int) get_transient($count_key);
        
        return max(0, $limits['requests_per_day'] - $count);
    }
    
    /**
     * Vérifier si c'est un plan PRO
     */
    public function is_pro() {
        $status = $this->get_status();
        return in_array($status['plan'] ?? 'free', ['pro', 'business', 'agency']);
    }
    
    /**
     * Récupérer le plan actuel
     */
    public function get_plan() {
        $status = $this->get_status();
        return $status['plan'] ?? 'free';
    }
    
    /**
     * Gérer l'activation de licence via admin
     */
    public function handle_license_activation() {
        if (!current_user_can('manage_options')) return;
        
        // Activation
        if (isset($_POST['ia_pilote_activate_license']) && wp_verify_nonce($_POST['_wpnonce'], 'ia_pilote_license')) {
            $key = sanitize_text_field($_POST['license_key'] ?? '');
            $result = $this->activate($key);
            
            if (!empty($result['success'])) {
                add_settings_error('ia_pilote_license', 'activated', 
                    '✅ Licence activée avec succès ! Plan: ' . strtoupper($result['plan'] ?? 'PRO'), 
                    'success'
                );
            } else {
                add_settings_error('ia_pilote_license', 'error', 
                    '❌ Erreur: ' . ($result['message'] ?? 'Clé invalide'), 
                    'error'
                );
            }
        }
        
        // Désactivation
        if (isset($_POST['ia_pilote_deactivate_license']) && wp_verify_nonce($_POST['_wpnonce'], 'ia_pilote_license')) {
            $this->deactivate();
            add_settings_error('ia_pilote_license', 'deactivated', 
                '✅ Licence désactivée.', 
                'success'
            );
        }
    }
    
    /**
     * Afficher les notices de licence
     */
    public function show_license_notices() {
        $screen = get_current_screen();
        if (strpos($screen->id ?? '', 'ia-pilote') === false) return;
        
        settings_errors('ia_pilote_license');
        
        $status = $this->get_status();
        
        // Notice si FREE
        if (($status['plan'] ?? 'free') === 'free') {
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p>🔓 <strong>Version FREE</strong> - ';
            echo 'Passez à PRO pour débloquer toutes les abilities (WooCommerce, SEO, ACF...) + requêtes illimitées. ';
            echo '<a href="https://centerhome.net/ia-pilote-pro" target="_blank" class="button button-primary" style="margin-left:10px;">Passer à PRO →</a>';
            echo '</p></div>';
        }
        
        // Notice si licence expire bientôt
        if (!empty($status['expires'])) {
            $expires = strtotime($status['expires']);
            $days_left = floor(($expires - time()) / DAY_IN_SECONDS);
            
            if ($days_left > 0 && $days_left <= 30) {
                echo '<div class="notice notice-warning">';
                echo '<p>⚠️ Votre licence expire dans <strong>' . $days_left . ' jours</strong>. ';
                echo '<a href="https://centerhome.net/account" target="_blank">Renouveler →</a></p>';
                echo '</div>';
            }
        }
    }
    
    /**
     * Récupérer les features FREE
     */
    public static function get_free_features() {
        return self::$free_features;
    }
    
    /**
     * Récupérer les features PRO
     */
    public static function get_pro_features() {
        return self::$pro_features;
    }
    
    /**
     * Récupérer les limites par plan
     */
    public static function get_limits($plan = null) {
        if ($plan) {
            return self::$limits[$plan] ?? self::$limits['free'];
        }
        return self::$limits;
    }
}

// Initialiser
IA_Pilote_License::get_instance();

// Fonctions helpers globales
function ia_pilote_is_pro() {
    return IA_Pilote_License::get_instance()->is_pro();
}

function ia_pilote_can_use($group, $action = null) {
    return IA_Pilote_License::get_instance()->can_use_feature($group, $action);
}

function ia_pilote_check_limit() {
    return IA_Pilote_License::get_instance()->check_request_limit();
}
