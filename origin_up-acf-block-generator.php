<?php

/**
 * Plugin Name: NG1 ACF BLOCK GENERATOR
 * Description: Générateur de blocs ACF
 * Version: 1.0.0
 * Author: NG1
 */


class Ng1BlockGenerator {
    private static $instance = null;
    private $page_slug = 'ng1-block-generator';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_create_ng1_block', array($this, 'handle_block_creation'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'NG1 Block Generator',
            'NG1 Blocks',
            'manage_options',
            $this->page_slug,
            array($this, 'render_admin_page'),
            'dashicons-block-default',
            30
        );
    }

    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1>NG1 Block Generator</h1>
            
            <div class="card">
                <h2>Créer un nouveau bloc</h2>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <?php wp_nonce_field('create_ng1_block', 'ng1_block_nonce'); ?>
                    <input type="hidden" name="action" value="create_ng1_block">
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="block_name">Nom du bloc</label>
                            </th>
                            <td>
                                <input type="text" id="block_name" name="block_name" class="regular-text" required 
                                       pattern="[a-z0-9-]+" title="Lettres minuscules, chiffres et tirets uniquement">
                                <p class="description">Exemple: mon-super-bloc (lettres minuscules, chiffres et tirets uniquement)</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="block_title">Titre du bloc</label>
                            </th>
                            <td>
                                <input type="text" id="block_title" name="block_title" class="regular-text" required>
                                <p class="description">Le titre qui apparaîtra dans l'éditeur</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="block_location">Emplacement</label>
                            </th>
                            <td>
                                <select id="block_location" name="block_location" required>
                                    <option value="theme">Theme</option>
                                    <option value="mu-plugins">MU-Plugins</option>
                                </select>
                                <p class="description">
                                    <strong>Theme:</strong> Le bloc sera lié au thème actif (recommandé pour les blocs spécifiques au thème)<br>
                                    <strong>MU-Plugins:</strong> Le bloc sera disponible quel que soit le thème (recommandé pour les blocs réutilisables)
                                </p>
                            </td>
                        </tr>
                    </table>
                    
                    <?php submit_button('Créer le bloc'); ?>
                </form>
            </div>
        </div>
        <?php
    }

    public function handle_block_creation() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        check_admin_referer('create_ng1_block', 'ng1_block_nonce');

        $block_name = sanitize_title($_POST['block_name']);
        $block_title = sanitize_text_field($_POST['block_title']);
        $location = $_POST['block_location'];

        // Définit le chemin de base selon l'emplacement choisi
        $base_path = $location === 'theme' 
            ? get_stylesheet_directory() . '/acf-blocks/' 
            : WPMU_PLUGIN_DIR . '/acf-blocks/';

        // Crée le dossier du bloc
        $block_path = $base_path . $block_name;
        wp_mkdir_p($block_path);
        wp_mkdir_p($block_path . '/acf');
        wp_mkdir_p($block_path . '/assets/js');
        wp_mkdir_p($block_path . '/assets/scss');

        // Crée les fichiers
        $this->create_block_json($block_path, $block_name, $block_title);
        $this->create_fields_json($block_path, $block_name, $block_title);
        $this->create_view_php($block_path);
        $this->create_function_js($block_path);
        $this->create_scss_files($block_path);

        // Redirige avec un message de succès
        wp_redirect(add_query_arg(
            array('page' => $this->page_slug, 'created' => '1'),
            admin_url('admin.php')
        ));
        exit;
    }

    private function create_block_json($path, $name, $title) {
        $json = [
            '$schema' => 'https://schemas.wp.org/trunk/block.json',
            'name' => "ng1/$name",
            'title' => $title,
            'description' => "Description du bloc",
            'category' => 'pixelea',
            'icon' => 'admin-comments',
            'apiVersion' => 3,
            'keywords' => ['ng1', $name],
            'acf' => [
                'mode' => 'preview',
                'renderTemplate' => 'view.php'
            ],
            'supports' => [
                'align' => true,
                'anchor' => true,
                'html' => false,
                'jsx' => true,
                'color' => [
                    'background' => true,
                    'text' => true,
                    'link' => true
                ],
                'spacing' => [
                    'margin' => true,
                    'padding' => true
                ],
                'typography' => [
                    'fontSize' => true,
                    'lineHeight' => true
                ]
            ],
            'style' => ['file:./style.css'],
            'script' => ['file:./assets/js/function.js']
        ];

        file_put_contents(
            "$path/block.json",
            json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    private function create_fields_json($path, $name, $title) {
        $json = [[
            'key' => 'group_' . wp_generate_uuid4(),
            'title' => $title,
            'fields' => [],
            'location' => [[
                [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => "ng1/$name"
                ]
            ]],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0
        ]];

        file_put_contents(
            "$path/acf/fields.json",
            json_encode($json, JSON_PRETTY_PRINT)
        );
    }

    private function create_view_php($path) {
        $template = <<<PHP
<?php
/**
 * Template du bloc
 *
 * @param array \$block Les attributs du bloc
 * @param string \$content Le contenu du bloc
 * @param bool \$is_preview True durant l'aperçu du bloc dans l'éditeur
 * @param int \$post_id L'ID du post étant édité
 */

// Récupère toutes les classes et attributs du bloc
\$wrapper_attributes = get_block_wrapper_attributes();
\$fields = get_fields() ?: []; // évite les erreurs si les champs ne sont pas définis
extract(\$fields);
?>

<div <?php echo \$wrapper_attributes; ?>>
    <?php if (!empty(\$title)) : ?>
        <h2><?php echo esc_html(\$title); ?></h2>
    <?php endif; ?>
</div>
PHP;

        file_put_contents("$path/view.php", $template);
    }

    private function create_function_js($path) {
        $js = "console.log('Block loaded');";
        file_put_contents("$path/assets/js/function.js", $js);
    }

    private function create_scss_files($path) {
        // Crée _mixins.scss
        $mixins = <<<SCSS
@function px(\$target-px) {
    @return \$target-px * 1px;
}

@function PxToRem(\$size) {
    \$remSize: \$size / 16;
    @return #{$remSize}rem;
}

@function variable(\$x) {
    @return unquote("\$" + \$x);
}

@mixin b(\$min,\$max){
    @media (min-width: px(\$min)) and (max-width: px(\$max)) {
        @content;
    }
}

@mixin m(\$min){
    @media (max-width: px(\$min)){
        @content;
    }
}

@mixin p(\$max){
    @media (min-width: px(\$max)){
        @content;
    }
}
SCSS;

        // Crée style.scss
        $style = <<<SCSS
@import "mixins";

.wp-block-ng1-{block_name} {
    // Styles du bloc
}
SCSS;

        file_put_contents("$path/assets/scss/_mixins.scss", $mixins);
        file_put_contents("$path/assets/scss/style.scss", $style);
    }
}

// Initialisation
Ng1BlockGenerator::get_instance();
