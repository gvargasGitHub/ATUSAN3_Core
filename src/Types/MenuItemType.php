<?php

namespace Atusan\Types;

use Atusan\XML\XMLExtended;

class MenuItemType
{
  public string $itemname;
  public string $text;
  public string $icon;
  public string $route;
  public string $href;
  public string $view;

  function __construct(public string $menuname, protected int $number, protected XMLExtended $xml)
  {
    $this->itemname = ($xml->hasAttribute('name')) ? $xml->getAttribute('name') : "item{$itemNumber}";
    $this->text = ($xml->hasAttribute('text')) ? $xml->getAttribute('text') : '';
    $this->icon = ($xml->hasAttribute('icon')) ? $xml->getAttribute('icon') : '';
    $this->route = ($xml->hasAttribute('route')) ? $xml->getAttribute('route') : '';
    $this->href = ($xml->hasAttribute('href')) ? $xml->getAttribute('href') : '';
    $this->view = ($xml->hasAttribute('view')) ? $xml->getAttribute('view') : '';
  }

  public function pairs()
  {
    return implode(' ', [
      'ats-menuname="' . $this->menuname . '"',
      'ats-itemname="' . $this->itemname . '"',
      'ats-route="' . $this->route . '"'
    ]);
  }
}
