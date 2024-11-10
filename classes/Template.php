<?php

declare(strict_types=1);

namespace GpMachineTranslate;

class Template
{
    private array $templateFiles;

    public function __construct()
    {
        $directory = __DIR__ . '/../templates/';
        $files = glob($directory . '*.php');

        foreach ($files as $file) {
            $templateName = basename($file, '.php');
            $this->templateFiles[$templateName] = $file;
        }
    }

    public function render(string $template, array $templateData = []): string
    {
        if (!isset($this->templateFiles[$template])) {
            return str_replace(
                '{{ templateFile }}',
                $template,
                __('Unknown template file "{{ templateFile }}".', 'gp-machine-translate'),
            );
        }

        ob_start();

        include $this->templateFiles[$template];

        return ob_get_clean();
    }
}
