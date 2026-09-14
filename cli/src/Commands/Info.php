<?php

declare(strict_types=1);

namespace AtusanCLI\Commands;

use AtusanCLI\Manifest;

class Info
{
    public function __construct(
        protected array $args
    ) {
    }

    public function handle(): void
    {
        $manifest = Manifest::load();

        $framework =
            $manifest['framework'];

        $project =
            $manifest['project'];

        $applications =
            $manifest['applications'];

        echo 'ATUSAN '
            . $framework['version']
            . EOL
            . EOL;

        echo 'Project' . EOL;
        echo '  Name:         '
            . $project['name']
            . EOL;

        echo '  PHP:          '
            . $framework['php']
            . EOL;

        echo '  Environment:  '
            . $project['environment']
            . EOL
            . EOL;

        echo 'Architecture' . EOL;
        echo '  Type:         '
            . $framework['architecture']
            . EOL
            . EOL;

        echo 'Applications' . EOL;

        $active =
            $applications['active'] ?? '';

        foreach (
            $applications['items'] ?? []
            as $name => $app
        ) {
            $marker =
                $name === $active
                ? '*'
                : ' ';

            echo " {$marker} {$name}"
                . EOL;

            echo '     Directory: '
                . $app['directory']
                . EOL;
        }
    }
}