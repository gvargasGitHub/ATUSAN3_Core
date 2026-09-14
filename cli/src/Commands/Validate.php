<?php

declare(strict_types=1);

namespace AtusanCLI\Commands;

use AtusanCLI\Manifest;

class Validate
{
    public function __construct(
        protected array $args
    ) {
    }

    public function handle(): void
    {
        $errors = Manifest::validate();

        if ($errors === []) {
            echo 'atusan.json es válido.'
                . EOL;

            return;
        }

        echo 'atusan.json contiene errores:'
            . EOL
            . EOL;

        foreach ($errors as $error) {
            echo '- ' . $error . EOL;
        }

        throw new \RuntimeException(
            'La validación no fue satisfactoria.'
        );
    }
}