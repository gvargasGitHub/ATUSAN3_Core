<?php

namespace Atusan\Http\Response;

use Atusan\Controller\Module;

interface ResponseInterface
{
  public function add(string $key, mixed $value): void;

  public function view(Module $module): void;

  public function json(array $data = []): void;

  public function exception(string $message, string $detail): void;

  public function notice(string $message): void;

  public function warning(string $message): void;

  public function unknow(string $message, string $detail): void;
}
