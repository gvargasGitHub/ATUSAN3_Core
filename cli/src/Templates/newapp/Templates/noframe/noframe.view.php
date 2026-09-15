<?php
  /**
   * @file noframe.view.php
   * @brief Plantilla de marco (frame) para la vista de un módulo.
   * 
   * Esta plantilla se utiliza para envolver el contenido de un módulo en un marco HTML.
   */
  /** @var \Atusan\Controller\Module $module */
  ?>
<content>
  <?php self::$module->write() ?>
</content>