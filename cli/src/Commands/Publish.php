<?php

namespace AtusanCLI\Commands;

class Publish extends MakeBase
{
  function required(): array
  {
    return [];
  }

  function defaults(): array
  {
    return [];
  }

  function resolver()
  {
    // Copia plantilla de la Aplicación
    self::copyR(
      ATUSANCLI_SRC . DS . 'Templates' . DS . 'public',
      APP_ROOT . DS . 'public'
    );

    echo "Public assets publicados." . EOL;
  }
}
