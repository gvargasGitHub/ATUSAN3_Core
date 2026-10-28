<?php
# Recorre colección de datos para integrar fila por fila.
$nofRows = count($this->data);
for ($row = 0; $row < $nofRows; $row++) {
  foreach ($this->sources->getAllByName('Tbody') as $source) {
?>
    <tr class="row">
      <?php
      foreach($this->namespaces as $ns=>$url) {
        foreach ($source->xml->children($ns, true) as $child) {
          $component = $this->components->getById($child->getAttribute('id'));

          if (is_subclass_of($component, 'Atusan\\Controls\\DataViewControlBase')) {
            $component->setData($this->data[$row]);
            $component->setRow($row);
          }

          echo $component->make();
        }
      }
      ?>
    </tr>
<?php
  }
}
