<?php
# Recorre colección de datos para integrar fila por fila.
$nofRows = count($this->data);
for ($row = 0; $row < $nofRows; $row++) {
  foreach ($this->sources->getAllByName('Tbody') as $source) {
?>
    <tr class="row">
      <?php
      foreach ($source->xml->children() as $child) {
        $component = $this->components->getByName($child->getAttribute('name'));

        if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase')) {
          $component->setData($this->data[$row]);
          $component->setRow($row);
        }

        $component->write();
      }
      ?>
    </tr>
<?php
  }
}
