<?php
/**
 * Plugin Name: IA Pilote MCP Ability
 * Plugin URI: https://centerhome.net
 * Description: Solution MCP complète pour WordPress - Abilities API + MCP Adapter + Abilities intégrées
 * Version: 1.6.0
 * Author: CenterHome
 * Author URI: https://centerhome.net
 * License: GPL-2.0+
 * Text Domain: adjm-mcp
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

// Constantes du plugin
define('IA_PILOTE_VERSION', '1.6.0');
define('IA_PILOTE_PATH', plugin_dir_path(__FILE__));
define('IA_PILOTE_URL', plugin_dir_url(__FILE__));
define('IA_PILOTE_BASENAME', plugin_basename(__FILE__));

// Constantes legacy pour compatibilité


/**
 * Classe principale du plugin IA Pilote MCP
 */
final class IA_Pilote_Core {

    private static $instance = null;
    
    /**
     * Abilities enregistrées
     */
    private static $abilities = [];
    
    /**
     * Catégories d'abilities
     */
    private static $categories = [];
    
    /**
     * Settings des abilities activées
     */
    private static $abilities_settings = [];

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
        // Charger les composants
        $this->load_dependencies();
        
        // Hooks d'activation
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        
        // Initialisation
        add_action('plugins_loaded', [$this, 'init'], 5);
        add_action('init', [$this, 'register_abilities'], 10);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        
        // Admin
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        
        // AJAX
        add_action('wp_ajax_adjm_test_ability', [$this, 'ajax_test_ability']);
        add_action('wp_ajax_adjm_save_settings', [$this, 'ajax_save_settings']);

        // Actions admin (POST)
        add_action('admin_post_ia_pilote_regenerate_api_key', [$this, 'handle_regenerate_api_key']);
    }

    /**
     * Charger les dépendances
     */
    private function load_dependencies() {
        // Définir le chemin de base
        $base_path = trailingslashit(plugin_dir_path(__FILE__));
        
        // Liste des fichiers critiques à charger
        $files = [
            'includes/class-ability.php',
            'includes/abilities-functions.php',
            'includes/class-license.php',
            'includes/class-mcp-server.php'
        ];
        
        foreach ($files as $file) {
            $path = $base_path . $file;
            if (file_exists($path)) {
                require_once $path;
            } else {
                // Log l'erreur mais ne pas planter fatalement si possible, ou afficher une admin notice
                error_log("IA PILOTE ERROR: Impossible de trouver le fichier critique : " . $path);
                
                // Si on est dans l'admin, on essaie d'afficher un message
                if (is_admin()) {
                    add_action('admin_notices', function() use ($path) {
                        echo '<div class="error"><p><strong>IA Pilote MCP Error:</strong> Fichier manquant : ' . esc_html($path) . '</p></div>';
                    });
                }
            }
        }
    }

    /**
     * Activation
     */
    public function activate() {
        // Options par défaut
        $default_settings = [
            'pages' => true,
            'posts' => true,
            'media' => true,
            'categories' => true,
            'menus' => true,
            'theme' => true,
            'users' => true,
            'options' => true,
            'site_info' => true,
            'plugins' => false,
            'woocommerce' => true,
            'seo' => true,
            'acf' => true,
            'forms' => true,
            'bulk' => false,
            'multisite' => false,
            'logs' => true,
            'adjm_custom' => true,
        ];
        
        add_option('adjm_mcp_abilities', $default_settings);
        add_option('adjm_mcp_version', IA_PILOTE_VERSION);
        add_option('adjm_mcp_api_key', wp_generate_password(32, false));
        
        // Créer table de logs
        $this->create_logs_table();
        
        flush_rewrite_rules();
    }

    /**
     * Désactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Créer table de logs
     */
    private function create_logs_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'adjm_mcp_logs';
        $charset = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            ability VARCHAR(100) NOT NULL,
            user_id BIGINT UNSIGNED,
            input LONGTEXT,
            output LONGTEXT,
            status VARCHAR(20),
            ip VARCHAR(45),
            duration FLOAT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ability (ability),
            INDEX idx_user (user_id),
            INDEX idx_created (created_at)
        ) $charset;";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Initialisation
     */
    public function init() {
        self::$abilities_settings = get_option('adjm_mcp_abilities', []);
        
        // Déclencher l'init des catégories
        do_action('adjm_mcp_categories_init');
        
        // Enregistrer les catégories par défaut
        $this->register_default_categories();
    }

    /**
     * Catégories par défaut
     */
    private function register_default_categories() {
        $categories = [
            'content' => ['label' => 'Contenu', 'description' => 'Gestion du contenu WordPress'],
            'appearance' => ['label' => 'Apparence', 'description' => 'Thèmes et menus'],
            'users' => ['label' => 'Utilisateurs', 'description' => 'Gestion des utilisateurs'],
            'system' => ['label' => 'Système', 'description' => 'Options et plugins'],
            'woocommerce' => ['label' => 'WooCommerce', 'description' => 'E-commerce'],
            'seo' => ['label' => 'SEO', 'description' => 'Référencement'],
            'forms' => ['label' => 'Formulaires', 'description' => 'CF7 et Gravity Forms'],
            'acf' => ['label' => 'ACF', 'description' => 'Custom Fields'],
            'adjm' => ['label' => 'ADJM Custom', 'description' => 'Abilities ADJM personnalisées'],
        ];
        
        foreach ($categories as $slug => $data) {
            self::register_category($slug, $data);
        }
    }

    /**
     * Enregistrer une catégorie
     */
    public static function register_category($slug, $args) {
        self::$categories[$slug] = $args;
    }

    /**
     * Enregistrer une ability
     */
    public static function register_ability($name, $args) {
        if ($args instanceof IA_Pilote_Ability) {
            $ability = $args;
        } else {
            $ability = new IA_Pilote_Ability($name, $args);
        }
        self::$abilities[$name] = $ability;
    }

    /**
     * Récupérer une ability
     */
    public static function get_ability($name) {
        return self::$abilities[$name] ?? null;
    }

    /**
     * Récupérer toutes les abilities
     */
    public static function get_all_abilities() {
        return self::$abilities;
    }

    /**
     * Vérifier si un groupe est activé
     */
    public static function is_group_enabled($group) {
        return !empty(self::$abilities_settings[$group]);
    }

    /**
     * Enregistrer les abilities
     */
    public function register_abilities() {
        // Charger tous les fichiers d'abilities
        $abilities_dir = IA_PILOTE_PATH . 'abilities/';
        
        if (is_dir($abilities_dir)) {
            foreach (glob($abilities_dir . '*.php') as $file) {
                require_once $file;
            }
        }
        
        // Hook pour abilities custom
        do_action('adjm_mcp_register_abilities');
    }

    /**
     * Enregistrer les routes REST
     */
    public function register_rest_routes() {
        // Initialize Server
        $server = new IA_Pilote_Server();
        $server->register_routes();
    }

    /**
     * Menu admin
     */
    public function add_admin_menu() {
        add_menu_page(
            __('IA Pilote MCP', 'adjm-mcp'),
            __('IA Pilote MCP', 'adjm-mcp'),
            'manage_options',
            'ia-pilote',
            [$this, 'render_dashboard'],
            'dashicons-controls-play',
            80
        );
        
        add_submenu_page('ia-pilote', __('Tableau de bord', 'adjm-mcp'), __('Tableau de bord', 'adjm-mcp'), 'manage_options', 'ia-pilote', [$this, 'render_dashboard']);
        add_submenu_page('ia-pilote', __('Abilities', 'adjm-mcp'), __('Abilities', 'adjm-mcp'), 'manage_options', 'ia-pilote-abilities', [$this, 'render_abilities']);
        add_submenu_page('ia-pilote', __('Licence', 'adjm-mcp'), __('Licence', 'adjm-mcp'), 'manage_options', 'ia-pilote-license', [$this, 'render_license']);
        add_submenu_page('ia-pilote', __('Logs', 'adjm-mcp'), __('Logs', 'adjm-mcp'), 'manage_options', 'ia-pilote-logs', [$this, 'render_logs']);
        add_submenu_page('ia-pilote', __('Documentation', 'adjm-mcp'), __('Documentation', 'adjm-mcp'), 'manage_options', 'ia-pilote-docs', [$this, 'render_docs']);
    }

    /**
     * Settings
     */
    public function register_settings() {
        register_setting('adjm_mcp_settings', 'adjm_mcp_abilities');
    }

    /**
     * Assets admin
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'ia-pilote') === false) return;
        
        wp_enqueue_style('ia-pilote-admin', IA_PILOTE_URL . 'assets/admin.css', [], IA_PILOTE_VERSION);
        wp_enqueue_script('ia-pilote-admin', IA_PILOTE_URL . 'assets/admin.js', ['jquery'], IA_PILOTE_VERSION, true);
        wp_localize_script('ia-pilote-admin', 'adjmMcp', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('adjm_mcp'),
            'restUrl' => rest_url('adjm-mcp/v1/'),
            'isPro' => ia_pilote_is_pro(),
        ]);
    }

    /**
     * Régénérer la clé API (Bearer)
     */
    public function handle_regenerate_api_key() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Permissions insuffisantes', 'adjm-mcp'));
        }

        check_admin_referer('ia_pilote_regenerate_api_key');

        update_option('adjm_mcp_api_key', wp_generate_password(32, false));

        wp_safe_redirect(admin_url('admin.php?page=ia-pilote&api_key_regenerated=1'));
        exit;
    }

    /**
     * Page Dashboard
     */
    public function render_dashboard() {
        $abilities_count = count(self::$abilities);
        $enabled_groups = count(array_filter(self::$abilities_settings));
        $license = IA_Pilote_License::get_instance();
        $plan = $license->get_plan();
        $api_key = (string) get_option('adjm_mcp_api_key', '');
        $api_key_masked = $api_key ? (substr($api_key, 0, 4) . str_repeat('•', max(0, strlen($api_key) - 8)) . substr($api_key, -4)) : '';
        $api_key_regenerated = !empty($_GET['api_key_regenerated']);
        ?>
        <div class="wrap adjm-mcp-wrap">
            <h1><span class="dashicons dashicons-controls-play"></span> IA Pilote MCP Ability</h1>

            <?php if ($api_key_regenerated): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e('Clé API MCP régénérée avec succès.', 'adjm-mcp'); ?></p>
                </div>
            <?php endif; ?>
            
            <div style="background: <?php echo $plan === 'free' ? '#fef3c7' : '#d1fae5'; ?>; padding: 10px 15px; border-radius: 8px; margin-bottom: 20px; display: inline-block;">
                <strong>Plan :</strong> <?php echo strtoupper($plan); ?>
                <?php if ($plan === 'free'): ?>
                    — <a href="<?php echo admin_url('admin.php?page=ia-pilote-license'); ?>">Passer à PRO →</a>
                <?php endif; ?>
            </div>
            
            <div class="adjm-cards">
                <div class="adjm-card adjm-card-success">
                    <div class="adjm-card-icon"><span class="dashicons dashicons-yes-alt"></span></div>
                    <div class="adjm-card-content">
                        <h3><?php _e('Système opérationnel', 'adjm-mcp'); ?></h3>
                        <p><?php _e('Toutes les composantes sont intégrées et actives.', 'adjm-mcp'); ?></p>
                    </div>
                </div>
                
                <div class="adjm-card">
                    <div class="adjm-card-icon"><span class="dashicons dashicons-admin-tools"></span></div>
                    <div class="adjm-card-content">
                        <h3><?php _e('Abilities disponibles', 'adjm-mcp'); ?></h3>
                        <p class="adjm-stat"><?php echo $abilities_count; ?></p>
                    </div>
                </div>
                
                <div class="adjm-card">
                    <div class="adjm-card-icon"><span class="dashicons dashicons-plugins-checked"></span></div>
                    <div class="adjm-card-content">
                        <h3><?php _e('Groupes activés', 'adjm-mcp'); ?></h3>
                        <p class="adjm-stat"><?php echo $enabled_groups; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="adjm-section">
                <h2><?php _e('Configuration MCP Client', 'adjm-mcp'); ?></h2>
                <div class="adjm-code-block">
                    <p><?php _e('Ajoutez cette configuration dans votre fichier mcp_config.json :', 'adjm-mcp'); ?></p>
                    <pre><code>{
  "mcpServers": {
    "wordpress-<?php echo sanitize_title(get_bloginfo('name')); ?>": {
      "command": "npx",
      "args": ["-y", "@anthropic-ai/mcp-wordpress"],
      "env": {
        "WP_URL": "<?php echo esc_url(home_url()); ?>",
        "WP_USERNAME": "votre-utilisateur",
        "WP_APP_PASSWORD": "xxxx xxxx xxxx xxxx"
      }
    }
  }
}</code></pre>
                </div>

                <details style="margin-top: 12px;">
                    <summary style="cursor: pointer; font-weight: 600;"><?php _e('Alternative : MCP Remote (endpoint WordPress)', 'adjm-mcp'); ?></summary>
                    <div class="adjm-code-block" style="margin-top: 12px;">
                        <p><?php _e('Cette variante utilise l\'endpoint MCP natif du plugin (tools/list, tools/call).', 'adjm-mcp'); ?></p>
                        <pre><code>{
  "mcpServers": {
    "wordpress-<?php echo sanitize_title(get_bloginfo('name')); ?>": {
      "command": "npx",
      "args": ["-y", "@anthropic-ai/mcp-remote"],
      "env": {
        "MCP_ENDPOINT": "<?php echo esc_url(untrailingslashit(rest_url('adjm-mcp/v1/mcp'))); ?>",
        "MCP_HEADERS": "{\"Authorization\":\"Basic BASE64_ENCODED_CREDENTIALS\"}"
      }
    }
  }
}</code></pre>
                    </div>
                </details>
            </div>
            
            <div class="adjm-section">
                <h2><?php _e('Endpoints REST API', 'adjm-mcp'); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Endpoint', 'adjm-mcp'); ?></th>
                            <th><?php _e('Méthode', 'adjm-mcp'); ?></th>
                            <th><?php _e('Description', 'adjm-mcp'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code><?php echo rest_url('adjm-mcp/v1/health'); ?></code></td>
                            <td>GET</td>
                            <td><?php _e('Vérifier le statut du serveur', 'adjm-mcp'); ?></td>
                        </tr>
                        <tr>
                            <td><code><?php echo rest_url('adjm-mcp/v1/discover'); ?></code></td>
                            <td>GET</td>
                            <td><?php _e('Liste des abilities disponibles', 'adjm-mcp'); ?></td>
                        </tr>
                        <tr>
                            <td><code><?php echo rest_url('adjm-mcp/v1/execute'); ?></code></td>
                            <td>POST</td>
                            <td><?php _e('Exécuter une ability', 'adjm-mcp'); ?></td>
                        </tr>
                        <tr>
                            <td><code><?php echo rest_url('adjm-mcp/v1/schema/{ability}'); ?></code></td>
                            <td>GET</td>
                            <td><?php _e('Schéma d\'une ability', 'adjm-mcp'); ?></td>
                        </tr>
                        <tr>
                            <td><code><?php echo rest_url('adjm-mcp/v1/mcp/tools/list'); ?></code></td>
                            <td>GET</td>
                            <td><?php _e('Liste des tools (format MCP)', 'adjm-mcp'); ?></td>
                        </tr>
                        <tr>
                            <td><code><?php echo rest_url('adjm-mcp/v1/mcp/tools/call'); ?></code></td>
                            <td>POST</td>
                            <td><?php _e('Appeler un tool (format MCP)', 'adjm-mcp'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="adjm-section">
                <h2><?php _e('Authentification', 'adjm-mcp'); ?></h2>

                <p><?php _e('Recommandé : créer un utilisateur WordPress dédié + un mot de passe d’application (Profil → Mots de passe d’application).', 'adjm-mcp'); ?></p>

                <h3 style="margin: 0 0 10px;"><?php _e('Option A — Basic (Application Password)', 'adjm-mcp'); ?></h3>
                <div class="adjm-code-block">
                    <pre><code>Authorization: Basic BASE64(username:application_password)</code></pre>
                </div>

                <details style="margin-top: 12px;">
                    <summary style="cursor: pointer; font-weight: 600;"><?php _e('Aide : générer le Base64 (Windows / Mac / Linux)', 'adjm-mcp'); ?></summary>
                    <div class="adjm-code-block" style="margin-top: 12px;">
                        <pre><code># PowerShell (Windows)
[Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes("username:application_password"))

# Bash (Mac/Linux)
echo -n "username:application_password" | base64</code></pre>
                    </div>
                </details>

                <h3 style="margin: 20px 0 10px;"><?php _e('Option B — Bearer (clé API)', 'adjm-mcp'); ?></h3>
                <p style="margin-top: 0;"><?php _e('Utile pour automatiser sans Application Password. Gardez cette clé secrète.', 'adjm-mcp'); ?></p>

                <div class="adjm-code-block">
                    <p><?php echo $api_key ? sprintf(__('Clé actuelle (masquée) : %s', 'adjm-mcp'), esc_html($api_key_masked)) : __('Clé introuvable (option manquante). Réactivez le plugin ou régénérez la clé.', 'adjm-mcp'); ?></p>
                    <pre><code>Authorization: Bearer <?php echo esc_html($api_key ?: 'VOTRE_CLE_API'); ?></code></pre>
                </div>

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top: 12px;">
                    <input type="hidden" name="action" value="ia_pilote_regenerate_api_key">
                    <?php wp_nonce_field('ia_pilote_regenerate_api_key'); ?>
                    <?php submit_button(__('Régénérer la clé API', 'adjm-mcp'), 'secondary', 'submit', false); ?>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Page Abilities
     */
    public function render_abilities() {
        $groups = [
            'content' => ['label' => '📝 Contenu', 'items' => ['pages', 'posts', 'media', 'categories']],
            'appearance' => ['label' => '🎨 Apparence', 'items' => ['menus', 'theme', 'header_footer', 'responsive']],
            'page_builders' => ['label' => '🏗️ Page Builders', 'items' => ['elementor', 'divi']],
            'design' => ['label' => '🎯 Design System', 'items' => ['design_system', 'social_links']],
            'users' => ['label' => '👥 Utilisateurs', 'items' => ['users']],
            'system' => ['label' => '⚙️ Système', 'items' => ['options', 'site_info', 'plugins']],
            'extensions' => ['label' => '🔌 Extensions', 'items' => ['woocommerce', 'seo', 'acf', 'forms']],
            'advanced' => ['label' => '🚀 Avancées', 'items' => ['bulk', 'multisite', 'logs']],
            'custom' => ['label' => '⭐ ADJM Custom', 'items' => ['adjm_custom']],
        ];
        ?>
        <div class="wrap adjm-mcp-wrap">
            <h1><span class="dashicons dashicons-admin-tools"></span> <?php _e('Gestion des Abilities', 'adjm-mcp'); ?></h1>
            
            <form method="post" action="options.php">
                <?php settings_fields('adjm_mcp_settings'); ?>
                
                <?php foreach ($groups as $group_key => $group): ?>
                <div class="adjm-section">
                    <h2><?php echo esc_html($group['label']); ?></h2>
                    <div class="adjm-abilities-grid">
                        <?php foreach ($group['items'] as $item): 
                            $enabled = !empty(self::$abilities_settings[$item]);
                        ?>
                        <div class="adjm-ability-card <?php echo $enabled ? 'enabled' : 'disabled'; ?>">
                            <label>
                                <input type="checkbox" 
                                       name="adjm_mcp_abilities[<?php echo esc_attr($item); ?>]" 
                                       value="1" 
                                       <?php checked($enabled); ?>>
                                <span class="adjm-ability-name"><?php echo esc_html(ucfirst(str_replace('_', ' ', $item))); ?></span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php submit_button(__('Enregistrer', 'adjm-mcp')); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Page Licence
     */
    public function render_license() {
        $license = IA_Pilote_License::get_instance();
        $status = $license->get_status();
        $plan = $status['plan'] ?? 'free';
        $is_pro = in_array($plan, ['pro', 'business', 'agency']);
        $license_key = get_option('ia_pilote_license_key', '');
        ?>
        <div class="wrap adjm-mcp-wrap">
            <h1><span class="dashicons dashicons-admin-network"></span> <?php _e('Licence IA Pilote MCP', 'adjm-mcp'); ?></h1>
            
            <!-- Statut actuel -->
            <div class="adjm-section" style="background: <?php echo $is_pro ? 'linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)' : 'linear-gradient(135deg, #fef3c7 0%, #fde68a 100%)'; ?>;">
                <div style="display: flex; align-items: center; gap: 20px;">
                    <div style="font-size: 48px;"><?php echo $is_pro ? '💎' : '🆓'; ?></div>
                    <div>
                        <h2 style="margin: 0; font-size: 24px;">Plan <?php echo strtoupper($plan); ?></h2>
                        <?php if (!$is_pro): ?>
                            <p style="margin: 5px 0 0;"><?php _e('Passez à PRO pour débloquer toutes les fonctionnalités !', 'adjm-mcp'); ?></p>
                        <?php else: ?>
                            <p style="margin: 5px 0 0;">
                                <?php _e('Licence active', 'adjm-mcp'); ?>
                                <?php if (!empty($status['expires'])): ?>
                                    — <?php printf(__('Expire le %s', 'adjm-mcp'), date_i18n(get_option('date_format'), strtotime($status['expires']))); ?>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Formulaire de licence -->
            <div class="adjm-section">
                <h2><?php _e('Clé de Licence', 'adjm-mcp'); ?></h2>
                
                <form method="post" action="">
                    <?php wp_nonce_field('ia_pilote_license'); ?>
                    
                    <?php if ($is_pro): ?>
                        <p>
                            <strong><?php _e('Clé active :', 'adjm-mcp'); ?></strong>
                            <code><?php echo substr($license_key, 0, 8) . '••••••••' . substr($license_key, -4); ?></code>
                        </p>
                        <p>
                            <input type="submit" name="ia_pilote_deactivate_license" class="button" value="<?php _e('Désactiver la licence', 'adjm-mcp'); ?>">
                        </p>
                    <?php else: ?>
                        <p>
                            <input type="text" name="license_key" class="regular-text" placeholder="XXXX-XXXX-XXXX-XXXX" style="width: 350px;">
                            <input type="submit" name="ia_pilote_activate_license" class="button button-primary" value="<?php _e('Activer', 'adjm-mcp'); ?>">
                        </p>
                        <p class="description">
                            <?php _e('Entrez votre clé de licence pour activer les fonctionnalités PRO.', 'adjm-mcp'); ?>
                            <a href="https://centerhome.net/ia-pilote-pro" target="_blank"><?php _e('Acheter une licence →', 'adjm-mcp'); ?></a>
                        </p>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- Comparatif FREE vs PRO -->
            <div class="adjm-section">
                <h2><?php _e('Comparatif FREE vs PRO', 'adjm-mcp'); ?></h2>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Fonctionnalité', 'adjm-mcp'); ?></th>
                            <th style="text-align:center;">🆓 FREE</th>
                            <th style="text-align:center;">💎 PRO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php _e('Pages & Posts (list, get, create)', 'adjm-mcp'); ?></td>
                            <td style="text-align:center;">✅</td>
                            <td style="text-align:center;">✅</td>
                        </tr>
                        <tr>
                            <td><?php _e('Pages & Posts (update, delete)', 'adjm-mcp'); ?></td>
                            <td style="text-align:center;">❌</td>
                            <td style="text-align:center;">✅</td>
                        </tr>
                        <tr>
                            <td><?php _e('Médias (upload)', 'adjm-mcp'); ?></td>
                            <td style="text-align:center;">❌</td>
                            <td style="text-align:center;">✅</td>
                        </tr>
                        <tr>
                            <td><?php _e('Menus & Thème', 'adjm-mcp'); ?></td>
                            <td style="text-align:center;">❌</td>
                            <td style="text-align:center;">✅</td>
                        </tr>
                        <tr>
                            <td><?php _e('Utilisateurs', 'adjm-mcp'); ?></td>
                            <td style="text-align:center;">❌</td>
                            <td style="text-align:center;">✅</td>
                        </tr>
                        <tr>
                            <td><?php _e('Options système', 'adjm-mcp'); ?></td>
                            <td style="text-align:center;">❌</td>
                            <td style="text-align:center;">✅</td>
                        </tr>
                        <tr style="background: #fef3c7;">
                            <td><strong><?php _e('WooCommerce (Produits, Commandes, Stats)', 'adjm-mcp'); ?></strong></td>
                            <td style="text-align:center;">❌</td>
                            <td style="text-align:center;">✅</td>
                        </tr>
                        <tr style="background: #fef3c7;">
                            <td><strong><?php _e('SEO (Yoast / Rank Math)', 'adjm-mcp'); ?></strong></td>
                            <td style="text-align:center;">❌</td>
                            <td style="text-align:center;">✅</td>
                        </tr>
                        <tr style="background: #fef3c7;">
                            <td><strong><?php _e('ACF (Custom Fields)', 'adjm-mcp'); ?></strong></td>
                            <td style="text-align:center;">❌</td>
                            <td style="text-align:center;">✅</td>
                        </tr>
                        <tr>
                            <td><?php _e('Formulaires (CF7 / Gravity)', 'adjm-mcp'); ?></td>
                            <td style="text-align:center;">❌</td>
                            <td style="text-align:center;">✅</td>
                        </tr>
                        <tr>
                            <td><?php _e('Bulk Operations', 'adjm-mcp'); ?></td>
                            <td style="text-align:center;">❌</td>
                            <td style="text-align:center;">✅</td>
                        </tr>
                        <tr>
                            <td><?php _e('Requêtes par jour', 'adjm-mcp'); ?></td>
                            <td style="text-align:center;"><strong>100</strong></td>
                            <td style="text-align:center;"><strong>♾️ Illimité</strong></td>
                        </tr>
                        <tr>
                            <td><?php _e('Mises à jour automatiques', 'adjm-mcp'); ?></td>
                            <td style="text-align:center;">❌</td>
                            <td style="text-align:center;">✅</td>
                        </tr>
                        <tr>
                            <td><?php _e('Support prioritaire', 'adjm-mcp'); ?></td>
                            <td style="text-align:center;">❌</td>
                            <td style="text-align:center;">✅</td>
                        </tr>
                    </tbody>
                </table>
                
                <?php if (!$is_pro): ?>
                <div style="text-align: center; margin-top: 30px;">
                    <a href="https://centerhome.net/ia-pilote-pro" target="_blank" class="button button-primary button-hero" style="font-size: 18px; padding: 15px 40px;">
                        🚀 <?php _e('Passer à PRO - 49€/an', 'adjm-mcp'); ?>
                    </a>
                    <p style="margin-top: 10px; color: #666;">
                        <?php _e('Garantie satisfait ou remboursé 30 jours', 'adjm-mcp'); ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Page Logs
     */
    public function render_logs() {
        global $wpdb;
        $table = $wpdb->prefix . 'adjm_mcp_logs';
        $logs = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 100");
        ?>
        <div class="wrap adjm-mcp-wrap">
            <h1><span class="dashicons dashicons-list-view"></span> <?php _e('Logs MCP', 'adjm-mcp'); ?></h1>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Date', 'adjm-mcp'); ?></th>
                        <th><?php _e('Ability', 'adjm-mcp'); ?></th>
                        <th><?php _e('User', 'adjm-mcp'); ?></th>
                        <th><?php _e('Status', 'adjm-mcp'); ?></th>
                        <th><?php _e('Durée', 'adjm-mcp'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                    <tr><td colspan="5"><?php _e('Aucun log pour le moment.', 'adjm-mcp'); ?></td></tr>
                    <?php else: foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo esc_html($log->created_at); ?></td>
                        <td><code><?php echo esc_html($log->ability); ?></code></td>
                        <td><?php echo esc_html(get_userdata($log->user_id)->display_name ?? 'N/A'); ?></td>
                        <td><span class="adjm-badge adjm-badge-<?php echo $log->status; ?>"><?php echo esc_html($log->status); ?></span></td>
                        <td><?php echo esc_html(round($log->duration * 1000)); ?>ms</td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * Page Documentation
     */
    public function render_docs() {
        $api_base = untrailingslashit(rest_url('adjm-mcp/v1'));
        $mcp_base = $api_base . '/mcp';

        $fiche_path = IA_PILOTE_PATH . 'docs/FICHE-INSTALLATION-CONFIG-MCP.md';
        $guide_path = IA_PILOTE_PATH . 'docs/CONFIGURATION-MCP.md';

        $fiche_md = (file_exists($fiche_path) && is_readable($fiche_path)) ? (string) file_get_contents($fiche_path) : '';
        $guide_md = (file_exists($guide_path) && is_readable($guide_path)) ? (string) file_get_contents($guide_path) : '';
        ?>
        <div class="wrap adjm-mcp-wrap">
            <h1><span class="dashicons dashicons-book"></span> <?php _e('Documentation', 'adjm-mcp'); ?></h1>
            
            <div class="adjm-section">
                <h2><?php _e('Structure d\'une Ability', 'adjm-mcp'); ?></h2>
                <div class="adjm-code-block">
                    <pre><code>adjm_register_ability('namespace/nom', [
    'label'       => 'Nom affiché',
    'description' => 'Description',
    'category'    => 'content',
    'input_schema' => [
        'type' => 'object',
        'properties' => [
            'param' => ['type' => 'string'],
        ],
        'required' => ['param'],
    ],
    'execute_callback' => function($input) {
        return ['result' => 'ok'];
    },
    'permission_callback' => function() {
        return current_user_can('edit_posts');
    },
]);</code></pre>
                </div>
            </div>

            <div class="adjm-section">
                <h2><?php _e('Installation et configuration MCP', 'adjm-mcp'); ?></h2>

                <h3><?php _e('Prérequis', 'adjm-mcp'); ?></h3>
                <ul>
                    <li><?php _e('Plugin IA Pilote MCP Ability activé.', 'adjm-mcp'); ?></li>
                    <li><?php _e('Permaliens activés (Réglages → Permaliens → Enregistrer).', 'adjm-mcp'); ?></li>
                    <li><?php _e('Node.js LTS installé côté client (pour npx).', 'adjm-mcp'); ?></li>
                    <li><?php _e('Un utilisateur dédié avec mot de passe d’application.', 'adjm-mcp'); ?></li>
                </ul>

                <h3><?php _e('Endpoints utiles', 'adjm-mcp'); ?></h3>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Usage', 'adjm-mcp'); ?></th>
                            <th><?php _e('Endpoint', 'adjm-mcp'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php _e('Health check', 'adjm-mcp'); ?></td>
                            <td><code><?php echo esc_html($api_base . '/health'); ?></code></td>
                        </tr>
                        <tr>
                            <td><?php _e('REST discover', 'adjm-mcp'); ?></td>
                            <td><code><?php echo esc_html($api_base . '/discover'); ?></code></td>
                        </tr>
                        <tr>
                            <td><?php _e('REST execute', 'adjm-mcp'); ?></td>
                            <td><code><?php echo esc_html($api_base . '/execute'); ?></code></td>
                        </tr>
                        <tr>
                            <td><?php _e('MCP tools list', 'adjm-mcp'); ?></td>
                            <td><code><?php echo esc_html($mcp_base . '/tools/list'); ?></code></td>
                        </tr>
                        <tr>
                            <td><?php _e('MCP tools call', 'adjm-mcp'); ?></td>
                            <td><code><?php echo esc_html($mcp_base . '/tools/call'); ?></code></td>
                        </tr>
                    </tbody>
                </table>

                <h3><?php _e('Claude Desktop (recommandé)', 'adjm-mcp'); ?></h3>
                <div class="adjm-code-block">
                    <p><?php _e('Fichier: %APPDATA%\\Claude\\claude_desktop_config.json (Windows) ou ~/Library/Application Support/Claude/claude_desktop_config.json (macOS)', 'adjm-mcp'); ?></p>
                    <pre><code>{
  "mcpServers": {
    "wordpress": {
      "command": "npx",
      "args": ["-y", "@anthropic-ai/mcp-remote"],
      "env": {
        "MCP_ENDPOINT": "<?php echo esc_html($mcp_base); ?>",
        "MCP_HEADERS": "{\"Authorization\":\"Basic BASE64_ENCODED_CREDENTIALS\"}"
      }
    }
  }
}</code></pre>
                </div>

                <div class="adjm-code-block" style="margin-top:12px;">
                    <p><?php _e('Générer le Base64 (Windows / Mac / Linux)', 'adjm-mcp'); ?></p>
                    <pre><code># PowerShell
[Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes("USERNAME:APP_PASSWORD"))

# Bash
echo -n "USERNAME:APP_PASSWORD" | base64</code></pre>
                </div>

                <h3><?php _e('Tests rapides', 'adjm-mcp'); ?></h3>
                <div class="adjm-code-block">
                    <pre><code># Health
curl <?php echo esc_html($api_base . '/health'); ?>

# MCP tools list
curl -H "Authorization: Basic BASE64_CREDENTIALS" \
  <?php echo esc_html($mcp_base . '/tools/list'); ?></code></pre>
                </div>

                <details style="margin-top: 12px;">
                    <summary style="cursor: pointer; font-weight: 600;"><?php _e('📋 Fiche rapide (copier/coller)', 'adjm-mcp'); ?></summary>
                    <div class="adjm-code-block" style="margin-top: 12px;">
                        <pre style="max-height: 520px; overflow: auto;"><code><?php echo esc_html($fiche_md ?: __('Fichier de fiche introuvable.', 'adjm-mcp')); ?></code></pre>
                    </div>
                </details>

                <details style="margin-top: 12px;">
                    <summary style="cursor: pointer; font-weight: 600;"><?php _e('🔧 Guide complet (CONFIGURATION-MCP.md)', 'adjm-mcp'); ?></summary>
                    <div class="adjm-code-block" style="margin-top: 12px;">
                        <pre style="max-height: 520px; overflow: auto;"><code><?php echo esc_html($guide_md ?: __('Fichier de guide introuvable.', 'adjm-mcp')); ?></code></pre>
                    </div>
                </details>
            </div>
            
            <div class="adjm-section">
                <h2><?php _e('Abilities disponibles', 'adjm-mcp'); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Ability', 'adjm-mcp'); ?></th>
                            <th><?php _e('Description', 'adjm-mcp'); ?></th>
                            <th><?php _e('Catégorie', 'adjm-mcp'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (self::$abilities as $name => $ability): ?>
                        <tr>
                            <td><code><?php echo esc_html($name); ?></code></td>
                            <td><?php echo esc_html($ability->get_description()); ?></td>
                            <td><?php echo esc_html($ability->get_category()); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX Test Ability
     */
    public function ajax_test_ability() {
        check_ajax_referer('adjm_mcp', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permissions insuffisantes']);
        }
        
        $ability_name = sanitize_text_field($_POST['ability'] ?? '');
        $params = json_decode(stripslashes($_POST['params'] ?? '{}'), true);
        
        $ability = self::get_ability($ability_name);
        if (!$ability) {
            wp_send_json_error(['message' => 'Ability non trouvée']);
        }
        
        $result = $ability->execute($params);
        
        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }
        
        wp_send_json_success(['result' => $result]);
    }

    /**
     * AJAX Save Settings
     */
    public function ajax_save_settings() {
        check_ajax_referer('adjm_mcp', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permissions insuffisantes']);
        }
        
        $settings = $_POST['settings'] ?? [];
        update_option('adjm_mcp_abilities', $settings);
        
        wp_send_json_success(['message' => 'Paramètres enregistrés']);
    }
}

// Fonctions d'aide globales - DOIVENT être définies AVANT l'initialisation
if (!function_exists('adjm_register_ability')) {
    function adjm_register_ability($name, $args) {
        if (!class_exists('IA_Pilote_Core')) return;
        IA_Pilote_Core::register_ability($name, $args);
    }
}

if (!function_exists('adjm_get_ability')) {
    function adjm_get_ability($name) {
        if (!class_exists('IA_Pilote_Core')) return null;
        return IA_Pilote_Core::get_ability($name);
    }
}

if (!function_exists('adjm_is_group_enabled')) {
    function adjm_is_group_enabled($group) {
        if (!class_exists('IA_Pilote_Core')) return false;
        return IA_Pilote_Core::is_group_enabled($group);
    }
}

// ========================================
// INJECTION CSS DYNAMIQUE (DESIGN SYSTEM)
// ========================================

/**
 * Récupère le Design System (même logique que dans system.php)
 * Fonction indépendante pour éviter les dépendances de chargement
 */
function ia_pilote_get_design_system() {
    $defaults = [
        'colors' => [
            'primary' => '#132060',
            'secondary' => '#C6A87C',
            'dark' => '#0f172a',
            'light' => '#f8fafc',
            'background' => '#ffffff',
            'accent' => '#3b82f6',
        ],
        'fonts' => [
            'header' => "'Barlow', sans-serif",
            'body' => "'Inter', sans-serif",
            'google_url' => 'https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700;800&family=Inter:wght@400;600&display=swap'
        ],
        'borderRadius' => '50px',
        'spacing' => [
            'small' => '8px',
            'medium' => '16px',
            'large' => '32px',
        ],
    ];
    
    $saved = get_option('adjm_design_system', []);
    return array_replace_recursive($defaults, $saved);
}

/**
 * Injecte le CSS dynamique sur le frontend
 */
add_action('wp_enqueue_scripts', 'ia_pilote_inject_design_system_css', 99);

function ia_pilote_inject_design_system_css() {
    $design = ia_pilote_get_design_system();
    
    // 1. Charger Google Fonts si défini
    if (!empty($design['fonts']['google_url'])) {
        wp_enqueue_style('ia-pilote-google-fonts', $design['fonts']['google_url'], [], null);
    }
    
    // 2. Générer le CSS dynamique
    $css = "
        :root {
            /* Couleurs */
            --ia-color-primary: " . esc_attr($design['colors']['primary']) . ";
            --ia-color-secondary: " . esc_attr($design['colors']['secondary']) . ";
            --ia-color-dark: " . esc_attr($design['colors']['dark']) . ";
            --ia-color-light: " . esc_attr($design['colors']['light']) . ";
            --ia-color-background: " . esc_attr($design['colors']['background']) . ";
            --ia-color-accent: " . esc_attr($design['colors']['accent']) . ";
            
            /* Polices */
            --ia-font-header: " . $design['fonts']['header'] . ";
            --ia-font-body: " . $design['fonts']['body'] . ";
            
            /* Espacements */
            --ia-spacing-small: " . esc_attr($design['spacing']['small']) . ";
            --ia-spacing-medium: " . esc_attr($design['spacing']['medium']) . ";
            --ia-spacing-large: " . esc_attr($design['spacing']['large']) . ";
            
            /* Bordures */
            --ia-border-radius: " . esc_attr($design['borderRadius']) . ";
        }
        
        /* Application globale */
        body {
            font-family: var(--ia-font-body);
            color: var(--ia-color-dark);
            background-color: var(--ia-color-background);
        }
        
        h1, h2, h3, h4, h5, h6,
        .wp-block-heading,
        .wp-block-site-title a {
            font-family: var(--ia-font-header);
            font-weight: 700;
            color: var(--ia-color-primary);
            letter-spacing: -0.01em;
            text-decoration: none;
        }
        
        /* Liens */
        a {
            color: var(--ia-color-primary);
            transition: color 0.2s ease;
        }
        a:hover {
            color: var(--ia-color-secondary);
        }
        
        /* Boutons Gutenberg */
        .wp-block-button__link {
            border-radius: var(--ia-border-radius) !important;
            font-family: var(--ia-font-header);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .wp-block-button__link:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        /* Bouton primaire (fill) */
        .is-style-fill .wp-block-button__link,
        .wp-block-button:not(.is-style-outline) .wp-block-button__link {
            background-color: var(--ia-color-secondary) !important;
            color: var(--ia-color-primary) !important;
        }
        
        /* Bouton outline */
        .is-style-outline .wp-block-button__link {
            border: 2px solid var(--ia-color-secondary) !important;
            color: var(--ia-color-secondary) !important;
            background-color: transparent !important;
        }
        
        /* Navigation */
        .wp-block-navigation-item__content {
            font-family: var(--ia-font-header);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            color: var(--ia-color-primary);
        }
        
        /* Sélections et focus */
        ::selection {
            background-color: var(--ia-color-secondary);
            color: var(--ia-color-primary);
        }
        
        /* Formulaires */
        input, textarea, select {
            border-radius: calc(var(--ia-border-radius) / 2);
            border: 1px solid var(--ia-color-light);
            padding: var(--ia-spacing-small) var(--ia-spacing-medium);
            font-family: var(--ia-font-body);
        }
        
        input:focus, textarea:focus, select:focus {
            border-color: var(--ia-color-secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(198, 168, 124, 0.2);
        }
    ";
    
    // 3. Injecter le CSS inline
    wp_add_inline_style('wp-block-library', $css);
}

/**
 * Injecte également dans l'éditeur Gutenberg (admin)
 */
add_action('enqueue_block_editor_assets', 'ia_pilote_inject_design_system_editor');

function ia_pilote_inject_design_system_editor() {
    $design = ia_pilote_get_design_system();
    
    // Google Fonts dans l'éditeur
    if (!empty($design['fonts']['google_url'])) {
        wp_enqueue_style('ia-pilote-editor-fonts', $design['fonts']['google_url'], [], null);
    }
    
    // CSS variables pour l'éditeur
    $editor_css = "
        :root {
            --ia-color-primary: " . esc_attr($design['colors']['primary']) . ";
            --ia-color-secondary: " . esc_attr($design['colors']['secondary']) . ";
            --ia-font-header: " . $design['fonts']['header'] . ";
            --ia-font-body: " . $design['fonts']['body'] . ";
        }
        .editor-styles-wrapper {
            font-family: var(--ia-font-body);
        }
        .editor-styles-wrapper h1,
        .editor-styles-wrapper h2,
        .editor-styles-wrapper h3,
        .editor-styles-wrapper h4 {
            font-family: var(--ia-font-header);
            color: var(--ia-color-primary);
        }
    ";
    
    wp_add_inline_style('wp-edit-blocks', $editor_css);
}

// Initialiser le plugin
IA_Pilote_Core::get_instance();


