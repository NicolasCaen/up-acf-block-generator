<?php
namespace UP\AcfBlockGenerator\Core;

class Plugin {
    private static $instance = null;
    private $version = '1.0.0';
    private $plugin_path;
    private $plugin_url;

    public static function getInstance(): self {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->plugin_path = plugin_dir_path(dirname(__DIR__));
        $this->plugin_url = plugin_dir_url(dirname(__DIR__));
        $this->init();
    }

    private function init(): void {
        if (file_exists($this->plugin_path . 'vendor/autoload.php')) {
            require_once $this->plugin_path . 'vendor/autoload.php';
        }

        new \UP\AcfBlockGenerator\Admin\AdminPage();
    }

    public function getPluginPath(): string {
        return $this->plugin_path;
    }

    public function getPluginUrl(): string {
        return $this->plugin_url;
    }

    public function getVersion(): string {
        return $this->version;
    }

    public function getTemplatesPath(): string {
        return $this->plugin_path . 'config/default-templates/';
    }
} 