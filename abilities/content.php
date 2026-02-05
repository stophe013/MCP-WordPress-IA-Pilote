<?php
/**
 * Abilities Contenu - Pages, Posts, Catégories, Médias
 * 
 * @package IA_Pilote_MCP
 */

if (!defined('ABSPATH')) exit;

// ========================================
// PAGES
// ========================================

if (adjm_is_group_enabled('pages')) {
    
    // Lister les pages
    adjm_register_ability('adjm/list-pages', [
        'label' => __('Lister les pages', 'adjm-mcp'),
        'description' => __('Récupère la liste des pages WordPress', 'adjm-mcp'),
        'category' => 'content',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'per_page' => ['type' => 'integer', 'default' => 20, 'minimum' => 1, 'maximum' => 100],
                'page' => ['type' => 'integer', 'default' => 1],
                'status' => ['type' => 'string', 'enum' => ['publish', 'draft', 'pending', 'private', 'any'], 'default' => 'publish'],
                'orderby' => ['type' => 'string', 'enum' => ['date', 'title', 'menu_order', 'ID'], 'default' => 'date'],
                'order' => ['type' => 'string', 'enum' => ['ASC', 'DESC'], 'default' => 'DESC'],
                'search' => ['type' => 'string'],
                'parent' => ['type' => 'integer'],
            ],
        ],
        'execute_callback' => function($input) {
            $args = [
                'post_type' => 'page',
                'post_status' => $input['status'] ?? 'publish',
                'posts_per_page' => min(absint($input['per_page'] ?? 20), 100),
                'paged' => absint($input['page'] ?? 1),
                'orderby' => $input['orderby'] ?? 'date',
                'order' => $input['order'] ?? 'DESC',
            ];
            
            if (!empty($input['search'])) $args['s'] = sanitize_text_field($input['search']);
            if (!empty($input['parent'])) $args['post_parent'] = absint($input['parent']);
            
            $query = new WP_Query($args);
            
            $pages = array_map(function($post) {
                return [
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'slug' => $post->post_name,
                    'status' => $post->post_status,
                    'url' => get_permalink($post->ID),
                    'template' => get_page_template_slug($post->ID) ?: 'default',
                    'parent' => $post->post_parent,
                    'menu_order' => $post->menu_order,
                    'date' => $post->post_date,
                    'modified' => $post->post_modified,
                ];
            }, $query->posts);
            
            return [
                'pages' => $pages,
                'total' => $query->found_posts,
                'pages_count' => $query->max_num_pages,
                'current_page' => absint($input['page'] ?? 1),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_pages'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    // Récupérer une page
    adjm_register_ability('adjm/get-page', [
        'label' => __('Récupérer une page', 'adjm-mcp'),
        'description' => __('Récupère les détails complets d\'une page par son ID', 'adjm-mcp'),
        'category' => 'content',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID de la page'],
            ],
            'required' => ['id'],
        ],
        'execute_callback' => function($input) {
            $post = get_post(absint($input['id']));
            
            if (!$post || $post->post_type !== 'page') {
                return new WP_Error('not_found', 'Page introuvable');
            }
            
            return [
                'id' => $post->ID,
                'title' => $post->post_title,
                'content' => $post->post_content,
                'excerpt' => $post->post_excerpt,
                'slug' => $post->post_name,
                'status' => $post->post_status,
                'url' => get_permalink($post->ID),
                'edit_url' => get_edit_post_link($post->ID, 'raw'),
                'template' => get_page_template_slug($post->ID) ?: 'default',
                'parent' => $post->post_parent,
                'menu_order' => $post->menu_order,
                'author' => get_the_author_meta('display_name', $post->post_author),
                'date' => $post->post_date,
                'modified' => $post->post_modified,
                'featured_image' => get_the_post_thumbnail_url($post->ID, 'full'),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_pages'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    // Créer une page
    adjm_register_ability('adjm/create-page', [
        'label' => __('Créer une page', 'adjm-mcp'),
        'description' => __('Crée une nouvelle page WordPress', 'adjm-mcp'),
        'category' => 'content',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'title' => ['type' => 'string', 'description' => 'Titre de la page'],
                'content' => ['type' => 'string', 'description' => 'Contenu HTML'],
                'status' => ['type' => 'string', 'enum' => ['draft', 'publish', 'pending', 'private'], 'default' => 'draft'],
                'template' => ['type' => 'string', 'description' => 'Slug du template'],
                'parent' => ['type' => 'integer', 'description' => 'ID de la page parente'],
                'menu_order' => ['type' => 'integer'],
            ],
            'required' => ['title'],
        ],
        'execute_callback' => function($input) {
            $post_data = [
                'post_type' => 'page',
                'post_title' => sanitize_text_field($input['title']),
                'post_content' => wp_kses_post($input['content'] ?? ''),
                'post_status' => sanitize_key($input['status'] ?? 'draft'),
            ];
            
            if (!empty($input['parent'])) $post_data['post_parent'] = absint($input['parent']);
            if (!empty($input['menu_order'])) $post_data['menu_order'] = absint($input['menu_order']);
            
            $page_id = wp_insert_post($post_data, true);
            
            if (is_wp_error($page_id)) return $page_id;
            
            if (!empty($input['template'])) {
                update_post_meta($page_id, '_wp_page_template', sanitize_file_name($input['template']));
            }
            
            return [
                'success' => true,
                'id' => $page_id,
                'url' => get_permalink($page_id),
                'edit_url' => get_edit_post_link($page_id, 'raw'),
            ];
        },
        'permission_callback' => function() { return current_user_can('publish_pages'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    // Modifier une page
    adjm_register_ability('adjm/update-page', [
        'label' => __('Modifier une page', 'adjm-mcp'),
        'description' => __('Met à jour une page existante', 'adjm-mcp'),
        'category' => 'content',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID de la page'],
                'title' => ['type' => 'string'],
                'content' => ['type' => 'string'],
                'status' => ['type' => 'string', 'enum' => ['draft', 'publish', 'pending', 'private']],
                'template' => ['type' => 'string'],
                'parent' => ['type' => 'integer'],
            ],
            'required' => ['id'],
        ],
        'execute_callback' => function($input) {
            $id = absint($input['id']);
            $post = get_post($id);
            
            if (!$post || $post->post_type !== 'page') {
                return new WP_Error('not_found', 'Page introuvable');
            }
            
            $post_data = ['ID' => $id];
            if (isset($input['title'])) $post_data['post_title'] = sanitize_text_field($input['title']);
            if (isset($input['content'])) $post_data['post_content'] = wp_kses_post($input['content']);
            if (isset($input['status'])) $post_data['post_status'] = sanitize_key($input['status']);
            if (isset($input['parent'])) $post_data['post_parent'] = absint($input['parent']);
            
            $result = wp_update_post($post_data, true);
            if (is_wp_error($result)) return $result;
            
            if (isset($input['template'])) {
                update_post_meta($id, '_wp_page_template', sanitize_file_name($input['template']));
            }
            
            return [
                'success' => true,
                'id' => $id,
                'url' => get_permalink($id),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_pages'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    // Supprimer une page
    adjm_register_ability('adjm/delete-page', [
        'label' => __('Supprimer une page', 'adjm-mcp'),
        'description' => __('Supprime une page (corbeille ou définitif)', 'adjm-mcp'),
        'category' => 'content',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID de la page'],
                'force' => ['type' => 'boolean', 'default' => false, 'description' => 'Supprimer définitivement'],
            ],
            'required' => ['id'],
        ],
        'execute_callback' => function($input) {
            $id = absint($input['id']);
            $force = (bool)($input['force'] ?? false);
            
            $post = get_post($id);
            if (!$post || $post->post_type !== 'page') {
                return new WP_Error('not_found', 'Page introuvable');
            }
            
            $result = wp_delete_post($id, $force);
            if (!$result) {
                return new WP_Error('delete_failed', 'Échec de la suppression');
            }
            
            return [
                'success' => true,
                'id' => $id,
                'message' => $force ? 'Page supprimée définitivement' : 'Page mise à la corbeille',
            ];
        },
        'permission_callback' => function() { return current_user_can('delete_pages'); },
        'meta' => [
            'annotations' => ['readonly' => false, 'destructive' => true, 'confirmation' => true],
            'mcp' => ['public' => true, 'type' => 'tool'],
        ],
    ]);
}

// ========================================
// POSTS (Articles)
// ========================================

if (adjm_is_group_enabled('posts')) {
    
    // Lister les articles
    adjm_register_ability('adjm/list-posts', [
        'label' => __('Lister les articles', 'adjm-mcp'),
        'description' => __('Récupère la liste des articles', 'adjm-mcp'),
        'category' => 'content',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'per_page' => ['type' => 'integer', 'default' => 20],
                'page' => ['type' => 'integer', 'default' => 1],
                'status' => ['type' => 'string', 'enum' => ['publish', 'draft', 'pending', 'any'], 'default' => 'publish'],
                'category' => ['type' => 'string', 'description' => 'Slug de catégorie'],
                'tag' => ['type' => 'string', 'description' => 'Slug de tag'],
                'search' => ['type' => 'string'],
                'author' => ['type' => 'integer'],
                'orderby' => ['type' => 'string', 'enum' => ['date', 'title', 'modified', 'rand'], 'default' => 'date'],
                'order' => ['type' => 'string', 'enum' => ['ASC', 'DESC'], 'default' => 'DESC'],
            ],
        ],
        'execute_callback' => function($input) {
            $args = [
                'post_type' => 'post',
                'post_status' => $input['status'] ?? 'publish',
                'posts_per_page' => min(absint($input['per_page'] ?? 20), 100),
                'paged' => absint($input['page'] ?? 1),
                'orderby' => $input['orderby'] ?? 'date',
                'order' => $input['order'] ?? 'DESC',
            ];
            
            if (!empty($input['category'])) $args['category_name'] = sanitize_title($input['category']);
            if (!empty($input['tag'])) $args['tag'] = sanitize_title($input['tag']);
            if (!empty($input['search'])) $args['s'] = sanitize_text_field($input['search']);
            if (!empty($input['author'])) $args['author'] = absint($input['author']);
            
            $query = new WP_Query($args);
            
            $posts = array_map(function($post) {
                return [
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'excerpt' => get_the_excerpt($post),
                    'slug' => $post->post_name,
                    'status' => $post->post_status,
                    'url' => get_permalink($post->ID),
                    'categories' => wp_get_post_categories($post->ID, ['fields' => 'names']),
                    'tags' => wp_get_post_tags($post->ID, ['fields' => 'names']),
                    'author' => get_the_author_meta('display_name', $post->post_author),
                    'date' => $post->post_date,
                    'featured_image' => get_the_post_thumbnail_url($post->ID, 'medium'),
                ];
            }, $query->posts);
            
            return [
                'posts' => $posts,
                'total' => $query->found_posts,
                'pages' => $query->max_num_pages,
                'current_page' => absint($input['page'] ?? 1),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_posts'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    // Récupérer un article
    adjm_register_ability('adjm/get-post', [
        'label' => __('Récupérer un article', 'adjm-mcp'),
        'description' => __('Récupère les détails d\'un article', 'adjm-mcp'),
        'category' => 'content',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer'],
            ],
            'required' => ['id'],
        ],
        'execute_callback' => function($input) {
            $post = get_post(absint($input['id']));
            
            if (!$post || $post->post_type !== 'post') {
                return new WP_Error('not_found', 'Article introuvable');
            }
            
            return [
                'id' => $post->ID,
                'title' => $post->post_title,
                'content' => $post->post_content,
                'excerpt' => $post->post_excerpt,
                'slug' => $post->post_name,
                'status' => $post->post_status,
                'url' => get_permalink($post->ID),
                'edit_url' => get_edit_post_link($post->ID, 'raw'),
                'categories' => wp_get_post_categories($post->ID, ['fields' => 'names']),
                'tags' => wp_get_post_tags($post->ID, ['fields' => 'names']),
                'author' => get_the_author_meta('display_name', $post->post_author),
                'date' => $post->post_date,
                'modified' => $post->post_modified,
                'featured_image' => get_the_post_thumbnail_url($post->ID, 'full'),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_posts'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    // Créer un article
    adjm_register_ability('adjm/create-post', [
        'label' => __('Créer un article', 'adjm-mcp'),
        'description' => __('Crée un nouvel article', 'adjm-mcp'),
        'category' => 'content',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'title' => ['type' => 'string'],
                'content' => ['type' => 'string'],
                'excerpt' => ['type' => 'string'],
                'status' => ['type' => 'string', 'enum' => ['draft', 'publish', 'pending'], 'default' => 'draft'],
                'category' => ['type' => 'string', 'description' => 'Slug de catégorie'],
                'tags' => ['type' => 'array', 'items' => ['type' => 'string']],
            ],
            'required' => ['title'],
        ],
        'execute_callback' => function($input) {
            $post_data = [
                'post_type' => 'post',
                'post_title' => sanitize_text_field($input['title']),
                'post_content' => wp_kses_post($input['content'] ?? ''),
                'post_excerpt' => sanitize_textarea_field($input['excerpt'] ?? ''),
                'post_status' => sanitize_key($input['status'] ?? 'draft'),
            ];
            
            $post_id = wp_insert_post($post_data, true);
            if (is_wp_error($post_id)) return $post_id;
            
            // Catégorie
            if (!empty($input['category'])) {
                $term = get_term_by('slug', sanitize_title($input['category']), 'category');
                if ($term) wp_set_post_categories($post_id, [$term->term_id]);
            }
            
            // Tags
            if (!empty($input['tags'])) {
                wp_set_post_tags($post_id, array_map('sanitize_text_field', $input['tags']));
            }
            
            return [
                'success' => true,
                'id' => $post_id,
                'url' => get_permalink($post_id),
                'edit_url' => get_edit_post_link($post_id, 'raw'),
            ];
        },
        'permission_callback' => function() { return current_user_can('publish_posts'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    // Modifier un article
    adjm_register_ability('adjm/update-post', [
        'label' => __('Modifier un article', 'adjm-mcp'),
        'description' => __('Met à jour un article existant', 'adjm-mcp'),
        'category' => 'content',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer'],
                'title' => ['type' => 'string'],
                'content' => ['type' => 'string'],
                'excerpt' => ['type' => 'string'],
                'status' => ['type' => 'string', 'enum' => ['draft', 'publish', 'pending', 'private']],
                'category' => ['type' => 'string'],
                'tags' => ['type' => 'array', 'items' => ['type' => 'string']],
            ],
            'required' => ['id'],
        ],
        'execute_callback' => function($input) {
            $id = absint($input['id']);
            $post = get_post($id);
            
            if (!$post || $post->post_type !== 'post') {
                return new WP_Error('not_found', 'Article introuvable');
            }
            
            $post_data = ['ID' => $id];
            if (isset($input['title'])) $post_data['post_title'] = sanitize_text_field($input['title']);
            if (isset($input['content'])) $post_data['post_content'] = wp_kses_post($input['content']);
            if (isset($input['excerpt'])) $post_data['post_excerpt'] = sanitize_textarea_field($input['excerpt']);
            if (isset($input['status'])) $post_data['post_status'] = sanitize_key($input['status']);
            
            $result = wp_update_post($post_data, true);
            if (is_wp_error($result)) return $result;
            
            if (!empty($input['category'])) {
                $term = get_term_by('slug', sanitize_title($input['category']), 'category');
                if ($term) wp_set_post_categories($id, [$term->term_id]);
            }
            
            if (isset($input['tags'])) {
                wp_set_post_tags($id, array_map('sanitize_text_field', $input['tags']));
            }
            
            return ['success' => true, 'id' => $id, 'url' => get_permalink($id)];
        },
        'permission_callback' => function() { return current_user_can('edit_posts'); },
        'meta' => adjm_mcp_meta(false),
    ]);

    // Supprimer un article
    adjm_register_ability('adjm/delete-post', [
        'label' => __('Supprimer un article', 'adjm-mcp'),
        'description' => __('Supprime un article', 'adjm-mcp'),
        'category' => 'content',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer'],
                'force' => ['type' => 'boolean', 'default' => false],
            ],
            'required' => ['id'],
        ],
        'execute_callback' => function($input) {
            $id = absint($input['id']);
            $force = (bool)($input['force'] ?? false);
            
            $post = get_post($id);
            if (!$post || $post->post_type !== 'post') {
                return new WP_Error('not_found', 'Article introuvable');
            }
            
            $result = wp_delete_post($id, $force);
            if (!$result) return new WP_Error('delete_failed', 'Échec');
            
            return ['success' => true, 'id' => $id, 'message' => $force ? 'Supprimé définitivement' : 'Mis à la corbeille'];
        },
        'permission_callback' => function() { return current_user_can('delete_posts'); },
        'meta' => [
            'annotations' => ['readonly' => false, 'destructive' => true, 'confirmation' => true],
            'mcp' => ['public' => true, 'type' => 'tool'],
        ],
    ]);
}

// ========================================
// CATÉGORIES
// ========================================

if (adjm_is_group_enabled('categories')) {
    
    adjm_register_ability('adjm/list-categories', [
        'label' => __('Lister les catégories', 'adjm-mcp'),
        'description' => __('Récupère la liste des catégories', 'adjm-mcp'),
        'category' => 'content',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'hide_empty' => ['type' => 'boolean', 'default' => false],
                'parent' => ['type' => 'integer'],
            ],
        ],
        'execute_callback' => function($input) {
            $args = [
                'taxonomy' => 'category',
                'hide_empty' => (bool)($input['hide_empty'] ?? false),
            ];
            if (isset($input['parent'])) $args['parent'] = absint($input['parent']);
            
            $terms = get_terms($args);
            if (is_wp_error($terms)) return $terms;
            
            return [
                'categories' => array_map(function($term) {
                    return [
                        'id' => $term->term_id,
                        'name' => $term->name,
                        'slug' => $term->slug,
                        'description' => $term->description,
                        'parent' => $term->parent,
                        'count' => $term->count,
                        'url' => get_term_link($term),
                    ];
                }, $terms),
                'total' => count($terms),
            ];
        },
        'permission_callback' => function() { return current_user_can('edit_posts'); },
        'meta' => adjm_mcp_meta(true),
    ]);
}

// ========================================
// MÉDIAS
// ========================================

if (adjm_is_group_enabled('media')) {
    
    adjm_register_ability('adjm/list-media', [
        'label' => __('Lister les médias', 'adjm-mcp'),
        'description' => __('Récupère la liste des médias', 'adjm-mcp'),
        'category' => 'content',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'per_page' => ['type' => 'integer', 'default' => 20],
                'page' => ['type' => 'integer', 'default' => 1],
                'mime_type' => ['type' => 'string', 'description' => 'Ex: image, image/jpeg, video'],
                'search' => ['type' => 'string'],
            ],
        ],
        'execute_callback' => function($input) {
            $args = [
                'post_type' => 'attachment',
                'post_status' => 'inherit',
                'posts_per_page' => min(absint($input['per_page'] ?? 20), 100),
                'paged' => absint($input['page'] ?? 1),
            ];
            
            if (!empty($input['mime_type'])) $args['post_mime_type'] = sanitize_text_field($input['mime_type']);
            if (!empty($input['search'])) $args['s'] = sanitize_text_field($input['search']);
            
            $query = new WP_Query($args);
            
            return [
                'media' => array_map(function($post) {
                    return [
                        'id' => $post->ID,
                        'title' => $post->post_title,
                        'url' => wp_get_attachment_url($post->ID),
                        'mime_type' => $post->post_mime_type,
                        'alt' => get_post_meta($post->ID, '_wp_attachment_image_alt', true),
                        'caption' => $post->post_excerpt,
                        'date' => $post->post_date,
                        'sizes' => wp_get_attachment_image_sizes($post->ID),
                    ];
                }, $query->posts),
                'total' => $query->found_posts,
            ];
        },
        'permission_callback' => function() { return current_user_can('upload_files'); },
        'meta' => adjm_mcp_meta(true),
    ]);

    adjm_register_ability('adjm/upload-media', [
        'label' => __('Uploader un média', 'adjm-mcp'),
        'description' => __('Upload un média depuis une URL', 'adjm-mcp'),
        'category' => 'content',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'url' => ['type' => 'string', 'description' => 'URL du fichier à télécharger'],
                'title' => ['type' => 'string'],
                'alt' => ['type' => 'string'],
                'caption' => ['type' => 'string'],
            ],
            'required' => ['url'],
        ],
        'execute_callback' => function($input) {
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
            
            $url = esc_url_raw($input['url']);
            
            $tmp = download_url($url);
            if (is_wp_error($tmp)) return $tmp;
            
            $file_array = [
                'name' => basename(parse_url($url, PHP_URL_PATH)),
                'tmp_name' => $tmp,
            ];
            
            $attachment_id = media_handle_sideload($file_array, 0, $input['title'] ?? null);
            
            @unlink($tmp);
            
            if (is_wp_error($attachment_id)) return $attachment_id;
            
            if (!empty($input['alt'])) {
                update_post_meta($attachment_id, '_wp_attachment_image_alt', sanitize_text_field($input['alt']));
            }
            if (!empty($input['caption'])) {
                wp_update_post(['ID' => $attachment_id, 'post_excerpt' => sanitize_textarea_field($input['caption'])]);
            }
            
            return [
                'success' => true,
                'id' => $attachment_id,
                'url' => wp_get_attachment_url($attachment_id),
            ];
        },
        'permission_callback' => function() { return current_user_can('upload_files'); },
        'meta' => adjm_mcp_meta(false),
    ]);
}
