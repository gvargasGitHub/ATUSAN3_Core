<?php

namespace Atusan\Controller;

abstract class ModuleNested extends Module
{
  /* ----------------------------------
    TABGROUP SUPPORT
  ---------------------------------- */
  /**
   * Build Nested
   * Este método es invocado desde "NestedModule->nestedToJson"
   */
  public function buildNested(): string
  {
    ob_start();

    // $this->attachComponents();

    $this->index();

    $content = ob_get_contents();

    ob_clean();

    return $content;
  }

  /**
   * Nested
   * Este método es invicado por TabGroupContent.View.
   */
  public function nested()
  {
    echo $this->buildNested();
  }

  /**
   * Nested to Json
   * Este método es invocado por Route::process para las rutas establecidas
   * mediante Route::nested(Uri, Controller)
   */
  public function nestedToJson()
  {
    $this->response->add('name', $this->name);
    $this->response->add('title', $this->title);
    $this->response->add('content', $this->buildNested());
    $this->response->json();
  }
}
