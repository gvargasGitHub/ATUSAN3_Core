<?php

namespace Atusan\Components;

use Atusan\Types\DataTreeDataType;

class DataTree extends DataViewBase
{
  protected int $numOfLevels = 0;
  protected int $levelsBound = -1;
  protected int $numOfItems = 0;

  /**
   * 
   */
  protected function setType()
  {
    $this->type = 'DataTree';
  }

  /**
   * 
   */
  protected function initProperties()
  {
    $this->numOfLevels = count($this->xml->Level);
    $this->levelsBound = $this->numOfLevels - 1;
    $this->numOfItems = 0;
  }

  /**
   * Build
   */
  public function build(): string
  {
    ob_start();

    include __DIR__ . DS . 'Views' . DS . strtolower($this->type) . '/body.view.php';

    $html = ob_get_contents();

    ob_clean();

    return $html;
  }

  /**
   * 
   */
  public function retrieve()
  {
    $this->data = $this->recursive(0, func_get_args());
  }

  /**
   * 
   */
  protected function recursive(int $level, array $args = [])
  {
    if ($level > $this->levelsBound) return [];

    $data = [];
    $model = $this->xml->Level[$level]->getAttribute('model');

    $modelResults = call_user_func_array($model, $args);

    for ($i = 0; $i < count($modelResults); $i++) {
      $this->numOfItems += 1;
      $type = new DataTreeDataType($this->numOfItems, $level, $modelResults[$i]);
      $type->children = $this->recursive(($level + 1), array_values($modelResults[$i]));

      $data[] = $type;
    }

    return $data;
  }

  /**
   * 
   */
  protected function writeBodyRecursive(array $data, int $level)
  {
    foreach ($data as $datatype) {
?>
      <li id="<?= $this->name ?>-<?= $datatype->index ?>" <?= $this->buildDataPairs($datatype->data) ?>>
        <table id="<?= $this->name ?>-<?= $datatype->index ?>-table" class="item">
          <tbody>
            <?php
            $container = $this->sources->getByPosition($level);
            echo "<tr>\n";
            foreach ($container->children() as $control) {
              $component = $this->components->getByName($control->getAttribute('name'));
              $component->setDataType($datatype);
              $component->write();
            }
            echo "</tr>\n";
            ?>
          </tbody>
        </table>
        <?php
        if ($datatype->hasChildren()) {
        ?>
          <ul id="<?= $this->name ?>-<?= $datatype->index ?>-content" class="content">
            <?php
            $this->writeBodyRecursive($datatype->children, $level + 1);
            ?>
          </ul>
        <?php
        }
        ?>
      </li>
<?php
    }
  }

  protected function buildDataPairs(array $data): string
  {
    $output = [];
    foreach ($data as $k => $v) $output[] = "data-{$k}=\"{$v}\"";

    return implode(' ', $output);
  }

  // ----------------------------------
  //  DataViewBase
  // ----------------------------------
  /**
   * Import
   */
  public function import(array $data): int
  {
    $this->data = (is_array($data) && array_diff_key($data, array_keys($data)))
      ? [$data] : $data;

    return count($this->data);
  }
  
  // ----------------------------------
  //  TraitComponent
  // ----------------------------------
  /**
   * 
   */
  protected function setSources(): void
  {
    $this->setType();

    $this->initProperties();

    // Recorre las secciones Level
    foreach ($this->xml->children() as $section) {
      $sectioName = (string) $section->getName();

      $this->addSource($sectioName, $section);
    }
  }

  /**
   * 
   */
  protected function finalDefinitions(): void
  {
    // Recorre cada Componente para localizar Controles y asignarles el "Padre"
    foreach ($this->components as $component) if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase')) $component->setParent($this);
  }

  /**
   * Write
   */
  public function write(): void
  {
    if (!property_exists($this, 'title')) $this->title = '';
    if (!property_exists($this, 'footer')) $this->footer = '';

    include __DIR__ . DS . 'Views' . DS . strtolower($this->type) . '/view.php';
  }
}
