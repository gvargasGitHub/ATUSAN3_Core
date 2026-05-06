<?php

namespace Atusan\Http\Response;

use Atusan\Module\ModuleInterface;

interface ResponseInterface
{
  public function add(string $key, mixed $value): void;

  public function view(ModuleInterface $module): void;

  // public function nested(ModuleInterface $module): void;

  public function json(array $data = []): string;

  public function error(string $message, string $detail): void;
}
