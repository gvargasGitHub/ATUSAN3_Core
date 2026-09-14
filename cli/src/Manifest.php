<?php

declare(strict_types=1);

namespace AtusanCLI;

class Manifest
{
    private const FILE = 'atusan.json';

    public static function path(): string
    {
        return APP_ROOT . DS . self::FILE;
    }

    public static function exists(): bool
    {
        return is_file(self::path());
    }

    public static function load(): array
    {
        if (!self::exists()) {
            throw new \RuntimeException(
                'No existe el archivo atusan.json.'
            );
        }

        $content = file_get_contents(self::path());

        if ($content === false) {
            throw new \RuntimeException(
                'No fue posible leer atusan.json.'
            );
        }

        try {
            $data = json_decode(
                $content,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $ex) {
            throw new \RuntimeException(
                'atusan.json contiene JSON inválido: '
                . $ex->getMessage()
            );
        }

        if (!is_array($data)) {
            throw new \RuntimeException(
                'atusan.json no contiene una estructura válida.'
            );
        }

        return $data;
    }

    public static function save(array $data): void
    {
        try {
            $json = json_encode(
                $data,
                JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
                | JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $ex) {
            throw new \RuntimeException(
                'No fue posible generar atusan.json: '
                . $ex->getMessage()
            );
        }

        if (file_put_contents(
            self::path(),
            $json . EOL
        ) === false) {
            throw new \RuntimeException(
                'No fue posible escribir atusan.json.'
            );
        }
    }

    public static function create(
        string $projectName
    ): array {
        $env = self::readRootEnv();

        $appsDirectory =
            $env['APPS_DIRECTORY'] ?? 'app';

        $activeApp =
            $env['APP_NAME'] ?? '';

        $environment =
            $env['APP_ENV'] ?? 'production';

        $manifest = [
            '$schema' => './schemas/atusan-3.schema.json',

            'framework' => [
                'name' => 'ATUSAN',
                'version' => '3',
                'php' => '>=8.2',
                'architecture' => 'mvc-modular'
            ],

            'project' => [
                'name' => $projectName,
                'environment' => $environment
            ],

            'paths' => [
                'public' => 'public',
                'entrypoint' => 'public/index.php',
                'applications' => $appsDirectory,
                'logs' => 'logs',
                'vendor' => 'vendor'
            ],

            'applications' => [
                'active' => $activeApp,
                'items' => []
            ],

            'frontend' => [
                'languages' => [
                    'HTML',
                    'CSS',
                    'JavaScript'
                ],
                'framework' => null,
                'buildTool' => null
            ],

            'agents' => [
                'instructions' => 'AGENTS.md',
                'stack' => 'STACK.md'
            ]
        ];

        foreach (
            self::discoverApplications($appsDirectory)
            as $app
        ) {
            $manifest['applications']['items'][$app] =
                self::applicationDefinition(
                    $appsDirectory,
                    $app
                );
        }

        return $manifest;
    }

    public static function registerApplication(
        string $appName
    ): void {
        if (!self::exists()) {
            return;
        }

        $manifest = self::load();

        $appsDirectory =
            $manifest['paths']['applications'] ?? 'app';

        $manifest['applications']['items'][$appName] =
            self::applicationDefinition(
                $appsDirectory,
                $appName
            );

        self::save($manifest);
    }

    public static function validate(): array
    {
        $errors = [];

        try {
            $manifest = self::load();
        } catch (\Throwable $ex) {
            return [$ex->getMessage()];
        }

        if (
            ($manifest['framework']['name'] ?? null)
            !== 'ATUSAN'
        ) {
            $errors[] =
                'framework.name debe ser ATUSAN.';
        }

        if (
            ($manifest['framework']['version'] ?? null)
            !== '3'
        ) {
            $errors[] =
                'framework.version debe ser 3.';
        }

        if (
            ($manifest['framework']['php'] ?? null)
            !== '>=8.2'
        ) {
            $errors[] =
                'framework.php debe ser >=8.2.';
        }

        if (
            empty($manifest['project']['name'])
        ) {
            $errors[] =
                'project.name es obligatorio.';
        }

        $appsDirectory =
            $manifest['paths']['applications'] ?? null;

        if (!$appsDirectory) {
            $errors[] =
                'paths.applications es obligatorio.';
        }

        $active =
            $manifest['applications']['active'] ?? '';

        $items =
            $manifest['applications']['items'] ?? [];

        if (
            $active !== ''
            && !isset($items[$active])
        ) {
            $errors[] =
                "La aplicación activa '{$active}' "
                . 'no está registrada.';
        }

        foreach ($items as $name => $definition) {
            $directory =
                $definition['directory'] ?? '';

            if (
                $directory === ''
                || !is_dir(APP_ROOT . DS . $directory)
            ) {
                $errors[] =
                    "No existe el directorio "
                    . "de la aplicación '{$name}'.";
            }

            $route =
                $definition['routing'] ?? 'Route.php';

            $routePath =
                APP_ROOT
                . DS
                . $directory
                . DS
                . $route;

            if (!is_file($routePath)) {
                $errors[] =
                    "La aplicación '{$name}' "
                    . 'no contiene Route.php.';
            }
        }

        return $errors;
    }

    private static function applicationDefinition(
        string $appsDirectory,
        string $appName
    ): array {
        return [
            'namespace' => 'App',
            'directory' =>
                $appsDirectory . '/' . $appName,
            'routing' => 'Route.php'
        ];
    }

    private static function discoverApplications(
        string $appsDirectory
    ): array {
        $path =
            APP_ROOT . DS . $appsDirectory;

        if (!is_dir($path)) {
            return [];
        }

        $apps = [];

        foreach (scandir($path) ?: [] as $item) {
            if (
                $item === '.'
                || $item === '..'
            ) {
                continue;
            }

            if (
                is_dir($path . DS . $item)
                && is_file(
                    $path
                    . DS
                    . $item
                    . DS
                    . 'Route.php'
                )
            ) {
                $apps[] = $item;
            }
        }

        sort($apps);

        return $apps;
    }

    private static function readRootEnv(): array
    {
        $path = APP_ROOT . DS . '.env';

        if (!is_file($path)) {
            return [];
        }

        $data = parse_ini_file(
            $path,
            false,
            INI_SCANNER_RAW
        );

        return is_array($data) ? $data : [];
    }
}