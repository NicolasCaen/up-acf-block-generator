<?php
namespace UP\AcfBlockGenerator\Generator;

class FileGenerator {
    public function createDirectoryStructure(string $base_path): void {
        $directories = [
            '',
            '/acf',
            '/assets/js',
            '/assets/scss'
        ];

        foreach ($directories as $dir) {
            wp_mkdir_p($base_path . $dir);
        }
    }

    public function createFile(string $path, string $content): void {
        file_put_contents($path, $content);
    }
} 