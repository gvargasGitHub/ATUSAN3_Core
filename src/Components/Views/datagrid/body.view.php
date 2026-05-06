<table id="<?= $this->name ?>-table">
  <thead>
    <?php
    foreach ($this->sources->getAllByName('Thead') as $source) {
    ?>
      <tr>
        <?php
        foreach ($source->xml->children() as $child) $this->components->getByName($child->getAttribute('name'))->write();
        ?>
      </tr>
    <?php
    }
    ?>
  </thead>
  <tbody class="detail">
    <?php include 'detail.view.php' ?>
  </tbody>
  <tbody class="summary"></tbody>
  <tfoot>
    <?php
    foreach ($this->sources->getAllByName('Tfoot') as $source) {
    ?>
      <tr>
        <?php
        foreach ($source->xml->children() as $child) $this->components->getByName($child->getAttribute('name'))->write();
        ?>
      </tr>
    <?php
    }
    ?>
  </tfoot>
</table>