<?php
/**
 * Abilities Système - Menus, Thème, Options, Plugins, Site Info
 * 
 * @package IA_Pilote_MCP
 */

if (!defined('ABSPATH')) exit;

// ========================================
// MENUS - GESTION COMPLÈTE
// ========================================

if (adjm_is_group_enabled('menus')) {
    
    // ----- LECTURE DES MENUS -----
    
    adjm_register_ability('adjm/list-menus', [
        'label' => __('Lister les menus', 'adjm-mcp'),
        'description' => __('Récupère tous les menus de navigation', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object', 
            'properties' => [
                '_unused' => ['type' => 'string', 'description' => 'Ignored']
            ]
        ],
        'execute_callback' => function($input) {
            $menus = wp_get_nav_menus();
            return [
                'menus' => array_map(function($menu) {
                    return [
                        'id' => $menu->term_id,
                        'name' => $menu->name,
                        'slug' => $menu->slug,
                        'count' => $menu->count,
                    ];
                }, $menus),
                'total' => count($menus),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/get-menu-items', [
        'label' => __('Récupérer les items d\'un menu', 'adjm-mcp'),
        'description' => __('Liste les éléments d\'un menu spécifique', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'menu_id' => ['type' => 'integer', 'description' => 'ID ou slug du menu'],
            ],
            'required' => ['menu_id'],
        ],
        'execute_callback' => function($input) {
            $items = wp_get_nav_menu_items(absint($input['menu_id']));
            if (!$items) return new WP_Error('not_found', 'Menu introuvable');
            
            return [
                'items' => array_map(function($item) {
                    return [
                        'id' => $item->ID,
                        'title' => $item->title,
                        'url' => $item->url,
                        'type' => $item->type,
                        'object' => $item->object,
                        'object_id' => $item->object_id,
                        'parent' => $item->menu_item_parent,
                        'order' => $item->menu_order,
                        'target' => $item->target,
                        'classes' => implode(' ', $item->classes),
                        'description' => $item->description,
                        'attr_title' => $item->attr_title,
                    ];
                }, $items),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    // ----- EMPLACEMENTS DE MENU -----

    adjm_register_ability('adjm/get-menu-locations', [
        'label' => __('Récupérer les emplacements de menu', 'adjm-mcp'),
        'description' => __('Liste tous les emplacements de menu du thème et les menus assignés', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            $locations = get_registered_nav_menus();
            $assigned = get_nav_menu_locations();
            
            $result = [];
            foreach ($locations as $location => $description) {
                $menu_id = isset($assigned[$location]) ? $assigned[$location] : 0;
                $menu = $menu_id ? wp_get_nav_menu_object($menu_id) : null;
                
                $result[] = [
                    'location' => $location,
                    'description' => $description,
                    'assigned_menu_id' => $menu_id,
                    'assigned_menu_name' => $menu ? $menu->name : null,
                ];
            }
            
            return [
                'locations' => $result,
                'count' => count($result),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/set-menu-location', [
        'label' => __('Assigner un menu à un emplacement', 'adjm-mcp'),
        'description' => __('Assigne un menu à un emplacement du thème (header, footer, etc.)', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'location' => ['type' => 'string', 'description' => 'Identifiant de l\'emplacement (ex: primary, footer)'],
                'menu_id' => ['type' => 'integer', 'description' => 'ID du menu à assigner (0 pour retirer)'],
            ],
            'required' => ['location', 'menu_id'],
        ],
        'execute_callback' => function($input) {
            $locations = get_nav_menu_locations();
            $locations[$input['location']] = absint($input['menu_id']);
            set_theme_mod('nav_menu_locations', $locations);
            
            return [
                'success' => true,
                'location' => $input['location'],
                'menu_id' => $input['menu_id'],
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    // ----- CRÉATION & MODIFICATION DE MENUS -----

    adjm_register_ability('adjm/create-menu', [
        'label' => __('Créer un menu', 'adjm-mcp'),
        'description' => __('Crée un nouveau menu de navigation', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string', 'description' => 'Nom du menu'],
            ],
            'required' => ['name'],
        ],
        'execute_callback' => function($input) {
            $menu_id = wp_create_nav_menu(sanitize_text_field($input['name']));
            if (is_wp_error($menu_id)) {
                return $menu_id;
            }
            return [
                'success' => true,
                'menu_id' => $menu_id,
                'name' => $input['name'],
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    adjm_register_ability('adjm/delete-menu', [
        'label' => __('Supprimer un menu', 'adjm-mcp'),
        'description' => __('Supprime un menu de navigation', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'menu_id' => ['type' => 'integer', 'description' => 'ID du menu à supprimer'],
            ],
            'required' => ['menu_id'],
        ],
        'execute_callback' => function($input) {
            $result = wp_delete_nav_menu(absint($input['menu_id']));
            return [
                'success' => $result !== false,
                'deleted_id' => $input['menu_id'],
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    adjm_register_ability('adjm/add-menu-item', [
        'label' => __('Ajouter un item au menu', 'adjm-mcp'),
        'description' => __('Ajoute un élément à un menu (page, lien, catégorie...)', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'menu_id' => ['type' => 'integer', 'description' => 'ID du menu'],
                'title' => ['type' => 'string', 'description' => 'Titre affiché'],
                'url' => ['type' => 'string', 'description' => 'URL du lien (pour type custom)'],
                'type' => ['type' => 'string', 'description' => 'Type: custom, post_type, taxonomy'],
                'object' => ['type' => 'string', 'description' => 'Objet: page, post, category...'],
                'object_id' => ['type' => 'integer', 'description' => 'ID de l\'objet (pour page/post)'],
                'parent_id' => ['type' => 'integer', 'description' => 'ID de l\'item parent (sous-menu)'],
                'position' => ['type' => 'integer', 'description' => 'Position dans le menu (ordre)'],
                'target' => ['type' => 'string', 'description' => '_blank pour nouvel onglet'],
                'classes' => ['type' => 'string', 'description' => 'Classes CSS additionnelles'],
            ],
            'required' => ['menu_id', 'title'],
        ],
        'execute_callback' => function($input) {
            $menu_item_data = [
                'menu-item-title' => sanitize_text_field($input['title']),
                'menu-item-status' => 'publish',
            ];
            
            // Type de menu item
            $type = $input['type'] ?? 'custom';
            $menu_item_data['menu-item-type'] = $type;
            
            if ($type === 'custom') {
                $menu_item_data['menu-item-url'] = esc_url($input['url'] ?? '#');
            } elseif ($type === 'post_type') {
                $menu_item_data['menu-item-object'] = $input['object'] ?? 'page';
                $menu_item_data['menu-item-object-id'] = absint($input['object_id'] ?? 0);
            } elseif ($type === 'taxonomy') {
                $menu_item_data['menu-item-object'] = $input['object'] ?? 'category';
                $menu_item_data['menu-item-object-id'] = absint($input['object_id'] ?? 0);
            }
            
            // Options additionnelles
            if (!empty($input['parent_id'])) {
                $menu_item_data['menu-item-parent-id'] = absint($input['parent_id']);
            }
            if (!empty($input['position'])) {
                $menu_item_data['menu-item-position'] = absint($input['position']);
            }
            if (!empty($input['target'])) {
                $menu_item_data['menu-item-target'] = $input['target'];
            }
            if (!empty($input['classes'])) {
                $menu_item_data['menu-item-classes'] = sanitize_text_field($input['classes']);
            }
            
            $item_id = wp_update_nav_menu_item(absint($input['menu_id']), 0, $menu_item_data);
            
            if (is_wp_error($item_id)) {
                return $item_id;
            }
            
            return [
                'success' => true,
                'item_id' => $item_id,
                'menu_id' => $input['menu_id'],
                'title' => $input['title'],
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    adjm_register_ability('adjm/update-menu-item', [
        'label' => __('Modifier un item de menu', 'adjm-mcp'),
        'description' => __('Met à jour un élément de menu existant', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'menu_id' => ['type' => 'integer', 'description' => 'ID du menu'],
                'item_id' => ['type' => 'integer', 'description' => 'ID de l\'item à modifier'],
                'title' => ['type' => 'string', 'description' => 'Nouveau titre'],
                'url' => ['type' => 'string', 'description' => 'Nouvelle URL'],
                'parent_id' => ['type' => 'integer', 'description' => 'Nouveau parent'],
                'position' => ['type' => 'integer', 'description' => 'Nouvelle position'],
                'target' => ['type' => 'string', 'description' => 'Target (_blank, etc.)'],
                'classes' => ['type' => 'string', 'description' => 'Classes CSS'],
            ],
            'required' => ['menu_id', 'item_id'],
        ],
        'execute_callback' => function($input) {
            $menu_item_data = ['menu-item-status' => 'publish'];
            
            if (!empty($input['title'])) {
                $menu_item_data['menu-item-title'] = sanitize_text_field($input['title']);
            }
            if (!empty($input['url'])) {
                $menu_item_data['menu-item-url'] = esc_url($input['url']);
            }
            if (isset($input['parent_id'])) {
                $menu_item_data['menu-item-parent-id'] = absint($input['parent_id']);
            }
            if (isset($input['position'])) {
                $menu_item_data['menu-item-position'] = absint($input['position']);
            }
            if (!empty($input['target'])) {
                $menu_item_data['menu-item-target'] = $input['target'];
            }
            if (!empty($input['classes'])) {
                $menu_item_data['menu-item-classes'] = sanitize_text_field($input['classes']);
            }
            
            $result = wp_update_nav_menu_item(absint($input['menu_id']), absint($input['item_id']), $menu_item_data);
            
            return [
                'success' => !is_wp_error($result),
                'item_id' => $input['item_id'],
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    adjm_register_ability('adjm/delete-menu-item', [
        'label' => __('Supprimer un item de menu', 'adjm-mcp'),
        'description' => __('Supprime un élément d\'un menu', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'item_id' => ['type' => 'integer', 'description' => 'ID de l\'item à supprimer'],
            ],
            'required' => ['item_id'],
        ],
        'execute_callback' => function($input) {
            $result = wp_delete_post(absint($input['item_id']), true);
            return [
                'success' => $result !== false,
                'deleted_id' => $input['item_id'],
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    adjm_register_ability('adjm/reorder-menu-items', [
        'label' => __('Réorganiser les items d\'un menu', 'adjm-mcp'),
        'description' => __('Change l\'ordre des éléments dans un menu', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'menu_id' => ['type' => 'integer', 'description' => 'ID du menu'],
                'items_order' => ['type' => 'array', 'description' => 'Array d\'IDs dans le nouvel ordre'],
            ],
            'required' => ['menu_id', 'items_order'],
        ],
        'execute_callback' => function($input) {
            $position = 1;
            foreach ($input['items_order'] as $item_id) {
                wp_update_nav_menu_item($input['menu_id'], $item_id, [
                    'menu-item-position' => $position,
                    'menu-item-status' => 'publish',
                ]);
                $position++;
            }
            return [
                'success' => true,
                'menu_id' => $input['menu_id'],
                'new_order' => $input['items_order'],
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);
}

// ========================================
// PAGE LAYOUT & TEMPLATES
// ========================================

adjm_register_ability('adjm/get-page-template', [
    'label' => __('Récupérer le template d\'une page', 'adjm-mcp'),
    'description' => __('Indique quel template est utilisé par une page', 'adjm-mcp'),
    'category' => 'content',
    'input_schema' => [
        'type' => 'object',
        'properties' => [
            'page_id' => ['type' => 'integer', 'description' => 'ID de la page'],
        ],
        'required' => ['page_id'],
    ],
    'execute_callback' => function($input) {
        $page_id = absint($input['page_id']);
        $template = get_page_template_slug($page_id);
        
        return [
            'page_id' => $page_id,
            'template' => $template ?: 'default',
            'is_fullwidth' => strpos($template, 'full') !== false || strpos($template, 'blank') !== false,
            'available_templates' => array_merge(
                ['default' => 'Template par défaut'],
                wp_get_theme()->get_page_templates()
            ),
        ];
    },
    'permission_callback' => function() { return current_user_can('edit_pages'); },
    'meta' => adjm_mcp_meta(true),
]);

adjm_register_ability('adjm/set-page-template', [
    'label' => __('Définir le template d\'une page', 'adjm-mcp'),
    'description' => __('Change le template d\'une page (pleine largeur, etc.)', 'adjm-mcp'),
    'category' => 'content',
    'input_schema' => [
        'type' => 'object',
        'properties' => [
            'page_id' => ['type' => 'integer', 'description' => 'ID de la page'],
            'template' => ['type' => 'string', 'description' => 'Slug du template (ex: template-fullwidth.php, blank, default)'],
        ],
        'required' => ['page_id', 'template'],
    ],
    'execute_callback' => function($input) {
        $page_id = absint($input['page_id']);
        $template = $input['template'] === 'default' ? '' : sanitize_file_name($input['template']);
        
        update_post_meta($page_id, '_wp_page_template', $template);
        
        return [
            'success' => true,
            'page_id' => $page_id,
            'new_template' => $template ?: 'default',
        ];
    },
    'permission_callback' => function() { return current_user_can('edit_pages'); },
    'meta' => adjm_mcp_meta(false),
]);

adjm_register_ability('adjm/get-page-layout-options', [
    'label' => __('Récupérer les options de layout d\'une page', 'adjm-mcp'),
    'description' => __('Métadonnées de layout : sidebar, header, footer, largeur', 'adjm-mcp'),
    'category' => 'content',
    'input_schema' => [
        'type' => 'object',
        'properties' => [
            'page_id' => ['type' => 'integer', 'description' => 'ID de la page'],
        ],
        'required' => ['page_id'],
    ],
    'execute_callback' => function($input) {
        $page_id = absint($input['page_id']);
        
        return [
            'page_id' => $page_id,
            'template' => get_page_template_slug($page_id) ?: 'default',
            // Métadonnées communes des thèmes/builders
            'hide_header' => get_post_meta($page_id, '_hide_header', true) ?: get_post_meta($page_id, 'theme_hide_header', true),
            'hide_footer' => get_post_meta($page_id, '_hide_footer', true) ?: get_post_meta($page_id, 'theme_hide_footer', true),
            'hide_sidebar' => get_post_meta($page_id, '_hide_sidebar', true) ?: get_post_meta($page_id, 'sidebar_layout', true),
            'hide_title' => get_post_meta($page_id, '_hide_title', true) ?: get_post_meta($page_id, 'theme_hide_title', true),
            'fullwidth' => get_post_meta($page_id, '_fullwidth', true) ?: get_post_meta($page_id, 'content_width', true),
            'custom_body_class' => get_post_meta($page_id, '_custom_body_class', true),
            // Elementor
            'elementor_canvas' => get_post_meta($page_id, '_elementor_template_type', true),
            // Divi
            'et_pb_page_layout' => get_post_meta($page_id, '_et_pb_page_layout', true),
            'et_pb_side_nav' => get_post_meta($page_id, '_et_pb_side_nav', true),
        ];
    },
    'permission_callback' => function() { return current_user_can('edit_pages'); },
    'meta' => adjm_mcp_meta(true),
]);

adjm_register_ability('adjm/set-page-layout-option', [
    'label' => __('Modifier une option de layout de page', 'adjm-mcp'),
    'description' => __('Change header/footer/sidebar/largeur d\'une page', 'adjm-mcp'),
    'category' => 'content',
    'input_schema' => [
        'type' => 'object',
        'properties' => [
            'page_id' => ['type' => 'integer', 'description' => 'ID de la page'],
            'option' => ['type' => 'string', 'description' => 'Option: hide_header, hide_footer, hide_sidebar, hide_title, fullwidth, custom_body_class'],
            'value' => ['description' => 'Valeur de l\'option'],
        ],
        'required' => ['page_id', 'option', 'value'],
    ],
    'execute_callback' => function($input) {
        $page_id = absint($input['page_id']);
        $option = sanitize_key($input['option']);
        $meta_key = '_' . $option;
        
        update_post_meta($page_id, $meta_key, $input['value']);
        
        return [
            'success' => true,
            'page_id' => $page_id,
            'option' => $option,
            'new_value' => get_post_meta($page_id, $meta_key, true),
        ];
    },
    'permission_callback' => function() { return current_user_can('edit_pages'); },
    'meta' => adjm_mcp_meta(false),
]);

// ========================================
// DIMENSIONS & RESPONSIVE
// ========================================

if (adjm_is_group_enabled('responsive')) {

adjm_register_ability('adjm/get-responsive-settings', [
    'label' => __('Récupérer les paramètres responsive', 'adjm-mcp'),
    'description' => __('Breakpoints et dimensions responsive du site', 'adjm-mcp'),
    'category' => 'appearance',
    'input_schema' => ['type' => 'object', 'properties' => []],
    'execute_callback' => function($input) {
        $result = [
            'container_width' => get_option('large_size_w', 1024),
            'content_width' => isset($GLOBALS['content_width']) ? $GLOBALS['content_width'] : 'non défini',
        ];
        
        // FSE / Block themes
        if (function_exists('wp_get_global_settings')) {
            $settings = wp_get_global_settings();
            $result['fse_layout'] = $settings['layout'] ?? null;
            $result['fse_content_size'] = $settings['layout']['contentSize'] ?? null;
            $result['fse_wide_size'] = $settings['layout']['wideSize'] ?? null;
        }
        
        // Elementor
        if (defined('ELEMENTOR_VERSION')) {
            $result['elementor'] = [
                'container_width' => get_option('elementor_container_width', 1140),
                'viewport_lg' => get_option('elementor_viewport_lg', 1025),
                'viewport_md' => get_option('elementor_viewport_md', 768),
                'space_between_widgets' => get_option('elementor_space_between_widgets', 20),
            ];
        }
        
        // Divi
        if (function_exists('et_get_option')) {
            $result['divi'] = [
                'gutter_width' => et_get_option('gutter_width', 3),
                'use_sidebar_width' => et_get_option('use_sidebar_width', 'on'),
                'sidebar_width' => et_get_option('sidebar_width', 21),
                'section_padding' => et_get_option('section_padding', 4),
                'row_padding' => et_get_option('row_padding', 2),
            ];
        }
        
        return $result;
    },
    'permission_callback' => function() { return current_user_can('edit_theme_options'); },
    'meta' => adjm_mcp_meta(true),
]);

adjm_register_ability('adjm/set-container-width', [
    'label' => __('Définir la largeur du conteneur', 'adjm-mcp'),
    'description' => __('Change la largeur max du contenu principal', 'adjm-mcp'),
    'category' => 'appearance',
    'input_schema' => [
        'type' => 'object',
        'properties' => [
            'width' => ['type' => 'integer', 'description' => 'Largeur en pixels (ex: 1140, 1200, 1400)'],
            'builder' => ['type' => 'string', 'description' => 'Builder cible: elementor, divi, theme (par défaut: auto-detect)'],
        ],
        'required' => ['width'],
    ],
    'execute_callback' => function($input) {
        $width = absint($input['width']);
        $builder = $input['builder'] ?? 'auto';
        $updated = [];
        
        // Elementor
        if (($builder === 'auto' || $builder === 'elementor') && defined('ELEMENTOR_VERSION')) {
            update_option('elementor_container_width', $width);
            $updated[] = 'elementor';
        }
        
        // Divi - pas de largeur directe, mais on peut ajuster via CSS
        if (($builder === 'auto' || $builder === 'divi') && function_exists('et_get_option')) {
            // Divi utilise un système de pourcentage, on log juste
            $updated[] = 'divi (requires CSS override)';
        }
        
        // Design System custom
        $design = get_option('adjm_design_system', []);
        $design['layout'] = $design['layout'] ?? [];
        $design['layout']['containerWidth'] = $width . 'px';
        update_option('adjm_design_system', $design);
        $updated[] = 'design_system';
        
        return [
            'success' => true,
            'width' => $width,
            'updated_in' => $updated,
        ];
    },
    'permission_callback' => function() { return current_user_can('edit_theme_options'); },
    'meta' => adjm_mcp_meta(false),
]);

adjm_register_ability('adjm/set-responsive-breakpoints', [
    'label' => __('Définir les breakpoints responsive', 'adjm-mcp'),
    'description' => __('Configure les points de rupture mobile/tablette/desktop', 'adjm-mcp'),
    'category' => 'appearance',
    'input_schema' => [
        'type' => 'object',
        'properties' => [
            'mobile' => ['type' => 'integer', 'description' => 'Breakpoint mobile (ex: 768)'],
            'tablet' => ['type' => 'integer', 'description' => 'Breakpoint tablette (ex: 1024)'],
            'desktop' => ['type' => 'integer', 'description' => 'Breakpoint desktop (ex: 1200)'],
        ],
    ],
    'execute_callback' => function($input) {
        $updated = [];
        
        // Elementor
        if (defined('ELEMENTOR_VERSION')) {
            if (!empty($input['mobile'])) {
                update_option('elementor_viewport_md', $input['mobile']);
                $updated['elementor_mobile'] = $input['mobile'];
            }
            if (!empty($input['tablet'])) {
                update_option('elementor_viewport_lg', $input['tablet']);
                $updated['elementor_tablet'] = $input['tablet'];
            }
        }
        
        // Design System custom
        $design = get_option('adjm_design_system', []);
        $design['breakpoints'] = [
            'mobile' => ($input['mobile'] ?? 768) . 'px',
            'tablet' => ($input['tablet'] ?? 1024) . 'px',
            'desktop' => ($input['desktop'] ?? 1200) . 'px',
        ];
        update_option('adjm_design_system', $design);
        $updated['design_system_breakpoints'] = $design['breakpoints'];
        
        return [
            'success' => true,
            'updated' => $updated,
        ];
    },
    'permission_callback' => function() { return current_user_can('edit_theme_options'); },
    'meta' => adjm_mcp_meta(false),
]);

} // Fin groupe responsive

// ========================================
// HEADER & FOOTER - GESTION COMPLÈTE
// ========================================

if (adjm_is_group_enabled('header_footer')) {

adjm_register_ability('adjm/get-header-settings', [
    'label' => __('Récupérer les paramètres du header', 'adjm-mcp'),
    'description' => __('Configuration complète du header (logo, navigation, couleurs)', 'adjm-mcp'),
    'category' => 'appearance',
    'input_schema' => ['type' => 'object', 'properties' => []],
    'execute_callback' => function($input) {
        $result = [
            // Logo & identité
            'custom_logo_id' => get_theme_mod('custom_logo'),
            'custom_logo_url' => wp_get_attachment_url(get_theme_mod('custom_logo')),
            'site_title' => get_bloginfo('name'),
            'tagline' => get_bloginfo('description'),
            'display_header_text' => display_header_text(),
            'header_text_color' => get_header_textcolor(),
            
            // Image header
            'header_image' => get_header_image(),
            'header_image_width' => get_custom_header()->width ?? null,
            'header_image_height' => get_custom_header()->height ?? null,
            
            // Menu principal
            'primary_menu_location' => null,
            'primary_menu_id' => null,
        ];
        
        // Trouver le menu principal
        $locations = get_nav_menu_locations();
        $primary_locations = ['primary', 'main', 'header', 'primary-menu', 'main-menu'];
        foreach ($primary_locations as $loc) {
            if (!empty($locations[$loc])) {
                $result['primary_menu_location'] = $loc;
                $result['primary_menu_id'] = $locations[$loc];
                break;
            }
        }
        
        // Thèmes spécifiques
        if (function_exists('et_get_option')) {
            $result['divi'] = [
                'logo' => et_get_option('divi_logo', ''),
                'fixed_nav' => et_get_option('divi_fixed_nav', 'on'),
                'primary_nav_bg' => et_get_option('primary_nav_bg', '#ffffff'),
                'primary_nav_dropdown_bg' => et_get_option('primary_nav_dropdown_bg', '#ffffff'),
                'menu_height' => et_get_option('menu_height', '66'),
                'logo_height' => et_get_option('logo_height', '54'),
                'hide_nav' => et_get_option('divi_hide_nav', 'off'),
                'vertical_nav' => et_get_option('vertical_nav', 'off'),
            ];
        }
        
        // FSE - Template parts
        if (function_exists('get_block_templates')) {
            $header_parts = get_block_templates(['area' => 'header'], 'wp_template_part');
            $result['fse_header_templates'] = array_map(function($p) {
                return ['id' => $p->id, 'slug' => $p->slug, 'title' => $p->title];
            }, $header_parts);
        }
        
        return $result;
    },
    'permission_callback' => function() { return current_user_can('edit_theme_options'); },
    'meta' => adjm_mcp_meta(true),
]);

adjm_register_ability('adjm/get-footer-settings', [
    'label' => __('Récupérer les paramètres du footer', 'adjm-mcp'),
    'description' => __('Configuration complète du footer (widgets, copyright, réseaux sociaux)', 'adjm-mcp'),
    'category' => 'appearance',
    'input_schema' => ['type' => 'object', 'properties' => []],
    'execute_callback' => function($input) {
        $result = [
            // Couleurs de fond
            'background_color' => get_background_color(),
            'background_image' => get_background_image(),
            
            // Widgets footer
            'footer_sidebars' => [],
        ];
        
        // Trouver les sidebars footer
        global $wp_registered_sidebars;
        foreach ($wp_registered_sidebars as $id => $sidebar) {
            if (strpos($id, 'footer') !== false || strpos(strtolower($sidebar['name']), 'footer') !== false) {
                $widgets = wp_get_sidebars_widgets();
                $result['footer_sidebars'][] = [
                    'id' => $id,
                    'name' => $sidebar['name'],
                    'widget_count' => isset($widgets[$id]) ? count($widgets[$id]) : 0,
                ];
            }
        }
        
        // Menu footer
        $locations = get_nav_menu_locations();
        $footer_locations = ['footer', 'footer-menu', 'secondary', 'bottom'];
        foreach ($footer_locations as $loc) {
            if (!empty($locations[$loc])) {
                $result['footer_menu_location'] = $loc;
                $result['footer_menu_id'] = $locations[$loc];
                break;
            }
        }
        
        // Options courantes
        $result['copyright_text'] = get_option('adjm_footer_copyright', '');
        $result['footer_text'] = get_theme_mod('footer_text', '');
        
        // Divi
        if (function_exists('et_get_option')) {
            $result['divi'] = [
                'footer_bg' => et_get_option('footer_bg', '#222222'),
                'footer_widget_bg' => et_get_option('footer_widget_bg', '#292929'),
                'footer_widget_text_color' => et_get_option('footer_widget_text_color', '#fff'),
                'footer_widget_link_color' => et_get_option('footer_widget_link_color', '#fff'),
                'bottom_bar_bg' => et_get_option('bottom_bar_bg', '#1d1d1d'),
                'bottom_bar_text_color' => et_get_option('bottom_bar_text_color', '#666'),
                'footer_columns' => et_get_option('footer_columns', '4'),
                'show_footer_social_icons' => et_get_option('show_footer_social_icons', 'on'),
                'footer_credits' => et_get_option('custom_footer_credits', ''),
            ];
        }
        
        // FSE
        if (function_exists('get_block_templates')) {
            $footer_parts = get_block_templates(['area' => 'footer'], 'wp_template_part');
            $result['fse_footer_templates'] = array_map(function($p) {
                return ['id' => $p->id, 'slug' => $p->slug, 'title' => $p->title];
            }, $footer_parts);
        }
        
        return $result;
    },
    'permission_callback' => function() { return current_user_can('edit_theme_options'); },
    'meta' => adjm_mcp_meta(true),
]);

adjm_register_ability('adjm/set-footer-copyright', [
    'label' => __('Définir le texte de copyright', 'adjm-mcp'),
    'description' => __('Change le texte de copyright du footer', 'adjm-mcp'),
    'category' => 'appearance',
    'input_schema' => [
        'type' => 'object',
        'properties' => [
            'text' => ['type' => 'string', 'description' => 'Texte de copyright (HTML autorisé)'],
        ],
        'required' => ['text'],
    ],
    'execute_callback' => function($input) {
        $text = wp_kses_post($input['text']);
        update_option('adjm_footer_copyright', $text);
        
        // Aussi mettre à jour theme_mod si disponible
        set_theme_mod('footer_text', $text);
        
        // Divi
        if (function_exists('et_update_option')) {
            et_update_option('custom_footer_credits', $text);
        }
        
        return [
            'success' => true,
            'copyright' => $text,
        ];
    },
    'permission_callback' => function() { return current_user_can('edit_theme_options'); },
    'meta' => adjm_mcp_meta(false),
]);

adjm_register_ability('adjm/set-header-logo', [
    'label' => __('Définir le logo du header', 'adjm-mcp'),
    'description' => __('Change le logo affiché dans le header', 'adjm-mcp'),
    'category' => 'appearance',
    'input_schema' => [
        'type' => 'object',
        'properties' => [
            'attachment_id' => ['type' => 'integer', 'description' => 'ID du média à utiliser comme logo'],
        ],
        'required' => ['attachment_id'],
    ],
    'execute_callback' => function($input) {
        $id = absint($input['attachment_id']);
        
        if (!wp_attachment_is_image($id)) {
            return new WP_Error('invalid_image', 'L\'ID fourni n\'est pas une image valide');
        }
        
        // WordPress core
        set_theme_mod('custom_logo', $id);
        
        // Divi
        if (function_exists('et_update_option')) {
            et_update_option('divi_logo', wp_get_attachment_url($id));
        }
        
        return [
            'success' => true,
            'logo_id' => $id,
            'logo_url' => wp_get_attachment_url($id),
        ];
    },
    'permission_callback' => function() { return current_user_can('edit_theme_options'); },
    'meta' => adjm_mcp_meta(false),
]);

adjm_register_ability('adjm/set-header-style', [
    'label' => __('Définir le style du header', 'adjm-mcp'),
    'description' => __('Configure le style du header (fixe, transparent, couleurs)', 'adjm-mcp'),
    'category' => 'appearance',
    'input_schema' => [
        'type' => 'object',
        'properties' => [
            'fixed' => ['type' => 'boolean', 'description' => 'Header fixe en haut'],
            'transparent' => ['type' => 'boolean', 'description' => 'Header transparent'],
            'background_color' => ['type' => 'string', 'description' => 'Couleur de fond (#HEX)'],
            'text_color' => ['type' => 'string', 'description' => 'Couleur du texte (#HEX)'],
            'height' => ['type' => 'integer', 'description' => 'Hauteur en pixels'],
        ],
    ],
    'execute_callback' => function($input) {
        $updated = [];
        
        // Divi
        if (function_exists('et_update_option')) {
            if (isset($input['fixed'])) {
                et_update_option('divi_fixed_nav', $input['fixed'] ? 'on' : 'off');
                $updated['divi_fixed'] = $input['fixed'];
            }
            if (!empty($input['background_color'])) {
                et_update_option('primary_nav_bg', sanitize_hex_color($input['background_color']));
                $updated['divi_bg'] = $input['background_color'];
            }
            if (!empty($input['height'])) {
                et_update_option('menu_height', absint($input['height']));
                $updated['divi_height'] = $input['height'];
            }
        }
        
        // Theme mods génériques
        if (!empty($input['background_color'])) {
            set_theme_mod('header_background_color', sanitize_hex_color($input['background_color']));
        }
        if (!empty($input['text_color'])) {
            set_theme_mod('header_textcolor', str_replace('#', '', sanitize_hex_color($input['text_color'])));
        }
        
        // Design System
        $design = get_option('adjm_design_system', []);
        $design['header'] = $design['header'] ?? [];
        if (isset($input['fixed'])) $design['header']['fixed'] = $input['fixed'];
        if (isset($input['transparent'])) $design['header']['transparent'] = $input['transparent'];
        if (!empty($input['background_color'])) $design['header']['backgroundColor'] = $input['background_color'];
        if (!empty($input['height'])) $design['header']['height'] = $input['height'] . 'px';
        update_option('adjm_design_system', $design);
        $updated['design_system'] = $design['header'];
        
        return [
            'success' => true,
            'updated' => $updated,
        ];
    },
    'permission_callback' => function() { return current_user_can('edit_theme_options'); },
    'meta' => adjm_mcp_meta(false),
]);

adjm_register_ability('adjm/set-footer-style', [
    'label' => __('Définir le style du footer', 'adjm-mcp'),
    'description' => __('Configure le style du footer (couleurs, colonnes)', 'adjm-mcp'),
    'category' => 'appearance',
    'input_schema' => [
        'type' => 'object',
        'properties' => [
            'background_color' => ['type' => 'string', 'description' => 'Couleur de fond (#HEX)'],
            'text_color' => ['type' => 'string', 'description' => 'Couleur du texte (#HEX)'],
            'columns' => ['type' => 'integer', 'description' => 'Nombre de colonnes (1-4)'],
            'show_social_icons' => ['type' => 'boolean', 'description' => 'Afficher les icônes réseaux sociaux'],
        ],
    ],
    'execute_callback' => function($input) {
        $updated = [];
        
        // Divi
        if (function_exists('et_update_option')) {
            if (!empty($input['background_color'])) {
                et_update_option('footer_bg', sanitize_hex_color($input['background_color']));
                $updated['divi_footer_bg'] = $input['background_color'];
            }
            if (!empty($input['text_color'])) {
                et_update_option('footer_widget_text_color', sanitize_hex_color($input['text_color']));
                $updated['divi_footer_text'] = $input['text_color'];
            }
            if (!empty($input['columns'])) {
                et_update_option('footer_columns', min(4, max(1, absint($input['columns']))));
                $updated['divi_columns'] = $input['columns'];
            }
            if (isset($input['show_social_icons'])) {
                et_update_option('show_footer_social_icons', $input['show_social_icons'] ? 'on' : 'off');
                $updated['divi_social'] = $input['show_social_icons'];
            }
        }
        
        // Design System
        $design = get_option('adjm_design_system', []);
        $design['footer'] = $design['footer'] ?? [];
        if (!empty($input['background_color'])) $design['footer']['backgroundColor'] = $input['background_color'];
        if (!empty($input['text_color'])) $design['footer']['textColor'] = $input['text_color'];
        if (!empty($input['columns'])) $design['footer']['columns'] = $input['columns'];
        update_option('adjm_design_system', $design);
        $updated['design_system'] = $design['footer'];
        
        return [
            'success' => true,
            'updated' => $updated,
        ];
    },
    'permission_callback' => function() { return current_user_can('edit_theme_options'); },
    'meta' => adjm_mcp_meta(false),
]);

adjm_register_ability('adjm/get-social-links', [
    'label' => __('Récupérer les liens réseaux sociaux', 'adjm-mcp'),
    'description' => __('URLs des réseaux sociaux configurés', 'adjm-mcp'),
    'category' => 'appearance',
    'input_schema' => ['type' => 'object', 'properties' => []],
    'execute_callback' => function($input) {
        $socials = get_option('adjm_social_links', []);
        
        // Divi
        if (function_exists('et_get_option')) {
            $socials['divi'] = [
                'facebook' => et_get_option('divi_facebook_url', ''),
                'twitter' => et_get_option('divi_twitter_url', ''),
                'instagram' => et_get_option('divi_instagram_url', ''),
                'linkedin' => et_get_option('divi_linkedin_url', ''),
                'youtube' => et_get_option('divi_youtube_url', ''),
                'pinterest' => et_get_option('divi_pinterest_url', ''),
            ];
        }
        
        // Theme mods
        $socials['theme'] = [
            'facebook' => get_theme_mod('social_facebook', ''),
            'twitter' => get_theme_mod('social_twitter', ''),
            'instagram' => get_theme_mod('social_instagram', ''),
            'linkedin' => get_theme_mod('social_linkedin', ''),
            'youtube' => get_theme_mod('social_youtube', ''),
        ];
        
        return $socials;
    },
    'permission_callback' => function() { return current_user_can('edit_theme_options'); },
    'meta' => adjm_mcp_meta(true),
]);

adjm_register_ability('adjm/set-social-links', [
    'label' => __('Définir les liens réseaux sociaux', 'adjm-mcp'),
    'description' => __('Configure les URLs des réseaux sociaux', 'adjm-mcp'),
    'category' => 'appearance',
    'input_schema' => [
        'type' => 'object',
        'properties' => [
            'facebook' => ['type' => 'string', 'description' => 'URL Facebook'],
            'twitter' => ['type' => 'string', 'description' => 'URL Twitter/X'],
            'instagram' => ['type' => 'string', 'description' => 'URL Instagram'],
            'linkedin' => ['type' => 'string', 'description' => 'URL LinkedIn'],
            'youtube' => ['type' => 'string', 'description' => 'URL YouTube'],
            'tiktok' => ['type' => 'string', 'description' => 'URL TikTok'],
            'pinterest' => ['type' => 'string', 'description' => 'URL Pinterest'],
        ],
    ],
    'execute_callback' => function($input) {
        $networks = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'tiktok', 'pinterest'];
        $updated = [];
        
        foreach ($networks as $network) {
            if (isset($input[$network])) {
                $url = esc_url($input[$network]);
                
                // Theme mods
                set_theme_mod('social_' . $network, $url);
                
                // Divi
                if (function_exists('et_update_option')) {
                    et_update_option('divi_' . $network . '_url', $url);
                }
                
                $updated[$network] = $url;
            }
        }
        
        // Save to custom option
        $socials = get_option('adjm_social_links', []);
        $socials = array_merge($socials, $updated);
        update_option('adjm_social_links', $socials);
        
        return [
            'success' => true,
            'updated' => $updated,
        ];
    },
    'permission_callback' => function() { return current_user_can('edit_theme_options'); },
    'meta' => adjm_mcp_meta(false),
]);

} // Fin groupe header_footer

// ========================================
// THÈME - LECTURE & MODIFICATION COMPLÈTE
// ========================================

if (adjm_is_group_enabled('theme')) {
    
    // ----- INFORMATIONS GÉNÉRALES -----
    
    adjm_register_ability('adjm/get-theme-info', [
        'label' => __('Infos thème actif', 'adjm-mcp'),
        'description' => __('Récupère les informations complètes du thème actif', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            $theme = wp_get_theme();
            return [
                'name' => $theme->get('Name'),
                'version' => $theme->get('Version'),
                'author' => $theme->get('Author'),
                'author_uri' => $theme->get('AuthorURI'),
                'description' => $theme->get('Description'),
                'template' => $theme->get_template(),
                'stylesheet' => $theme->get_stylesheet(),
                'screenshot' => $theme->get_screenshot(),
                'theme_uri' => $theme->get('ThemeURI'),
                'tags' => $theme->get('Tags'),
                'text_domain' => $theme->get('TextDomain'),
                'is_child' => $theme->parent() ? true : false,
                'parent' => $theme->parent() ? $theme->parent()->get('Name') : null,
                'is_block_theme' => function_exists('wp_is_block_theme') ? wp_is_block_theme() : false,
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/list-templates', [
        'label' => __('Lister les templates', 'adjm-mcp'),
        'description' => __('Liste les templates de page disponibles', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            $templates = wp_get_theme()->get_page_templates();
            return [
                'templates' => array_merge(
                    ['default' => 'Template par défaut'],
                    $templates
                ),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    // ----- THEME MODS (Personnalisation) -----

    adjm_register_ability('adjm/get-all-theme-mods', [
        'label' => __('Récupérer toutes les options du thème', 'adjm-mcp'),
        'description' => __('Liste toutes les personnalisations du thème actif (theme_mods)', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            $mods = get_theme_mods();
            return [
                'theme' => get_stylesheet(),
                'mods' => $mods ?: [],
                'count' => is_array($mods) ? count($mods) : 0,
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/get-theme-mod', [
        'label' => __('Récupérer une option du thème', 'adjm-mcp'),
        'description' => __('Récupère une valeur spécifique de personnalisation', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string', 'description' => 'Nom de l\'option'],
            ],
            'required' => ['name'],
        ],
        'execute_callback' => function($input) {
            $name = sanitize_key($input['name']);
            $value = get_theme_mod($name);
            return [
                'name' => $name,
                'value' => $value,
                'exists' => $value !== false,
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/set-theme-mod', [
        'label' => __('Modifier une option du thème', 'adjm-mcp'),
        'description' => __('Met à jour une valeur de personnalisation du thème', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string', 'description' => 'Nom de l\'option'],
                'value' => ['description' => 'Nouvelle valeur'],
            ],
            'required' => ['name', 'value'],
        ],
        'execute_callback' => function($input) {
            $name = sanitize_key($input['name']);
            set_theme_mod($name, $input['value']);
            return [
                'success' => true,
                'name' => $name,
                'new_value' => get_theme_mod($name),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    adjm_register_ability('adjm/remove-theme-mod', [
        'label' => __('Supprimer une option du thème', 'adjm-mcp'),
        'description' => __('Supprime une valeur de personnalisation', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string', 'description' => 'Nom de l\'option à supprimer'],
            ],
            'required' => ['name'],
        ],
        'execute_callback' => function($input) {
            $name = sanitize_key($input['name']);
            remove_theme_mod($name);
            return ['success' => true, 'removed' => $name];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    // ----- LOGO & IDENTITÉ DU SITE -----

    adjm_register_ability('adjm/get-site-identity', [
        'label' => __('Récupérer l\'identité du site', 'adjm-mcp'),
        'description' => __('Logo, titre, description, favicon du site', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            $custom_logo_id = get_theme_mod('custom_logo');
            $site_icon_id = get_option('site_icon');
            
            return [
                'site_title' => get_bloginfo('name'),
                'tagline' => get_bloginfo('description'),
                'logo' => [
                    'id' => $custom_logo_id,
                    'url' => $custom_logo_id ? wp_get_attachment_url($custom_logo_id) : null,
                ],
                'favicon' => [
                    'id' => $site_icon_id,
                    'url' => $site_icon_id ? wp_get_attachment_url($site_icon_id) : null,
                ],
                'show_tagline' => get_theme_mod('header_text', true),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/set-site-logo', [
        'label' => __('Définir le logo du site', 'adjm-mcp'),
        'description' => __('Change le logo du site (ID d\'un média)', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'attachment_id' => ['type' => 'integer', 'description' => 'ID du média à utiliser comme logo'],
            ],
            'required' => ['attachment_id'],
        ],
        'execute_callback' => function($input) {
            $id = absint($input['attachment_id']);
            if (!wp_attachment_is_image($id)) {
                return new WP_Error('invalid_image', 'L\'ID fourni n\'est pas une image valide');
            }
            set_theme_mod('custom_logo', $id);
            return [
                'success' => true,
                'logo_id' => $id,
                'logo_url' => wp_get_attachment_url($id),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    adjm_register_ability('adjm/set-site-favicon', [
        'label' => __('Définir le favicon', 'adjm-mcp'),
        'description' => __('Change l\'icône du site (favicon)', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'attachment_id' => ['type' => 'integer', 'description' => 'ID du média (512x512 recommandé)'],
            ],
            'required' => ['attachment_id'],
        ],
        'execute_callback' => function($input) {
            $id = absint($input['attachment_id']);
            update_option('site_icon', $id);
            return [
                'success' => true,
                'favicon_id' => $id,
                'favicon_url' => wp_get_attachment_url($id),
            ];
        },
        'permission_callback' => function() { return current_user_can('manage_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    // ----- CSS PERSONNALISÉ -----

    adjm_register_ability('adjm/get-custom-css', [
        'label' => __('Récupérer le CSS personnalisé', 'adjm-mcp'),
        'description' => __('CSS ajouté via le Customizer', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            return [
                'css' => wp_get_custom_css(),
                'theme' => get_stylesheet(),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/set-custom-css', [
        'label' => __('Définir le CSS personnalisé', 'adjm-mcp'),
        'description' => __('Remplace le CSS personnalisé du Customizer', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'css' => ['type' => 'string', 'description' => 'Code CSS à appliquer'],
            ],
            'required' => ['css'],
        ],
        'execute_callback' => function($input) {
            $result = wp_update_custom_css_post($input['css']);
            if (is_wp_error($result)) {
                return $result;
            }
            return [
                'success' => true,
                'post_id' => $result->ID,
                'css_length' => strlen($input['css']),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    adjm_register_ability('adjm/append-custom-css', [
        'label' => __('Ajouter du CSS personnalisé', 'adjm-mcp'),
        'description' => __('Ajoute du CSS sans écraser l\'existant', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'css' => ['type' => 'string', 'description' => 'CSS à ajouter'],
            ],
            'required' => ['css'],
        ],
        'execute_callback' => function($input) {
            $current = wp_get_custom_css();
            $new_css = $current . "\n\n/* Ajouté via MCP */\n" . $input['css'];
            $result = wp_update_custom_css_post($new_css);
            return [
                'success' => !is_wp_error($result),
                'total_length' => strlen($new_css),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    // ----- WIDGETS & SIDEBARS -----

    adjm_register_ability('adjm/list-sidebars', [
        'label' => __('Lister les zones de widgets', 'adjm-mcp'),
        'description' => __('Liste toutes les sidebars/zones de widgets enregistrées', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            global $wp_registered_sidebars;
            $sidebars = [];
            foreach ($wp_registered_sidebars as $id => $sidebar) {
                $sidebars[] = [
                    'id' => $id,
                    'name' => $sidebar['name'],
                    'description' => $sidebar['description'] ?? '',
                    'class' => $sidebar['class'] ?? '',
                ];
            }
            return ['sidebars' => $sidebars, 'count' => count($sidebars)];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/get-sidebar-widgets', [
        'label' => __('Récupérer les widgets d\'une sidebar', 'adjm-mcp'),
        'description' => __('Liste les widgets dans une zone spécifique', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'sidebar_id' => ['type' => 'string', 'description' => 'ID de la sidebar'],
            ],
            'required' => ['sidebar_id'],
        ],
        'execute_callback' => function($input) {
            $sidebar_id = sanitize_key($input['sidebar_id']);
            $sidebars_widgets = get_option('sidebars_widgets', []);
            
            if (!isset($sidebars_widgets[$sidebar_id])) {
                return new WP_Error('not_found', 'Sidebar introuvable');
            }
            
            $widgets = [];
            foreach ($sidebars_widgets[$sidebar_id] as $widget_id) {
                $widgets[] = [
                    'id' => $widget_id,
                    'type' => preg_replace('/-\d+$/', '', $widget_id),
                ];
            }
            
            return [
                'sidebar' => $sidebar_id,
                'widgets' => $widgets,
                'count' => count($widgets),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    // ----- GLOBAL STYLES (FSE / Block Themes) -----

    adjm_register_ability('adjm/get-global-styles', [
        'label' => __('Récupérer les styles globaux (FSE)', 'adjm-mcp'),
        'description' => __('Récupère theme.json et les styles globaux pour les thèmes FSE', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            if (!function_exists('wp_get_global_settings')) {
                return ['error' => 'Non disponible (WordPress < 5.9 ou thème non-FSE)'];
            }
            
            return [
                'settings' => wp_get_global_settings(),
                'styles' => function_exists('wp_get_global_styles') ? wp_get_global_styles() : null,
                'is_block_theme' => wp_is_block_theme(),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/get-theme-color-palette', [
        'label' => __('Récupérer la palette de couleurs du thème', 'adjm-mcp'),
        'description' => __('Couleurs définies dans theme.json ou le thème', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            if (function_exists('wp_get_global_settings')) {
                $settings = wp_get_global_settings();
                $palette = $settings['color']['palette'] ?? [];
            } else {
                $palette = get_theme_support('editor-color-palette');
                $palette = $palette ? $palette[0] : [];
            }
            
            return [
                'palette' => $palette,
                'count' => is_array($palette) ? count($palette) : 0,
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/get-theme-font-sizes', [
        'label' => __('Récupérer les tailles de police du thème', 'adjm-mcp'),
        'description' => __('Tailles de police définies dans le thème', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            if (function_exists('wp_get_global_settings')) {
                $settings = wp_get_global_settings();
                $sizes = $settings['typography']['fontSizes'] ?? [];
            } else {
                $sizes = get_theme_support('editor-font-sizes');
                $sizes = $sizes ? $sizes[0] : [];
            }
            
            return ['fontSizes' => $sizes];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    // ----- BLOCK PATTERNS -----

    adjm_register_ability('adjm/list-block-patterns', [
        'label' => __('Lister les patterns de blocs', 'adjm-mcp'),
        'description' => __('Liste tous les block patterns enregistrés', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            if (!class_exists('WP_Block_Patterns_Registry')) {
                return ['patterns' => [], 'error' => 'Non disponible'];
            }
            
            $registry = WP_Block_Patterns_Registry::get_instance();
            $patterns = $registry->get_all_registered();
            
            return [
                'patterns' => array_map(function($p) {
                    return [
                        'name' => $p['name'],
                        'title' => $p['title'],
                        'description' => $p['description'] ?? '',
                        'categories' => $p['categories'] ?? [],
                    ];
                }, $patterns),
                'count' => count($patterns),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    // ----- TEMPLATE PARTS (FSE) -----

    adjm_register_ability('adjm/list-template-parts', [
        'label' => __('Lister les Template Parts', 'adjm-mcp'),
        'description' => __('Headers, footers et autres parties de template (FSE)', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            if (!function_exists('get_block_templates')) {
                return ['error' => 'Non disponible (pas un thème FSE)'];
            }
            
            $parts = get_block_templates([], 'wp_template_part');
            
            return [
                'template_parts' => array_map(function($p) {
                    return [
                        'id' => $p->id,
                        'slug' => $p->slug,
                        'title' => $p->title,
                        'area' => $p->area ?? 'uncategorized',
                        'source' => $p->source,
                    ];
                }, $parts),
                'count' => count($parts),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/list-block-templates', [
        'label' => __('Lister les Block Templates', 'adjm-mcp'),
        'description' => __('Templates FSE (index, single, page, archive...)', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            if (!function_exists('get_block_templates')) {
                return ['error' => 'Non disponible (pas un thème FSE)'];
            }
            
            $templates = get_block_templates();
            
            return [
                'templates' => array_map(function($t) {
                    return [
                        'id' => $t->id,
                        'slug' => $t->slug,
                        'title' => $t->title,
                        'source' => $t->source,
                        'has_theme_file' => $t->has_theme_file,
                    ];
                }, $templates),
                'count' => count($templates),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    // ----- HEADER & FOOTER SETTINGS -----

    adjm_register_ability('adjm/get-header-footer-settings', [
        'label' => __('Récupérer les paramètres header/footer', 'adjm-mcp'),
        'description' => __('Options courantes de header et footer', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            return [
                'header_image' => get_header_image(),
                'header_text_color' => get_header_textcolor(),
                'background_color' => get_background_color(),
                'background_image' => get_background_image(),
                'custom_logo' => get_theme_mod('custom_logo'),
                'display_header_text' => display_header_text(),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);
}

// ========================================
// ELEMENTOR - Page Builder
// ========================================

// Détection automatique d'Elementor
if (defined('ELEMENTOR_VERSION') || class_exists('Elementor\Plugin')) {
    
    adjm_register_ability('adjm/elementor-get-settings', [
        'label' => __('Récupérer les paramètres Elementor', 'adjm-mcp'),
        'description' => __('Paramètres globaux d\'Elementor (couleurs, typo, breakpoints)', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            if (!class_exists('Elementor\Plugin')) {
                return ['error' => 'Elementor non actif'];
            }
            
            $kit_id = get_option('elementor_active_kit');
            $kit_settings = get_post_meta($kit_id, '_elementor_page_settings', true);
            
            return [
                'version' => ELEMENTOR_VERSION,
                'active_kit_id' => $kit_id,
                'global_colors' => $kit_settings['system_colors'] ?? [],
                'custom_colors' => $kit_settings['custom_colors'] ?? [],
                'global_typography' => $kit_settings['system_typography'] ?? [],
                'custom_typography' => $kit_settings['custom_typography'] ?? [],
                'container_width' => get_option('elementor_container_width', 1140),
                'space_between_widgets' => get_option('elementor_space_between_widgets', 20),
                'stretched_section_container' => get_option('elementor_stretched_section_container', ''),
                'page_title_selector' => get_option('elementor_page_title_selector', 'h1.entry-title'),
                'viewport_lg' => get_option('elementor_viewport_lg', 1025),
                'viewport_md' => get_option('elementor_viewport_md', 768),
                'lightbox_enable_counter' => get_option('elementor_lightbox_enable_counter', 'yes'),
                'lightbox_enable_fullscreen' => get_option('elementor_lightbox_enable_fullscreen', 'yes'),
                'lightbox_enable_zoom' => get_option('elementor_lightbox_enable_zoom', 'yes'),
            ];
        },
        'permission_callback' => function() { return current_user_can('manage_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/elementor-update-setting', [
        'label' => __('Modifier un paramètre Elementor', 'adjm-mcp'),
        'description' => __('Met à jour un paramètre global d\'Elementor', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'option_name' => ['type' => 'string', 'description' => 'Nom de l\'option (ex: elementor_container_width)'],
                'value' => ['description' => 'Nouvelle valeur'],
            ],
            'required' => ['option_name', 'value'],
        ],
        'execute_callback' => function($input) {
            $allowed = [
                'elementor_container_width',
                'elementor_space_between_widgets',
                'elementor_stretched_section_container',
                'elementor_page_title_selector',
                'elementor_viewport_lg',
                'elementor_viewport_md',
                'elementor_default_generic_fonts',
                'elementor_lightbox_enable_counter',
                'elementor_lightbox_enable_fullscreen',
                'elementor_lightbox_enable_zoom',
            ];
            
            $option = sanitize_key($input['option_name']);
            if (!in_array($option, $allowed)) {
                return new WP_Error('forbidden', 'Option non autorisée');
            }
            
            update_option($option, $input['value']);
            return [
                'success' => true,
                'option' => $option,
                'new_value' => get_option($option),
            ];
        },
        'permission_callback' => function() { return current_user_can('manage_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    adjm_register_ability('adjm/elementor-get-global-colors', [
        'label' => __('Récupérer les couleurs globales Elementor', 'adjm-mcp'),
        'description' => __('Palette de couleurs définie dans Elementor', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            $kit_id = get_option('elementor_active_kit');
            $kit_settings = get_post_meta($kit_id, '_elementor_page_settings', true);
            
            return [
                'system_colors' => $kit_settings['system_colors'] ?? [],
                'custom_colors' => $kit_settings['custom_colors'] ?? [],
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/elementor-get-global-fonts', [
        'label' => __('Récupérer les polices globales Elementor', 'adjm-mcp'),
        'description' => __('Typographies définies dans Elementor', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            $kit_id = get_option('elementor_active_kit');
            $kit_settings = get_post_meta($kit_id, '_elementor_page_settings', true);
            
            return [
                'system_typography' => $kit_settings['system_typography'] ?? [],
                'custom_typography' => $kit_settings['custom_typography'] ?? [],
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);
}

// ========================================
// DIVI - Theme & Builder
// ========================================

// Détection automatique de Divi
if (defined('ET_BUILDER_VERSION') || function_exists('et_get_option')) {
    
    adjm_register_ability('adjm/divi-get-settings', [
        'label' => __('Récupérer les paramètres Divi', 'adjm-mcp'),
        'description' => __('Options du thème Divi et du Builder', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            if (!function_exists('et_get_option')) {
                return ['error' => 'Divi non actif'];
            }
            
            return [
                'version' => defined('ET_BUILDER_VERSION') ? ET_BUILDER_VERSION : 'N/A',
                'accent_color' => et_get_option('accent_color', '#2ea3f2'),
                'link_color' => et_get_option('link_color', '#2ea3f2'),
                'primary_nav_bg' => et_get_option('primary_nav_bg', '#ffffff'),
                'primary_nav_text_color' => et_get_option('primary_nav_text_color', 'rgba(0,0,0,0.6)'),
                'secondary_nav_bg' => et_get_option('secondary_nav_bg', '#2ea3f2'),
                'secondary_nav_text_color' => et_get_option('secondary_nav_text_color', '#ffffff'),
                'menu_link' => et_get_option('menu_link', 'rgba(0,0,0,0.6)'),
                'menu_link_active' => et_get_option('menu_link_active', '#2ea3f2'),
                'header_font_size' => et_get_option('header_font_size', '30'),
                'body_font_size' => et_get_option('body_font_size', '14'),
                'logo' => et_get_option('divi_logo', ''),
                'favicon' => et_get_option('divi_favicon', ''),
                'fixed_nav' => et_get_option('divi_fixed_nav', 'on'),
                'boxed_layout' => et_get_option('boxed_layout', 'off'),
                'sidebar' => et_get_option('divi_sidebar', 'right'),
                'grab_image' => et_get_option('divi_grab_image', 'on'),
                'blog_style' => et_get_option('divi_blog_style', 'false'),
                'show_footer_social_icons' => et_get_option('show_footer_social_icons', 'on'),
                'footer_columns' => et_get_option('footer_columns', '4'),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/divi-update-setting', [
        'label' => __('Modifier un paramètre Divi', 'adjm-mcp'),
        'description' => __('Met à jour une option du thème Divi', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'option_name' => ['type' => 'string', 'description' => 'Nom de l\'option Divi'],
                'value' => ['description' => 'Nouvelle valeur'],
            ],
            'required' => ['option_name', 'value'],
        ],
        'execute_callback' => function($input) {
            if (!function_exists('et_update_option')) {
                return new WP_Error('not_available', 'Divi non actif');
            }
            
            $allowed = [
                'accent_color', 'link_color', 'primary_nav_bg', 'primary_nav_text_color',
                'secondary_nav_bg', 'secondary_nav_text_color', 'menu_link', 'menu_link_active',
                'header_font_size', 'body_font_size', 'divi_logo', 'divi_favicon',
                'divi_fixed_nav', 'boxed_layout', 'divi_sidebar', 'divi_grab_image',
                'divi_blog_style', 'show_footer_social_icons', 'footer_columns',
            ];
            
            $option = sanitize_key($input['option_name']);
            if (!in_array($option, $allowed)) {
                return new WP_Error('forbidden', 'Option non autorisée');
            }
            
            et_update_option($option, $input['value']);
            return [
                'success' => true,
                'option' => $option,
                'new_value' => et_get_option($option),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    adjm_register_ability('adjm/divi-get-color-scheme', [
        'label' => __('Récupérer le schéma de couleurs Divi', 'adjm-mcp'),
        'description' => __('Toutes les couleurs configurées dans Divi', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            if (!function_exists('et_get_option')) {
                return ['error' => 'Divi non actif'];
            }
            
            return [
                'accent_color' => et_get_option('accent_color', '#2ea3f2'),
                'link_color' => et_get_option('link_color', '#2ea3f2'),
                'primary_nav_bg' => et_get_option('primary_nav_bg', '#ffffff'),
                'primary_nav_text_color' => et_get_option('primary_nav_text_color', 'rgba(0,0,0,0.6)'),
                'secondary_nav_bg' => et_get_option('secondary_nav_bg', '#2ea3f2'),
                'secondary_nav_text_color' => et_get_option('secondary_nav_text_color', '#ffffff'),
                'menu_link' => et_get_option('menu_link', 'rgba(0,0,0,0.6)'),
                'menu_link_active' => et_get_option('menu_link_active', '#2ea3f2'),
                'footer_bg' => et_get_option('footer_bg', '#222222'),
                'footer_widget_bg' => et_get_option('footer_widget_bg', '#292929'),
                'bottom_bar_bg' => et_get_option('bottom_bar_bg', '#1d1d1d'),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/divi-get-typography', [
        'label' => __('Récupérer la typographie Divi', 'adjm-mcp'),
        'description' => __('Polices et tailles configurées dans Divi', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            if (!function_exists('et_get_option')) {
                return ['error' => 'Divi non actif'];
            }
            
            return [
                'heading_font' => et_get_option('heading_font', 'none'),
                'body_font' => et_get_option('body_font', 'none'),
                'header_font_size' => et_get_option('header_font_size', '30'),
                'body_font_size' => et_get_option('body_font_size', '14'),
                'body_font_height' => et_get_option('body_font_height', '1.7'),
                'body_header_size' => et_get_option('body_header_size', '26'),
                'body_header_spacing' => et_get_option('body_header_spacing', '2'),
                'body_header_height' => et_get_option('body_header_height', '1'),
                'body_header_style' => et_get_option('body_header_style', ''),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);
}

// ========================================
// DÉTECTION PAGE BUILDERS ACTIFS
// ========================================

adjm_register_ability('adjm/detect-page-builders', [
    'label' => __('Détecter les Page Builders actifs', 'adjm-mcp'),
    'description' => __('Liste les page builders installés et actifs', 'adjm-mcp'),
    'category' => 'system',
    'input_schema' => ['type' => 'object', 'properties' => []],
    'execute_callback' => function($input) {
        $builders = [];
        
        // Elementor
        if (defined('ELEMENTOR_VERSION')) {
            $builders['elementor'] = [
                'active' => true,
                'version' => ELEMENTOR_VERSION,
                'pro' => defined('ELEMENTOR_PRO_VERSION'),
                'pro_version' => defined('ELEMENTOR_PRO_VERSION') ? ELEMENTOR_PRO_VERSION : null,
            ];
        }
        
        // Divi
        if (defined('ET_BUILDER_VERSION')) {
            $builders['divi'] = [
                'active' => true,
                'version' => ET_BUILDER_VERSION,
                'theme' => function_exists('et_get_theme_version'),
            ];
        }
        
        // Beaver Builder
        if (class_exists('FLBuilder')) {
            $builders['beaver_builder'] = [
                'active' => true,
                'version' => defined('FL_BUILDER_VERSION') ? FL_BUILDER_VERSION : 'N/A',
            ];
        }
        
        // WPBakery
        if (defined('WPB_VC_VERSION')) {
            $builders['wpbakery'] = [
                'active' => true,
                'version' => WPB_VC_VERSION,
            ];
        }
        
        // Brizy
        if (defined('BRIZY_VERSION')) {
            $builders['brizy'] = [
                'active' => true,
                'version' => BRIZY_VERSION,
            ];
        }
        
        // Oxygen
        if (defined('CT_VERSION')) {
            $builders['oxygen'] = [
                'active' => true,
                'version' => CT_VERSION,
            ];
        }
        
        // Gutenberg (toujours actif sur WP récent)
        $builders['gutenberg'] = [
            'active' => true,
            'version' => $GLOBALS['wp_version'] ?? 'N/A',
            'fse_enabled' => function_exists('wp_is_block_theme') && wp_is_block_theme(),
        ];
        
        return [
            'builders' => $builders,
            'count' => count($builders),
            'primary' => !empty($builders) ? array_key_first($builders) : 'gutenberg',
        ];
    },
    'permission_callback' => function() { return current_user_can('edit_theme_options'); },
    'meta' => adjm_mcp_meta(true),
]);

// ========================================
// UTILISATEURS
// ========================================

if (adjm_is_group_enabled('users')) {
    
    adjm_register_ability('adjm/list-users', [
        'label' => __('Lister les utilisateurs', 'adjm-mcp'),
        'description' => __('Récupère la liste des utilisateurs', 'adjm-mcp'),
        'category' => 'users',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'role' => ['type' => 'string', 'description' => 'Filtrer par rôle'],
                'per_page' => ['type' => 'integer', 'default' => 20],
                'page' => ['type' => 'integer', 'default' => 1],
                'search' => ['type' => 'string'],
                'orderby' => ['type' => 'string', 'enum' => ['ID', 'login', 'email', 'registered'], 'default' => 'registered'],
            ],
        ],
        'execute_callback' => function($input) {
            $args = [
                'number' => min(absint($input['per_page'] ?? 20), 100),
                'paged' => absint($input['page'] ?? 1),
                'orderby' => $input['orderby'] ?? 'registered',
            ];
            
            if (!empty($input['role'])) $args['role'] = sanitize_key($input['role']);
            if (!empty($input['search'])) $args['search'] = '*' . sanitize_text_field($input['search']) . '*';
            
            $users_query = new WP_User_Query($args);
            
            return [
                'users' => array_map(function($user) {
                    return [
                        'id' => $user->ID,
                        'username' => $user->user_login,
                        'email' => $user->user_email,
                        'display_name' => $user->display_name,
                        'roles' => $user->roles,
                        'registered' => $user->user_registered,
                        'avatar' => get_avatar_url($user->ID),
                    ];
                }, $users_query->get_results()),
                'total' => $users_query->get_total(),
            ];
        },
        'permission_callback' => function() { return current_user_can('list_users'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/get-user', [
        'label' => __('Récupérer un utilisateur', 'adjm-mcp'),
        'description' => __('Détails d\'un utilisateur', 'adjm-mcp'),
        'category' => 'users',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer'],
            ],
            'required' => ['id'],
        ],
        'execute_callback' => function($input) {
            $user = get_userdata(absint($input['id']));
            if (!$user) return new WP_Error('not_found', 'Utilisateur introuvable');
            
            return [
                'id' => $user->ID,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'display_name' => $user->display_name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'nickname' => $user->nickname,
                'description' => $user->description,
                'roles' => $user->roles,
                'capabilities' => array_keys(array_filter($user->allcaps)),
                'registered' => $user->user_registered,
                'url' => $user->user_url,
                'avatar' => get_avatar_url($user->ID),
            ];
        },
        'permission_callback' => function() { return current_user_can('list_users'); },
        'meta' => adjm_mcp_meta(true),
    ]);
}

// ========================================
// OPTIONS
// ========================================

if (adjm_is_group_enabled('options')) {
    
    adjm_register_ability('adjm/get-option', [
        'label' => __('Récupérer une option', 'adjm-mcp'),
        'description' => __('Récupère la valeur d\'une option WordPress', 'adjm-mcp'),
        'category' => 'system',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string', 'description' => 'Nom de l\'option'],
            ],
            'required' => ['name'],
        ],
        'execute_callback' => function($input) {
            $name = sanitize_key($input['name']);
            
            // Bloquer les options sensibles
            $blocked = ['admin_email', 'users_can_register', 'default_role', 'db_version'];
            if (in_array($name, $blocked) && !current_user_can('manage_options')) {
                return new WP_Error('forbidden', 'Option protégée');
            }
            
            $value = get_option($name);
            return ['name' => $name, 'value' => $value, 'exists' => $value !== false];
        },
        'permission_callback' => function() { return current_user_can('manage_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/update-option', [
        'label' => __('Modifier une option', 'adjm-mcp'),
        'description' => __('Met à jour une option WordPress', 'adjm-mcp'),
        'category' => 'system',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
                'value' => ['description' => 'Nouvelle valeur (string, number, array, object)'],
            ],
            'required' => ['name', 'value'],
        ],
        'execute_callback' => function($input) {
            $name = sanitize_key($input['name']);
            
            // Options interdites
            $forbidden = ['siteurl', 'home', 'admin_email', 'users_can_register', 'default_role'];
            if (in_array($name, $forbidden)) {
                return new WP_Error('forbidden', 'Option non modifiable via MCP');
            }
            
            $result = update_option($name, $input['value']);
            return ['success' => true, 'name' => $name, 'updated' => $result];
        },
        'permission_callback' => function() { return current_user_can('manage_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);
}

// ========================================
// SITE INFO
// ========================================

if (adjm_is_group_enabled('site_info')) {
    
    adjm_register_ability('adjm/get-site-info', [
        'label' => __('Informations du site', 'adjm-mcp'),
        'description' => __('Récupère les infos générales du site', 'adjm-mcp'),
        'category' => 'system',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            return [
                'name' => get_bloginfo('name'),
                'description' => get_bloginfo('description'),
                'url' => home_url(),
                'admin_url' => admin_url(),
                'rest_url' => rest_url(),
                'language' => get_bloginfo('language'),
                'charset' => get_bloginfo('charset'),
                'wordpress_version' => get_bloginfo('version'),
                'php_version' => PHP_VERSION,
                'timezone' => wp_timezone_string(),
                'date_format' => get_option('date_format'),
                'time_format' => get_option('time_format'),
                'posts_per_page' => get_option('posts_per_page'),
                'is_multisite' => is_multisite(),
            ];
        },
        'permission_callback' => function() { return current_user_can('manage_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);
}

// ========================================
// PLUGINS
// ========================================

if (adjm_is_group_enabled('plugins')) {
    
    adjm_register_ability('adjm/list-plugins', [
        'label' => __('Lister les plugins', 'adjm-mcp'),
        'description' => __('Liste tous les plugins installés', 'adjm-mcp'),
        'category' => 'system',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'status' => ['type' => 'string', 'enum' => ['all', 'active', 'inactive'], 'default' => 'all'],
            ],
        ],
        'execute_callback' => function($input) {
            if (!function_exists('get_plugins')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            
            $all_plugins = get_plugins();
            $active_plugins = get_option('active_plugins', []);
            $status = $input['status'] ?? 'all';
            
            $plugins = [];
            foreach ($all_plugins as $file => $data) {
                $is_active = in_array($file, $active_plugins);
                
                if ($status === 'active' && !$is_active) continue;
                if ($status === 'inactive' && $is_active) continue;
                
                $plugins[] = [
                    'file' => $file,
                    'name' => $data['Name'],
                    'version' => $data['Version'],
                    'author' => $data['Author'],
                    'description' => $data['Description'],
                    'active' => $is_active,
                    'network' => $data['Network'] ?? false,
                ];
            }
            
            return ['plugins' => $plugins, 'total' => count($plugins)];
        },
        'permission_callback' => function() { return current_user_can('activate_plugins'); },
        'meta' => adjm_mcp_meta(true),
    ]);
}

// ========================================
// DESIGN SYSTEM DYNAMIQUE
// ========================================

if (adjm_is_group_enabled('theme')) {
    
    /**
     * Récupérer la configuration par défaut du Design System
     */
    function adjm_get_default_design() {
        return [
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
    }

    /**
     * Récupérer le Design System actuel (fusion DB + defaults)
     */
    function adjm_get_design_system() {
        $defaults = adjm_get_default_design();
        $saved = get_option('adjm_design_system', []);
        return array_replace_recursive($defaults, $saved);
    }

    adjm_register_ability('adjm/get-design-system', [
        'label' => __('Récupérer le Design System', 'adjm-mcp'),
        'description' => __('Récupère la configuration complète des couleurs, polices et styles globaux', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            return adjm_get_design_system();
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/update-design-system', [
        'label' => __('Modifier le Design System', 'adjm-mcp'),
        'description' => __('Met à jour les couleurs, polices et styles globaux du site', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'colors' => [
                    'type' => 'object',
                    'description' => 'Palette de couleurs (primary, secondary, dark, light, background, accent)',
                    'additionalProperties' => true
                ],
                'fonts' => [
                    'type' => 'object',
                    'description' => 'Familles de polices (header, body, google_url)',
                    'additionalProperties' => true
                ],
                'borderRadius' => [
                    'type' => 'string',
                    'description' => 'Arrondi des boutons (ex: 50px, 4px, 0)'
                ],
                'spacing' => [
                    'type' => 'object',
                    'description' => 'Espacements (small, medium, large)',
                    'additionalProperties' => true
                ],
            ],
        ],
        'execute_callback' => function($input) {
            $current = adjm_get_design_system();
            $updates = [];

            if (!empty($input['colors'])) {
                $updates['colors'] = array_merge($current['colors'], $input['colors']);
            }
            if (!empty($input['fonts'])) {
                $updates['fonts'] = array_merge($current['fonts'], $input['fonts']);
            }
            if (isset($input['borderRadius'])) {
                $updates['borderRadius'] = sanitize_text_field($input['borderRadius']);
            }
            if (!empty($input['spacing'])) {
                $updates['spacing'] = array_merge($current['spacing'], $input['spacing']);
            }

            $final = array_replace_recursive($current, $updates);
            update_option('adjm_design_system', $final);

            return [
                'success' => true,
                'message' => __('Design System mis à jour avec succès', 'adjm-mcp'),
                'new_config' => $final
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    adjm_register_ability('adjm/reset-design-system', [
        'label' => __('Réinitialiser le Design System', 'adjm-mcp'),
        'description' => __('Remet le Design System aux valeurs par défaut', 'adjm-mcp'),
        'category' => 'appearance',
        'input_schema' => ['type' => 'object', 'properties' => []],
        'execute_callback' => function($input) {
            delete_option('adjm_design_system');
            return [
                'success' => true,
                'message' => __('Design System réinitialisé aux valeurs par défaut', 'adjm-mcp'),
                'config' => adjm_get_default_design()
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_theme_options'); },
        'meta' => adjm_mcp_meta(false),
    ]);
}

// ========================================
// QUOTA & LICENCE - ALERTES IA
// ========================================

/**
 * ⚠️ ABILITY CRITIQUE : Vérification du quota et alertes
 * Cette ability est TOUJOURS disponible, même en version FREE
 * L'IA doit l'appeler régulièrement pour monitorer l'utilisation
 */
adjm_register_ability('adjm/check-quota', [
    'label' => __('Vérifier le quota et la licence', 'adjm-mcp'),
    'description' => __('Vérifie le quota journalier et alerte si dépassé avec proposition upgrade', 'adjm-mcp'),
    'category' => 'system',
    'input_schema' => ['type' => 'object', 'properties' => []],
    'execute_callback' => function($input) {
        $license = IA_Pilote_License::get_instance();
        $status = $license->get_status();
        $plan = $status['plan'] ?? 'free';
        $limits = IA_Pilote_License::get_limits($plan);
        
        // Calculer l'utilisation
        $today = date('Y-m-d');
        $count_key = 'ia_pilote_requests_' . $today;
        $used_today = (int) get_transient($count_key);
        $limit_per_day = $limits['requests_per_day'];
        $remaining = $license->get_remaining_requests();
        
        // Construire la réponse
        $response = [
            'plan' => strtoupper($plan),
            'is_pro' => $license->is_pro(),
            'quota' => [
                'used_today' => $used_today,
                'limit_per_day' => $limit_per_day === -1 ? 'illimité' : $limit_per_day,
                'remaining' => $remaining === -1 ? 'illimité' : $remaining,
                'percentage_used' => $limit_per_day === -1 ? 0 : round(($used_today / $limit_per_day) * 100, 1),
            ],
            'expires' => $status['expires'] ?? null,
        ];
        
        // ⚠️ ALERTES
        $alerts = [];
        
        // Alerte quota dépassé
        if ($limit_per_day !== -1 && $remaining <= 0) {
            $alerts[] = [
                'type' => 'quota_exceeded',
                'severity' => 'critical',
                'message' => "🚨 QUOTA JOURNALIER DÉPASSÉ ! Vous avez utilisé {$used_today}/{$limit_per_day} requêtes aujourd'hui. Les abilities sont bloquées jusqu'à demain.",
                'action' => 'upgrade',
            ];
        }
        // Alerte quota proche de la limite (>80%)
        elseif ($limit_per_day !== -1 && $remaining <= ($limit_per_day * 0.2)) {
            $alerts[] = [
                'type' => 'quota_warning',
                'severity' => 'warning',
                'message' => "⚠️ ATTENTION : Il ne reste que {$remaining} requêtes pour aujourd'hui ({$used_today}/{$limit_per_day} utilisées).",
                'action' => 'upgrade',
            ];
        }
        
        // Alerte plan FREE
        if ($plan === 'free') {
            $alerts[] = [
                'type' => 'free_plan',
                'severity' => 'info',
                'message' => "💡 Vous utilisez le plan FREE avec {$limit_per_day} requêtes/jour max. Passez à PRO pour des requêtes illimitées et toutes les abilities !",
                'action' => 'upgrade',
            ];
        }
        
        // Alerte expiration proche
        if (!empty($status['expires'])) {
            $expires = strtotime($status['expires']);
            $days_left = floor(($expires - time()) / DAY_IN_SECONDS);
            
            if ($days_left <= 0) {
                $alerts[] = [
                    'type' => 'license_expired',
                    'severity' => 'critical',
                    'message' => "🚨 LICENCE EXPIRÉE ! Votre licence a expiré. Renouvelez pour continuer à utiliser les fonctionnalités PRO.",
                    'action' => 'renew',
                ];
            } elseif ($days_left <= 7) {
                $alerts[] = [
                    'type' => 'license_expiring_soon',
                    'severity' => 'warning',
                    'message' => "⚠️ Votre licence expire dans {$days_left} jours. Pensez à la renouveler !",
                    'action' => 'renew',
                ];
            }
        }
        
        $response['alerts'] = $alerts;
        $response['has_alerts'] = !empty($alerts);
        
        // ✅ PROPOSITION D'UPGRADE
        if ($plan === 'free' || (!empty($alerts) && $alerts[0]['action'] === 'upgrade')) {
            $response['upgrade'] = [
                'recommended_plan' => 'PRO',
                'benefits' => [
                    '✅ Requêtes illimitées (vs 100/jour)',
                    '✅ Toutes les abilities débloquées',
                    '✅ WooCommerce, SEO, ACF, Forms...',
                    '✅ Bulk operations',
                    '✅ Support prioritaire',
                ],
                'pricing' => [
                    'pro' => ['price' => '49€/an', 'sites' => 1],
                    'business' => ['price' => '149€/an', 'sites' => 5],
                    'agency' => ['price' => '299€/an', 'sites' => 'illimité'],
                ],
                'buy_links' => [
                    'pro' => 'https://centerhome.net/ia-pilote-pro?utm_source=plugin&utm_medium=quota_alert',
                    'business' => 'https://centerhome.net/ia-pilote-business?utm_source=plugin&utm_medium=quota_alert',
                    'agency' => 'https://centerhome.net/ia-pilote-agency?utm_source=plugin&utm_medium=quota_alert',
                ],
                'call_to_action' => $remaining <= 0 
                    ? "🛒 URGENT: Achetez une licence PRO maintenant pour continuer à travailler !"
                    : "💎 Passez à PRO pour des requêtes illimitées et toutes les fonctionnalités !",
            ];
        }
        
        // Lien de renouvellement si licence expirante/expirée
        if (!empty($alerts) && isset($alerts[0]['action']) && $alerts[0]['action'] === 'renew') {
            $response['renew'] = [
                'link' => 'https://centerhome.net/account/renew?utm_source=plugin&utm_medium=expiry_alert',
                'message' => 'Renouvelez votre licence pour continuer à profiter de toutes les fonctionnalités.',
            ];
        }
        
        return $response;
    },
    'permission_callback' => '__return_true', // Toujours accessible !
    'meta' => [
        'is_read_only' => true,
        'category' => 'system',
        'priority' => 'high', // Priorité haute pour l'IA
    ],
]);

/**
 * Ability pour récupérer les statistiques d'utilisation détaillées
 */
adjm_register_ability('adjm/get-usage-stats', [
    'label' => __('Statistiques d\'utilisation', 'adjm-mcp'),
    'description' => __('Récupère les statistiques d\'utilisation du plugin sur les 7 derniers jours', 'adjm-mcp'),
    'category' => 'system',
    'input_schema' => ['type' => 'object', 'properties' => []],
    'execute_callback' => function($input) {
        $license = IA_Pilote_License::get_instance();
        $status = $license->get_status();
        $plan = $status['plan'] ?? 'free';
        $limits = IA_Pilote_License::get_limits($plan);
        
        // Historique sur 7 jours
        $history = [];
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $count_key = 'ia_pilote_requests_' . $date;
            $count = (int) get_transient($count_key);
            $history[$date] = $count;
        }
        
        // Abilities les plus utilisées (si logs activés)
        $top_abilities = [];
        global $wpdb;
        $table = $wpdb->prefix . 'ia_pilote_logs';
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") === $table) {
            $results = $wpdb->get_results("
                SELECT ability, COUNT(*) as count 
                FROM $table 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY ability 
                ORDER BY count DESC 
                LIMIT 10
            ");
            foreach ($results as $row) {
                $top_abilities[$row->ability] = (int) $row->count;
            }
        }
        
        return [
            'plan' => strtoupper($plan),
            'limits' => $limits,
            'usage_7_days' => $history,
            'total_7_days' => array_sum($history),
            'average_per_day' => round(array_sum($history) / 7, 1),
            'top_abilities' => $top_abilities,
            'recommendation' => array_sum($history) > 500 && $plan === 'free' 
                ? "Avec ~" . round(array_sum($history) / 7) . " requêtes/jour en moyenne, vous dépassez régulièrement le quota FREE. Passez à PRO pour éviter les blocages !"
                : null,
        ];
    },
    'permission_callback' => function() { return current_user_can('manage_options'); },
    'meta' => adjm_mcp_meta(true),
]);

/**
 * Ability pour obtenir les infos de licence complètes
 */
adjm_register_ability('adjm/get-license-info', [
    'label' => __('Informations de licence', 'adjm-mcp'),
    'description' => __('Récupère toutes les informations sur la licence active', 'adjm-mcp'),
    'category' => 'system',
    'input_schema' => ['type' => 'object', 'properties' => []],
    'execute_callback' => function($input) {
        $license = IA_Pilote_License::get_instance();
        $status = $license->get_status();
        $plan = $status['plan'] ?? 'free';
        
        return [
            'plan' => strtoupper($plan),
            'is_pro' => $license->is_pro(),
            'status' => $status,
            'features' => [
                'free' => IA_Pilote_License::get_free_features(),
                'pro' => IA_Pilote_License::get_pro_features(),
            ],
            'limits' => IA_Pilote_License::get_limits(),
            'current_limits' => IA_Pilote_License::get_limits($plan),
            'upgrade_url' => 'https://centerhome.net/ia-pilote-pro',
            'account_url' => 'https://centerhome.net/account',
        ];
    },
    'permission_callback' => function() { return current_user_can('manage_options'); },
    'meta' => adjm_mcp_meta(true),
]);

/**
 * ⚠️ ABILITY CRITIQUE : Vérifier l'accès à une fonctionnalité
 * L'IA doit appeler cette ability AVANT d'utiliser une fonctionnalité PRO
 * pour éviter les erreurs et proposer l'upgrade à l'utilisateur
 */
adjm_register_ability('adjm/check-feature-access', [
    'label' => __('Vérifier l\'accès à une fonctionnalité', 'adjm-mcp'),
    'description' => __('Vérifie si l\'utilisateur a accès à une fonctionnalité et propose l\'upgrade si nécessaire', 'adjm-mcp'),
    'category' => 'system',
    'input_schema' => [
        'type' => 'object',
        'properties' => [
            'feature' => ['type' => 'string', 'description' => 'Nom de la fonctionnalité (ex: woocommerce, seo, acf, bulk, menus)'],
            'action' => ['type' => 'string', 'description' => 'Action spécifique (ex: update, delete, upload)'],
        ],
        'required' => ['feature'],
    ],
    'execute_callback' => function($input) {
        $license = IA_Pilote_License::get_instance();
        $status = $license->get_status();
        $plan = $status['plan'] ?? 'free';
        
        $feature = sanitize_key($input['feature']);
        $action = isset($input['action']) ? sanitize_key($input['action']) : null;
        
        // Vérifier l'accès
        $has_access = $license->can_use_feature($feature, $action);
        
        // Features descriptions pour les messages
        $feature_descriptions = [
            'woocommerce' => [
                'name' => 'WooCommerce',
                'description' => 'Gestion des produits, commandes et clients',
                'actions' => ['products', 'orders', 'customers', 'stats'],
            ],
            'seo' => [
                'name' => 'SEO (Yoast/RankMath)',
                'description' => 'Optimisation du référencement',
                'actions' => ['get', 'update'],
            ],
            'acf' => [
                'name' => 'ACF (Advanced Custom Fields)',
                'description' => 'Champs personnalisés avancés',
                'actions' => ['get', 'update'],
            ],
            'forms' => [
                'name' => 'Formulaires (CF7, Gravity)',
                'description' => 'Gestion des formulaires de contact',
                'actions' => ['list', 'entries'],
            ],
            'bulk' => [
                'name' => 'Opérations en masse',
                'description' => 'Mise à jour et suppression en lot',
                'actions' => ['update', 'delete'],
            ],
            'menus' => [
                'name' => 'Menus de navigation',
                'description' => 'Création et modification des menus',
                'actions' => ['list', 'items', 'create', 'update', 'delete'],
            ],
            'theme' => [
                'name' => 'Personnalisation du thème',
                'description' => 'Modification des options du thème',
                'actions' => ['info', 'templates', 'update'],
            ],
            'users' => [
                'name' => 'Gestion des utilisateurs',
                'description' => 'Création et modification d\'utilisateurs',
                'actions' => ['list', 'get', 'create', 'update', 'delete'],
            ],
            'media' => [
                'name' => 'Médiathèque',
                'description' => 'Upload et gestion des médias',
                'actions' => ['list', 'upload', 'delete'],
            ],
            'pages' => [
                'name' => 'Pages',
                'description' => 'Gestion des pages WordPress',
                'actions' => ['list', 'get', 'create', 'update', 'delete'],
            ],
            'posts' => [
                'name' => 'Articles',
                'description' => 'Gestion des articles du blog',
                'actions' => ['list', 'get', 'create', 'update', 'delete'],
            ],
            'options' => [
                'name' => 'Options WordPress',
                'description' => 'Lecture et modification des options',
                'actions' => ['get', 'update'],
            ],
            'logs' => [
                'name' => 'Logs d\'activité',
                'description' => 'Consultation des logs du plugin',
                'actions' => ['view', 'clear'],
            ],
        ];
        
        $feature_info = $feature_descriptions[$feature] ?? [
            'name' => ucfirst($feature),
            'description' => "Fonctionnalité {$feature}",
            'actions' => [],
        ];
        
        // Construire la réponse
        $response = [
            'feature' => $feature,
            'action' => $action,
            'has_access' => $has_access,
            'current_plan' => strtoupper($plan),
            'feature_info' => $feature_info,
        ];
        
        // Si pas d'accès, construire l'alerte upgrade
        if (!$has_access) {
            $action_text = $action ? " ({$action})" : "";
            
            $response['access_denied'] = true;
            $response['alert'] = [
                'type' => 'feature_locked',
                'severity' => 'warning',
                'message' => "🔒 FONCTIONNALITÉ VERROUILLÉE : {$feature_info['name']}{$action_text} n'est pas disponible avec le plan {$plan}.",
                'details' => "Cette fonctionnalité ({$feature_info['description']}) nécessite un plan PRO ou supérieur.",
            ];
            
            $response['upgrade'] = [
                'required_plan' => 'PRO',
                'message' => "💎 Passez à PRO pour débloquer {$feature_info['name']} !",
                'feature_benefits' => [
                    "✅ {$feature_info['name']} - {$feature_info['description']}",
                    "✅ Requêtes illimitées",
                    "✅ Toutes les abilities débloquées",
                    "✅ Support prioritaire",
                ],
                'pricing' => [
                    'pro' => ['price' => '49€/an', 'sites' => 1, 'best_for' => '1 site'],
                    'business' => ['price' => '149€/an', 'sites' => 5, 'best_for' => 'Agence'],
                    'agency' => ['price' => '299€/an', 'sites' => 'illimité', 'best_for' => 'Multi-sites'],
                ],
                'buy_links' => [
                    'pro' => "https://centerhome.net/ia-pilote-pro?utm_source=plugin&utm_medium=feature_locked&utm_content={$feature}",
                    'business' => "https://centerhome.net/ia-pilote-business?utm_source=plugin&utm_medium=feature_locked&utm_content={$feature}",
                    'agency' => "https://centerhome.net/ia-pilote-agency?utm_source=plugin&utm_medium=feature_locked&utm_content={$feature}",
                ],
                'call_to_action' => "🛒 Achetez une licence PRO pour utiliser {$feature_info['name']} dès maintenant !",
            ];
            
            // Lister les autres fonctionnalités PRO
            $pro_features = IA_Pilote_License::get_pro_features();
            $locked_features = [];
            foreach ($pro_features as $f => $actions) {
                if (!$license->can_use_feature($f)) {
                    $locked_features[] = $feature_descriptions[$f]['name'] ?? ucfirst($f);
                }
            }
            $response['other_locked_features'] = array_slice($locked_features, 0, 5);
        } else {
            $response['access_granted'] = true;
            $response['message'] = "✅ Vous avez accès à {$feature_info['name']}" . ($action ? " ({$action})" : "") . ".";
        }
        
        return $response;
    },
    'permission_callback' => '__return_true', // Toujours accessible
    'meta' => [
        'is_read_only' => true,
        'category' => 'system',
        'priority' => 'high',
    ],
]);

/**
 * Ability pour lister toutes les fonctionnalités et leur statut d'accès
 */
adjm_register_ability('adjm/list-available-features', [
    'label' => __('Lister les fonctionnalités disponibles', 'adjm-mcp'),
    'description' => __('Liste toutes les fonctionnalités avec leur statut d\'accès selon le plan actuel', 'adjm-mcp'),
    'category' => 'system',
    'input_schema' => ['type' => 'object', 'properties' => []],
    'execute_callback' => function($input) {
        $license = IA_Pilote_License::get_instance();
        $status = $license->get_status();
        $plan = $status['plan'] ?? 'free';
        
        $all_features = [
            // Fonctionnalités FREE
            ['group' => 'pages', 'action' => 'list', 'name' => 'Lister les pages', 'plan_required' => 'free'],
            ['group' => 'pages', 'action' => 'get', 'name' => 'Voir une page', 'plan_required' => 'free'],
            ['group' => 'pages', 'action' => 'create', 'name' => 'Créer une page', 'plan_required' => 'free'],
            ['group' => 'pages', 'action' => 'update', 'name' => 'Modifier une page', 'plan_required' => 'pro'],
            ['group' => 'pages', 'action' => 'delete', 'name' => 'Supprimer une page', 'plan_required' => 'pro'],
            
            ['group' => 'posts', 'action' => 'list', 'name' => 'Lister les articles', 'plan_required' => 'free'],
            ['group' => 'posts', 'action' => 'get', 'name' => 'Voir un article', 'plan_required' => 'free'],
            ['group' => 'posts', 'action' => 'create', 'name' => 'Créer un article', 'plan_required' => 'free'],
            ['group' => 'posts', 'action' => 'update', 'name' => 'Modifier un article', 'plan_required' => 'pro'],
            ['group' => 'posts', 'action' => 'delete', 'name' => 'Supprimer un article', 'plan_required' => 'pro'],
            
            ['group' => 'media', 'action' => 'list', 'name' => 'Lister les médias', 'plan_required' => 'free'],
            ['group' => 'media', 'action' => 'upload', 'name' => 'Uploader un média', 'plan_required' => 'pro'],
            ['group' => 'media', 'action' => 'delete', 'name' => 'Supprimer un média', 'plan_required' => 'pro'],
            
            ['group' => 'menus', 'action' => 'list', 'name' => 'Lister les menus', 'plan_required' => 'pro'],
            ['group' => 'menus', 'action' => 'items', 'name' => 'Voir les items de menu', 'plan_required' => 'pro'],
            
            ['group' => 'theme', 'action' => 'info', 'name' => 'Infos thème', 'plan_required' => 'pro'],
            ['group' => 'theme', 'action' => 'templates', 'name' => 'Templates de page', 'plan_required' => 'pro'],
            
            ['group' => 'users', 'action' => 'list', 'name' => 'Lister les utilisateurs', 'plan_required' => 'pro'],
            ['group' => 'users', 'action' => 'create', 'name' => 'Créer un utilisateur', 'plan_required' => 'pro'],
            
            ['group' => 'woocommerce', 'action' => 'products', 'name' => 'Produits WooCommerce', 'plan_required' => 'pro'],
            ['group' => 'woocommerce', 'action' => 'orders', 'name' => 'Commandes WooCommerce', 'plan_required' => 'pro'],
            
            ['group' => 'seo', 'action' => 'get', 'name' => 'Lire SEO', 'plan_required' => 'pro'],
            ['group' => 'seo', 'action' => 'update', 'name' => 'Modifier SEO', 'plan_required' => 'pro'],
            
            ['group' => 'acf', 'action' => 'get', 'name' => 'Lire champs ACF', 'plan_required' => 'pro'],
            ['group' => 'acf', 'action' => 'update', 'name' => 'Modifier champs ACF', 'plan_required' => 'pro'],
            
            ['group' => 'bulk', 'action' => 'update', 'name' => 'Mise à jour en masse', 'plan_required' => 'pro'],
            ['group' => 'bulk', 'action' => 'delete', 'name' => 'Suppression en masse', 'plan_required' => 'pro'],
        ];
        
        $available = [];
        $locked = [];
        
        foreach ($all_features as $feature) {
            $has_access = $license->can_use_feature($feature['group'], $feature['action']);
            $feature['has_access'] = $has_access;
            
            if ($has_access) {
                $available[] = $feature;
            } else {
                $locked[] = $feature;
            }
        }
        
        $response = [
            'current_plan' => strtoupper($plan),
            'is_pro' => $license->is_pro(),
            'available_features' => $available,
            'available_count' => count($available),
            'locked_features' => $locked,
            'locked_count' => count($locked),
        ];
        
        // Si des fonctionnalités sont verrouillées, proposer l'upgrade
        if (count($locked) > 0) {
            $response['upgrade'] = [
                'message' => "💎 " . count($locked) . " fonctionnalités sont verrouillées. Passez à PRO pour tout débloquer !",
                'buy_link' => 'https://centerhome.net/ia-pilote-pro?utm_source=plugin&utm_medium=feature_list',
                'pricing' => '49€/an pour 1 site',
            ];
        }
        
        return $response;
    },
    'permission_callback' => '__return_true',
    'meta' => adjm_mcp_meta(true),
]);
