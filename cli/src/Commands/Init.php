<?php

declare(strict_types=1);

namespace AtusanCLI\Commands;

use AtusanCLI\Manifest;

class Init
{
    public function __construct(
        protected array $args
    ) {
    }

    public function handle(): void
    {
        if (Manifest::exists()) {
            throw new \RuntimeException(
                'atusan.json ya existe.'
            );
        }

        $projectName =
            $this->args['--name']
            ?? basename(APP_ROOT);

        $manifest =
            Manifest::create($projectName);

        Manifest::save($manifest);

        echo 'atusan.json creado exitosamente.'
            . EOL;
    }
}