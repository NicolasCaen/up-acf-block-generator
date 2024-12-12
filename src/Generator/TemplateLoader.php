<?php
namespace UP\AcfBlockGenerator\Generator;

class TemplateLoader {
    private $templates_path;

    public function __construct() {
        $this->templates_path = dirname(dirname(dirname(__FILE__))) . '/config/default-templates/';
    }

    public function getTemplate(string $template_name): string {
        $file_path = $this->templates_path . $template_name . '.dist';
        
        if (!file_exists($file_path)) {
            throw new \Exception("Template file not found: {$template_name}");
        }

        return file_get_contents($file_path);
    }

    public function parseTemplate(string $template, array $vars): string {
        return strtr($template, $vars);
    }
} 