<?php
/**
 * Plugin Name: UP Acf Block Generator
 * Description: Générateur de blocs ACF
 * Version: 1.0.0
 * Author: UP
 */

if (!defined('ABSPATH')) {
    exit;
}

// Définition des constantes
define('UP_BLOCK_GENERATOR_PATH', plugin_dir_path(__FILE__));
define('UP_BLOCK_GENERATOR_URL', plugin_dir_url(__FILE__));
define('UP_BLOCK_GENERATOR_VERSION', '1.0.0');

// Chargement manuel des classes
require_once UP_BLOCK_GENERATOR_PATH . 'src/Core/Plugin.php';
require_once UP_BLOCK_GENERATOR_PATH . 'src/Admin/AdminPage.php';
require_once UP_BLOCK_GENERATOR_PATH . 'src/Generator/BlockGenerator.php';
require_once UP_BLOCK_GENERATOR_PATH . 'src/Generator/FileGenerator.php';
require_once UP_BLOCK_GENERATOR_PATH . 'src/Generator/TemplateLoader.php';

// Enregistrement des assets
function up_block_generator_register_assets() {
    wp_register_style(
        'up-block-generator-admin',
        UP_BLOCK_GENERATOR_URL . 'assets/css/admin.css',
        [],
        UP_BLOCK_GENERATOR_VERSION
    );

    wp_register_script(
        'up-block-generator-admin',
        UP_BLOCK_GENERATOR_URL . 'assets/js/admin.js',
        ['jquery'],
        UP_BLOCK_GENERATOR_VERSION,
        true
    );
}
add_action('admin_init', 'up_block_generator_register_assets');

// Initialisation du plugin
UP\AcfBlockGenerator\Core\Plugin::getInstance();