<?php
namespace UP\AcfBlockGenerator\Generator;

class BlockGenerator {
    private $template_loader;
    private $file_generator;

    public function __construct() {
        $this->template_loader = new TemplateLoader();
        $this->file_generator = new FileGenerator();
    }

    public function generateBlock(array $config): bool {
        try {
            $block_path = $this->createBlockDirectory($config);
            $this->generateBlockFiles($block_path, $config);
            return true;
        } catch (\Exception $e) {
            // Log error
            return false;
        }
    }

    private function createBlockDirectory(array $config): string {
        $base_path = $config['location'] === 'theme' 
            ? get_stylesheet_directory() . '/acf-blocks/' 
            : WPMU_PLUGIN_DIR . '/acf-blocks/';

        $block_path = $base_path . $config['name'];
        
        $this->file_generator->createDirectoryStructure($block_path);
        
        return $block_path;
    }

    private function generateBlockFiles(string $block_path, array $config): void {
        $templates = [
            'block.json' => [
                'path' => $block_path, 
                'vars' => $this->getBlockJsonVars($config)
            ],
            'fields.json' => [
                'path' => $block_path . '/acf', 
                'vars' => $this->getFieldsJsonVars($config)
            ],
            'view.php' => [
                'path' => $block_path, 
                'vars' => []
            ],
            'function.js' => [
                'path' => $block_path . '/assets/js', 
                'vars' => []
            ],
            '_mixins.scss' => [
                'path' => $block_path . '/assets/scss', 
                'vars' => []
            ],
            'style.scss' => [
                'path' => $block_path . '/assets/scss', 
                'vars' => ['block_name' => $config['name']]
            ],
        ];

        foreach ($templates as $template_name => $data) {
            $content = $this->template_loader->getTemplate($template_name);
            $content = $this->template_loader->parseTemplate($content, $data['vars']);
            $this->file_generator->createFile($data['path'] . '/' . $template_name, $content);
        }
    }

    private function getBlockJsonVars(array $config): array {
        // Construction du tableau supports
        $supports = [
            'html' => false,
            'jsx' => true
        ];

        if (!empty($config['supports'])) {
            foreach ($config['supports'] as $support) {
                switch ($support) {
                    case 'color':
                        $supports['color'] = [
                            'background' => true,
                            'text' => true,
                            'link' => true
                        ];
                        break;
                    case 'spacing':
                        $supports['spacing'] = [
                            'margin' => true,
                            'padding' => true
                        ];
                        break;
                    case 'typography':
                        $supports['typography'] = [
                            'fontSize' => true,
                            'lineHeight' => true
                        ];
                        break;
                    default:
                        $supports[$support] = true;
                }
            }
        }

        // Traitement des mots-clés
        $keywords = ['up'];
        if (!empty($config['keywords'])) {
            $custom_keywords = array_map('trim', explode(',', $config['keywords']));
            $keywords = array_merge($keywords, array_slice($custom_keywords, 0, 2));
        }

        // Variables de remplacement pour le template
        $vars = [
            '{{block_name}}' => "up/{$config['name']}",
            '{{block_title}}' => $config['title'],
            '{{block_description}}' => !empty($config['description']) ? $config['description'] : 'Description du bloc',
            '{{block_icon}}' => !empty($config['icon']) ? $config['icon'] : 'admin-comments',
            '{{block_keywords}}' => json_encode($keywords),
            '{{block_supports}}' => json_encode($supports, JSON_PRETTY_PRINT)
        ];

        return $vars;
    }

    private function getFieldsJsonVars(array $config): array {
        return [
            '{{block_name}}' => "ng1/{$config['name']}",
            '{{block_title}}' => $config['title'],
            '{{group_key}}' => 'group_' . wp_generate_uuid4(),
        ];
    }
} 