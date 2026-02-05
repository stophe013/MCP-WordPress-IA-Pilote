<?php
/**
 * Abilities Extensions - WooCommerce, SEO, ACF, Forms
 * 
 * @package IA_Pilote_MCP
 */

if (!defined('ABSPATH')) exit;

// ========================================
// WOOCOMMERCE
// ========================================

if (adjm_is_group_enabled('woocommerce') && class_exists('WooCommerce')) {
    
    adjm_register_ability('adjm/woo-list-products', [
        'label' => __('Lister les produits', 'adjm-mcp'),
        'description' => __('Liste les produits WooCommerce', 'adjm-mcp'),
        'category' => 'woocommerce',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'per_page' => ['type' => 'integer', 'default' => 20],
                'page' => ['type' => 'integer', 'default' => 1],
                'status' => ['type' => 'string', 'enum' => ['publish', 'draft', 'any'], 'default' => 'publish'],
                'category' => ['type' => 'string'],
                'featured' => ['type' => 'boolean'],
                'on_sale' => ['type' => 'boolean'],
            ],
        ],
        'execute_callback' => function($input) {
            $args = [
                'post_type' => 'product',
                'post_status' => $input['status'] ?? 'publish',
                'posts_per_page' => min(absint($input['per_page'] ?? 20), 100),
                'paged' => absint($input['page'] ?? 1),
            ];
            
            if (!empty($input['category'])) {
                $args['tax_query'] = [[
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => sanitize_title($input['category']),
                ]];
            }
            
            $query = new WP_Query($args);
            
            return [
                'products' => array_map(function($post) {
                    $product = wc_get_product($post->ID);
                    return [
                        'id' => $post->ID,
                        'name' => $product->get_name(),
                        'slug' => $product->get_slug(),
                        'type' => $product->get_type(),
                        'status' => $product->get_status(),
                        'price' => $product->get_price(),
                        'regular_price' => $product->get_regular_price(),
                        'sale_price' => $product->get_sale_price(),
                        'sku' => $product->get_sku(),
                        'stock_status' => $product->get_stock_status(),
                        'stock_quantity' => $product->get_stock_quantity(),
                        'url' => $product->get_permalink(),
                        'image' => wp_get_attachment_url($product->get_image_id()),
                    ];
                }, $query->posts),
                'total' => $query->found_posts,
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_products'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/woo-list-orders', [
        'label' => __('Lister les commandes', 'adjm-mcp'),
        'description' => __('Liste les commandes WooCommerce', 'adjm-mcp'),
        'category' => 'woocommerce',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'per_page' => ['type' => 'integer', 'default' => 20],
                'page' => ['type' => 'integer', 'default' => 1],
                'status' => ['type' => 'string', 'enum' => ['pending', 'processing', 'completed', 'cancelled', 'any'], 'default' => 'any'],
            ],
        ],
        'execute_callback' => function($input) {
            $args = [
                'limit' => min(absint($input['per_page'] ?? 20), 100),
                'page' => absint($input['page'] ?? 1),
                'status' => $input['status'] ?? 'any',
            ];
            
            $orders = wc_get_orders($args);
            
            return [
                'orders' => array_map(function($order) {
                    return [
                        'id' => $order->get_id(),
                        'number' => $order->get_order_number(),
                        'status' => $order->get_status(),
                        'total' => $order->get_total(),
                        'currency' => $order->get_currency(),
                        'customer_id' => $order->get_customer_id(),
                        'billing_email' => $order->get_billing_email(),
                        'billing_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                        'items_count' => $order->get_item_count(),
                        'date_created' => $order->get_date_created()->format('Y-m-d H:i:s'),
                        'payment_method' => $order->get_payment_method_title(),
                    ];
                }, $orders),
                'total' => count($orders),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_shop_orders'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/woo-stats', [
        'label' => __('Statistiques WooCommerce', 'adjm-mcp'),
        'description' => __('Récupère les statistiques de ventes', 'adjm-mcp'),
        'category' => 'woocommerce',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'period' => ['type' => 'string', 'enum' => ['today', 'week', 'month', 'year'], 'default' => 'month'],
            ],
        ],
        'execute_callback' => function($input) {
            $period = $input['period'] ?? 'month';
            $date_from = '';
            
            switch ($period) {
                case 'today': $date_from = date('Y-m-d'); break;
                case 'week': $date_from = date('Y-m-d', strtotime('-7 days')); break;
                case 'month': $date_from = date('Y-m-d', strtotime('-30 days')); break;
                case 'year': $date_from = date('Y-m-d', strtotime('-1 year')); break;
            }
            
            $orders = wc_get_orders([
                'status' => ['completed', 'processing'],
                'date_created' => '>=' . $date_from,
                'limit' => -1,
            ]);
            
            $total_sales = 0;
            $items_sold = 0;
            foreach ($orders as $order) {
                $total_sales += floatval($order->get_total());
                $items_sold += $order->get_item_count();
            }
            
            return [
                'period' => $period,
                'orders_count' => count($orders),
                'total_sales' => number_format($total_sales, 2),
                'currency' => get_woocommerce_currency(),
                'items_sold' => $items_sold,
                'average_order' => count($orders) > 0 ? number_format($total_sales / count($orders), 2) : 0,
            ];
        },
        'permission_callback' => function() { return current_user_can('view_woocommerce_reports'); },
        'meta' => adjm_mcp_meta(true),
    ]);
}

// ========================================
// SEO (Yoast / Rank Math)
// ========================================

if (adjm_is_group_enabled('seo')) {
    
    // Détecter le plugin SEO
    $seo_plugin = null;
    if (defined('WPSEO_VERSION')) $seo_plugin = 'yoast';
    elseif (class_exists('RankMath')) $seo_plugin = 'rankmath';

    if ($seo_plugin) {
        adjm_register_ability('adjm/seo-get-meta', [
            'label' => __('Récupérer les métadonnées SEO', 'adjm-mcp'),
            'description' => __('Récupère les métadonnées SEO d\'un post', 'adjm-mcp'),
            'category' => 'seo',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'post_id' => ['type' => 'integer'],
                ],
                'required' => ['post_id'],
            ],
            'execute_callback' => function($input) use ($seo_plugin) {
                $post_id = absint($input['post_id']);
                
                if ($seo_plugin === 'yoast') {
                    return [
                        'plugin' => 'Yoast SEO',
                        'title' => get_post_meta($post_id, '_yoast_wpseo_title', true),
                        'description' => get_post_meta($post_id, '_yoast_wpseo_metadesc', true),
                        'focus_keyword' => get_post_meta($post_id, '_yoast_wpseo_focuskw', true),
                        'canonical' => get_post_meta($post_id, '_yoast_wpseo_canonical', true),
                        'noindex' => get_post_meta($post_id, '_yoast_wpseo_meta-robots-noindex', true),
                    ];
                } else {
                    return [
                        'plugin' => 'Rank Math',
                        'title' => get_post_meta($post_id, 'rank_math_title', true),
                        'description' => get_post_meta($post_id, 'rank_math_description', true),
                        'focus_keyword' => get_post_meta($post_id, 'rank_math_focus_keyword', true),
                        'canonical' => get_post_meta($post_id, 'rank_math_canonical_url', true),
                        'robots' => get_post_meta($post_id, 'rank_math_robots', true),
                    ];
                }
            },
            'permission_callback' => function() { return current_user_can('edit_posts'); },
            'meta' => adjm_mcp_meta(true),
        ]);

        adjm_register_ability('adjm/seo-update-meta', [
            'label' => __('Modifier les métadonnées SEO', 'adjm-mcp'),
            'description' => __('Met à jour les métadonnées SEO', 'adjm-mcp'),
            'category' => 'seo',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'post_id' => ['type' => 'integer'],
                    'title' => ['type' => 'string'],
                    'description' => ['type' => 'string'],
                    'focus_keyword' => ['type' => 'string'],
                ],
                'required' => ['post_id'],
            ],
            'execute_callback' => function($input) use ($seo_plugin) {
                $post_id = absint($input['post_id']);
                
                if ($seo_plugin === 'yoast') {
                    if (isset($input['title'])) update_post_meta($post_id, '_yoast_wpseo_title', sanitize_text_field($input['title']));
                    if (isset($input['description'])) update_post_meta($post_id, '_yoast_wpseo_metadesc', sanitize_textarea_field($input['description']));
                    if (isset($input['focus_keyword'])) update_post_meta($post_id, '_yoast_wpseo_focuskw', sanitize_text_field($input['focus_keyword']));
                } else {
                    if (isset($input['title'])) update_post_meta($post_id, 'rank_math_title', sanitize_text_field($input['title']));
                    if (isset($input['description'])) update_post_meta($post_id, 'rank_math_description', sanitize_textarea_field($input['description']));
                    if (isset($input['focus_keyword'])) update_post_meta($post_id, 'rank_math_focus_keyword', sanitize_text_field($input['focus_keyword']));
                }
                
                return ['success' => true, 'post_id' => $post_id];
            },
            'permission_callback' => function() { return current_user_can('edit_posts'); },
            'meta' => adjm_mcp_meta(false),
        ]);
    }
}

// ========================================
// ACF (Advanced Custom Fields)
// ========================================

if (adjm_is_group_enabled('acf') && class_exists('ACF')) {
    
    adjm_register_ability('adjm/acf-get-fields', [
        'label' => __('Récupérer les champs ACF', 'adjm-mcp'),
        'description' => __('Récupère tous les champs ACF d\'un post', 'adjm-mcp'),
        'category' => 'acf',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'post_id' => ['type' => 'integer'],
            ],
            'required' => ['post_id'],
        ],
        'execute_callback' => function($input) {
            $post_id = absint($input['post_id']);
            $fields = get_fields($post_id);
            
            return [
                'post_id' => $post_id,
                'fields' => $fields ?: [],
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_posts'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/acf-update-field', [
        'label' => __('Modifier un champ ACF', 'adjm-mcp'),
        'description' => __('Met à jour un champ ACF', 'adjm-mcp'),
        'category' => 'acf',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'post_id' => ['type' => 'integer'],
                'field_name' => ['type' => 'string'],
                'value' => ['description' => 'Nouvelle valeur'],
            ],
            'required' => ['post_id', 'field_name', 'value'],
        ],
        'execute_callback' => function($input) {
            $result = update_field(
                sanitize_key($input['field_name']),
                $input['value'],
                absint($input['post_id'])
            );
            
            return ['success' => (bool)$result, 'post_id' => absint($input['post_id'])];
        },
        'permission_callback' => function() { return current_user_can('edit_posts'); },
        'meta' => adjm_mcp_meta(false),
    ]);
}

// ========================================
// FORMULAIRES (CF7 / Gravity Forms)
// ========================================

if (adjm_is_group_enabled('forms')) {
    
    // Contact Form 7
    if (class_exists('WPCF7')) {
        adjm_register_ability('adjm/cf7-list-forms', [
            'label' => __('Lister les formulaires CF7', 'adjm-mcp'),
            'description' => __('Liste tous les formulaires Contact Form 7', 'adjm-mcp'),
            'category' => 'forms',
            'input_schema' => ['type' => 'object', 'properties' => []],
            'execute_callback' => function($input) {
                $forms = WPCF7_ContactForm::find();
                return [
                    'forms' => array_map(function($form) {
                        return [
                            'id' => $form->id(),
                            'title' => $form->title(),
                            'shortcode' => $form->shortcode(),
                        ];
                    }, $forms),
                    'total' => count($forms),
                ];
            },
            'permission_callback' => function() { return current_user_can('wpcf7_edit_contact_forms'); },
            'meta' => adjm_mcp_meta(true),
        ]);
    }
    
    // Gravity Forms
    if (class_exists('GFAPI')) {
        adjm_register_ability('adjm/gf-list-forms', [
            'label' => __('Lister les formulaires Gravity', 'adjm-mcp'),
            'description' => __('Liste tous les formulaires Gravity Forms', 'adjm-mcp'),
            'category' => 'forms',
            'input_schema' => ['type' => 'object', 'properties' => []],
            'execute_callback' => function($input) {
                $forms = GFAPI::get_forms();
                return [
                    'forms' => array_map(function($form) {
                        return [
                            'id' => $form['id'],
                            'title' => $form['title'],
                            'entries_count' => GFAPI::count_entries($form['id']),
                            'is_active' => $form['is_active'],
                        ];
                    }, $forms),
                    'total' => count($forms),
                ];
            },
            'permission_callback' => function() { return current_user_can('gravityforms_edit_forms'); },
            'meta' => adjm_mcp_meta(true),
        ]);
    }
}
