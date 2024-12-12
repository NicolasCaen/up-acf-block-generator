<?php
namespace UP\AcfBlockGenerator\Admin;

use UP\AcfBlockGenerator\Generator\BlockGenerator;

class AdminPage {
    private $page_slug = 'up-block-generator';
    private $block_generator;

    public function __construct() {
        $this->block_generator = new BlockGenerator();
        add_action('admin_post_create_up_block', [$this, 'handleBlockCreation']);
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_notices', [$this, 'displayAdminNotices']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function addAdminMenu(): void {
        add_menu_page(
            __('UP Block Generator', 'up-blocks'),
            __('UP Blocks', 'up-blocks'),
            'manage_options',
            $this->page_slug,
            [$this, 'renderAdminPage'],
            'dashicons-block-default',
            30
        );
    }

    public function renderAdminPage(): void {
        ?>
        <div class="wrap">
            <h1><?php _e('UP Block Generator', 'up-blocks'); ?></h1>
            
            <div class="card">
                <h2><?php _e('Créer un nouveau bloc', 'up-blocks'); ?></h2>
                
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="up-block-form">
                    <?php wp_nonce_field('create_up_block', 'up_block_nonce'); ?>
                    <input type="hidden" name="action" value="create_up_block">
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="block_name"><?php _e('Nom du bloc', 'up-blocks'); ?></label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="block_name" 
                                       name="block_name" 
                                       class="regular-text" 
                                       required 
                                       pattern="[a-z0-9-]+" 
                                       title="<?php _e('Lettres minuscules, chiffres et tirets uniquement', 'up-blocks'); ?>">
                                <p class="description">
                                    <?php _e('Exemple: mon-super-bloc (lettres minuscules, chiffres et tirets uniquement)', 'up-blocks'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="block_title"><?php _e('Titre du bloc', 'up-blocks'); ?></label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="block_title" 
                                       name="block_title" 
                                       class="regular-text" 
                                       required>
                                <p class="description">
                                    <?php _e('Le titre qui apparaîtra dans l\'éditeur', 'up-blocks'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="block_location"><?php _e('Emplacement', 'up-blocks'); ?></label>
                            </th>
                            <td>
                                <select id="block_location" name="block_location" required>
                                    <option value="theme"><?php _e('Theme', 'up-blocks'); ?></option>
                                    <option value="mu-plugins"><?php _e('MU-Plugins', 'up-blocks'); ?></option>
                                </select>
                                
                                <div class="location-info">
                                    <div class="location-info-theme" style="margin-top: 10px;">
                                        <strong><?php _e('Theme:', 'up-blocks'); ?></strong>
                                        <ul>
                                            <li><?php _e('✓ Idéal pour les blocs spécifiques au thème', 'up-blocks'); ?></li>
                                            <li><?php _e('✓ Facilement modifiable via le thème', 'up-blocks'); ?></li>
                                            <li><?php _e('⚠ Ne sera plus disponible si vous changez de thème', 'up-blocks'); ?></li>
                                        </ul>
                                    </div>
                                    
                                    <div class="location-info-mu" style="margin-top: 10px;">
                                        <strong><?php _e('MU-Plugins:', 'up-blocks'); ?></strong>
                                        <ul>
                                            <li><?php _e('✓ Disponible quel que soit le thème actif', 'up-blocks'); ?></li>
                                            <li><?php _e('✓ Idéal pour les blocs réutilisables', 'up-blocks'); ?></li>
                                            <li><?php _e('⚠ Nécessite un accès au serveur pour les modifications', 'up-blocks'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="block_icon"><?php _e('Icône du bloc', 'up-blocks'); ?></label>
                            </th>
                            <td>
                                <div class="icon-selector">
                                    <input type="text" 
                                           id="block_icon" 
                                           name="block_icon" 
                                           class="regular-text" 
                                           value="admin-comments"
                                           readonly>
                                    <button type="button" class="button toggle-icons"><?php _e('Choisir une icône', 'up-blocks'); ?></button>
                                    
                                    <div class="dashicons-picker" style="display:none;">
                                        <div class="dashicons-picker-container">
                                            <?php
                                            $dashicons = [
                                                'admin-appearance', 'admin-collapse', 'admin-comments', 'admin-customizer',
                                                'admin-generic', 'admin-home', 'admin-links', 'admin-media', 'admin-network',
                                                'admin-page', 'admin-plugins', 'admin-post', 'admin-settings', 'admin-site',
                                                'admin-tools', 'admin-users', 'album', 'align-center', 'align-left',
                                                'align-none', 'align-right', 'analytics', 'archive', 'arrow-down',
                                                'arrow-left', 'arrow-right', 'arrow-up', 'art', 'awards',
                                                'backup', 'block-default', 'button', 'calendar', 'calendar-alt',
                                                'camera', 'category', 'chart-area', 'chart-bar', 'chart-line',
                                                'chart-pie', 'clipboard', 'clock', 'cloud', 'columns',
                                                'controls-back', 'controls-forward', 'controls-pause', 'controls-play',
                                                'controls-repeat', 'controls-skipback', 'controls-skipforward',
                                                'controls-volumeoff', 'controls-volumeon', 'dashboard', 'desktop',
                                                'dismiss', 'download', 'edit', 'editor-aligncenter', 'editor-alignleft',
                                                'editor-alignright', 'editor-bold', 'editor-break', 'editor-code',
                                                'editor-contract', 'editor-customchar', 'editor-expand', 'editor-help',
                                                'editor-indent', 'editor-insertmore', 'editor-italic', 'editor-justify',
                                                'editor-kitchensink', 'editor-ltr', 'editor-ol', 'editor-outdent',
                                                'editor-paragraph', 'editor-paste-text', 'editor-paste-word',
                                                'editor-quote', 'editor-removeformatting', 'editor-rtl', 'editor-spellcheck',
                                                'editor-strikethrough', 'editor-table', 'editor-textcolor', 'editor-ul',
                                                'editor-underline', 'editor-unlink', 'editor-video', 'email',
                                                'email-alt', 'email-alt2', 'excerpt-view', 'external', 'facebook',
                                                'facebook-alt', 'feedback', 'filter', 'flag', 'format-aside',
                                                'format-audio', 'format-chat', 'format-gallery', 'format-image',
                                                'format-quote', 'format-status', 'format-video', 'forms', 'googleplus',
                                                'grid-view', 'groups', 'hammer', 'heart', 'hidden',
                                                'id-alt', 'id', 'image-crop', 'image-filter', 'image-flip-horizontal',
                                                'image-flip-vertical', 'image-rotate-left', 'image-rotate-right',
                                                'image-rotate', 'images-alt', 'images-alt2', 'index-card', 'info',
                                                'instagram', 'layout', 'leftright', 'lightbulb', 'list-view',
                                                'location-alt', 'location', 'lock', 'marker', 'media-archive',
                                                'media-audio', 'media-code', 'media-default', 'media-document',
                                                'media-interactive', 'media-spreadsheet', 'media-text', 'media-video',
                                                'megaphone', 'menu', 'microphone', 'migrate', 'minus', 'money',
                                                'move', 'nametag', 'networking', 'no-alt', 'no', 'palmtree',
                                                'performance', 'phone', 'playlist-audio', 'playlist-video', 'plus-alt',
                                                'plus', 'portfolio', 'post-status', 'post-trash', 'pressthis',
                                                'products', 'randomize', 'redo', 'rss', 'schedule', 'screenoptions',
                                                'search', 'share', 'share1', 'shield', 'shield-alt', 'slides',
                                                'smartphone', 'smiley', 'sort', 'sos', 'star-empty', 'star-filled',
                                                'star-half', 'sticky', 'store', 'tablet', 'tag', 'tagcloud',
                                                'testimonial', 'text', 'thumbs-down', 'thumbs-up', 'translation',
                                                'twitter', 'undo', 'universal-access', 'universal-access-alt',
                                                'unlock', 'update', 'upload', 'vault', 'video-alt', 'video-alt2',
                                                'video-alt3', 'visibility', 'warning', 'welcome-add-page',
                                                'welcome-comments', 'welcome-learn-more', 'welcome-view-site',
                                                'welcome-widgets-menus', 'welcome-write-blog', 'wordpress',
                                                'wordpress-alt', 'yes'
                                            ];
                                            
                                            foreach ($dashicons as $icon) {
                                                echo sprintf(
                                                    '<div class="dashicons-picker-item" data-icon="%s"><span class="dashicons dashicons-%s"></span></div>',
                                                    esc_attr($icon),
                                                    esc_attr($icon)
                                                );
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <p class="description">
                                    <?php _e('Sélectionnez une icône pour votre bloc', 'up-blocks'); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="block_keywords"><?php _e('Mots-clés', 'up-blocks'); ?></label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="block_keywords" 
                                       name="block_keywords" 
                                       class="regular-text" 
                                       value="up">
                                <p class="description">
                                    <?php _e('Mots-clés séparés par des virgules (max 3)', 'up-blocks'); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label><?php _e('Fonctionnalités supportées', 'up-blocks'); ?></label>
                            </th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="checkbox" name="block_supports[]" value="align" checked>
                                        <?php _e('Alignement', 'up-blocks'); ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="block_supports[]" value="anchor" checked>
                                        <?php _e('Ancre HTML', 'up-blocks'); ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="block_supports[]" value="color" checked>
                                        <?php _e('Couleurs', 'up-blocks'); ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="block_supports[]" value="spacing" checked>
                                        <?php _e('Espacement', 'up-blocks'); ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="block_supports[]" value="typography" checked>
                                        <?php _e('Typographie', 'up-blocks'); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="block_description"><?php _e('Description', 'up-blocks'); ?></label>
                            </th>
                            <td>
                                <textarea id="block_description" 
                                          name="block_description" 
                                          class="large-text" 
                                          rows="3"><?php _e('Description du bloc', 'up-blocks'); ?></textarea>
                            </td>
                        </tr>
                    </table>
                    
                    <?php submit_button(__('Créer le bloc', 'up-blocks')); ?>
                </form>
            </div>
        </div>
        <?php
    }

    public function handleBlockCreation(): void {
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'up-blocks'));
        }

        check_admin_referer('create_up_block', 'up_block_nonce');

        // Récupération et nettoyage des données du formulaire
        $block_data = [
            'name' => sanitize_title($_POST['block_name']),
            'title' => sanitize_text_field($_POST['block_title']),
            'location' => $_POST['block_location'],
            'description' => sanitize_textarea_field($_POST['block_description'] ?? ''),
            'icon' => sanitize_text_field($_POST['block_icon'] ?? 'admin-comments'),
            'keywords' => sanitize_text_field($_POST['block_keywords'] ?? ''),
            'supports' => isset($_POST['block_supports']) ? (array) $_POST['block_supports'] : []
        ];

        try {
            $success = $this->block_generator->generateBlock($block_data);

            if ($success) {
                $message = sprintf(
                    __('Le bloc "%s" a été créé avec succès dans %s.', 'up-blocks'),
                    $block_data['title'],
                    $block_data['location'] === 'theme' ? 'le thème' : 'mu-plugins'
                );
                $this->addAdminNotice('success', $message);
            } else {
                throw new \Exception(__('Erreur lors de la création du bloc.', 'up-blocks'));
            }
        } catch (\Exception $e) {
            $this->addAdminNotice('error', $e->getMessage());
        }

        wp_redirect(add_query_arg(['page' => $this->page_slug], admin_url('admin.php')));
        exit;
    }

    private function addAdminNotice(string $type, string $message): void {
        set_transient('up_block_notice', [
            'type' => $type,
            'message' => $message
        ], 45);
    }

    public function displayAdminNotices(): void {
        $notice = get_transient('up_block_notice');
        if ($notice) {
            $class = $notice['type'] === 'success' ? 'notice-success' : 'notice-error';
            ?>
            <div class="notice <?php echo esc_attr($class); ?> is-dismissible">
                <p><?php echo esc_html($notice['message']); ?></p>
            </div>
            <?php
            delete_transient('up_block_notice');
        }
    }

    public function enqueueAssets($hook): void {
        if (strpos($hook, $this->page_slug) === false) {
            return;
        }

        wp_enqueue_style('dashicons');
        wp_enqueue_style('up-block-generator-admin');
        wp_enqueue_script('up-block-generator-admin');
    }
}
